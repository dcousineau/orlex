<?php
namespace Orlex\AnnotationManager\Compiler;

use Silex\Application;
use Mockery as m;

use Orlex\AnnotationManager\Loader\FileLoader;

class RouteTest extends \PHPUnit_Framework_TestCase {
    /**
     * @var \Mockery\MockInterface
     */
    protected $mock;

    public function setUp() {
        $this->mock = m::mock('Orlex\AnnotationManager\Compiler\Route');
    }

    public function testCompileClass() {
        $this->markTestIncomplete('Perform an integration test');
    }

    public function testCompileMethod() {
        $this->markTestIncomplete('Perform an integration test');
    }

    public function tearDown() {
        m::close();
    }
}