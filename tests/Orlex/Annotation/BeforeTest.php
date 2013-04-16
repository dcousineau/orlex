<?php
namespace Orlex\Annotation;

use Silex\Application;
use Mockery as m;

class BeforeTest extends \PHPUnit_Framework_TestCase {
    /**
     * @var Before
     */
    protected $annotation;

    public function setUp() {
        $this->annotation = new Before();
    }

    public function testWeight() {
        $this->assertEquals(1, $this->annotation->weight());
    }

    public function testModify() {
        $this->annotation->callback = 'callbackMethod';

        $serviceid  = 'foobar';
        $controller = m::mock('Silex\Controller');
        $app        = new Application();
        $reflClass  = new \ReflectionClass(new BeforeControllerClass);
        $reflMethod = $reflClass->getMethod('originalMethod');

        $app[$serviceid] = new BeforeControllerClass;

        $controller->shouldReceive('before')
            ->once()
            ->with([$app[$serviceid], $this->annotation->callback]);

        $this->annotation->modify($serviceid, $controller, $app, $reflClass, $reflMethod);
    }

    public function tearDown() {
        m::close();
    }
}

class BeforeControllerClass {
    public function originalMethod() {}
    public function callbackMethod() {}
}