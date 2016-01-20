<?php

namespace adelowo\cfar;

require_once 'fixtures/GlobalController.php';

use adelowo\cfar\Cfar;
use Aura\Router\RouterFactory;

class CfarTest extends \PHPUnit_Framework_TestCase
{
    protected $cfarInstance ;

    protected $auraRouterInstance ;

    protected $server ;

    public function setUp()
    {
        $this->auraRouterInstance = $this->getRouter();

        $this->server = array('REQUEST_METHOD' => 'GET');


        $this->cfarInstance = new Cfar($this->auraRouterInstance);
    }

    protected function getRouter()
    {
        $auraRouter = new RouterFactory();

        return $auraRouter->newInstance();
    }


    public function testRouteParameters()
    {
        $this->auraRouterInstance->addGet(null , '/{id}/{name}')
                ->addValues([
                    "cfar" => [
                        "namespace" => "adelowo\\controller\\",
                        "controller" => "HomeController@showUser"
                    ]
                ]);

        $route = $this->auraRouterInstance->match("/10/adelowo" , $this->server);

        $expectedParameters = [
            "id" => "10", //params are strings
            "name" => "adelowo"
        ];

        $this->cfarInstance->dispatch();

        $this->assertEquals($expectedParameters , $this->cfarInstance->getParameters());

        $this->assertSame($expectedParameters , $this->cfarInstance->getParameters());

    }

    public function testNameSpacedController()
    {
        $this->auraRouterInstance->addGet(null , '/{id}/{name}')
                ->addValues([
                    "cfar" => [
                        "namespace" => "adelowo\\controller\\",
                        "controller" => "HomeController@showUser"
                    ]
                ]);

        $route = $this->auraRouterInstance->match("/10/adelowo" , $this->server);

        $this->cfarInstance->dispatch();

        $controller = $this->cfarInstance->getController();

        $this->assertInstanceOf("adelowo\\controller\\HomeController" ,
         new $controller);

    }

    public function testControllerInGlobalNameSpace()
    {
        $this->auraRouterInstance->addGet(null , '/{id}/{name}')
                ->addValues([
                    "cfar" => [
                        "controller" => "GlobalController@indexAction"
                    ]
                ]);

        $route = $this->auraRouterInstance->match("/10/adelowo" , $this->server);

        $this->cfarInstance->dispatch();

        $controller = $this->cfarInstance->getController();

        $this->assertInstanceOf("\\GlobalController" ,
         new $controller);

    }
}
