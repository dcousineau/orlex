<?php
namespace Orlex\Controller;

use Silex\Application;
use Mockery as m;

use Orlex\ContainerAwareTrait;

class SessionTraitTest extends \PHPUnit_Framework_TestCase {
    /**
     * @var Application
     */
    protected $app;

    /**
     * @var \Mockery\MockInterface
     */
    protected $session;

    /**
     * @var MockUsesSessionTrait
     */
    protected $mock;

    public function setUp() {
        $this->app = new Application();
        $this->app['session'] = $this->session = m::mock('Symfony\Component\HttpFoundation\Session\Session');

        $this->mock = new MockUsesSessionTrait();
        $this->mock->setContainer($this->app);
    }

    public function testGetSessionShortcut() {
        $this->assertEquals($this->session, $this->mock->session());
    }

    public function testSetFlashShortcut() {
        $type    = 'type';
        $message = 'Lorem Ipsum';

        $this->session->shouldReceive('getFlashBag->set')
                      ->once()
                      ->with($type, $message);

        $this->mock->setFlash($type, $message);
    }

    public function tearDown() {
        m::close();
    }
}

class MockUsesSessionTrait {
    use ContainerAwareTrait;
    use SessionTrait;
}