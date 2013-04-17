<?php
namespace app\Controllers;

use Orlex\Annotation\Route;
use Symfony\Component\HttpFoundation\Response;

/**
 * @Route(path="/test")
 */
class TestController {

    /**
     * @Route(path="/")
     */
    public function indexAction() {
        return new Response('<strong>Response from ' . __METHOD__ . '</strong>');
    }

    /**
     * @Route(path="/hello")
     */
    public function helloAction() {
        return new Response('<strong>Response from ' . __METHOD__ . '</strong>');
    }
}