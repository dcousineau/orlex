<?php
namespace Orlex\AnnotationManager\Compiler;

use Orlex\Annotation\RouteModifier;
use Orlex\ContainerAwareTrait;

class Route extends AbstractCompiler {
    use ContainerAwareTrait;

    protected $globals = [
        'path' => '',
    ];

    protected $globalAnnotations = [];

    public function compileClass(\ReflectionClass $class, array $annotations) {
        $app = $this->getContainer();

        if (isset($annotations['Orlex\Annotation\Route'])) {
            /** @var $route \Orlex\Annotation\Route */
            $route = $annotations['Orlex\Annotation\Route'];

            $this->globals['path'] = rtrim($route->path, '/');
        }

        $this->globalAnnotations = $annotations;

        //Register Controller With App

        $app['controller.' . $this->formatName($class)] = $app->share(function($app) use ($class) {
            $controller = $class->newInstance();

            if (method_exists($controller, 'setContainer'))
                $controller->setContainer($app);

            return $controller;
        });
    }

    public function compileMethod(\ReflectionClass $class, \ReflectionMethod $method, array $annotations) {
        $app = $this->getContainer();

        if (!isset($annotations['Orlex\Annotation\Route'])) return;

        /** @var $route \Orlex\Annotation\Route */
        $route = $annotations['Orlex\Annotation\Route']; unset($annotations['Orlex\Annotation\Route']);

        $serviceid = 'controller.' . $this->formatName($class);

        $path = $this->globals['path'] . '/' . ltrim($route->path, '/');
        $httpMethod = strtoupper(implode('|', $route->methods));
        if (empty($httpMethod))
            $httpMethod = 'GET';
        $name = $route->name;
        if (empty($name))
            $name = $this->formatName($class, $method);

        $controller = $app->match($path, "$serviceid:{$method->getName()}")
                          ->method($httpMethod)
                          ->bind($name);

        $modifiers = array_filter($annotations, function($a) { return $a instanceOf RouteModifier;});
        usort($modifiers, function (RouteModifier $a, RouteModifier $b) {
            return $a->weight() > $b->weight() ? 1 : -1;
        });

        foreach ($modifiers as $annotation) {
            /** @var $annotation RouteModifier */
            $annotation->modify($serviceid, $controller, $app, $class, $method);
        }
    }

    protected function formatName(\ReflectionClass $class, \ReflectionMethod $method = null) {
        $classname = strtolower(str_replace('\\', '_', $class->name));

        if ($method) {
            $classname .= '_' . strtolower($method->name);
        }

        return $classname;
    }
}