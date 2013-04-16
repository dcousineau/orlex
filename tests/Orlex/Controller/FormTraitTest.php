<?php
namespace Orlex\Controller {
    use Silex\Application;
    use Mockery as m;

    use Orlex\ContainerAwareTrait;

    class FormTraitTest extends \PHPUnit_Framework_TestCase {
        /**
         * @var Application
         */
        protected $app;

        /**
         * @var MockUsesFormTrait
         */
        protected $mock;

        public function setUp() {
            $this->app = new Application();
            $this->mock = new MockUsesFormTrait();
            $this->mock->setContainer($this->app);
        }

        public function testCreateBuilder() {
            $this->app['form.factory'] = m::mock();

            $type    = 'type';
            $data    = 'data';
            $options = ['options'];
            $builder = m::mock('Symfony\Component\Form\FormBuilderInterface');

            $unique = time();

            $this->app['form.factory']->shouldReceive('createBuilder')
                                      ->times(1)
                                      ->with($type, $data, $options, $builder)
                                      ->andReturn($unique);

            $this->mock->createBuilder($type, $data, $options, $builder);
        }

        public function tearDown() {
            m::close();
        }
    }

    class MockUsesFormTrait {
        use ContainerAwareTrait;
        use FormTrait;
    }
}

////
// Since we do not intend to make Symfony\Form a dependency, declare namespace to prevent PHP errors when trying to load
// this trait for testing. DIRTY HAX
////

namespace Symfony\Component\Form {
    interface FormBuilderInterface {}
}