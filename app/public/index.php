<?php

use App\Controller\ShortenController;

spl_autoload_register(function ($class) {
    $path = str_replace('\\', DIRECTORY_SEPARATOR, $class) . '.php';
    $appRoot = dirname(__DIR__);
    $path = preg_replace('#^App#', $appRoot, $path);

    if (file_exists($path)) {
        require_once $path;
        return true;
    }
    return false;
});

$routes = require_once './../Config/routes.php';

$requestUri = $_SERVER['REQUEST_URI'];
if (preg_match('#/short/(\w+)#', $requestUri, $matches)) {
    $hash = $matches[1];
    $class = ShortenController::class;
    $method = 'redirectLongUrl';
    $obj = new $class();
    $result = $obj->$method($hash);
    echo json_encode($result);
} elseif (isset($routes[$requestUri])) {
    if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_SERVER["HTTP_X_REQUESTED_WITH"]) && strtolower($_SERVER["HTTP_X_REQUESTED_WITH"]) === "xmlhttprequest") {
        list($class, $method) = $routes[$requestUri];
        $obj = new $class();
        $result = $obj->$method();
        echo json_encode($result);
    } else {
        list($class, $method) = $routes[$requestUri];
        $obj = new $class();
        $result = $obj->$method();
        $data = $result['data'];
        extract($data);
        $viewName = $result['view'];
        require_once "./../View/$viewName.phtml";
    }
} else {
    require_once '../View/notFound.html';
}