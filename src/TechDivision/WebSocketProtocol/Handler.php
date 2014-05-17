<?php

/**
 * TechDivision\WebSocketProtocol\Handler
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

use Ratchet\MessageComponentInterface;
use TechDivision\WebSocketProtocol\HandlerConfig;

/**
 * Interface for all handlers.
 *
 * @category  Library
 * @package   TechDivision_WebSocketProtocol
 * @author    Tim Wagner <tw@techdivision.com>
 * @copyright 2014 TechDivision GmbH <info@techdivision.com>
 * @license   http://opensource.org/licenses/osl-3.0.php Open Software License (OSL 3.0)
 * @link      https://github.com/techdivision/TechDivision_WebSocketProtocol
 * @link      http://www.appserver.io
 */
interface Handler extends MessageComponentInterface
{

    /**
     * Initializes the handler with the passed configuration.
     *
     * @param \TechDivision\WebSocketProtocol\HandlerConfig $config The configuration to initialize the handler with
     *
     * @return void
     * @throws \TechDivision\WebSocketProtocol\HandlerException Is thrown if the configuration has errors
     */
    public function init(HandlerConfig $config);

    /**
     * Return's the servlets configuration.
     *
     * @return \TechDivision\WebSocketProtocol\HandlerConfig The handlers configuration
     */
    public function getHandlerConfig();

    /**
     * Returns the handler context instance
     *
     * @return \TechDivision\WebSocketProtocol\HandlerContext The handler context instance
     */
    public function getHandlerContext();
}
