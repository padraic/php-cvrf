<?php
/**
 * php-cvrf
 *
 * LICENSE
 *
 * This source file is subject to the new BSD license that is bundled
 * with this package in the file LICENSE.txt.
 * It is also available through the world-wide-web at this URL:
 * https://github.com/padraic/php-cvrf/blob/master/LICENSE
 * If you did not receive a copy of the license and are unable to
 * obtain it through the world-wide-web, please send an email
 * to padraic@php.net so we can send you a copy immediately.
 *
 * @category   php-cvrf
 * @package    CvrfParser
 * @subpackage php-cvrf
 * @copyright  Copyright (c) 2013 Pádraic Brady (http://blog.astrumfutura.com)
 * @license    http://github.com/padraic/php-cvrf/blob/master/LICENSE New BSD License
 */

class PhpCvrf_LoaderTest extends PHPUnit_Framework_TestCase
{

    public function setUp()
    {
        spl_autoload_unregister('\PhpCvrf\Loader::loadClass');
    }

    public function testCallingRegisterRegistersSelfAsSplAutoloaderFunction()
    {
        require_once 'PhpCvrf/Loader.php';
        $loader = new \PhpCvrf\Loader;
        $loader->register();
        $expected = array($loader, 'loadClass');
        $this->assertTrue(in_array($expected, spl_autoload_functions()));
    }

    public function tearDown()
    {
        spl_autoload_unregister('\PhpCvrf\Loader::loadClass');
        $loader = new \PhpCvrf\Loader;
        $loader->register();
    }

}
