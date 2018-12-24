<?php
/**
 * Created by PhpStorm.
 * User: Swann
 * Date: 03/12/2018
 * Time: 17:58
 */

namespace Core;

class Session
{
    private $sessionID;
    private $startTime;
    private $user;

    public function __construct($userName = "", $cleSession = "") {
        if($userName !== "" && !$this->sessionIsActive()) {
            $this->createSession($userName, $cleSession);
        }
    }

    public function createSession($userName, $cleSession = "") {
        $this->sessionID = session_create_id($cleSession);
        $date = new \DateTime();
        $this->startTime = $date->format("YYYY-MM-DD hh:ii:ss");
        $this->user = new \Core\Model\User($userName);
        session_destroy();
        session_start();
    }

    public function destroySession() {
        session_destroy();
    }

    public function sessionIsActive() {
        return session_status() === PHP_SESSION_ACTIVE;
    }

    public function registerSession() {
        $_SESSION = $this->toArray();
    }

    private function toArray() {
        $session = [];
        $session['session_id'] = $this->sessionID;
        $session['start_time'] = $this->startTime;
        $session['user'] = $this->user;

        return $session;
    }
}