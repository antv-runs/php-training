<?php

class Router {
    public static function dispath() {
        $url = $_GET['url'] ?? 'product/index';
        [$controller, $method] = explode('/', $url);

        $controller = ucfirst($controller) . 'Controller';
        require "../app/Controllers/$controller.php";

        $obj = new $controller();
        $obj->$method();
    }
}