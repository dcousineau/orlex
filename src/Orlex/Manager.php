<?php
namespace Orlex;

use Silex\Application;
use Silex\Provider\ServiceControllerServiceProvider;
use Doctrine\Common\Annotations\AnnotationReader;
use Symfony\Component\Routing\RouteCollection;
use Doctrine\Common\Annotations\AnnotationRegistry;

class Manager {
    use ContainerAwareTrait;
    use ConfigurationSetterMapTrait;

    /**
     * @var array
     */
    protected $configuration;

    /**
     * @var AnnotationManager
     */
    protected $annotationManager;

    /**
     * @var RouteCollection
     */
    protected $routes;
    
    public function __construct(Application $app, array $configuration = []) {
        $this->setContainer($app);
        
        $this->configuration = $configuration;

        $this->processConfigurationArray($configuration);
    }
    
    public function scaffold() {
        $this->getContainer()->register(new ServiceControllerServiceProvider());

        $this->routes = new RouteCollection();
        foreach ($this->configuration['controller_paths'] as $path) {
            $this->routes->addCollection($this->getAnnotationManager()->getLoader()->load($path));
        }

        return $this;
    }
    
    public function run() {
        return $this->getContainer()->run();
    }

    public function getAnnotationManager() {
        if (!$this->annotationManager) {
            $this->annotationManager = new AnnotationManager($this->getContainer());
        }

        return $this->annotationManager;
    }

    public function setAnnotationManager(AnnotationManager $manager) {
        $this->annotationManager = $manager;
    }
}