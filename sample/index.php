<?php
require_once dirname(__DIR__) . '/vendor/autoload.php';
spl_autoload_register(function($class) {
    $classpath = __DIR__ . '/' . str_replace('\\','/', $class) . '.php';
    if (file_exists($classpath))
        include_once $classpath;
});



$app = new \Silex\Application();

$orlex = new \Orlex\Manager($app, [
    'controller_paths' => [
        __DIR__ . '/app/Controllers',
    ],
]);

//Ignore Symfony's deprectation error until silex catches up
$error_reporting = error_reporting(error_reporting() ^ E_USER_DEPRECATED);
$orlex->scaffold()
      ->run();
error_reporting($error_reporting);