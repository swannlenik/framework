<?php
/**
 * Created by PhpStorm.
 * User: Swann
 * Date: 03/12/2018
 * Time: 17:58
 */

namespace Core;

use Core\Model\User;

class Session
{
    /**
     * @var string
     */
    private $sessionID;
    /**
     * @var \DateTime
     */
    private $startTime;
    /**
     * @var User
     */
    private $user;
    /**
     * @var string
     */
    private $cleSession;
    /**
     * @var bool
     */
    public $isActive = false;

    /**
     * Session constructor.
     * @param string $userName
     * @param string $cleSession
     */
    public function __construct($userName = "", $cleSession = "") {
        if($userName !== "" && !$this->sessionIsActive()) {
            $this->cleSession = $cleSession;
            $this->createSession($userName);
        }
    }

    /**
     * @param string $userName
     */
    public function createSession($userName) {
        session_destroy();
        $this->sessionID = session_create_id($this->cleSession);
        $this->startTime = new \DateTime();
        $this->user = new Model\User($userName);
        $this->isActive = true;
        session_start();
    }

    /**
     * Regénérer une session si elle n'est pas expirée
     * @param string $userName
     */
    public function regenerateSession($userName) {
        if($this->sessionIsActive()) {
            $this->createSession($userName);
            $this->registerSession();
        }
    }

    /**
     * Détruit la session courante et réinitialise l'objet
     */
    public function destroySession() {
        session_destroy();
        $this->isActive = false;
        $this->user = null;
        $this->cleSession = null;
        $this->startTime = null;
        $this->sessionID = null;
        $this->registerSession();
    }

    /**
     * @return bool true si la session en cours existe et est active
     */
    public function sessionIsActive() {
        if(!isset($this->startTime) || !isset($this->user) || !isset($this->sessionID) || (isset($this->isActive) && !$this->isActive)) {
            $this->isActive = false;
            return $this->isActive;
        }

        $sessionExpiration = ini_get("session.gc_maxlifetime");
        $interval = \DateInterval::createFromDateString($sessionExpiration . " seconds");
        $date = new \DateTime($this->startTime->format("Y-m-d h:i:s"));
        $date->add($interval);

        $this->isActive = session_status() === PHP_SESSION_ACTIVE && $this->isActive && $date > new \DateTime();
        return $this->isActive;
    }

    /**
     * Sauvegarde une copie de l'objet SESSION dans la variable globale $_SESSION
     */
    public function registerSession() {
        $_SESSION = $this->toArray();
    }

    /**
     * Transforme l'objet SESSION en tableau
     * @return array
     */
    private function toArray() {
        $session = [];
        $session['session_id'] = $this->sessionID;
        $session['start_time'] = $this->startTime;
        $session['user'] = $this->user;
        $session['cle_session'] = $this->cleSession;
        $session['is_active'] = $this->isActive;

        return $session;
    }

    /**
     * Depuis la variable globale $_SESSION, recrée l'objet SESSION s'il y a lieu
     * @return Session|null
     */
    public static function fromSessionArray() {
        if(isset($_SESSION)) {
            $session = new Session();
            if(isset($_SESSION['session_id'])) {
                $session->sessionID = $_SESSION['session_id'];
            }
            if(isset($_SESSION['start_time'])) {
                if(is_string($_SESSION['start_time'])) {
                    $session->startTime = date_create_from_format("Y-m-d h:i:s", $_SESSION['start_time']);
                } elseif ( gettype($_SESSION['start_time']) === "object" && get_class($_SESSION['start_time']) === "DateTime") {
                    $session->startTime = $_SESSION['start_time'];
                } else {
                    $session->startTime = new \DateTime();
                }
            }
            if(isset($_SESSION['user'])) {
                $session->user = $_SESSION['user'];
            }
            if(isset($_SESSION['cle_session'])) {
                $session->cleSession = $_SESSION['cle_session'];
            }
            if(isset($_SESSION['is_active'])) {
                $session->isActive = $_SESSION['is_active'];
            }
            $session->isActive = $session->sessionIsActive();
            return $session;
        } else {
            return null;
        }
    }

    /**
     * @return \DateTime
     */
    public function getStartTime(): \DateTime
    {
        return $this->startTime;
    }

    /**
     * @param \DateTime $startTime
     */
    public function setStartTime(\DateTime $startTime): void
    {
        $this->startTime = $startTime;
    }

    /**
     * @return User
     */
    public function getUser(): User
    {
        return $this->user;
    }

    /**
     * @param User $user
     */
    public function setUser(User $user): void
    {
        $this->user = $user;
    }

    /**
     * @return string
     */
    public function getCleSession(): string
    {
        return $this->cleSession;
    }

    /**
     * @param string $cleSession
     */
    public function setCleSession(string $cleSession): void
    {
        $this->cleSession = $cleSession;
    }


}