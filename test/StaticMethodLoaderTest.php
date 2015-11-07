<?php
/*
 * This file is part of chrisguitarguy/static-method-loader
 *
 * Copyright (c) Christopher Davis <http://christopherdavis.me>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Chrisguitarguy\StaticMethodLoader;

use Symfony\Component\Routing;
use Symfony\Component\Config\Loader\DelegatingLoader;
use Symfony\Component\Config\Loader\Loader;
use Symfony\Component\Config\Loader\LoaderResolver;
use Symfony\Component\Config\Resource\FileResource;

class StaticMethodLoaderTest extends \PHPUnit_Framework_TestCase
{
    private static $routes = null;

    /**
     * @expectedException InvalidArgumentException
     */
    public function testUnsupportedResourceTypesCauseErrors()
    {
        $router = self::makeRouter('test.yml', 'yaml');
        $router->match('/');
    }

    /**
     * @expectedException InvalidArgumentException
     * @expectedExceptionMessage expects resources to be in the format FCQN::methodName
     */
    public function testInvalidResourceFormatCausesError()
    {
        $router = self::makeRouter('DoesNotHaveADoubleColon');
        $router->match('/');
    }

    /**
     * @expectedException LogicException
     * @expectedExceptionMessage class DoesNotExist does not exist
     */
    public function testNotExistentClassInResourceNameCausesError()
    {
        $router = self::makeRouter('DoesNotExist::method');
        $router->match('/');
    }

    /**
     * @expectedException LogicException
     * @expectedExceptionMessage Chrisguitarguy\StaticMethodLoader\StaticMethodLoaderTest::notExist is not callable
     */
    public function testInvalidMethodNameCausesError()
    {
        $router = self::makeRouter(__CLASS__.'::notExist');
        $router->match('/');
    }

    public function testValidResourceCallsTheStaticMethodAndLoadRoutes()
    {
        $router = self::makeRouter(TestLoader::class.'::load');
        $match = $router->match('/');

        $this->assertArrayHasKey('_route', $match);
        $this->assertEquals('home', $match['_route']);
        $this->assertInstanceOf(Routing\RouteCollection::class, TestLoader::$routes, sprintf(
            'should have called %s::load with a route collection',
            TestLoader::class
        ));
        $this->assertEquals([new FileResource(__FILE__)], TestLoader::$routes->getResources());
    }

    public function testStaticMethodsCanImportRoutesFromOtherLoaders()
    {
        $router = new Routing\Router(new DelegatingLoader(new LoaderResolver([
            new StaticMethodLoader(),
        ])), ImportingTestLoader::class.'::load', ['resource_type' => 'staticmethod']);

        $match = $router->match('/');

        $this->assertArrayHasKey('_route', $match);
        $this->assertEquals('home', $match['_route']);
        $this->assertInstanceOf(Routing\RouteCollection::class, TestLoader::$routes, sprintf(
            'should have called %s::load with a route collection',
            TestLoader::class
        ));
        $this->assertEquals([new FileResource(__FILE__)], TestLoader::$routes->getResources());
    }

    protected function setUp()
    {
        TestLoader::$routes = null;
        ImportingTestLoader::$routes = null;
    }

    private static function makeRouter($resource, $resourceType=StaticMethodLoader::TYPE)
    {
        return new Routing\Router(new StaticMethodLoader(), $resource, [
            'resource_type'     => $resourceType,
        ]);
    }
}

final class TestLoader
{
    public static $routes = null;

    public static function load(Routing\RouteCollection $routes)
    {
        self::$routes = $routes;
        $routes->add('home', new Routing\Route('/'));
    }
}

final class ImportingTestLoader
{
    public static $routes = null;

    public static function load(Routing\RouteCollection $routes, Loader $loader)
    {
        self::$routes = $routes;
        $routes->addCollection($loader->import(TestLoader::class.'::load', 'staticmethod'));
    }
}
