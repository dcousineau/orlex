<?php
namespace Orlex\Annotation;

use Silex\Controller;
use Silex\Application;

interface RouteModifier {
    /**
     * @return int
     */
    public function weight();

    /**
     * @param string $serviceid
     * @param \Silex\Controller $controller
     * @param \Silex\Application $app
     * @param \ReflectionClass $class
     * @param \ReflectionMethod $method
     * @return mixed
     */
    public function modify($serviceid, Controller $controller, Application $app, \ReflectionClass $class, \ReflectionMethod $method);
}