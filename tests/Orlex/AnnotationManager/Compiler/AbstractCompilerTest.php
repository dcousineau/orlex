<?php
namespace Orlex\AnnotationManager\Compiler;

use Silex\Application;
use Mockery as m;

use Orlex\AnnotationManager\Loader\FileLoader;

class AbstractCompilerTest extends \PHPUnit_Framework_TestCase {
    /**
     * @var \Mockery\MockInterface
     */
    protected $mock;

    public function setUp() {
        $this->mock = m::mock('Orlex\AnnotationManager\Compiler\AbstractCompiler');
    }

    public function testCompile() {
        $this->markTestIncomplete('Perform an integration test');
    }

    public function testGetAndSetLoader() {
        $this->mock->shouldDeferMissing();

        $this->mock->setLoader($loader = new FileLoader());
        $this->assertEquals($loader, $this->mock->getLoader());
    }

    public function testGetAndSetReader() {
        $this->mock->shouldDeferMissing();

        $this->mock->setReader($reader = m::mock('Doctrine\Common\Annotations\IndexedReader'));
        $this->assertEquals($reader, $this->mock->getReader());
    }

    public function tearDown() {
        m::close();
    }
}