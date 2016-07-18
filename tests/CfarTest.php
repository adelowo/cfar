<?php

namespace Adelowo\Cfar;

require_once 'fixtures/GlobalController.php';
require_once 'fixtures/HomeController.php';

use adelowo\controller\HomeController;
use Aura\Router\Map;
use Aura\Router\Matcher;
use Aura\Router\Route;
use Aura\Router\RouterContainer;
use Zend\Diactoros\ServerRequestFactory;

class CfarTest extends \PHPUnit_Framework_TestCase
{
<<<<<<< HEAD
    public function setUp()
    {

    }

    public function tearDown()
    {

    }
=======

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
        $controller = '\\Adelowo\\Controller\\HomeController';

        $route = $this->route->path("users")
            ->attributes([])
            ->handler($controller);

        $this->getCfar($route)->dispatch();
        $cfarController = $this->cfar->getController();

        $this->assertInstanceOf($controller, new $cfarController);
        $this->assertEquals(Cfar::CFAR_DEFAULT_METHOD, $this->cfar->getMethod());
    }

>>>>>>> heads/1.0
}
