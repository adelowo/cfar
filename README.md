# CFAR - Controller for Aura Router

This library was written to enable users of Aura.Router make use of symfony/laravel "type" controllers. I had a 3 day stint with the laravel framework and i liked the idea behind the controllers - even though i didn't end up using laravel - , i then decided to bring such a thing to my favourite router.

### Installation

You are recommended to make use of the latest available PHP version, but CFAR should run on >=5.3.

CFAR has a dependency, which is [Aura.Router 2.x](https://github.com/auraphp/Aura.Router.

Install CFAR via one of the following methods :

- [Composer](https://getcomposer.org) :

```bash
    composer require adelowo/cfar
```

- Repo Cloning :

```bash
    git clone https://github.com/adelowo/cfar
```

- [Download a release](https://github.com/adelowo/cfar/releases)

> If downloading the library without composer, you'd have to write an autoloader yourself


### Usage

Cfar doesn't require any special config - other than specify the class that acts as your controller (namespaced) and method to invoke - you'd still write your routes as specified in Aura.Router's (2.x) doc.

In the `addValues()` method provided by Aura.Router, you have to add an array named `cfar` which must contain at least one key which is the `controller`.

The `cfar` array can optionally contain a `namespace` key - if your controller lives under a namespace. If a namespace key isn't provided, Cfar would assume the controller class lives in the global namespace .

> Internally, Cfar uses PHP's Reflection Api.

```php
<?php

$router->addValues(
[ "cfar" => [
    "namespace" => "adelowo\\controller\\" ,
    "controller" => "HomeController@showUser"
]]);


```

You can also leave the `@` in the controller key definition and Cfar would instead search for and invoke a default method called `indexAction`.

Below is a little snippet that shows Aura.Router and Cfar ***fully integrated***

```php
<?php

use adelowo\cfar\Cfar;
use adelowo\cfar\CfarException;

require_once 'vendor/autoload.php';

$router_factory = new Aura\Router\RouterFactory();

$router = $router_factory->newInstance();

$router->addGet(null, '/')
    ->addValues(
    ["cfar" => [
        "controller" => "GlobalController@showAllUsers" //GlobalController would be loaded from the global namespace
    ]]);

$router->addGet(null , '/user/{id}/{name}')
    ->addTokens(["id" => "\d+" , "name" => "\w+"])
    ->addValues(
    [ "cfar" => [
        "namespace" => "adelowo\\controller\\" ,
        "controller" => "HomeController@showUser" //Namespaced controller with showUser() invoked
    ]]);

$router->addGet(null , '/{id}/{name}')
    ->addTokens(["id" => "\d+" , "name" => "\w+"])
    ->addValues(
    [ "cfar" => [
        "namespace" => "adelowo\\controller\\" ,
        "controller" => "HomeController" //Namespaced controller whose method implementation would default to indexAction.
    ]]);


$router->addGet(null, '/login');

$router->addGet(null , '/pdf/{name}')
    ->addTokens(["name" => "\w+"])
    ->addValues(
    [ "cfar" => [
        "namespace" => "adelowo\\controller\\" ,
        "controller" => "HomeController@showPdf"
    ]]);


$path = parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH);

server
$route = $router->match($path, $_SERVER);

if ( !$route ) {

    throw new \Exception("No route found");

} else {

    $cfar = new Cfar($router); //pass Cfar the router instance. This is used internally to get the matched route and values that have captured for the route.

    try {

        $cfar->dispatch(); //Doing the deed

    } catch (CfarException $e) {
        echo $e->getMessage();
    }
}   

```

### License
MIT
