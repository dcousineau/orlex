<?php
namespace Orlex\AnnotationManager\Loader;

use Silex\Application;
use Mockery as m;

/**
 * @group loader
 */
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

    /**
     * @dataProvider loadProvider
     */
    public function testLoad($samplePath, $expectedClass) {
        $that          = $this;
        $completed     = false;

        $callback = function($class, $path) use ($that, $expectedClass, $samplePath, &$completed) {
            if (!$expectedClass)
                $this->fail("Expected not to cross $path as it should not contain a PHP class");

            $that->assertEquals($expectedClass, $class, 'Loader determines the correct class with full namesapce');
            $this->assertEquals($samplePath, $path, 'Loader returns fully qualified path');

            $completed = true;
        };

        $this->instance->setCallback($callback);
        $this->instance->load($samplePath);

        if (!$completed && $expectedClass) $this->fail("Did not reach callback, meaning file or class was not found");
    }

    public function loadProvider() {
        return [
            [realpath("./tests/resources/SampleClass.php"),  'Sample\SampleClass'],
            [realpath("./tests/resources/AnotherClass.php"), 'AnotherNs\AnotherClassNotMatchingFilename'],
            [realpath("./tests/resources/NoNsClass.php"),    '\NoNsClass'],
            [realpath("./tests/resources/NotPhp.php"),       false],
            [realpath("./tests/resources/NoClass.php"),      false],
        ];
    }

    /**
     * @dataProvider supportsProvider
     */
    public function testSupports($resource, $expected) {
        $this->assertEquals($expected, $this->instance->supports($resource));
    }

    public function supportsProvider() {
        return [
            [realpath("./tests/resources/SampleClass.php"), true],
            [realpath("./tests/resources/NotPHP.rb"),       false],
        ];
    }

    public function tearDown() {
        m::close();
    }
}