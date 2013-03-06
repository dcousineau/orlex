<?php
namespace Orlex;

trait ContainerAwareTrait {
    /**
     * @var \Silex\Application
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
     * @return \Silex\Application
     */
    public function getContainer() {
        return $this->container;
    }

    /**
     * @param \Silex\Application $container
     * @return $this
     */
    public function setContainer(\Silex\Application $container) {
        $this->container = $container;
        return $this;
    }
}