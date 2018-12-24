<?php
/**
 * Created by PhpStorm.
 * User: S0078595
 * Date: 02/11/2018
 * Time: 10:44
 */

namespace Core;


class Route
{
    private $controller = DEFAULT_CONTROLLER;
    private $method = DEFAULT_METHOD;
    protected $requestMethod;
    protected $requestStatus;

    public function __construct($parameters = []) {
        $this->requestMethod = $_SERVER['REQUEST_METHOD'];
        $this->requestStatus = http_response_code();
        $this->setRoute($parameters);
    }

    private function setRoute(array $parameters) {
        $this->controller = isset($parameters['c']) ? $parameters['c'] : DEFAULT_CONTROLLER;
        $this->method = isset($parameters['m']) ? $parameters['m'] : DEFAULT_METHOD;
    }

    public function getClassName(): string {
        return "\\Controller\\" . ucfirst(strtolower($this->controller));
    }

    public function getController(): string {
        return $this->controller;
    }

    public function getMethod(): string {
        return $this->method;
    }

    protected function getRequestMethod() {
        return $this->requestMethod;
    }

    protected function getRequestStatus() {
        return $this->requestStatus;
    }


}