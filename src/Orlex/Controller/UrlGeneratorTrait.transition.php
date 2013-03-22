<?php
namespace Orlex\Controller;

trait UrlGeneratorTrait {
    /**
     * Generates a path from the given parameters.
     *
     * @param string $route      The name of the route
     * @param mixed  $parameters An array of parameters
     *
     * @return string The generated path
     */
    public function path($route, $parameters = array()) {
        return $this->get('url_generator')->generate($route, $parameters, false);
    }

    /**
     * Generates an absolute URL from the given parameters.
     *
     * @param string $route      The name of the route
     * @param mixed  $parameters An array of parameters
     *
     * @return string The generated URL
     */
    public function url($route, $parameters = array()) {
        return $this->get('url_generator')->generate($route, $parameters, true);
    }
}