<?php
namespace Orlex\AnnotationManager\Loader;

use Silex\Application;
use Mockery as m;

/**
 * @group loader
 */
class DirectoryLoaderTest extends \PHPUnit_Framework_TestCase {
    /**
     * @var DirectoryLoader
     */
    protected $instance;

    public function setUp() {
        $this->instance = new DirectoryLoader();
    }

    public function testLoad() {
        $that          = $this;
        $completed     = false;

        $expectedClasses = [
            ['AnotherNs\AnotherClassNotMatchingFilename', realpath('./tests/resources/AnotherClass.php')],
            ['\NoNsClass',                                realpath('./tests/resources/NoNsClass.php')],
            ['Sample\SampleClass',                        realpath('./tests/resources/SampleClass.php')],
            ['\ClassInSubDir',                            realpath('./tests/resources/SubDirectory/ClassInSubDir.php')],
        ];
        $foundClasses = [];

        $callback = function($class, $path) use ($that, &$foundClasses, &$completed) {
            $foundClasses[] = [$class, $path];
            $completed = true;
        };

        $this->instance->setCallback($callback);
        $this->instance->load($this->supportsProvider()[0][0]);

        $this->assertEquals($expectedClasses, $foundClasses, "Found all classes");

        if (!$completed) $this->fail("Did not reach callback, meaning 0 files were found");
    }

    /**
     * @dataProvider supportsProvider
     */
    public function testSupports($resource, $expected) {
        $this->assertEquals($expected, $this->instance->supports($resource));
    }

    public function supportsProvider() {
        return [
            [realpath("./tests/resources"),                  true],
            [realpath("./tests/directory/does/not/exist"),   false],
            [realpath("./tests/resources/AnotherClass.php"), false],
        ];
    }

    public function tearDown() {
        m::close();
    }
}