<?php
namespace Service;

use \Config;
/**
 * Created by PhpStorm.
 * User: S0078595
 * Date: 31/10/2018
 * Time: 15:47
 */

class Mock {

    private $content;

    /**
     * @return array
     */
    public function getContent()
    {
        return $this->content;
    }

    /**
     * @param array $content
     */
    public function setContent($content)
    {
        $this->content = $content;
    }

    public function __construct()
    {
        $this->content = explode(":", file_get_contents(Config\Config::getRootDir() . "mock/mock.txt"));
    }


}