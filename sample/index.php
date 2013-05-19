<?php
/** @var $loader \Composer\Autoload\ClassLoader */
$loader = require_once __DIR__ . '/../vendor/autoload.php';
$loader->add('app', __DIR__);

////////////////////////////////////////////////////////////////////////

$app = new \Silex\Application();

$app['debug'] = true;

$app->register(new \Orlex\ServiceProvider(),[
    'orlex.cache.dir' => __DIR__ . '/cache',
    'orlex.controller.dirs' => [
        __DIR__ . '/app/Controllers',
    ],
    'orlex.annotation.dirs' => [
        __DIR__ => 'app\Annotation',
    ]
]);

$app->run();