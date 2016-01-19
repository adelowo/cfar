<?php

namespace adelowo\cfar;

use Aura\Router\Router;

/**
 * CFAR -- Controller for Aura.Router
 * @author Adelowo Lanre <me@adelowolanre.com>
 * Allows you to use controllers and methods (laravel/symfony style) for Aura.Router 2.x.
 */
class Cfar
{

    /**
     * The array key that contains the configuration for the controllers and methods in Aura.Router's addValues method
     * @var string
     */
    const CFAR = 'cfar';

    /**
     * Array key denoting the class to be invoked on a matched route
     * @var string
     */
    const CFAR_CONTROLLER = 'controller';

    /**
     * Array key denoting the method to be invoked on a matched route
     * @var string
     */
    const CFAR_METHOD = 'method';

    /**
     * Array key denoting the namespace of the class to be invoked on a matched route
     * @var string
     */
    const CFAR_NS = 'namespace';

    /**
     * Denotes the seperator used in the CFAR config e.g "HomeController@showUser"
     * @var string
     */
    const CFAR_SEPERATOR = '@';

    /**
     * This is a fallback for folks that do not have the namespace key defined, we'd assume your controller lives in the global namespace.
     * @var string
     */
    const CFAR_GLOBAL = '\\';

    /**
     * This is a fallback if the method key of the CFAR config isn't provided, we'd assume the default method to be called is "indexAction".
     * @var string
     */
    const CFAR_DEFAULT_METHOD  = 'indexAction';

    /**
     * Aura Router Instance
     * @var Router
     */
    protected $auraRouter;

    /**
     * The name of the called controller class (plus it's fully quantified namespace).
     * @var string
     */
    protected $controller ;

    /**
     * The name of the method invoked on the called class .
     * @var string
     */
    protected $method ;

    /**
     * The parameters to be passed unto the method.
     * @var array
     */
    protected $parameters ;

    /**
     * @param Router $router
     */
    public function __construct(Router $router)
    {
        $this->auraRouter = $router;
    }

    /**
     * Returns an array of parameters for the matched route.
     * @return array
     */
    public function getParameters()
    {
        return $this->parameters ;
    }

    /**
     * Returns a string that denotes the called class plus it's namespace
     * @return string
     */
    public function getController()
    {
        return $this->controller ;
    }

    /**
     * Returns a string that denotes the method that was invoked.
     * @return string
     */
    public function getMethod()
    {
        return $this->method ;
    }

    /**
     * Gets the configuration settings for cfar from Aura.Router.
     * @return array
     */
    protected function getCfarConfig()
    {
        return $this->auraRouter->getMatchedRoute()->values[self::CFAR];
    }

    /**
     * Returns an array that contains the controller class and metho to invoke.
     * @param  string $value A string containing the controller and method (gotten from the cfar config).
     * @return array
     */
    protected function getControllerAndMethodValues($value)
    {
        return explode(self::CFAR_SEPERATOR, $value);
    }

    /**
     * Gets information about the method that is to be invoked.
     * @return \ReflectionMethod
     */
    protected function getMethodToInvoke()
    {
        $reflection = new \ReflectionClass($this->controller) ;

        return $reflection->getMethod($this->method);
    }

    /**
     * Dispatches parameters to the invoked method.
     * @param  array  $routeParameters An array of parameters captured during the routing process.
     * @return void
     */
    public function dispatch(array $routeParameters)
    {
        $cfarConfig = $this->getCfarConfig();

        if (!$cfarConfig) {

            throw new CfarException("There isn't an CFAR config array for this route");
        }

        if (!$cfarConfig[self::CFAR_CONTROLLER]) {

            throw new CfarException("This route does not have a controller key in it's CFAR config");

        }

        $namespace = $cfarConfig[self::CFAR_NS] ?: self::CFAR_GLOBAL;

        list($this->controller , $this->method) = $this->getControllerAndMethodValues($cfarConfig[self::CFAR_CONTROLLER]);

        $this->method = $this->method ?: self::CFAR_DEFAULT_METHOD; //Check if the method to invoke is defined ? else we'd call the "indexAction" method.

        $this->controller = $namespace.$this->controller;

        unset($routeParameters[self::CFAR]); //remove the cfar config to avoid passing dead parameters to the method.

        $this->parameters = $routeParameters;

        $this->getMethodToInvoke()
            ->invokeArgs(new $this->controller(), $this->parameters );

    }

}
