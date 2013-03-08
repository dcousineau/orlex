<?php
namespace Orlex;

use Silex\Application;

trait ContainerAwareTrait {
    /**
     * @var Application
     */
    protected $container;

    /**
     * @param string $key
     * @return mixed
     */
    public function get($key) {
        return $this->container[$key];
    }

    /**
     * @param string $key
     * @return mixed
     */
    public function raw($key) {
        return $this->container->raw($key);
    }

    /**
     * @return Application
     */
    public function getContainer() {
        return $this->container;
    }

    /**
     * @param Application $container
     * @return $this
     */
    public function setContainer(Application $container) {
        $this->container = $container;
        return $this;
    }
}