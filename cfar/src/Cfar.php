<?php

namespace Adelowo\Cfar;

use Aura\Router\Route;
use Interop\Container\ContainerInterface;

/**
 * CFAR -- Controller for Aura.Router
 * @author Lanre Adelowo <me@adelowolanre.com>
 * Allows you to use controllers and methods (laravel/symfony style) for `Aura.Router` 3.x.
 * If you still make use of `Aura.Router` 2.x, please see the <= 0.2.* releases
 */
class Cfar
{

    /**
     * This is here to bail the current request if a "listener" was not defined on the class.
     * @var string
     */
    const CFAR_DEFAULT_METHOD = 'indexAction';

    /**
     * "\SomeClass@method"
     * @var string
     */
    const SEPARATOR = "@";

    /**
     * Aura.Router Instance
     * @var Route
     */
    protected $matchedRoute;

    /**
     * The name of the called controller class (plus it's fully quantified namespace).
     * @var string
     */
    protected $controller;

    /**
     * The name of the method invoked on the called class .
     * @var string
     */
    protected $method;

    /**
     * The parameters to be passed unto the method.
     * @var array
     */
    protected $parameters;

    /**
     * Bring your own container to the party
     * @var \Interop\Container\ContainerInterface|null
     */
    protected $container;

    /**
     * @param \Aura\Router\Route                         $router
     * @param \Interop\Container\ContainerInterface|null $container
     */
    public function __construct(Route $router, ContainerInterface $container = null)
    {
        $this->matchedRoute = $router;
        $this->container = $container;
    }

    /**
     * Returns an array of parameters for the matched route.
     * @return array
     */
    public function getParameters()
    {
        return $this->parameters;
    }

    /**
     * Returns a string that denotes the called class plus it's namespace
     * @return string
     */
    public function getController()
    {
        return $this->controller;
    }

    /**
     * Returns a string that denotes the method that was invoked.
     * @return string
     */
    public function getMethod()
    {
        return $this->method;
    }

    /**
     * Calls the controller associated with the route and invoke the specified method.
     * @throws \Adelowo\Cfar\CfarException if the class cannot be be found
     */
    public function dispatch()
    {
        try {
            list($this->controller, $this->method) = $this->getRegisteredControllerAndMethod();


            $this->parameters = $this->matchedRoute->attributes;

            $this->getReflectionClass($this->controller)
                ->getMethod($this->method)
                ->invokeArgs(
                    new $this->controller($this->container),
                    $this->parameters
                );
        } catch (\ReflectionException $e) {
            throw new CfarException(
                CfarException::INVALID_DECLARATION . ". " . $e->getMessage()
            );
        }
    }

    /**
     * @return array
     */
    protected function getRegisteredControllerAndMethod()
    {
        $value = explode(self::SEPARATOR, $this->matchedRoute->handler);

        if (count($value) === 2) { //if a method was specified by the `@` delimiter
            return $value;
        }

        $value[1] = self::CFAR_DEFAULT_METHOD; //make `indexAction` the default if no method was specified

        return $value;

    }

    /**
     * @param $class string|object The class to resolve
     * @return \ReflectionClass
     */
    protected function getReflectionClass($class)
    {
        return new \ReflectionClass($class);
    }
}
