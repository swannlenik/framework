<?php
namespace Framework;

include "config.php";
include "library/Constants.php";

use \Core\Route as Route;

/**
 * Created by PhpStorm.
 * User: S0078595
 * Date: 31/10/2018
 * Time: 15:42
 */

/**
 * @param array $dir
 */

spl_autoload_register(function ($className) {
    if(!class_exists($className))
    @include $className . '.php';
});

$config = \Core\Config::getInstance();
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