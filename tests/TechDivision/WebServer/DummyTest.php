<?php
/**
 * \TechDivision\WebSocketProtocol\DummyTest
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the Open Software License (OSL 3.0)
 * that is available through the world-wide-web at this URL:
 * http://opensource.org/licenses/osl-3.0.php
 *
 * PHP version 5
 *
 * @category   Appserver
 * @package    TechDivision_WebSocketProtocol
 * @subpackage Tests
 * @author    Bernhard Wick <b.wick@techdivision.com>
 * @copyright  2014 TechDivision GmbH <info@techdivision.com>
 * @license    http://opensource.org/licenses/osl-3.0.php Open Software License (OSL 3.0)
 * @link       https://github.com/techdivision/TechDivision_Http
 */

namespace TechDivision\WebSocketProtocol;

/**
 * Class DummyTest
 *
 * @category   Appserver
 * @package    TechDivision_WebSocketProtocol
 * @subpackage Tests
 * @author    Bernhard Wick <b.wick@techdivision.com>
 * @copyright  2014 TechDivision GmbH <info@techdivision.com>
 * @license    http://opensource.org/licenses/osl-3.0.php Open Software License (OSL 3.0)
 * @link       https://github.com/techdivision/TechDivision_Http
 */
class DummyTest extends \PHPUnit_Framework_TestCase
{
    /**
     * Test set server var functionality on response object.
     *
     * @return void
     */
    public function testDummy()
    {
        $this->assertSame(1, 1);
    }
}
