# CFAR - Controller for Aura Router

![Build Status Images](https://travis-ci.org/adelowo/cfar.svg)
This library was written to enable users of Aura.Router make use of symfony/laravel "type" controllers.

### Installation

You are recommended to make use of the latest available PHP version, but CFAR should run on >=5.5.

CFAR has a dependency, which is Aura.Router 3.x . If you are still using Aura.Router 2.x, please install 0.x

Install CFAR via one of the following methods :

- [Composer](https://getcomposer.org) :

```bash
    composer require "adelowo/cfar" : "^1.0.0"
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


$routeMapper->get('blog.read', '/blog/{id}/{name}')
	->handler(\adelowo\controller\BlogController::class)
	->extras([
		"listener" => "show" // the method to be invoked, if this is not found,`indexAction` would be invoked.
	]);

$routeMapper->get(null, "/")
	->handler(GlobeController::class)
	->extras([
		"listener" => "oops"
	]);

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

try {
    $cfar = new \Adelowo\Cfar\Cfar($matched);

    $cfar->dispatch();

} catch (\Adelowo\Cfar\CfarException $exception) {
    echo $exception->getMessage();
}
```

> Parameters would be passed to the invoked method in the same order defined in the route.. A method for `/users/{id}/{name}` should have two parameters, where the first one would be passed the value captured by the router for `{id}` and viceversa

```php

<?php

namespace Adelowo\Controller;

class BlogController
{

    public function showUser($id , $param)
    {
        echo $id . PHP_EOL;
        echo $param;
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
