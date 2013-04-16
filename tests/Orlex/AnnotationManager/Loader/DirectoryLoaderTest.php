<?php
namespace Orlex\AnnotationManager\Loader;

use Silex\Application;
use Mockery as m;

class DirectoryLoaderTest extends \PHPUnit_Framework_TestCase {
    /**
     * @var DirectoryLoader
     */
    protected $instance;

    public function setUp() {
        $this->instance = new DirectoryLoader();
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