<?php
namespace Orlex\Annotation;

use Silex\Application;

/**
 * @Annotation
 * @Target({"METHOD", "CLASS"})
 */
class Route {
    /**
     * @var string
     */
    public $path;

    public function modifyApp(Application $app, \Reflection $refl = null) {
        
    }
}