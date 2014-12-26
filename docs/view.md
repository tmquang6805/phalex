# View

## Config

In Phalex, service `view` will be set automatically, so you mustn't set `view DI` manually. Instead of, you just only set **views_dir** for each module. Check [Module](module.md) for setting config. Example

```php
return [
    'view'   => [
        __NAMESPACE__ => __DIR__ . '/../view/'
    ],
];
```

- `__NAMESPACE__`: that means the name of **module**
- In Phalex, `view service` will be register the **volt** and **phtml** engine by default. 

## Route - Controller - Action - Folder View name

If you config the router (check [Routing](routing.md) for more detail about Router in Phalex)

```php
'admin' => [
    'route' => '/admin/:controller/:action/([1-9][0-9]*)',
    'definitions' => [
        'controller' => 1,
        'action' => 2,
        'id' => 3
    ],
],
```

In the example, by accessing the following URI `/admin/shopping-cart/view-detail/10` then:

Route Key | Route Value
--- | ---
controller | shopping-cart
action | view-detail


And you must have controller in file `ShoppingCartController.php`

```php

<?php

namespace Admin\Controller;

use Phalcon\Mvc\Controller as ControllerBase;

class ShoppingCartController extends ControllerBase
{
    public function viewDetailAction()
    {
       /* Block code action here */
    }
}
```

And the `view service` when be rendered in **{path/to/views/dir}/shopping-cart/view-detail.phtml** (or **{path/to/views/dir}/shopping-cart/view-detail.volt** if you want to use *volt* template engine)
