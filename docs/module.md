# Module

## Introduction

Phalex bases on **Plug & Play Multi Modules** mechanism, so you can easy to develop multiple modules for your application.  
Each module in Phalex application must contain class `Module`, and namespace of the class that is **Module's name**. Example:

```php
<?php

namespace Application;

use Phalex\Mvc\Module\AbstractModule;

class Module extends AbstractModule
{
    public function getConfig()
    {
        /* Module's configurations */
    }

    public function getAutoloaderConfig()
    {
        /* Configurations for autoloader */
    }
}
```

## Class Module

- namespace: `Module's name`
- This class must extend abstract class `\Phalex\Mvc\Module\AbstractModule`
- When `Module` is extended abstract class `\Phalex\Mvc\Module\AbstractModule` must be implemented 2 abstract methods
    - `getConfig()`: return **PHP Array** for module's configurations.
    - `getAutoloaderConfig()`: return **PHP Array** for module's autoloader configurations.

### Method getConfig()

This configuration, you can config about [view](view.md), [service_manager](), [router](routing.md), and more... Example:

```php
public function getConfig()
{
    return [
        'error_handler' => [
            'options' => [
                'views_dir' => __DIR__ . '/../view/error',
                'template_500' => 'error',
                'template_404' => 'not-found'
            ],
        ],
        'view'   => [
            __NAMESPACE__ => __DIR__ . '/../view/'
        ],
        'router' => [
            'home'              => [
                'route'       => '/',
                'definitions' => [
                    'module'     => __NAMESPACE__,
                    'namespace'  => Controller::class,
                    'controller' => 'index',
                    'action'     => 'index'
                ],
            ],
            'controller/action' => [
                'route'       => '/:controller/:action',
                'definitions' => [
                    'module'     => __NAMESPACE__,
                    'namespace'  => Controller::class,
                    'controller' => 1,
                    'action'     => 2,
                    'params'     => 3
                ],
            ],
        ],
    ];
}
```

In `Phalex Module`, I recommend you should set this configuration into one file, and **require/include** it

```php
public function getConfig()
{
    return require __DIR__ . '/config/module.config.php';
}
```

### Method getAutoloaderConfig()
This is config for autoloader, in Phalex application, support autoload via **namespace** and **classmap**. Example:

```php
public function getAutoloaderConfig()
{
    return [
        'namespaces' => [
            Controller::class => __DIR__ . '/../src/Controller',
        ],
        'classmap'   => [
            __DIR__ . '/../module.classmap.php'
        ]
    ];
}
```

In this case, when runtime, classes with **namespace** `Application\Controller` or classes in **Array classmap** will be auto-loaded. As the same method `getConfig()`, I recommend you should set this configuration into one file, and **require/include** it.

```php
public function getAutoloaderConfig()
{
    return require __DIR__ . '/config/autoload.config.php';
}
```

## Enable Module in Phalex

In `Phalex-Skeleton`, you can **enable/disable** Module in `config/application.conf.php` file

```php
// This must be an array of module namespaces used in the application
'modules' => [
    'Application',
],
```
