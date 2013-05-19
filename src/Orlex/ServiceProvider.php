<?php
namespace Orlex;

use Silex\ServiceProviderInterface;
use Silex\Application;
use Silex\Provider\ServiceControllerServiceProvider;

use Orlex\AnnotationManager\Compiler;
use Orlex\AnnotationManager\Loader;

use Doctrine\Common\Annotations;
use Doctrine\Common\Annotations\AnnotationRegistry;

class ServiceProvider implements ServiceProviderInterface {
    public function register(Application $app) {
        ////
        // Absolute dependencies
        ////
        $app->register(new ServiceControllerServiceProvider());

        ////
        // User Configured Values
        ////
        $app['orlex.controller.dirs'] = [];
        $app['orlex.annotation.dirs'] = [];

        ////
        // Internal Services
        ////
        $app['orlex.annotation.reader'] = $app->share(function() {
            return new Annotations\AnnotationReader();
        });

        $app['orlex.directoryloader'] = $app->share(function() {
            return new Loader\DirectoryLoader();
        });

        $app['orlex.annotation.registry'] = $app->share(function($app) {
            AnnotationRegistry::registerAutoloadNamespace('Orlex\Annotation', dirname(__DIR__));
            foreach ($app['orlex.annotation.dirs'] as $dir => $namespace) {
                AnnotationRegistry::registerAutoloadNamespace($namespace, $dir);
            }
        });

        $app['orlex.route.compiler'] = $app->share(function($app) {
            $app['orlex.annotation.registry'];
            
            $compiler = new Compiler\Route($app['orlex.annotation.reader'], $app['orlex.directoryloader']);
            $compiler->setContainer($app);
            return $compiler;
        });
    }

    public function boot(Application $app) {
        /** @var $compiler Compiler\Route */
        $compiler = $app['orlex.route.compiler'];

        foreach ($app['orlex.controller.dirs'] as $path) {
            $compiler->compile($path);
        }
    }
}