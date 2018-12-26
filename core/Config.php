<?php
namespace Core;
include "../library/Constants.php";
/**
 * Created by PhpStorm.
 * User: S0078595
 * Date: 31/10/2018
 * Time: 15:49
 */

class Config {
    const ROOT_DIR = __DIR__;

    private static $hasHeader;
    private static $hasFooter;

    private $pageLayer = [];
    private $baseUrl = "";

    private $configurationData = [];

    private static $instance;

    public function __construct()
    {
        $configFilePath = ROOT_DIR . "/core/config/config.ini";
        $this->getConfiguration($configFilePath);


        self::$hasFooter = defined("HAS_FOOTER") ? HAS_FOOTER : false;
        self::$hasHeader = defined("HAS_HEADER") ? HAS_HEADER : false;

        $this->pageLayer = ["content"=> "content"];
        $this->baseUrl = 'http://' . $_SERVER['HTTP_HOST'];
    }

    public static function debugData($data) {
        if(defined("DEBUG") && DEBUG) {
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
        if(self::$hasFooter) {
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

    public function getLibraryUrl(): string {
        return $this->getBaseUrl(false) . "/library/";
    }

    public static function getInstance($baseUrlWithIndex = true) {
        if(is_null(self::$instance)) {
            self::$instance = new Config($baseUrlWithIndex);
        }
        return self::$instance;
    }

    private function getConfiguration($configFilePath) {
        $configFileContent = explode("\r\n", file_get_contents($configFilePath));
        $this->configurationData = [];

        foreach($configFileContent as $line) {
            if(substr($line, 0, 1) == ";" || $line == "") {
                continue;
            }
            $value = explode("=", $line);
            if(count($value) === 2) {
                if($value[0] === strtoupper($value[0])) {
                    @define($value[0], $value[1]);
                } else {
                    $this->configurationData[$value[0]] = $value[1];
                }
            }
        }
    }

    public function getConfigurationData($key) {
        if(array_key_exists($key, $this->configurationData)) {
            return $this->configurationData[$key];
        } else {
            return "Key $key does not exists in configuration !";
        }
    }

    public static function getRootDir(): string
    {
        return dirname(self::ROOT_DIR) . "/";
    }

    public static function getViewDir(): string
    {
        return self::getRootDir() . "view/";
    }
}