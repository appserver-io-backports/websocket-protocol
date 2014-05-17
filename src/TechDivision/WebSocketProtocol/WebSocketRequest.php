<?php

/**
 * TechDivision\WebSocketProtocol\WebSocketRequest
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the Open Software License (OSL 3.0)
 * that is available through the world-wide-web at this URL:
 * http://opensource.org/licenses/osl-3.0.php
 *
 * PHP version 5
 *
 * @category  Library
 * @package   TechDivision_WebSocketProtocol
 * @author    Tim Wagner <tw@techdivision.com>
 * @copyright 2014 TechDivision GmbH <info@techdivision.com>
 * @license   http://opensource.org/licenses/osl-3.0.php Open Software License (OSL 3.0)
 * @link      https://github.com/techdivision/TechDivision_WebSocketProtocol
 * @link      http://www.appserver.io
 */
namespace TechDivision\WebSocketProtocol;

use Guzzle\Http\Message\RequestInterface;
use TechDivision\ApplicationServer\Interfaces\ApplicationInterface;

/**
 * A simple websocket request implementation.
 *
 * @category  Library
 * @package   TechDivision_WebSocketProtocol
 * @author    Tim Wagner <tw@techdivision.com>
 * @copyright 2014 TechDivision GmbH <info@techdivision.com>
 * @license   http://opensource.org/licenses/osl-3.0.php Open Software License (OSL 3.0)
 * @link      https://github.com/techdivision/TechDivision_WebSocketProtocol
 * @link      http://www.appserver.io
 */
class WebSocketRequest implements Request
{

    /**
     * The guzzle request instance passed from ratchet.
     *
     * @var \Guzzle\Http\Message\RequestInterface
     */
    protected $request;

    /**
     * The application context instance.
     *
     * @var \TechDivision\ApplicationServer\Interfaces\ApplicationInterface
     */
    protected $context;

    /**
     * The context path (application name).
     *
     * @var string
     */
    protected $contextPath;

    /**
     * The path to the handler, that is always absolute to the context path.
     *
     * @var string
     */
    protected $handlerPath;

    /**
     * Injects the guzzle request instance passed from ratchet.
     *
     * @param \Guzzle\Http\Message\RequestInterface $request The guzzle request passed from ratchet
     *
     * @return void
     * @see \TechDivision\WebSocketProtocol\Request::injectRequest()
     */
    public function injectRequest(RequestInterface $request)
    {
        $this->request = $request;
    }

    /**
     * Injects the request context that is the web application almost.
     *
     * @param \TechDivision\ApplicationServer\Interfaces\ApplicationInterface $context The request context instance
     *
     * @return void
     */
    public function injectContext(ApplicationInterface $context)
    {
        $this->context = $context;
    }

    /**
     * Returns the guzzle request instance passed from ratchet.
     *
     * @return \Guzzle\Http\Message\RequestInterface The guzzle request instance
     * @see \TechDivision\WebSocketProtocol\Request::getRequest()
     */
    public function getRequest()
    {
        return $this->request;
    }

    /**
     * Returns the request context that is the web application almost.
     *
     * @return \TechDivision\ApplicationServer\Interfaces\ApplicationInterface The request context
     * @see \TechDivision\WebSocketProtocol\Request::getContext()
     */
    public function getContext()
    {
        return $this->context;
    }

    /**
     * Sets the path the handler that is always absolute from the context path.
     *
     * @param string $handlerPath The path to the handler
     *
     * @return void
     */
    public function setHandlerPath($handlerPath)
    {
        $this->handlerPath = $handlerPath;
    }

    /**
     * Returns the path the handler that is always absolute from the context path.
     *
     * @return string The path to the handler
     * @see \TechDivision\WebSocketProtocol\Request::getHandlerPath()
     */
    public function getHandlerPath()
    {
        return $this->handlerPath;
    }

    /**
     * Sets the context path (application name).
     *
     * @param string $contextPath The context path
     *
     * @return void
     */
    public function setContextPath($contextPath)
    {
        $this->contextPath = $contextPath;
    }

    /**
     * Returns the context path (application name).
     *
     * @return string The context path
     * @see \TechDivision\WebSocketProtocol\Request::getHandlerPath()
     */
    public function getContextPath()
    {
        return $this->contextPath;
    }

    /**
     * Returns the host that handles this request.
     *
     * @return string The host name that handles this request
     * @see \TechDivision\WebSocketProtocol\Request::getHost()
     */
    public function getHost()
    {
        return $this->getRequest()->getHost();
    }

    /**
     * Returns the request path, that will contain the application
     * name if we're not in an virtual host.
     *
     * @return string The request path
     * @see \TechDivision\WebSocketProtocol\Request::getPath()
     */
    public function getPath()
    {
        return $this->getRequest()->getPath();
    }
}
