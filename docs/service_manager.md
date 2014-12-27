# Service Manager

## Introduction

**Service Manager** in Phalex that is DI in native Phalcon. In native Phalcon, most of DI services is set by 
lambda function (Closure). The second way, you can set DI services by config `PHP Array`. Personally, 
I don't like use lambda function for setting DI services. Lambda functions are fast, but hard to maintain. And with 
the second way, I think them also hard to develop, maintain.

In Phalex, **Service Manager** has tree types: `invokables, factories, shared`.

## Invokables

### Example 1

```php
return [
    'service_manager' => [
        'invokables' => [
            'arrayObject' => \ArrayObject::class
        ]
    ],
];
```

In *example 1*, you set DI service `arrayObject` is instance of class `ArrayObject` . So, **invokables** is very simple.  
And in Controller/Action (or somewhere can call DI Phalcon), you can get this service by this way

```php
$this->getDI()->get('arrayObject')
```

## Factories

### Example 2

```php
return [
    'service_manager' => [
        'factories' => [
            'myDbInstance' => \My\Factory\Db::class
        ]
    ],
];
```

In *example 2*, you set DI service `myDbInstance`, but it is **NOT** instance of class `My\Factory\Db`. When you
config service manager with **factories**, you must specify the class name to create instance for you.

### Example 3

```php

<?php

namespace My\Factory;

use Phalex\Di\DiFactoryInterface;
use Phalex\Di\Di;

class Db implements DiFactoryInterface
{
    public function createService(Di $di)
    {
        $config = $di->get('config');
        $pdo = new \PDO($config['db']['dsn'], $config['db']['username'], $config['db']['password']);
        return $pdo;
    }
}
```

In *example 3*, you can see the class `My\Factory\Db` implements `Phalex\Di\DiFactoryInterface` interface. In
method `createService()`, it return instance of class `PDO`. So, when you get service DI **myDbInstance**, that means
you get instance of class `PDO`

## Shared

Shared service for get singleton instance. It apply for both `invokables` and `factories` service. By default, services
in Phalex application are always set shared instance. So, if you want to get new instance for each call, you just set
this config to `false`

### Example 4

```php
return [
    'service_manager' => [
        'factories' => [
            'myDbInstance' => \My\Factory\Db::class
        ],
        'shared' => [
            'myDbInstance' => false,
        ],
    ],
];
```

### Example 5

```php
return [
    'service_manager' => [
        'invokables' => [
            'arrayObject' => \ArrayObject::class
        ],
        'shared' => [
            'arrayObject' => false,
        ],
    ],
];
```

### Example 6

```php
return [
    'service_manager' => [
        'factories' => [
            'myDbInstance' => \My\Factory\Db::class
        ],
        'invokables' => [
            'arrayObject' => \ArrayObject::class
        ],
        'shared' => [
            'myDbInstance' => false,
            'arrayObject' => false,
        ],
    ],
];
```
