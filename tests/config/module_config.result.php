<?php

$dir = getcwd();

return array(
    'view'   => array(
        'Application' => $dir . '/tests/module/Application/view',
        'Backend'     => $dir . '/tests/module/Backend/view',
    ),
    'router' => array(
        'home'    => array(
            'route'       => '/',
            'definitions' => array(
                'module'     => 'Application',
                'namespace'  => 'Application\\Controller',
                'controller' => 'index',
                'action'     => 'index',
            ),
        ),
        'news'    => array(
            'route'       => '/news/([a-z0-9-]+)-([1-9][0-9]*)\\.html',
            'definitions' => array(
                'module'     => 'Application',
                'namespace'  => 'Application\\Controller',
                'controller' => 'article',
                'action'     => 'detail',
                'title'      => 1,
                'id'         => 2,
            ),
            'convertions' => array(
                'id' => array(
                    'class_name' => 'Application\\Router\\ConvertId',
                ),
            ),
        ),
        'be_home' => array(
            'route'       => '/',
            'definitions' => array(
                'module'     => 'Backend',
                'namespace'  => 'Backend\\Controller',
                'controller' => 'index',
                'action'     => 'index',
            ),
            'host_name'   => 'admin.test-example.com',
        ),
        'be_news' => array(
            'route'        => '/news/:action/([a-z0-9-]+)-([1-9][0-9]*)\\.html',
            'definitions'  => array(
                'module'     => 'Backend',
                'namespace'  => 'Backend\\Controller',
                'controller' => 'article',
                'action'     => 1,
                'title'      => 2,
                'id'         => 3,
            ),
            'convertions'  => array(
                'id' => array(
                    'class_name' => 'Application\\Router\\ConvertId',
                ),
            ),
            'before_match' => array(
                'class_name' => 'Backend\\Router\\Callback',
            ),
            'host_name'    => 'api.test-example.com',
        ),
    ),
);
