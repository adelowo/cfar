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
    }

    protected function getCfar(Route $route)
    {
        return $this->cfar = new Cfar($route);
    }

    public function tearDown()
    {
        parent::tearDown();
    }


    public function testCfarDispatchesExpectedParameters()
    {
        $this->route->path('/users/10/adelowo')
            ->attributes(["10", "adelowo"])
            ->handler(HomeController::class)
            ->extras(["listener" => "showUser"]);

        $this->getCfar($this->route)->dispatch();

        $this->assertSame($this->route->attributes, $this->cfar->getParameters());
    }

    public function testCfarCallsRightController()
    {
        $this->route->path("users")
            ->attributes([])
            ->handler("")
            ->extras()
    }

}
