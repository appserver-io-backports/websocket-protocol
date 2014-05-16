<?php

/**
 * TechDivision\WebSocketProtocol\WebSocketConnectionHandler
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the Open Software License (OSL 3.0)
 * that is available through the world-wide-web at this URL:
 * http://opensource.org/licenses/osl-3.0.php
 */
namespace TechDivision\WebSocketProtocol;

use Ratchet\WebSocket\WsServer;
use Ratchet\MessageComponentInterface;
use Ratchet\ConnectionInterface;
use Ratchet\WebSocket\Version;
use Ratchet\WebSocket\Encoding\ToggleableValidator;
use Ratchet\WebSocket\HttpRequestParser;
use Ratchet\WebSocket\VersionManager;
use Ratchet\WebSocket\Version\RFC6455;
use Ratchet\WebSocket\Version\HyBi10;
use Ratchet\WebSocket\Version\Hixie76;
use Guzzle\Http\Message\Response;
use Guzzle\Http\Message\RequestInterface;
use TechDivision\WebServer\Interfaces\ServerContextInterface;

/**
 * The adapter to handle WebSocket requests/responses.
 *
 * This is a mediator between the Server and the applications provided by
 * the container to handle real-time messaging through a web browser.
 *
 * @package TechDivision\WebSocketContainer
 * @copyright Copyright (c) 2010 <info@techdivision.com> - TechDivision GmbH
 * @license http://opensource.org/licenses/osl-3.0.php
 *          Open Software License (OSL 3.0)
 * @author Tim Wagner <tw@techdivision.com>
 * @link http://ca.php.net/manual/en/ref.http.php
 * @link http://dev.w3.org/html5/websockets/
 */
class WebSocketConnectionHandler implements MessageComponentInterface
{

    /**
     * Buffers incoming HTTP requests returning a Guzzle Request when coalesced.
     *
     * @var HttpRequestParser @note May not expose this in the future, may do through facade methods
     */
    public $reqParser;

    /**
     * Manage the various WebSocket versions to support.
     *
     * @var VersionManager @note May not expose this in the future, may do through facade methods
     */
    public $versioner;

    /**
     * Array with the applications to handle.
     *
     * @var array
     */
    protected $applications;

    /**
     * Storage for the connections.
     *
     * @var \SplObjectStorage
     */
    protected $connections;

    /**
     * For now, array_push accepted subprotocols to this array.
     *
     * @deprecated @temporary
     */
    protected $acceptedSubProtocols = array();

    /**
     * UTF-8 validator.
     *
     * @var \Ratchet\WebSocket\Encoding\ValidatorInterface
     */
    protected $validator;

    /**
     * Flag if we have checked the decorated component for sub-protocols.
     *
     * @var boolean
     */
    private $isSpGenerated = false;

    /**
     * The server context instance.
     *
     * @var \TechDivision\WebServer\Interfaces\ServerContextInterface
     */
    protected $serverContext;

    /**
     * Holds an array of modules to use for connection handler.
     *
     * @var array
     */
    protected $modules;

    /**
     * Initialize the web socket server with the container's applications.
     *
     * @param array $applications
     *            The initialized applications
     * @return void
     */
    public function __construct(&$applications)
    {
        // initialize the web socket server instance
        $this->reqParser = new HttpRequestParser();
        $this->versioner = new VersionManager();
        $this->validator = new ToggleableValidator();

        // enable the allowed web socket versions
        $this->versioner->enableVersion(new Version\RFC6455($this->validator))
            ->enableVersion(new Version\HyBi10($this->validator))
            ->enableVersion(new Version\Hixie76());

        // initialize connection pool and applications
        $this->connections = new \SplObjectStorage();
        $this->applications = &$applications;
    }

    /**
     * Inits the connection handler by given context and params
     *
     * @param \TechDivision\WebServer\Interfaces\ServerContextInterface $serverContext The servers context
     * @param array                                                     $params        The params for connection handler
     *
     * @return void
     */
    public function init(ServerContextInterface $serverContext, array $params = null)
    {

        // set server context
        $this->serverContext = $serverContext;

        // register shutdown handler
        register_shutdown_function(array(&$this, "shutdown"));
    }

