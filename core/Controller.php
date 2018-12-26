<?php
/**
 * Created by PhpStorm.
 * User: S0078595
 * Date: 31/10/2018
 * Time: 16:29
 */

namespace Core;
session_start();

class Controller extends Route
{
    private $viewDir = "";
    protected $controller = "Controller";
    protected $method = "Controller";

    protected $config;
    protected $session;
    protected $db;

    protected $post = [];
    protected $get = [];
    protected $cookie = [];

    protected $redirect = false;
    protected $api = false;
    protected $apiAuthentication = "";
    protected $apiAuthField = "";
    protected $apiAuthUsername = "";

    protected $view;
    protected $viewApi = "";

    public $header;
    public $layout;
    public $footer;

    public function __construct(string $controller, string $method, array $parameters = []) {
        parent::__construct($parameters);
        $this->view = new \stdClass();
        $this->view->url = new Url();
        $this->viewDir = Config::getViewDir();

        $this->config = new Config();
        $this->session = Session::fromSessionArray();
        $this->db = new Query($this->config);

        $this->header['title'] = "Controller : " . $this->controller . " - Vue : " . $this->method;
        $this->footer['copyright'] = "&copy; " . date("Y");
        $this->layout = [];

        $this->controller = $controller ? $controller : (defined("DEFAULT_CONTROLLER") ? DEFAULT_CONTROLLER : "index");
        $this->method = isset($method) ? $method : (defined("DEFAULT_METHOD") ? DEFAULT_METHOD : "index");

        $this->redirect = (bool)$this->config->getConfigurationData("redirect_request");
        $this->api = (bool)$this->config->getConfigurationData("activate_api");
        $this->apiAuthentication = $this->config->getConfigurationData("api_authenticate");
        $this->apiAuthField = $this->config->getConfigurationData("api_authenticate_field");
        $this->apiAuthUsername = $this->config->getConfigurationData("api_authenticate_username");

        if(isset($parameters['GET'])) {
            $this->get = $parameters['GET'];
        } else {
            $this->get = [];
        }
        if(isset($parameters['POST'])) {
            $this->post = $parameters['POST'];
        } else {
            $this->post = [];
        }
        if(isset($parameters['COOKIE'])) {
            $this->cookie = $parameters['COOKIE'];
        } else {
            $this->cookie = [];
        }

        if($this->getApi()) {
            $this->verifyAuthentication();
        }

        $this->viewApi = $method . ($this->getRedirect() ? "_" . strtolower($this->getRequestMethod()) : "");
    }

    public function run() {
        $this->setViewVariables();

        if(!$this->getApi()) {
            $this->layout = array_merge($this->layout, View::constructView());

            if (!method_exists($this, $this->method)) {
                $this->layout["content"] = $this->viewDir . $this->controller . "/index.php";
            } else {
                $this->layout["content"] = $this->viewDir . $this->controller . "/" . $this->method . ".php";
            }
            include $this->viewDir . "layout.php";
        }
    }

    private function verifyAuthentication() {
        if($this->variablesGranted() && $this->needAuthentication()) {
            if($this->apiAuthentication === API_AUTHENTICATE_TOKEN) {
                $grantAccess = $this->db->grantAccessToken($this->get[$this->apiAuthField]);
            } else {
                $grantAccess = $this->db->grantAccessUsernameKey($this->get[$this->apiAuthUsername], $this->get[strtolower($this->apiAuthField)]);
            }

            if($grantAccess->getStatus() !== STATUS_QUERY_OK || $grantAccess->getResult() !== 1) {
                $this->displayAccessInvalid();
            }
        } else {
            if ( !$this->variablesGranted() || $this->apiAuthentication !== API_AUTHENTICATE_NONE) {
                $this->displayAccessInvalid();
            }
        }
    }

    private function variablesGranted() {
        if ( $this->apiAuthentication === API_AUTHENTICATE_NONE) {
            return true;
        } elseif($this->apiAuthentication === API_AUTHENTICATE_TOKEN) {
            return isset($this->get[$this->apiAuthField]) && strlen($this->get[$this->apiAuthField]) > 0;
        } elseif($this->apiAuthentication === API_AUTHENTICATE_KEY) {
            return isset($this->get[$this->apiAuthField]) && strlen($this->get[$this->apiAuthField]) > 0 && isset($this->get[$this->apiAuthUsername]) && strlen($this->get[$this->apiAuthUsername]) > 0;
        }
        return false;
    }

    private function needAuthentication() {
        return $this->getApi() && in_array($this->apiAuthentication, [API_AUTHENTICATE_KEY, API_AUTHENTICATE_TOKEN]);
    }

    private function displayAccessInvalid() {
        http_response_code(STATUS_QUERY_UNAUTHORIZED_ACCESS);
        exit("Unauthorized access !");
    }

    private function setViewVariables() {
        if(isset($this->view)) {
            foreach (get_object_vars($this->view) as $variable => $value) {
                $this->$variable = $value;
            }
        }
        $this->header['dependancies'] = View::setDependancies();
    }

    public function getClassName(): string {
        return "\\Controller\\" . ucfirst(strtolower($this->controller));
    }

    public function getClassShortName(): string {
        return array_pop(explode("\\", __CLASS__));
    }

    public function getRequestMethod() {
        return parent::getRequestMethod();
    }

    public function getRequestStatus() {
        return parent::getRequestStatus();
    }

    public function getRedirect() {
        return $this->redirect;
    }

    public function getApi() {
        return $this->api;
    }

    public function getViewApi() {
        return $this->viewApi;
    }
}