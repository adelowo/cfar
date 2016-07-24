<?php

namespace Adelowo\Cfar\Tests;


use Adelowo\Cfar\Cfar;
use Aura\Router\Map;
use Aura\Router\Route;
use Aura\Router\Matcher;
use Adelowo\Cfar\CfarException;
use Aura\Router\RouterContainer;
use Zend\Diactoros\ServerRequestFactory;
use Adelowo\Cfar\Tests\Fixtures\HomeController;

class CfarTest extends \PHPUnit_Framework_TestCase
{

    /**
     * @var Route
     */
    protected $route;

    /**
     * @var Cfar
     */
    protected $cfar;

    public function setUp()
    {
        $this->route = new Route();
        parent::setUp();
    }

    protected function getCfar(Route $route)
    {
        return $this->cfar = new Cfar($route);
    }

    public function tearDown()
    {
        parent::tearDown();
    }


    public function testCfarInvokesTheRightMethodAndInjectsTheExpectedParameters()
    {
        $route = $this->route->path('/users/10/adelowo')
            ->attributes(["10", "adelowo"])
            ->handler(HomeController::class)
            ->extras(["listener" => "showUser"]);

        $this->getCfar($route)->dispatch();

        $this->assertEquals("showUser", $this->cfar->getMethod());
        $this->assertEquals($route->attributes, $this->cfar->getParameters());
    }

    public function testCfarCallsRightControllerAndDispatchesToTheDefaultMethod()
    {
        $controller = '\\Adelowo\\Cfar\\Tests\\Fixtures\\HomeController';

        $route = $this->route->path("users")
            ->attributes([])
            ->handler($controller);

        $this->getCfar($route)->dispatch();
        $cfarController = $this->cfar->getController();

        $this->assertInstanceOf($controller, new $cfarController);
        $this->assertEquals(Cfar::CFAR_DEFAULT_METHOD, $this->cfar->getMethod());
    }

    public function testCfarExceptionisThrown()
    {
        $route = $this->route->path('/users/10/adelowo')
            ->attributes(["10", "adelowo"])
            ->handler(UnKnownController::class)
            ->extras(["listener" => "showUser"]);

        try {
            $this->getCfar($route)->dispatch();
        } catch (CfarException $e) {
            $this->assertStringStartsWith("Invalid route declaration", $e->getMessage());
        }
    }

}
