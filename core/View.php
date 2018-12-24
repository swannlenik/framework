<?php
/**
 * Created by PhpStorm.
 * User: S0078595
 * Date: 02/11/2018
 * Time: 14:23
 */

namespace Core;

class View
{

    public static function constructView(): array
    {
        $config = new \Config\Config();
        $layout = $config->getPageLayer();
        $returnLayout = [];

        foreach ($layout as $value) {
            if($value === "content") {
                $returnLayout[$value] = "";
            } else {
                $returnLayout[$value] = \Config\Config::getViewDir() . $value . ".php";
            }
        }

        return $returnLayout;
    }
}