<?php
namespace app\Controllers;

use Symfony\Component\Routing\Annotation\Route;

/**
 * @Route(path="/")
 */
class IndexController {
    /**
     * @Route(path="/")
     */
    public function indexAction() {
        var_dump('here');die();
    }

    /**
     * @Route(path="/page/{id}")
     */
    public function pageAction($id) {
        var_dump($id);die();
    }
}