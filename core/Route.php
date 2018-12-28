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
    /**
     * @var string
     */
    private $controller = DEFAULT_CONTROLLER;
    /**
     * @var string
     */
    private $method = DEFAULT_METHOD;
    /**
     * @var string
     */
    protected $requestMethod;
    /**
     * @var int
     */
    protected $requestStatus;

    /**
     * Route constructor.
     * @param array $parameters
     */
    public function __construct($parameters = []) {
        $this->requestMethod = $_SERVER['REQUEST_METHOD'];
        $this->requestStatus = http_response_code();
        $this->setRoute($parameters);
    }

    /**
     * Crée la route à suivre
     * @param array $parameters
     */
    private function setRoute(array $parameters) {
        $this->controller = isset($parameters['c']) ? $parameters['c'] : DEFAULT_CONTROLLER;
        $this->method = isset($parameters['m']) ? $parameters['m'] : DEFAULT_METHOD;
    }

    /**
     * @return string
     */
    public function getClassName(): string {
        return "\\Controller\\" . ucfirst(strtolower($this->controller));
    }

    /**
     * @return string
     */
    public function getController(): string {
        return $this->controller;
    }

    /**
     * @return string
     */
    public function getMethod(): string {
        return $this->method;
    }

    /**
     * @return string
     */
    protected function getRequestMethod() {
        return $this->requestMethod;
    }

    /**
     * @return int
     */
    protected function getRequestStatus() {
        return $this->requestStatus;
    }


}