<?php
namespace app\Annotation;

use Orlex\Annotation\RouteModifier;
use Silex\Controller;
use Silex\Application;

/**
 * @Annotation
 * @Target({"METHOD"})
 */
class Test implements RouteModifier {
    public function weight() { return 1; }

    /**
     * @param string $serviceid
     * @param \Silex\Controller $controller
     * @param \Silex\Application $app
     * @param \ReflectionClass $class
     * @param \ReflectionMethod $method
     * @return mixed
     */
    public function modify($serviceid, Controller $controller, Application $app, \ReflectionClass $class, \ReflectionMethod $method) {
        $controller->before(function() {
            var_dump('From @Test annotation');
        });
    }
}
