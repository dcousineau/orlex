<?php
namespace Orlex\Controller;

use Symfony\Component\Form\FormBuilderInterface;

trait FormTrait {
    /**
     * @param string $type
     * @param null|array $data
     * @param array $options
     * @param \Symfony\Component\Form\FormBuilderInterface $parent
     * @return \Symfony\Component\Form\FormBuilder
     */
    public function createBuilder($type = 'form', $data = null, array $options = [], FormBuilderInterface $parent = null) {
        /** @var $factory \Symfony\Component\Form\FormFactory */
        $factory = $this->get('form.factory');
        return $factory->createBuilder($type, $data, $options, $parent);
    }
}