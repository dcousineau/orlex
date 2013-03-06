<?php
require_once dirname(__DIR__) . '/vendor/autoload.php';

$app = new \Silex\Application();

$orlex = new \Orlex\Manager($app, [
    'controller_paths' => [
        __DIR__ . '/app/Controllers',
    ],
]);

$orlex->scaffold()
      ->run();