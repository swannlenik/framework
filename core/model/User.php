<?php
/**
 * Created by PhpStorm.
 * User: Swann
 * Date: 20/12/2018
 * Time: 10:49
 */

namespace Core\Model;

class User
{
    /**
     * @var string nom de l'utilisateur
     */
    private $userName;
    /**
     * @var string Accès de l'utilisateur
     */
    private $access;

    /**
     * User constructor.
     * @param string $userName
     * @param string $access
     */
    public function __construct(string $userName, $access = "") {
        $this->userName = $userName;
        $this->access = $access;

    }

    /**
     * @return string
     */
    public function getUserName(): string
    {
        return $this->userName;
    }

    /**
     * @param string $userName
     */
    public function setUserName(string $userName): void
    {
        $this->userName = $userName;
    }

    /**
     * @return string
     */
    public function getAccess(): string
    {
        return $this->access;
    }

    /**
     * @param string $access
     */
    public function setAccess(string $access): void
    {
        $this->access = $access;
    }


}