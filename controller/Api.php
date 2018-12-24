<?php
/**
 * Created by PhpStorm.
 * User: Swann
 * Date: 21/12/2018
 * Time: 15:38
 */

namespace Controller;


class Api extends \Core\Controller
{

    public function __construct(string $controller, string $method, array $parameters = [])
    {
        parent::__construct($controller, $method, $parameters);
    }

    public function club() {
        echo "club SANS REDIRECTION";
    }

    public function club_get() {
        $idClub = $this->get['id_club'];
        //echo "SELECT * FROM club WHERE id_club > " . $idClub;
        $result = $this->db->run("SELECT * FROM club WHERE id_club > " . $idClub);

        $encode = json_encode($result->getResult());

        //var_dump($encode);

        if($encode) {
            http_response_code(STATUS_QUERY_OK);
            echo $encode;
        } else {
            $error = new \stdClass();
            $error->message = json_last_error_msg();
            $error->error = json_last_error();
            http_response_code(STATUS_QUERY_KO);
            echo json_encode($error);
        }
    }

    public function club_post() {
        var_dump($this->get);
        var_dump($this->post);
    }

    public function club_put() {
        $this->club_post();

    }

    public function club_delete() {
        $this->club_post();
    }
}