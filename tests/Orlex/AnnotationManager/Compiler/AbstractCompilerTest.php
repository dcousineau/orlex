<?php
namespace Orlex\AnnotationManager\Compiler;

use Silex\Application;
use Mockery as m;
use Doctrine\Common\Annotations;
use Orlex\AnnotationManager\Loader\FileLoader;

class AbstractCompilerTest extends \PHPUnit_Framework_TestCase {
    /**
     * @var MockAbstractCompiler
     */
    protected $mock;

    /**
     * @var \Mockery\MockInterface
     */
    protected $delegate;

    public function setUp() {
        $this->mock = new MockAbstractCompiler(new Annotations\IndexedReader(new Annotations\AnnotationReader()), new FileLoader());
        $this->delegate = m::mock(); //->shouldIgnoreMissing();

        $this->mock->delegate = $this->delegate;
    }

    public function testCompile() {
        $samplePath = realpath('./tests/resources/SampleClass.php');
        require_once $samplePath;

        $reflClass = new \ReflectionClass('Sample\SampleClass');
        $expectedAnnotations = [time()];

        $reflClassMatcher = m::on(function($inp) use ($reflClass){ return $inp->getName() == $reflClass->getName(); });
        $this->delegate->shouldReceive('compileClass')
                       ->once()
                       ->with($reflClassMatcher, $expectedAnnotations);

        foreach ($reflClass->getMethods(\ReflectionMethod::IS_PUBLIC) as $reflMethod) {
            $this->delegate->shouldReceive('compileMethod')
                           ->once()
                           ->with($reflClassMatcher, m::on(function($inp) use ($reflMethod){ return $inp->getName() == $reflMethod->getName(); }), $expectedAnnotations);
        }

        $reader = m::mock('Doctrine\Common\Annotations\IndexedReader');
        $reader->shouldReceive('getClassAnnotations')
               ->once()
               ->withAnyArgs()
               ->andReturn($expectedAnnotations);
        $reader->shouldReceive('getMethodAnnotations')
               ->twice()
               ->withAnyArgs()
               ->andReturn($expectedAnnotations);
        $this->mock->setReader($reader);

        $this->mock->compile($samplePath);
    }

    public function testGetAndSetLoader() {
        $this->mock->setLoader($loader = new FileLoader());
        $this->assertEquals($loader, $this->mock->getLoader());
    }

    public function testGetAndSetReader() {
        $this->mock->setReader($reader = m::mock('Doctrine\Common\Annotations\IndexedReader'));
        $this->assertEquals($reader, $this->mock->getReader());
    }

    public function tearDown() {
        m::close();
    }
}

class MockAbstractCompiler extends AbstractCompiler {
    public $delegate;

    public function compileClass(\ReflectionClass $class, array $annotations) {
        $this->delegate->compileClass($class, $annotations);
    }
    public function compileMethod(\ReflectionClass $class, \ReflectionMethod $method, array $annotations) {
        $this->delegate->compileMethod($class, $method, $annotations);
    }
}