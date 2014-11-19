<?php

/**
 * TechDivision\WebSocketProtocol\HandlerContext
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

use TechDivision\Application\Interfaces\ManagerInterface;

/**
 * The handler context inteface for all handler managers.
 *
 * @category  Library
 * @package   TechDivision_WebSocketProtocol
 * @author    Tim Wagner <tw@techdivision.com>
 * @copyright 2014 TechDivision GmbH <info@techdivision.com>
 * @license   http://opensource.org/licenses/osl-3.0.php Open Software License (OSL 3.0)
 * @link      https://github.com/techdivision/TechDivision_WebSocketProtocol
 * @link      http://www.appserver.io
 */
interface HandlerContext extends ManagerInterface
{

    /**
     * The unique identifier to be registered in the application context.
     *
     * @var string
     */
    const IDENTIFIER = 'HandlerContext';

    /**
     * Returns the registered handlers.
     *
     * @return array An array with the initialized web socket handlers
     */
    public function getHandlers();

    /**
     * Returns the registered handlers.
     *
     * @param string $key The key the handler to be returned has been registered with.
     *
     * @return \Ratchet\MessageComponentInterface The requested handler
     */
    public function getHandler($key);

    /**
     * Returns the path to the webapp.
     *
     * @return string The path to the webapp
     */
    public function getWebappPath();
}
