<?php
/**
 * Created by PhpStorm.
 * User: Swann
 * Date: 03/12/2018
 * Time: 18:00
 */

interface ISession {

    public function __construct();

    public function setTimeout($timeout);

    public function getSessionData();
    public function setSessionData($sessionData);

    public function initiateSession();
    public function destroySession();

}