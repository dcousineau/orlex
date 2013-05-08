<?php
namespace Orlex;

use Silex\Application;
use Mockery as m;

use Doctrine\Common\Annotations\AnnotationRegistry;

class ServiceProviderTest extends \PHPUnit_Framework_TestCase {
    /**
     * @var ServiceProvider
     */
    protected $provider;

    /**
     * @var Application
     */
    protected $app;

    public function setUp() {
        $this->app = new Application();
        $this->provider = new ServiceProvider();
    }

    public function testBoot() {
        $this->app['orlex.controller.dirs'] = [
            $path = '/sample/path/to/controllers'
        ];

        $this->app['orlex.route.compiler'] = m::mock('Orlex\AnnotationManager\Compiler');

        $this->app['orlex.route.compiler']->shouldReceive('compile')
                                          ->once()
                                          ->with($path)
                                          ->andReturnNull();

        $this->provider->boot($this->app);
    }

    public function testRegister() {
        $this->provider->register($this->app);

        $this->assertInstanceOf('Silex\ServiceControllerResolver', $this->app['resolver'], 'Registers the ServiceControllerServiceProvider');
        $this->assertEquals([], $this->app['orlex.controller.dirs'], 'Registers empty directory placeholder');
        $this->assertEquals([], $this->app['orlex.annotation.dirs'], 'Registers empty annotation placeholder');
        $this->assertTrue(isset($this->app['orlex.annotation.reader']), 'Registers annotation reader');
        $this->assertTrue(isset($this->app['orlex.directoryloader']), 'Registers directory loader');
        $this->assertTrue(isset($this->app['orlex.route.compiler']), 'Registers route compiler');

        //Override annotation dirs for a nice tick in code completion: we're going to have to trust the
        //AnnotationRegistry singleton until we refactor the registration
        $this->app['orlex.annotation.dirs'] = [
            '/path/to/annotation/dirs' => 'Sample\Namespace',
        ];

        //Check indexed reader
        /** @var $reader \Doctrine\Common\Annotations\Reader */
        $reader = $this->app['orlex.annotation.reader'];
        $this->assertInstanceOf('Doctrine\Common\Annotations\Reader', $reader);

        //Check directory loader
        /** @var $loader \Orlex\AnnotationManager\Loader\DirectoryLoader */
        $loader = $this->app['orlex.directoryloader'];
        $this->assertInstanceOf('Orlex\AnnotationManager\Loader\DirectoryLoader', $loader);

        //Check route compiler
        /** @var $compiler \Orlex\AnnotationManager\Compiler\Route */
        $compiler = $this->app['orlex.route.compiler'];
        $this->assertInstanceOf('Orlex\AnnotationManager\Compiler\Route', $compiler);
        $this->assertEquals($reader, $compiler->getReader(), 'Reader is set from orlex.annotation.reader');
        $this->assertEquals($loader, $compiler->getLoader(), 'Loader is set from orlex.directoryloader');


    }

    public function tearDown() {
        m::close();
    }
}