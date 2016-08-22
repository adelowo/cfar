# CFAR

Some changes between version 1.1 (1.1) and 1.2

## Migrate from version 1.0 or 1.1 to 1.2

### Handler Syntax
Having to describe a `listener` key in your route definition is just too verbose. Such as

```php

$routeMapper->get('blog.read', '/blog/{id}/{name}/{ide}')
    ->handler(\Http\Controller\BlogController::class)
    ->extras([ //LOOK
        "listener" => "show"
    ]);

```

This has been reverted to the original syntax used in 0.x and would be the syntax  to be used pending the lifetime of this package

```php

$routeMapper->get('blog.read', '/blog/{ide}')
    ->handler('\Http\Controller\BlogController@show'); //Awesome

```

NOTE that route definitions that rely on the `indexMethod` to be called (by CFAR) would still work the same.

```php

$routeMapper->get('blog.read', '/blog/{ide}')
    ->handler(\Http\Controller\BlogController::class); //`indexAction` would be called 
    
```