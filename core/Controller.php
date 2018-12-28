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
    /**
     * @var string
     */
    private $viewDir = "";
    /**
     * @var string
     */
    protected $controller = "Controller";
    /**
     * @var string
     */
    protected $method = "Controller";

    /**
     * @var Config
     */
    protected $config;
    /**
     * @var Session|null
     */
    protected $session;
    /**
     * @var Query
     */
    protected $db;

    /**
     * @var array
     */
    protected $post = [];
    /**
     * @var array
     */
    protected $get = [];
    /**
     * @var array
     */
    protected $cookie = [];

    /**
     * @var bool
     */
    protected $redirect = false;
    /**
     * @var bool
     */
    protected $api = false;
    /**
     * @var mixed|string
     */
    protected $apiAuthentication = "";
    /**
     * @var mixed|string
     */
    protected $apiAuthField = "";
    /**
     * @var mixed|string
     */
    protected $apiAuthUsername = "";

    /**
     * @var \stdClass
     */
    protected $view;
    /**
     * @var string
     */
    protected $viewApi = "";

    /**
     * @var array
     */
    public $header;
    /**
     * @var array
     */
    public $layout;
    /**
     * @var array
     */
    public $footer;

    /**
     * Controller constructor.
     * @param string $controller
     * @param string $method
     * @param array $parameters
     */
    public function __construct(string $controller, string $method, array $parameters = []) {
        parent::__construct($parameters);
        $this->view = new \stdClass();
        $this->view->url = new Url();
        $this->viewDir = Config::getViewDir();

        $this->config = new Config();
        $this->session = Session::fromSessionArray();
        if($this->session->sessionIsActive()) {
            $this->session->regenerateSession($this->session->getUser()->getUserName());
        }
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

    /**
     * Crée l'objet view qui contient les données à passer à la vue et affiche le layout
     */
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

    /**
     * API et REDIRECTION doivent être activés !
     * En fonction de la méthode souhaitée (TOKEN ou USERNAME + KEY), vérifie l'accès à l'API
     */
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

    /**
     * Vérifie que les variables nécessaires à l'authentification et configurées dans le fichier config.ini sont bien présentes en paramètres de l'appel
     * @return bool
     */
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

    /**
     * Vérifie s'il y a besoin d'une authentification pour accéder à l'API
     * @return bool
     */
    private function needAuthentication() {
        return $this->getApi() && in_array($this->apiAuthentication, [API_AUTHENTICATE_KEY, API_AUTHENTICATE_TOKEN]);
    }

    /**
     * Affiche un message d'erreur et renvoie une erreur 403 si l'accès n'est pas autorisé
     */
    protected function displayAccessInvalid() {
        http_response_code(STATUS_QUERY_UNAUTHORIZED_ACCESS);
        $retour = new \stdClass();
        $retour->message = "Unauthorized access !";
        echo json_encode($retour);
        exit();
    }

    /**
     * Crée les variables à passer à la vue
     */
    private function setViewVariables() {
        if(isset($this->view)) {
            foreach (get_object_vars($this->view) as $variable => $value) {
                $this->$variable = $value;
            }
        }
        $this->header['dependancies'] = View::setDependancies();
    }

    /**
     * Renvoie le nom de la classe appelante
     * @return string
     */
    public function getClassName(): string {
        return "\\Controller\\" . ucfirst(strtolower($this->controller));
    }

    /**
     * Renvoie le libellé court de la classe appelante
     * @return string
     */
    public function getClassShortName(): string {
        return array_pop(explode("\\", __CLASS__));
    }

    /**
     * Renvoie la méthode utilisée pour l'appel API (GET|POST|PUT|DELETE)
     * @return string
     */
    public function getRequestMethod() {
        return parent::getRequestMethod();
    }

    /**
     * Renvoie le statut de la requête API
     * @return int
     */
    public function getRequestStatus() {
        return parent::getRequestStatus();
    }

    /**
     * La redirection vers la fonction {function_name}_{method} est activée ou non
     * @return bool
     */
    public function getRedirect() {
        return $this->redirect;
    }

    /**
     * L'appel à l'API est activé ou non
     * @return bool
     */
    public function getApi() {
        return $this->api;
    }

    /**
     * @return string
     */
    public function getViewApi() {
        return $this->viewApi;
    }

    /**
     * Redirige vers la page concernée
     * @param $controller
     * @param $method
     */
    public function redirect($controller, $method) {
        $url = new Url();
        $get = $this->get;
        if(isset($get['c'])) {
            unset($get['c']);
        }
        if(isset($get['m'])) {
            unset($get['m']);
        }
        var_dump($controller);
        var_dump($method);
        var_dump($get);
        $location = $url->buildUrl($controller, $method, $this->get);
        header("location: $location");
    }
}