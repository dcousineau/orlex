<?php
namespace Orlex;

use Symfony\Component\Routing\Loader\AnnotationClassLoader as AbstractClassLoader;
use Symfony\Component\Routing\Route;

class AnnotationClassLoader extends AbstractClassLoader {
    use ContainerAwareTrait;

    protected function configureRoute(Route $route, \ReflectionClass $class, \ReflectionMethod $method, $annot) {
        $app = $this->getContainer();
        $classid = strtolower(str_replace('\\', '_', $class->getName()));

        if (!isset($app[$classid])) {
            $app[$classid] = $app->share(function() use ($app, $class) {
                $controller = $class->newInstance();

                if (method_exists($controller, 'setContainer'))
                    $controller->setContainer($app);

                return $controller;
            });
        }

        $httpMethod = strtoupper(implode('|', $route->getMethods()));
        if (empty($httpMethod))
            $httpMethod = 'GET';

        $app->match($route->getPath(), "$classid:{$method->getName()}")
            ->method($httpMethod);

        return $route;
    }
}