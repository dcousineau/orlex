<?php
require_once dirname(__DIR__) . '/vendor/autoload.php';
spl_autoload_register(function($class) {
    $classpath = __DIR__ . '/' . str_replace('\\','/', $class) . '.php';
    if (file_exists($classpath))
        include_once $classpath;
});



$app = new \Silex\Application();

$orlex = new \Orlex\Manager($app, [
    'controllers' => [
        __DIR__ . '/app/Controllers',
    ],
    'annotations' => [
        __DIR__ => 'app\Annotation',
    ]
]);

$orlex->scaffold()
      ->run();