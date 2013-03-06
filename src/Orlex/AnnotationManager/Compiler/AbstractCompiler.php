<?php
namespace Orlex\AnnotationManager\Compiler;

use Doctrine\Common\Annotations\IndexedReader;
use Orlex\AnnotationManager\Loader\FileLoader;

abstract class AbstractCompiler {
    /**
     * @var \Doctrine\Common\Annotations\IndexedReader
     */
    protected $reader;

    /**
     * @var \Orlex\AnnotationManager\Loader\FileLoader
     */
    protected $loader;

    public function __construct(IndexedReader $reader, FileLoader $loader) {
        $this->reader = $reader;
        $this->loader = $loader;
    }

    public function compile($path) {

        $this->loader->setCallback(function($class, $file) {
            $reflClass = new \ReflectionClass($class);
            //Ignore abstract classes
            if ($reflClass->isAbstract())
                return;

            $classAnnotations = $this->reader->getClassAnnotations($reflClass);

            $this->compileClass($reflClass, $classAnnotations);

            foreach ($reflClass->getMethods(\ReflectionMethod::IS_PUBLIC) as $reflMethod) {
                /** @var $reflMethod \ReflectionMethod */
                $methodAnnotations = $this->reader->getMethodAnnotations($reflMethod);

                $this->compileMethod($reflClass, $reflMethod, $methodAnnotations);
            }
        });

        $this->loader->load($path);
    }

    abstract public function compileClass(\ReflectionClass $class, array $annotations);
    abstract public function compileMethod(\ReflectionClass $class, \ReflectionMethod $method, array $annotations);
}