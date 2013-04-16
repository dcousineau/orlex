<?php
namespace Orlex\Controller;

use Silex\Application;
use Mockery as m;

use Orlex\ContainerAwareTrait;

class TwigTraitTest extends \PHPUnit_Framework_TestCase {
    /**
     * @var Application
     */
    protected $app;

    /**
     * @var \Mockery\MockInterface
     */
    protected $twig;

    /**
     * @var MockUsesTwigTrait
     */
    protected $mock;

    public function setUp() {
        $this->app = new Application();
        $this->app['twig'] = $this->twig = m::mock('Twig_Environment');

        $this->mock = new MockUsesTwigTrait();
        $this->mock->setContainer($this->app);
    }

    public function testRenderShortcut() {
        $template = 'template/file.twig';
        $vars     = ['foo' => 'bar'];
        $expected = '<figure>it out</figure>';

        $this->twig->shouldReceive('render')
                   ->once()
                   ->with($template, $vars)
                   ->andReturn($expected);

        $this->assertEquals($expected, $this->mock->render($template, $vars));
    }

    public function tearDown() {
        m::close();
    }
}

class MockUsesTwigTrait {
    use ContainerAwareTrait;
    use TwigTrait;
}