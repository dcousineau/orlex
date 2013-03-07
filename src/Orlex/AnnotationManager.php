<?php
namespace Orlex;

use Doctrine\Common\Annotations\AnnotationReader;
use Doctrine\Common\Annotations\IndexedReader;
use Doctrine\Common\Annotations\AnnotationRegistry;

use Symfony\Component\Routing\Loader\AnnotationDirectoryLoader;
use Symfony\Component\Config\FileLocator;

class AnnotationManager {
    use ContainerAwareTrait;

    /**
     * @var AnnotationReader
     */
    protected $reader;

    public function __construct(\Silex\Application $app, array $annotationPaths = []) {
        $this->setContainer($app);

        AnnotationRegistry::registerAutoloadNamespace('Orlex\Annotation', dirname(__DIR__));

        foreach ($annotationPaths as $dir => $namespace) {
            AnnotationRegistry::registerAutoloadNamespace($namespace, $dir);
        }
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
     * @return \Doctrine\Common\Annotations\IndexedReader
     */
    public function getIndexedReader() {
        return new IndexedReader($this->getReader());
    }
}