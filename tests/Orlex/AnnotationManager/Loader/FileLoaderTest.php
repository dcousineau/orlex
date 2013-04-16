<?php
namespace Orlex\AnnotationManager\Loader;

use Silex\Application;
use Mockery as m;

class FileLoaderTest extends \PHPUnit_Framework_TestCase {
    /**
     * @var FileLoader
     */
    protected $instance;

    public function setUp() {
        $this->instance = new FileLoader();
    }

    public function testGetAndSetCallback() {
        $unique   = time();
        $callback = function() use ($unique) { return $unique; };

        $this->instance->setCallback($callback);
        $returned = $this->instance->getCallback();
        $this->assertEquals($unique, $returned());
    }

    public function testLoad() {
        $this->markTestIncomplete('Test load against sample directory');
    }

    public function testSupports() {
        $this->markTestIncomplete('Test supports');
    }

    public function tearDown() {
        m::close();
    }
}