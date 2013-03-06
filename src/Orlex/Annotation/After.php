<?php
namespace Orlex\Annotation;

use Silex\Controller;
use Silex\Application;

/**
 * @Annotation
 * @Target({"METHOD"})
 */
class After implements RouteModifier {
    /**
     * @var string
     */
    public $callback;

    public function weight() { return 1; }

    /**
     * @param string $serviceid
     * @param \Silex\Controller $controller
     * @param \Silex\Application $app
     * @param \ReflectionClass $class
     * @param \ReflectionMethod $method
     * @return mixed|void
     */
    public function modify($serviceid, Controller $controller, Application $app, \ReflectionClass $class, \ReflectionMethod $method) {
        $callback = $this->callback;

        if (!is_callable($callback)) {
            if ($class->hasMethod($callback)) {
                $callback = [$app[$serviceid], $callback];
            }

        }

        $controller->after($callback);
    }
}