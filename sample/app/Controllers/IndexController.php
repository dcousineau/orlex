<?php
namespace app\Controllers;

use Orlex\Annotation\Route;
use Orlex\Annotation\Before;
use Orlex\Annotation\After;


/**
 * @Route(path="/")
 */
class IndexController {
    public function beforeIndex() {
        var_dump('Before');
    }

    /**
     * @Route(path="/")
     * @Before("beforeIndex")
     * @After("afterIndex")
     */
    public function indexAction() {
        var_dump('here');
    }

    public function afterIndex() {
        var_dump('After');
    }

    /**
     * @Route(path="/page/{id}")
     */
    public function pageAction($id) {
        var_dump($id);die();
    }
}