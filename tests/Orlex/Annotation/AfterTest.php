<?php
namespace Orlex\Annotation;

use Silex\Application;
use Mockery as m;

class AfterTest extends \PHPUnit_Framework_TestCase {
    /**
     * @var After
     */
    protected $annotation;

    public function setUp() {
        $this->annotation = new After();
    }

    public function testWeight() {
        $this->assertEquals(1, $this->annotation->weight());
    }

    public function testModify() {
        $this->annotation->callback = 'callbackMethod';

        $serviceid  = 'foobar';
        $controller = m::mock('Silex\Controller');
        $app        = new Application();
        $reflClass  = new \ReflectionClass(new AfterControllerClass);
        $reflMethod = $reflClass->getMethod('originalMethod');

        $app[$serviceid] = new AfterControllerClass;

        $controller->shouldReceive('after')
                   ->once()
                   ->with([$app[$serviceid], $this->annotation->callback]);

        $this->annotation->modify($serviceid, $controller, $app, $reflClass, $reflMethod);
    }

    public function tearDown() {
        m::close();
    }
}

class AfterControllerClass {
    public function originalMethod() {}
    public function callbackMethod() {}
}