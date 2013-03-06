<?php
namespace Orlex;

use Doctrine\Common\Annotations\AnnotationReader;
use Doctrine\Common\Annotations\AnnotationRegistry;

use Symfony\Component\Routing\Loader\AnnotationDirectoryLoader;
use Symfony\Component\Config\FileLocator;

class AnnotationManager {
    use ContainerAwareTrait;

    /**
     * @var AnnotationReader
     */
    protected $reader;

    protected $loader;

    public function __construct(\Silex\Application $app) {
        $this->setContainer($app);

        AnnotationRegistry::registerAutoloadNamespace('Symfony\Component\Routing\Annotation\Route', dirname(dirname(__DIR__)) .'/vendor/symfony/routing/');
        AnnotationRegistry::registerAutoloadNamespace('Orlex\Annotation', dirname(__DIR__));
    }

    /**
     * @param \Doctrine\Common\Annotations\AnnotationReader $reader
     */
    public function setReader(AnnotationReader $reader) {
        $this->reader = $reader;
    }

    /**
     * @return \Doctrine\Common\Annotations\AnnotationReader
     */
    public function getReader() {
        if (!$this->reader) {
            $this->setReader(new AnnotationReader());
        }

        return $this->reader;
    }

    /**
     * @param \Symfony\Component\Routing\Loader\AnnotationDirectoryLoader $loader
     */
    public function setLoader(AnnotationDirectoryLoader $loader) {
        $this->loader = $loader;
    }

    /**
     * @return \Symfony\Component\Routing\Loader\AnnotationDirectoryLoader
     */
    public function getLoader(){
        if (!$this->loader) {
            $locator      = new FileLocator();
            $classLoader  = new AnnotationClassLoader($this->getReader());
            $classLoader->setContainer($this->getContainer());
            $this->loader = new AnnotationDirectoryLoader($locator, $classLoader);
        }

        return $this->loader;
    }


}