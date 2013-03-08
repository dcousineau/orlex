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
        return new Response('Response from ' . __METHOD__);
    }

    /**
     * @Route(path="/hello")
     */
    public function helloAction() {
        return new Response('Response from ' . __METHOD__);
    }
}