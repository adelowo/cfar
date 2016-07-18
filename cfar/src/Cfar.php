<?php

namespace Adelowo\Cfar;

use Aura\Router\Route;

/**
 * CFAR -- Controller for Aura.Router
 * @author Adelowo Lanre <me@adelowolanre.com>
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
     * @param Route $router
     */
    public function __construct(Route $router)
    {
        $this->matchedRoute = $router;
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
     * Gets information about the method that is to be invoked.
     * @return \ReflectionMethod
     */
    protected function getMethodToInvoke()
    {
        $reflection = new \ReflectionClass($this->controller);

        return $reflection->getMethod($this->method);
    }

    public function dispatch()
    {
        $this->doesRouteHaveAValidDeclaration();

        list($this->controller, $this->method) = $this->getRegisteredControllerAndMethod();

        $this->parameters = $this->matchedRoute->attributes;

        $this->getReflectionClass($this->controller)
            ->getMethod($this->method)
            ->invokeArgs(new $this->controller, $this->parameters);

    }

    /**
     * @return array
     */
    protected function getRegisteredControllerAndMethod()
    {
        return [
            $this->matchedRoute->handler,
            array_key_exists('listener',
                $this->matchedRoute->extras) ? $this->matchedRoute->extras['listener'] : self::CFAR_DEFAULT_METHOD
        ];
    }

    /**
     * @return bool
     * @throws CfarException if the class can't be called
     */
    protected function doesRouteHaveAValidDeclaration()
    {

        if ($this->getReflectionClass($this->matchedRoute->handler)) {
            return true;
        }

        throw new CfarException("Invalid Route Declaration");
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
