<?php
namespace Config;
/**
 * Created by PhpStorm.
 * User: S0078595
 * Date: 31/10/2018
 * Time: 15:49
 */

class Config {

    const ROOT_DIR = __DIR__;

    private static $hasHeader = HAS_HEADER;
    private static $hasFooter = HAS_FOOTER;
    private $pageLayer = [];
    private $baseUrl = "";

    private static $instance;

    public function __construct()
    {
        $this->pageLayer = ["content"=> "content"];
        $this->baseUrl = 'http://' . $_SERVER['HTTP_HOST'];
    }

    public static function getRootDir(): string
    {
        return self::ROOT_DIR . "/";
    }

    public static function getViewDir(): string
    {
        return self::getRootDir() . "view/";
    }

    public static function debugData($data) {
        if(DEBUG) {
            echo "<pre>";
            var_dump($data);
            echo "</pre>";
        }
    }

    public function setPageLayer(array $layer) {
        $this->pageLayer = $layer;
        if(!array_key_exists("content", $layer)) {
            $this->pageLayer["content"] = "content";
        }
    }

    public function getPageLayer() {
        $pageLayer = [];
        if(self::$hasHeader) {
            $pageLayer["header"] = "header";
        }
        foreach($this->pageLayer as $layer) {
            $pageLayer[$layer] = $layer;
        }
        if(self::$hasHeader) {
            $pageLayer["footer"] = "footer";
        }

        return $pageLayer;
    }

    public function getBaseUrl(bool $baseUrlWithIndex = true): string {
        if($baseUrlWithIndex) {
            return $this->baseUrl . $_SERVER["PHP_SELF"];
        } else {
            return $this->baseUrl . "/";
        }
    }

    public static function getInstance($baseUrlWithIndex = true) {
        if(is_null(self::$instance)) {
            self::$instance = new Config($baseUrlWithIndex);
        }
        return self::$instance;
    }
}