<?php
namespace Orlex\Controller;

use Silex\Application;
use Mockery as m;

use Orlex\ContainerAwareTrait;

class UrlGeneratorTraitTest extends \PHPUnit_Framework_TestCase {
    /**
     * @var Application
     */
    protected $app;

    /**
     * @var \Mockery\MockInterface
     */
    protected $url;

    /**
     * @var MockUsesUrlGeneratorTrait
     */
    protected $mock;

    public function setUp() {
        $this->app = new Application();
        $this->app['url_generator'] = $this->url = m::mock('Symfony\Component\Routing\Generator\UrlGenerator');

        $this->mock = new MockUsesUrlGeneratorTrait();
        $this->mock->setContainer($this->app);
    }

    public function testPathShortcut() {
        $route      = 'route_name';
        $parameters = ['foo' => 'bar'];
        $expected   = '/route/name/foo/bar';

        $this->url->shouldReceive('generate')
                  ->once()
                  ->with($route, $parameters, false)
                  ->andReturn($expected);

        $this->assertEquals($expected, $this->mock->path($route, $parameters));
    }

    public function testUrlShortcut() {
        $route      = 'route_name';
        $parameters = ['foo' => 'bar'];
        $expected   = 'http://domain.tld/route/name/foo/bar';

        $this->url->shouldReceive('generate')
            ->once()
            ->with($route, $parameters, true)
            ->andReturn($expected);

        $this->assertEquals($expected, $this->mock->url($route, $parameters));
    }

    public function tearDown() {
        m::close();
    }
}

class MockUsesUrlGeneratorTrait {
    use ContainerAwareTrait;
    use UrlGeneratorTrait;
}