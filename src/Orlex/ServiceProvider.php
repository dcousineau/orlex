<?php
namespace Orlex;

use Silex\ServiceProviderInterface;
use Silex\Application;
use Silex\Provider\ServiceControllerServiceProvider;

use Orlex\AnnotationManager\Compiler;
use Orlex\AnnotationManager\Loader;

use Doctrine\Common\Annotations;
use Doctrine\Common\Annotations\AnnotationRegistry;
use Doctrine\Common\Cache;

class ServiceProvider implements ServiceProviderInterface {
    public function register(Application $app) {
        ////
        // Absolute dependencies
        ////
        $app->register(new ServiceControllerServiceProvider());

        ////
        // User Configured Values
        ////
        $app['orlex.cache.dir']       = null;
        $app['orlex.controller.dirs'] = [];
        $app['orlex.annotation.dirs'] = [];

        ////
        // Internal Services
        ////
        $app['orlex.cache'] = $app->share(function($app){
            $cache_dir = $app['orlex.cache.dir'];

            if (!$cache_dir) return false;

            $cache = new Cache\FilesystemCache($cache_dir);

            return $cache;
        });

        $app['orlex.annotation.reader'] = $app->share(function($app) {
            $reader = new Annotations\AnnotationReader();

            if ($cache = $app['orlex.cache']) {
                $reader = new Annotations\CachedReader($reader, $cache);
            }

            return $reader;
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