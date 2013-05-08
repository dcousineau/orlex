<?php
namespace Orlex\AnnotationManager\Compiler;

use Doctrine\Common\Annotations\Reader;
use Orlex\AnnotationManager\Loader\FileLoader;

abstract class AbstractCompiler {
    /**
     * @var \Doctrine\Common\Annotations\Reader
     */
    protected $reader;

    /**
     * @var \Orlex\AnnotationManager\Loader\FileLoader
     */
    protected $loader;

    /**
     * @param Reader $reader
     * @param FileLoader $loader
     */
    public function __construct(Reader $reader, FileLoader $loader) {
        $this->reader = $reader;
        $this->loader = $loader;
    }

    /**
     * @param string $path Directory path to compile
     */
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

    /**
     * @param FileLoader $loader
     */
    public function setLoader(FileLoader $loader) {
        $this->loader = $loader;
    }

    /**
     * @return FileLoader
     */
    public function getLoader() {
        return $this->loader;
    }

    /**
     * @param Reader $reader
     */
    public function setReader(Reader $reader) {
        $this->reader = $reader;
    }

    /**
     * @return Reader
     */
    public function getReader() {
        return $this->reader;
    }

    abstract public function compileClass(\ReflectionClass $class, array $annotations);
    abstract public function compileMethod(\ReflectionClass $class, \ReflectionMethod $method, array $annotations);
}