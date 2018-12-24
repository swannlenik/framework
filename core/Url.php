<?php
/**
 * Created by PhpStorm.
 * User: S0078595
 * Date: 02/11/2018
 * Time: 14:58
 */

namespace Core;

/**
 * Class Url
 * @package Core
 */
class Url
{
    /**
     * @var string
     */
    private $url = "";

    /**
     * Url constructor.
     */
    public function __construct()
    {
        $this->url = \Config\Config::getInstance()->getBaseUrl();
    }

    /**
     * @param string $controller
     * @param string $method
     * @param array $arguments
     * @return string
     */
    public function buildUrl(string $controller, string $method = "", array $arguments = []): string {
        $url = $this->url;
        $url .= "?c=" . $controller;
        if ( isset($method) && $method !== "") {
            $url .= "&m=" . $method;
        }
        foreach($arguments as $key => $argument) {
            $url .= "&" . $key . "=" . (string)$argument;
        }
        return $url;
    }

    /**
     * @param $controller
     * @param string $method
     * @param array $parameters
     * @param array $arguments
     * @return string
     */
    public function buildLink($controller, $method = "", $parameters = [], $arguments = []): string {
        $href = "<a href=\"" . $this->buildUrl($controller, $method, $arguments) . "\"";
        if(isset($parameters["target"])) {
            $href .= " target=\"" . $parameters["target"] . "\"";
        }
        if(isset($parameters["class"])) {
            $href .= " class=\"" . $parameters["class"] . "\"";
        }
        $href .= ">";
        if(isset($parameters["label"])) {
            $href .= $parameters["label"];
        }
        $href .= "</a>";
        return $href;
    }
}