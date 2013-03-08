<?php
namespace Orlex\Controller;

trait SessionTrait {
    /**
     * @return \Symfony\Component\HttpFoundation\Session\Session;
     */
    public function session() {
        return $this->get('session');
    }

    /**
     * @param string $type
     * @param string $message
     */
    public function setFlash($type, $message) {
        $this->session()->getFlashBag()->set($type, $message);
    }
}