    /**
     * Does shutdown logic for worker if something breaks in process.
     *
     * @return void
     */
    public function shutdown()
    {
    	// do nothing here
    }

    /**
     * Injects all needed modules for connection handler to process
     *
     * @param array $modules An array of Modules
     *
     * @return void
     */
    public function injectModules($modules)
    {
        $this->modules = $modules;
    }

    /**
     * Returns all needed modules as array for connection handler to process
     *
     * @return array An array of Modules
     */
    public function getModules()
    {
        return $this->modules;
    }

    /**
     * Returns the worker instance which starte this worker thread
     *
     * @return \TechDivision\WebServer\Interfaces\WorkerInterface
     */
    protected function getWorker()
    {
        return $this->worker;
    }

    /**
     * (non-PHPdoc)
     *
     * @see \Ratchet\ComponentInterface::onOpen()
     */
    public function onOpen(ConnectionInterface $conn)
    {
        $conn->WebSocket = new \StdClass();
        $conn->WebSocket->established = false;
    }

    /**
     * (non-PHPdoc)
     *
     * @see \Ratchet\MessageInterface::onMessage()
     */
    public function onMessage(ConnectionInterface $from, $msg)
    {
        if (true === $from->WebSocket->established) {
            return $from->WebSocket->version->onMessage($this->connections[$from], $msg);
        }

        if (isset($from->WebSocket->request)) {
            ;
            $from->WebSocket->request->getBody()->write($msg);
        } else {

            try {
                if (null === ($request = $this->reqParser->onMessage($from, $msg))) {
                    return;
                }
            } catch (\OverflowException $oe) {
                return $this->close($from, 413);
            }

            if (! $this->versioner->isVersionEnabled($request)) {
                return $this->close($from);
            }

            $from->WebSocket->request = $request;
            $from->WebSocket->version = $this->versioner->getVersion($request);
        }

        try {
            $response = $from->WebSocket->version->handshake($from->WebSocket->request);
        } catch (\UnderflowException $e) {
            return;
        }

        if (null !== ($subHeader = $from->WebSocket->request->getHeader('Sec-WebSocket-Protocol'))) {
            if ('' !== ($agreedSubProtocols = $this->getSubProtocolString($subHeader->normalize()))) {
                $response->setHeader('Sec-WebSocket-Protocol', $agreedSubProtocols);
            }
        }

        $response->setHeader('X-Powered-By', \Ratchet\VERSION);
        $from->send((string) $response);

        if (101 != $response->getStatusCode()) {
            return $from->close();
        }

        // locate handler and initialize it
        $handler = $this->locateHandler($request);
        $upgraded = $from->WebSocket->version->upgradeConnection($from, $handler);
        $this->connections->attach($from, $upgraded);
        $upgraded->WebSocket->established = true;
        return $handler->onOpen($upgraded);
    }

    /**
     * Locates the web socket handler for the passed request.
     *
     * @param \Guzzle\Http\Message\RequestInterface $request
     *            The request to find and return the application instance for
     * @return \Ratchet\MessageComponentInterface The handler instance
     */
    public function locateHandler(RequestInterface $guzzleRequest)
    {

    	// initialize a new web socket request
		$request = new WebSocketRequest();
		$request->injectRequest($guzzleRequest);

        return $this->findApplication($request)->locateHandler($request);
    }

