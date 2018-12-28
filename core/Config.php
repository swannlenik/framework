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
    /**
     *
     */
    const ROOT_DIR = __DIR__;

    /**
     * @var bool
     */
    private static $hasHeader;
    /**
     * @var bool
     */
    private static $hasFooter;

    /**
     * @var array
     */
    private $pageLayer = [];
    /**
     * @var string
     */
    private $baseUrl = "";

    /**
     * @var array
     */
    private $configurationData = [];

    /**
     * @var Config
     */
    private static $instance;

    /**
     * Config constructor.
     */
    public function __construct()
    {
        $configFilePath = ROOT_DIR . "/core/config/config.ini";
        $this->getConfiguration($configFilePath);


        self::$hasFooter = defined("HAS_FOOTER") ? HAS_FOOTER : false;
        self::$hasHeader = defined("HAS_HEADER") ? HAS_HEADER : false;

        $this->pageLayer = ["content"=> "content"];
        $this->baseUrl = 'http://' . $_SERVER['HTTP_HOST'];
    }

    /**
     * Affiche le paramètre au milieu des balises PRE et d'un "var_dump"
     * @param $data
     */
    public static function debugData($data) {
        if(defined("DEBUG") && DEBUG) {
            echo "<pre>";
            var_dump($data);
            echo "</pre>";
        }
    }

    /**
     * @param array $layer
     */
    public function setPageLayer(array $layer) {
        $this->pageLayer = $layer;
        if(!array_key_exists("content", $layer)) {
            $this->pageLayer["content"] = "content";
        }
    }

    /**
     * @return array
     */
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

    /**
     * @param bool $baseUrlWithIndex
     * @return string
     */
    public function getBaseUrl(bool $baseUrlWithIndex = true): string {
        if($baseUrlWithIndex) {
            return $this->baseUrl . $_SERVER["PHP_SELF"];
        } else {
            return $this->baseUrl . "/";
        }
    }

    /**
     * @return string
     */
    public function getLibraryUrl(): string {
        return $this->getBaseUrl(false) . "/library/";
    }

    /**
     * @param bool $baseUrlWithIndex
     * @return Config
     */
    public static function getInstance($baseUrlWithIndex = true) {
        if(is_null(self::$instance)) {
            self::$instance = new Config($baseUrlWithIndex);
        }
        return self::$instance;
    }

    /**
     * @param $configFilePath
     */
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

    /**
     * @param $key
     * @return mixed|string
     */
    public function getConfigurationData($key) {
        if(array_key_exists($key, $this->configurationData)) {
            return $this->configurationData[$key];
        } else {
            return "Key $key does not exists in configuration !";
        }
    }

    /**
     * @return string
     */
    public static function getRootDir(): string
    {
        return dirname(self::ROOT_DIR) . "/";
    }

    /**
     * @return string
     */
    public static function getViewDir(): string
    {
        return self::getRootDir() . "view/";
    }
}