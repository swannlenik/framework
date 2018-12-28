<?php
namespace Framework;

include "library/Constants.php";

use \Core\Route as Route;
use \Core\Config as Config;

spl_autoload_register(function ($className) {
    if(!class_exists($className))
    @include $className . '.php';
});

Config::getInstance();
$route = new Route(!empty($_GET) ? $_GET : []);

$controllerClassName = $route->getClassName();
if(class_exists($controllerClassName)) {
    $controller = new $controllerClassName($route->getController(), $route->getMethod(), ["GET" => $_GET, "POST" => $_POST]);

    if($controller->getRedirect() && method_exists($controller, $controller->getViewApi()) ) {
        $controller->{$controller->getViewApi()}();
    } else {
        if (method_exists($controller, $route->getMethod())) {
            $controller->{$route->getMethod()}();
        } else {
            $controller->index();
        }
    }

    $controller->run();
} else {
    include \Config\Config::getViewDir() . "error_class_exists.php";
}