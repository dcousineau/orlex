<?php
require_once dirname(__DIR__) . '/vendor/autoload.php';
spl_autoload_register(function($class) {
    $classpath = __DIR__ . '/' . str_replace('\\','/', $class) . '.php';
    if (file_exists($classpath))
        include_once $classpath;
});

////////////////////////////////////////////////////////////////////////

$app = new \Silex\Application();

$app->register(new \Orlex\ServiceProvider(),[
    'orlex.controller.dirs' => [
        __DIR__ . '/app/Controllers',
    ],
    'orlex.annotation.dirs' => [
        __DIR__ => 'app\Annotation',
    ]
]);

$app->run();