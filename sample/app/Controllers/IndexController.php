<?php
namespace app\Controllers;

use Orlex\Annotation\Route;
use Orlex\Annotation\Before;
use Orlex\Annotation\After;
use app\Annotation\Test;

use Symfony\Component\HttpFoundation\Response;

/**
 * @Route(path="/")
 */
class IndexController {
    protected $foo = [];

    public function beforeIndex() {
        $this->foo[] = 'before';
        echo "<pre>In " . __METHOD__ . "...</pre>";
    }

    /**
     * @Route(path="/",methods={"GET"})
     * @Before("beforeIndex")
     * @After("afterIndex")
     * @Test
     */
    public function indexAction() {
        $this->foo[] = 'during';
        echo "<pre>In " . __METHOD__ . "...</pre>";

        return new Response('<strong>Response from ' . __METHOD__ . '</strong>');
    }

    public function afterIndex() {
        $this->foo[] = 'after';
        echo "<pre>In " . __METHOD__ . "...</pre>";

        echo "<pre>Controller State: " . var_export($this->foo, true) . "</pre>";

        return new Response('<strong>Response from ' . __METHOD__ . '</strong>');
    }

    /**
     * @Route(path="/page/{id}")
     */
    public function pageAction($id) {
        echo "<pre>ID: $id</pre>";

        return new Response('<strong>Response from ' . __METHOD__ . '</strong>');
    }
}