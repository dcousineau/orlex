<?php
namespace app\Controllers;

use Orlex\Annotation\Route;
use Orlex\Annotation\Before;
use Orlex\Annotation\After;
use app\Annotation\Test;


/**
 * @Route(path="/")
 */
class IndexController {
    protected $foo = [];

    public function beforeIndex() {
        var_dump('Before');
        $this->foo[] = 'before';
    }

    /**
     * @Route(path="/")
     * @Before("beforeIndex")
     * @After("afterIndex")
     * @Test
     */
    public function indexAction() {
        var_dump('here');
        $this->foo[] = 'during';
    }

    public function afterIndex() {
        var_dump('After');
        $this->foo[] = 'after';

        var_dump($this->foo);
    }

    /**
     * @Route(path="/page/{id}")
     */
    public function pageAction($id) {
        var_dump($id);die();
    }
}