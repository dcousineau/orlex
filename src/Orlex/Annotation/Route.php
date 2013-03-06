<?php
namespace Orlex\Annotation;

use Silex\Controller;
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

    /**
     * @var array<string>
     */
    public $methods = ['GET'];

    /**
     * @var string
     */
    public $name;
}