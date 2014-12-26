# Error Handler

## Introduction

Phalex has default error handler, you only specify `views_dir`, templates for render error messages

```php

return [
    'error_handler' => [
        'options' => [
            'views_dir' => __DIR__ . '/view/error',
            'template_500' => 'error',
            'template_404' => 'not-found'
        ],
    ],
]
```

Default, **error handler** in Phalex is `Phalex\Mvc\Exception\HandlerDefault` class, it use `Phalcon\Mvc\View\Simple`
for rendering error messages. The view in `HandlerDefault` is registered **volt** and **phtml** template engine, so you
can easy to modify template, error messages.

## Implementing your own Error Handler

- First, the `Phalex\Mvc\Exception\HandlerInterface` interface must be implemented to create your own Error Handler 
replacing the one provided by Phalex.
- Second, you must re-config `error_handler` above like that

```php
return [
    'error_handler' => [
        'adapter' => Your\Error\Handler::class // String of class name
        'options' => [
            /* Other options for your error handler */
        ],
    ],
]

```
