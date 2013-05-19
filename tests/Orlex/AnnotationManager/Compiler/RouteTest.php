<?php
namespace Orlex\AnnotationManager\Compiler;

use Silex\Application;
use Mockery as m;

use Doctrine\Common\Annotations;
use Orlex\AnnotationManager\Loader\FileLoader;
use Orlex\AnnotationManager\Compiler;
use Orlex\Annotation;
use Silex\Controller;

class RouteTest extends \PHPUnit_Framework_TestCase {
    /**
     * @var Application
     */
    protected $app;

    /**
     * @var Compiler\Route
     */
    protected $compiler;

    /**
     * @var \ReflectionClass
     */
    protected $refl;

    public function setUp() {
        $this->app = new Application();

        $this->compiler = new Compiler\Route(new Annotations\AnnotationReader(), new FileLoader());
        $this->compiler->setContainer($this->app);
    }

    public function testCompileClass() {
        $annotations = [];

        $routeAnnotation = new Annotation\Route();
        $routeAnnotation->path = "/test";

        $annotations[] = $routeAnnotation;


        $reflWithContainerAware = new \ReflectionClass('Sample\ClassWithContainerAware');

        $this->compiler->compileClass($reflWithContainerAware, $annotations);

        $this->assertTrue(isset($this->app['controller.sample_classwithcontaineraware']), 'Controller service was registered with the container');
        $this->assertInstanceOf('Sample\ClassWithContainerAware', $this->app['controller.sample_classwithcontaineraware'], 'Controller service returns an instance of the controller');
        $this->assertEquals($this->app, $this->app['controller.sample_classwithcontaineraware']->getContainer(), 'Controller has container set');



        $reflNoContainer = new \ReflectionClass('Sample\ClassWithoutContainerAware');

        $this->compiler->compileClass($reflNoContainer, $annotations);

        $this->assertTrue(isset($this->app['controller.sample_classwithoutcontaineraware']), 'Controller service was registered with the container');
        $this->assertInstanceOf('Sample\ClassWithoutContainerAware', $this->app['controller.sample_classwithoutcontaineraware'], 'Controller service returns an instance of the controller');
    }

    /**
     * @expectedException \PHPUnit_Framework_Error
     */
    public function testCompileClassDisallowsMultipleRouteAnnotations() {
        $annotations = [];

        $routeAnnotation = new Annotation\Route();
        $routeAnnotation->path = "/test";

        $annotations[] = $routeAnnotation;
        $annotations[] = $routeAnnotation;

        //Should trigger an error (which PHPUnit intercepts and throws a PHPUnit_Framework_Error)
        $this->compiler->compileClass($this->refl, $annotations);
    }

    public function testCompileMethod() {
        $that = $this;

        $expectedService = "controller.sample_classwithoutcontaineraware";
        $expectedModify = ['first', 'second'];
        $acutalModify   = [];

        //Setup and run class annotation to ensure global state
        $reflClass = new \ReflectionClass('Sample\ClassWithoutContainerAware');

        $classAnnotations = [];
        $classAnnotations[] = $classAnnotation = new Annotation\Route();
        $classAnnotation->path = "/foo";

        $this->compiler->compileClass($reflClass, $classAnnotations);

        //Setup method annotations
        $reflMethod = $reflClass->getMethod('fooMethod');

        $annotations = [];

        $annotations[] = $routeAnnotation = new Annotation\Route();
        $routeAnnotation->name = "foo_route";
        $routeAnnotation->path = "/bar";
        $routeAnnotation->methods = ["GET", "POST"];

        //Assure sort and ordering works
        $annotations[] = $firstMockAnnot = new \Sample\MockFirstAnnotation();
        $firstMockAnnot->delegate = function($serviceid, Controller $controller, Application $app, \ReflectionClass $class, \ReflectionMethod $method)
                                         use($that, $expectedService, $reflClass, $reflMethod, $acutalModify) {
            $that->assertEquals($expectedService, $serviceid);
            $actualModify[] = 'first';
        };

        $annotations[] = $secondMockAnnot = new \Sample\MockFirstAnnotation();
        $secondMockAnnot->delegate = function($serviceid, Controller $controller, Application $app, \ReflectionClass $class, \ReflectionMethod $method)
                                          use($that, $expectedService, $reflClass, $reflMethod, $acutalModify) {
            $that->assertEquals($expectedService, $serviceid);
            $actualModify[] = 'second';
        };

        $this->compiler->compileMethod($reflClass, $reflMethod, $annotations);

        /** @var $controllerCollection \Silex\ControllerCollection */
        $controllerCollection = $this->app['controllers'];
        $routes = $controllerCollection->flush();

        $route = $routes->get('foo_route');

        $this->assertNotNull($route, 'Found route with expected name');
        $this->assertEquals('/foo/bar', $route->getPath(), 'Path includes parent path from controller annotation');
        $this->assertEquals(["GET", "POST"], $route->getMethods(), 'Expected methods set');
        $this->assertEquals('bar', $route->getDefault('foo'), 'Default value inferred from reflection method');
    }

    public function tearDown() {
        m::close();
    }
}

////

namespace Sample;

use Orlex\ContainerAwareTrait;
use Orlex\Annotation\RouteModifier;
use Silex\Application;
use Silex\Controller;

class ClassWithContainerAware {
    use ContainerAwareTrait;

    public function fooMethod() {}
}

class ClassWithoutContainerAware {
    public function fooMethod($foo = 'bar') {}
}

class MockFirstAnnotation implements RouteModifier {
    /**
     * @var \Closure
     */
    public $delegate;

    public function weight() { return 1; }

    public function modify($serviceid, Controller $controller, Application $app, \ReflectionClass $class, \ReflectionMethod $method) {
        $delegate = $this->delegate;
        $delegate($serviceid, $controller, $app, $class, $method);
    }
}

class MockSecondAnnotation extends MockFirstAnnotation {
    public function weight() { return 2; }
}