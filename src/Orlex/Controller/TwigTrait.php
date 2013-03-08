<?php
namespace Orlex\Controller;

trait TwigTrait {
    /**
     * @param string $template Template file (relative to path)
     * @param array $vars Variables to be passed to template
     * @return string Rendered template
     */
    public function render($template, array $vars = []) {
        /** @var $twig Twig_ */
        return $this->get('twig')->render($template, $vars);
    }
}