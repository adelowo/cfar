# CFAR - Controller for Aura Router

[![Latest Version on Packagist](https://img.shields.io/packagist/v/adelowo/cfar.svg?style=flat-square)](https://packagist.org/packages/adelowo/cfar)
[![Software License](https://img.shields.io/badge/license-MIT-brightgreen.svg?style=flat-square)](LICENSE.md)
[![Build Status](https://img.shields.io/travis/adelowo/cfar/master.svg?style=flat-square)](https://travis-ci.org/adelowo/cfar)
[![Scrutinizer Coverage](https://img.shields.io/scrutinizer/coverage/g/adelowo/cfar.svg?maxAge=2592000&style=flat-square)](https://scrutinizer-ci.com/g/adelowo/cfar/?branch=master)
[![Quality Score](https://img.shields.io/scrutinizer/g/adelowo/cfar.svg?style=flat-square)](https://scrutinizer-ci.com/g/adelowo/cfar)
[![SensioLabsInsight](https://img.shields.io/sensiolabs/i/09ff34ee-feb3-49c0-acbc-52781179deb9.svg?style=flat-square)](https://insight.sensiolabs.com/projects/09ff34ee-feb3-49c0-acbc-52781179deb9)
[![Total Downloads](https://img.shields.io/packagist/dt/adelowo/cfar.svg?style=flat-square)](https://packagist.org/packages/adelowo/cfar)

This library was written to enable users of Aura.Router make use of symfony/laravel "type" controllers.

#### Already using CFAR , Migrate to: [1.2](migration-to-1.2.md)

### Installation

You are recommended to make use of the latest available PHP version, but CFAR should run on >=5.5.

CFAR has a dependency, which is Aura.Router. >=1.0 releases require Aura.Router 3.x while <=0.2.1 requires Aura.Router 2.x .

Install CFAR via one of the following methods :

- [Composer](https://getcomposer.org) :

```bash
    composer require "adelowo/cfar" : "~1.0"
```

> If you are still using Aura.Router 2.x, please install 0.x

```bash
    composer require "adelowo/cfar" : "~0.2"
```


- Repo Cloning :

```bash
    git clone https://github.com/adelowo/cfar.git
```

- [Download a release](https://github.com/adelowo/cfar/releases)

> If downloading the library without composer or cloning directly from the repository, you'd have to write an autoloader yourself


### Usage

Cfar doesn't require any special config - other than specifying the class that acts as your controller (namespaced) and method to invoke - you'd still write your routes as specified in Aura.Router's (3.x) doc.

Aura.Router 3 is a big improvement to the much loved router with it being broken into many parts such as a Mapper, Matcher, Route (that contains the matched route).

> Internally, Cfar uses PHP's Reflection Api.

> By default, Cfar would search and invoke a method called `indexAction`

Below is a little snippet that shows Aura.Router and Cfar ***fully integrated***, an `index.php` file and controllers for the routes would be written.

```php
<?php

//filename : index.php

use Aura\Router\RouterContainer;

require_once "vendor/autoload.php";

$routeContainer = new RouterContainer();

$routeMapper = $routeContainer->getMap();


$routeMapper->get('blog.read', '/blog/{ide}')
    ->handler('\Http\controller\BlogController@show');

$routeMapper->get(null, "/")
    ->handler('\Http\controller\BlogController');

$routeMapper->get('dev', '/dev');

$routeMapper->get(null,'/error')
    ->handler('\Http\controller\ErrorController'); //`indexAction` would be the invoked method

$routeMatcher = $routeContainer->getMatcher();


$request = Zend\Diactoros\ServerRequestFactory::fromGlobals(
	$_SERVER,
	$_GET,
	$_POST,
	$_COOKIE,
	$_FILES
);

$matched = $routeMatcher->match($request);

if (!$matched) {
	throw new \Aura\Router\Exception("Route does not exists");
}

foreach ($matched->attributes as $key => $val) {
	$request = $request->withAttribute($key, $val);
}


/**
 * This is totally optional. But you could use some "Control Inverting", than have `new` wrap all lines of your code
*`SomeContainer` implement `Interop\Container\ContainerInterface`.;
* A neat way to do this is to extend your choosen container and have the `get` method exposed by the interface retrieve the service from the container.
* @see https://github.com/slimphp/slim/
*/
$container = new SomeContainer(); 

//Add an ORM, Doctrine in this case.
$container['db'] = function ($container) {
    
    $paths = array("/src/Entities");
    
    $isDevMode = false;

    $dbParams = [
        'driver' => 'pdo_mysql',
        'user' => 'root',
        'password' => 'xx-xxx-xx-xx',
        'dbname' => 'foo',
    ];
    
    $config = \Doctrine\ORM\Tools\Setup::createAnnotationMetadataConfiguration($paths, $isDevMode);
    
    return \Doctrine\ORM\EntityManager::create($dbParams, $config);    
};

//You def' need a logger
$container['logger'] = function ($container) {

    $logger = new \Monolog\Logger("Your App Name");
    
    $handler = new \Monolog\Handler\SyslogHandler('Owambe');
    $handler->setFormatter(new \Monolog\Formatter\LineFormatter());
    
    $logger->pushHandler($handler);

    return $logger;
};

//register X,Y,Z services 

try {

    $cfar = new \Adelowo\Cfar\Cfar($matched , $container);

    $cfar->dispatch();

} catch (\Adelowo\Cfar\CfarException $exception) {
    echo $exception->getMessage(); 
}
```
> The constructor of the controller would always receive a container. Might be null or a valid one. Your call.

> Parameters would be passed to the invoked method in the same order defined in the route.. A method for `/users/{id}/{name}` should have two parameters, where the first one would be passed the value captured by the router for `{id}` and vice-versa

```php

<?php

namespace Adelowo\Controller;

class BlogController
{

    protected $container;
    
    public function __construct(ContainerInterface $container = null)
    {
        $this->container = $container;
    }
    public function showUser($id , $param)
    {
        $db = $this->container->get('db');
        
        $data = $db->find("User" , $id);
        
        var_dump($data);
    }

    public function showPdf($name)
    {

            echo $name;
    }

    public function indexAction($id ,$name)
    {
        echo $id. PHP_EOL;
        echo $name;
    }
}


```

### License
MIT
