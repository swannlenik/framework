<?php
/**
 * Created by PhpStorm.
 * User: S0078595
 * Date: 02/11/2018
 * Time: 10:13
 */

namespace Controller;

use \Core\Controller as CoreController;

class Index extends CoreController
{
    public function __construct(string $controller, string $method, array $parameters = []) {
        parent::__construct($controller, $method, $parameters);
    }

    public function index() {
        $this->view->value = "index - index";
        $this->view->name = "Swann";
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
        $this->view();
    }

}