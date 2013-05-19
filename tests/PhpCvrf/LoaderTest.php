<?php
/**
 * CvrfParser
 *
 * LICENSE
 *
 * This source file is subject to the new BSD license that is bundled
 * with this package in the file LICENSE.txt.
 * It is also available through the world-wide-web at this URL:
 * https://github.com/padraic/CvrfParser/blob/master/LICENSE
 * If you did not receive a copy of the license and are unable to
 * obtain it through the world-wide-web, please send an email
 * to padraic@php.net so we can send you a copy immediately.
 *
 * @category   CvrfParser
 * @package    CvrfParser
 * @subpackage UnitTests
 * @copyright  Copyright (c) 2013 Pádraic Brady (http://blog.astrumfutura.com)
 * @license    http://github.com/padraic/CvrfParser/blob/master/LICENSE New BSD License
 */

class CvrfParser_LoaderTest extends PHPUnit_Framework_TestCase
{

    public function setUp()
    {
        spl_autoload_unregister('\CvrfParser\Loader::loadClass');
    }

    public function testCallingRegisterRegistersSelfAsSplAutoloaderFunction()
    {
        require_once 'CvrfParser/Loader.php';
        $loader = new \CvrfParser\Loader;
        $loader->register();
        $expected = array($loader, 'loadClass');
        $this->assertTrue(in_array($expected, spl_autoload_functions()));
    }

    public function tearDown()
    {
        spl_autoload_unregister('\CvrfParser\Loader::loadClass');
        $loader = new \CvrfParser\Loader;
        $loader->register();
    }

}
