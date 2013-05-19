<?php
namespace Orlex\AnnotationManager\Compiler;

use Orlex\Annotation\RouteModifier;
use Orlex\ContainerAwareTrait;
use Orlex\Annotation\Route as RouteAnnotation;

class Route extends AbstractCompiler {
    use ContainerAwareTrait;

    protected $globals;
    protected $globalAnnotations;

    /**
     * Reset globals to ensure clean slate
     */
    protected function resetState() {
        $this->globals = [
            'path' => '',
        ];

        $this->globalAnnotations = [];
    }

    /**
     * @param \ReflectionClass $class
     * @param array $annotations
     */
    public function compileClass(\ReflectionClass $class, array $annotations) {
        $app = $this->getContainer();

        //New class, reset internal state
        $this->resetState();


        $routes = array_filter($annotations, function($annotation) {
            return is_object($annotation) && $annotation instanceof RouteAnnotation;
        });

        if (count($routes) > 1) trigger_error('Multiple Route annotations at a class level are not supported', E_USER_ERROR);

        if ($routes) {
            /** @var $route \Orlex\Annotation\Route */
            $route = $routes[0];

            //Prefix path for all method routes
            $this->globals['path'] = rtrim($route->path, '/');
        }

        $this->globalAnnotations = $annotations;

        //Register Controller With App
        $app[$this->serviceId($class)] = $app->share(function($app) use ($class) {
            $controller = $class->newInstance();

            if (method_exists($controller, 'setContainer'))
                $controller->setContainer($app);

            return $controller;
        });
    }

    /**
     * @param \ReflectionClass $class
     * @param \ReflectionMethod $method
     * @param array $annotations
     */
    public function compileMethod(\ReflectionClass $class, \ReflectionMethod $method, array $annotations) {
        $app = $this->getContainer();

        //We're compiling routes, only concern ourselves with methods that have a @Route annotation


        $routes = array_filter($annotations, function($annotation) {
            return is_object($annotation) && $annotation instanceof RouteAnnotation;
        });

        if (!$routes) return;

        foreach($routes as $route) {
            /** @var $route \Orlex\Annotation\Route */

            $serviceid = $this->serviceId($class);

            $path = $this->globals['path'] . '/' . ltrim($route->path, '/');

            $httpMethod = strtoupper(implode('|', $route->methods));
            if (empty($httpMethod))
                $httpMethod = 'GET';

            $name = $route->name;
            if (empty($name))
                $name = $this->formatName($class, $method);

            //Bind route to Silex application
            $controller = $app->match($path, "$serviceid:{$method->getName()}")
                              ->method($httpMethod)
                              ->bind($name);

            //Get parameters from method and bind default values to Silex route to allow optional
            //parameters. E.g. public function fooAction($param_1, $param_2 = 'default'); will perform
            //a $controller->value('param_2', 'default');
            foreach ($method->getParameters() as $parameter) {
                /** @var $parameter \ReflectionParameter */
                if ($parameter->isDefaultValueAvailable()) {
                    $controller->value($parameter->getName(), $parameter->getDefaultValue());
                }
            }

            //Fetch and sort RouteModifier annotations for modification
            $modifiers = array_filter($annotations, function($a) { return $a instanceOf RouteModifier;});
            usort($modifiers, function (RouteModifier $a, RouteModifier $b) {
                return $a->weight() > $b->weight() ? 1 : -1;
            });

            foreach ($modifiers as $annotation) {
                /** @var $annotation RouteModifier */
                $annotation->modify($serviceid, $controller, $app, $class, $method);
            }
        }
    }

    /**
     * Formats Class into service id used by DI container
     *
     * @param \ReflectionClass $class
     * @return string
     */
    protected function serviceId(\ReflectionClass $class) {
        return 'controller.' . $this->formatName($class);
    }

    /**
     * Formats Class and optionally Method into a slug name in the format:
     *    namespace_class_method
     *
     * @param \ReflectionClass $class
     * @param \ReflectionMethod $method
     * @return string
     */
    protected function formatName(\ReflectionClass $class, \ReflectionMethod $method = null) {
        $classname = strtolower(str_replace('\\', '_', $class->name));

        return $classname;
    }
}