    /**
     * Tries to find and return the application for the passed request.
     *
     * @param \Guzzle\Http\Message\RequestInterface $request
     *            The request to find and return the application instance for
     * @return \TechDivision\WebSocketContainer\Application The application instance
     * @throws \TechDivision\WebSocketContainer\Exceptions\BadRequestException Is thrown if no application can be found for the passed application name
     */
    public function findApplication(Request $request)
    {

        // load the path information and the server name
        $host = $request->getHost();
        $pathInfo = $request->getPath();

        // strip the leading slash and explode the application name
        list ($applicationName, $path) = explode('/', substr($pathInfo, 1));

        // if not, check if the request matches a folder
        if (array_key_exists($applicationName, $this->applications)) {

        	$application = $this->applications[$applicationName];

        } else { // iterate over the applications and check if one of the virtual hosts match the request

	        foreach ($this->applications as $application) {
	            if ($application->isVhostOf($host)) {
	                break;
	            }
	        }
        }

        // if not throw an exception if we can't find an application
        if ($application == null) {
        	throw new BadRequestException("Can't find application for '$applicationName'");
        }

        // prepare and set the applications context path
        $request->setContextPath($contextPath = '/' . $application->getName());

        // prepare the path information depending if we're in a vhost or not
        if ($application->isVhostOf($host) === false) {
            $request->setHandlerPath(str_replace($contextPath, '', $request->getHandlerPath()));
        }

        // inject the application context into the handler request
        $request->injectContext($application);

        // return, because request has been prepared successfully
        return $application;
    }

    /**
     * (non-PHPdoc)
     *
     * @see \Ratchet\ComponentInterface::onClose()
     */
    public function onClose(ConnectionInterface $conn)
    {
        if ($this->connections->contains($conn)) {
            $decor = $this->connections[$conn];
            $this->connections->detach($conn);
            foreach ($this->applications as $application) {
                foreach ($application->getHandlerManager()->getHandler() as $handler) {
                    $handler->onClose($decor);
                }
            }
        }
    }

    /**
     * (non-PHPdoc)
     *
     * @see \Ratchet\ComponentInterface::onError()
     */
    public function onError(ConnectionInterface $conn, \Exception $e)
    {
        if ($conn->WebSocket->established) {
            foreach ($this->applications as $application) {
                foreach ($application->getHandlerManager()->getHandler() as $handler) {
                    $handler->onError($this->connections[$conn], $e);
                }
            }
        } else {
            $conn->close();
        }
    }

    /**
     * Disable a specific version of the WebSocket protocol
     *
     * @param int $versionId
     *            Version ID to disable
     * @return WsServer
     */
    public function disableVersion($versionId)
    {
        $this->versioner->disableVersion($versionId);
        return $this;
    }

    /**
     * Toggle weather to check encoding of incoming messages
     *
     * @param
     *            bool
     * @return WsServer
     */
    public function setEncodingChecks($opt)
    {
        $this->validator->on = (boolean) $opt;
        return $this;
    }

    /**
     *
     * @param
     *            string
     * @return boolean
     */
    public function isSubProtocolSupported($name)
    {
        if ($this->isSpGenerated === false) {
            foreach ($this->applications as $application) {
                foreach ($application->getHandlerManager()->getHandler() as $handler) {
                    if ($this->_decorating instanceof WsServerInterface) {
                        $this->acceptedSubProtocols = array_merge($this->acceptedSubProtocols, array_flip($handler->getSubProtocols()));
                    }
                }
            }
            $this->isSpGenerated = true;
        }
        return array_key_exists($name, $this->acceptedSubProtocols);
    }

    /**
     *
     * @param \Traversable|null $requested
     * @return string
     */
    protected function getSubProtocolString(\Traversable $requested = null)
    {
        if (null === $requested) {
            return '';
        }

        $result = array();

        foreach ($requested as $sub) {
            if ($this->isSubProtocolSupported($sub)) {
                $result[] = $sub;
            }
        }

        return implode(',', $result);
    }

    /**
     * Close a connection with an HTTP response.
     *
     * @param \Ratchet\ConnectionInterface $conn
     * @param int $code
     *            HTTP status code
     * @return void
     */
    protected function close(ConnectionInterface $conn, $code = 400)
    {
        $response = new Response($code, array(
            'Sec-WebSocket-Version' => $this->versioner->getSupportedVersionString(),
            'X-Powered-By' => Ratchet\VERSION
        ));
        $conn->send((string) $response);
        $conn->close();
    }
}