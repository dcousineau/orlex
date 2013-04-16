<?php
namespace Orlex;

use Silex\Application;

class ContainerAwareTraitTest extends \PHPUnit_Framework_TestCase {
    public function testCanSetAndRetrieveContainer() {
        $trait = $this->getObjectForTrait('\Orlex\ContainerAwareTrait');

        $container = new Application();

        $trait->setContainer($container);

        $this->assertEquals($container, $trait->getContainer());
    }

    public function testCanRetrieveServicesFromShortcut() {
        $trait = $this->getObjectForTrait('\Orlex\ContainerAwareTrait');

        $sampleService = md5(time());

        $container = new Application();
        $container['sample'] = $sampleService;


        $trait->setContainer($container);

        $this->assertEquals($sampleService, $trait->get('sample'));
    }

    public function testCanRetrieveRawServicesFromShortcut() {
        $trait = $this->getObjectForTrait('\Orlex\ContainerAwareTrait');

        $hash = md5(time());
        $sampleService = function() use ($hash) {
            return $hash;
        };

        $container = new Application();
        $container['sample'] = $sampleService;


        $trait->setContainer($container);

        $this->assertEquals($hash, $trait->get('sample'));
        $this->assertEquals($sampleService, $trait->raw('sample'));
    }
}