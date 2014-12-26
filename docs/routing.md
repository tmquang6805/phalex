# Routing
Routing configuration help you config routers component. Router component allows defining routes that are mapped to controllers or handlers that should receive the request. Routing in [Phalex]("Phalex") simple is **PHP Array**, you can set it in `module.config.php` file (for each module) or in `global/local` configuration (specify in `application.config.php`)

## Defining Routes
Define routes in `module.config.php` file (for each module) or in `global/local` configuration (specify in `application.config.php`). Defining route is defined in value of **router** key

```php
// module.config.php
<?php
return [
    // Other configurations
    'router' => [
        /* Difining routes here */
    ]
    // Other configurations
];
```

> **Note:** All example defining routes in this page will be written in `/* Difining routes here */` comment above

### Example 1

```php
'about' => [
    'route' => '/about-me',
    'definitions' => [
        'controller' => 'introduce',
        'action' => 'about-me'
    ],
],
```

In this case, if the URI is exactly: **/about-me**, then the `introduce` controller and its action `aboutMe` will be executed. The key `about` will be the route's name 

An application can have many paths, defines routes one by one can be a cumbersome task. In these case we can create more flexible routes.

### Example 2

```php
'admin' => [
    'route' => '/admin/:controller/:action/:params',
    'definitions' => [
        'controller' => 1,
        'action' => 2,
        'params' => 3
    ],
],
```

In the *example 2*, using wildcards we make a route valid for many URIs. For example, by accessing the following URI **/admin/users/search/dave/micheal** then

Route Key | Route Value
--- | ---
Controller | users
Action | search
Parameter | dave
Parameter | micheal


In the *example 2*, matching parts are placeholders or subpatterns delimited by parentheses (round brackets). In this case, the first subpattern matched (:controller) is the controller part of the route, the second the action and so on.

These placeholders help writing regular expressions that are more readable for developers and easier to understand. The following placeholders are supported 

Placeholder | Regular Expression | Usage
:--- | :--- | :---
/:module | /([a-zA-Z0-9_-]+) | Matches a valid **module name** with alpha-numeric charaters only
/:controller | /([a-zA-Z0-9_-]+) | Matches a valid **controller name** with alpha-numeric characters only
/:action | /([a-zA-Z0-9_]+) | Matches a valid **action name** with alpha-numeric characters only
/:params | (/.*)* | Matches a list of optional **words separated by slashes**. Use only this placeholder at the end of a route
/:namespace | /([a-zA-Z0-9_-]+) | Matches a **single level namespace name**
/:int | /([0-9]+) | Matches an **integer parameter**

> **Module, Controller, Action** names are camelized, this means that characters (-) and (_) are removed and the next character is uppercased

## Parameter with names
The example below demonstrates how to define names to route parameters

### Example 3
```php
'admin' => [
    'route' => '/admin/:controller/:action/([a-z0-9-]+)/([1-9][0-9]*)/:params',
    'definitions' => [
        'controller' => 1, // :controller
        'action' => 2, // :action
        'title' => 3, // ([a-z0-9-]+)
        'id' => 4, // ([1-9][0-9]*)
        'params' => 5 // :params
    ],
],
```
## Http method restriction
Sometimes we can restrict a route to specific method, this is especially useful when creating RESTful applications:

### Example 4

```php
'api-add-prod' => [
    'route' => '/api/product',
    'methods' => ['post'],
    'definitions' => [
        'controller' => 'production',
        'action' => 'add'
    ],
],
```

In the *example 4*, this route only will **be matched** if the HTTP method is `POST`

You can specify many method in the array

### Example 5

```php
'api-add-prod' => [
    'route' => '/api/product/([1-9][0-9]*)',
    'methods' => ['get', 'put'],
    'definitions' => [
        'controller' => 'production',
        'action' => 'get-or-edit',
        'id' => 1,
    ],
],
```

## Using convertions
Convertions allow to freely transform the route's parameters before passing them to the dispatcher, the following example show how to use them:

### Example 6
```php
'news' => [
    'route' => '/news/([a-zA-Z0-9-_]+)/([1-9][0-9]*)',
    'definitions' => [
        'controller' => 'news',
        'action' => 'detail',
        'title' => 1,
        'id' => 2
    ],
    'convertions' => [
        'title' => 'TitleConvertionClassName',
    ],
],
```

The `convertions` array is config for route convertion. The `key` is the parameter route's name, and the 'value' is the name of class implements `Phalex\Mvc\Router\ConvertingInterface`

## Match callback

Sometimes, routes must be matched if they meet specific conditions, you can add arbitrary conditions to routes using the `before_match` config. This config specify the class name to handle condition before match route. If them return `false`, the route will be treaded as **non-matched**

### Example 7
```php
'news' => [
    'route' => '/news/([a-zA-Z0-9-_]+)/([1-9][0-9]*)',
    'definitions' => [
        'controller' => 'news',
        'action' => 'detail',
        'title' => 1,
        'id' => 2
    ],
    'before_match' => 'ConditionNewsHandlerClassName',
],
```
The value of `before_match` is the class name that must be implemented `Phalex\Mvc\Router\BeforeMatchInterface`

## Host constraints
The router allow to set hostname constraints, this means that specific routes can be restricted to only match if the route also meets the hostname constraint:

### Example 8
```php
'api-add-prod' => [
    'route' => '/api/product/([1-9][0-9]*)',
    'methods' => ['get', 'put'],
    'definitions' => [
        'controller' => 'production',
        'action' => 'get-or-edit',
        'id' => 1,
    ],
    'host_name' => 'api.example.com',
],
```
Hostname can also be **regular expressions**
### Example 8
```php
'api-add-prod' => [
    'route' => '/api/product/([1-9][0-9]*)',
    'methods' => ['get', 'put'],
    'definitions' => [
        'controller' => 'production',
        'action' => 'get-or-edit',
        'id' => 1,
    ],
    'host_name' => '([a-z+]).example.com',
],
```

## Dealing with extra/trailing slashes
In [Phalex]("Phalex"), the router remove the trailing slashes from the end

## URI Sources
In [Phalex]("Phalex"), the `$_SERVER[‘REQUEST_URI’]` is default for URI source
