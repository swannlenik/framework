<?php
/**
 * Created by PhpStorm.
 * User: S0078595
 * Date: 02/11/2018
 * Time: 10:13
 */

namespace Controller;

use \Core\Controller as CoreController;
use Core\Session;

class Index extends CoreController
{
    public function __construct(string $controller, string $method, array $parameters = []) {
        parent::__construct($controller, $method, $parameters);
    }

    public function index() {
        $this->view->value = "index - index";
        $this->view->name = "Swann";
    }

    public function index_post() {
        $this->index();
    }

    public function view() {
        $this->view->value = "index - view";

        /*var_dump($this->view->club = ["id_club" => 0, "name" => "BCMS", "codep" => 67]);
        var_dump($this->view->clubAdded = $this->db->insert($this->view->club, "club"));
        var_dump($this->view->clubRemoved = $this->db->delete("club", "id_club", 33));

        var_dump($idNewClub = $this->view->clubAdded->getResult());

        var_dump($this->view->club = ["id_club" => 0, "name" => "BC Musau Strasbourg", "codep" => 67]);
        var_dump($this->view->clubUpdated = $this->db->update("club", $this->view->club, "id_club", $idNewClub ? $idNewClub : 28));*/

        $this->view->users = $this->db->run("SELECT * FROM club ORDER BY id_club DESC")->getResult();
    }

    public function view_get() {
        echo "VIEW GET";
        $this->view();
    }

    public function session() {
    }

    public function session_get() {

        /*$this->session = new Session($this->get['username'], "abcd");
        $this->session->registerSession();



        exit();

        //$this->session->regenerateSession($this->session->getUser()->getUserName());
        $this->session = new Session($this->get['username'], "abcd");
        $this->session->registerSession();
        //*/
        echo "<hr />";
        var_dump($this->session);
        var_dump($_SESSION);
        echo "<hr />";
        var_dump($this->session->sessionIsActive());
        echo "<hr />";

        $date = new \DateTime();
        $this->view->date = $date->format("d/m/Y h:i:s");

    }

    public function test() {
        $this->redirect($this->controller, "view");
    }

}