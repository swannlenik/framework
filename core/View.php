<?php
/**
 * Created by PhpStorm.
 * User: S0078595
 * Date: 02/11/2018
 * Time: 14:23
 */

namespace Core;
include "../library/Constants.php";

class View
{

    /**
     * @return array
     */
    public static function constructView(): array
    {
        $config = new Config();
        $layout = $config->getPageLayer();
        $returnLayout = [];

        foreach ($layout as $value) {
            if($value === "content") {
                $returnLayout[$value] = "";
            } else {
                $returnLayout[$value] = Config::getViewDir() . $value . ".php";
            }
        }

        return $returnLayout;
    }

    /**
     * Recherche les dépendances existantes et le chemin des fichiers à inclure en fonction de la configuration donnée
     * @param bool $minified
     * @return array
     */
    public static function setDependancies($minified = false) {
        $dependancies = [];
        $config = new Config();
        $modules = explode(",", $config->getConfigurationData("modules"));
        $npmDir = LIBRARY_DIR . "/node_modules/";
        $npmHttpDir = $config->getBaseUrl(false) . "library/node_modules/";
        if(is_dir($npmDir)) {
            foreach($modules as $module) {
                $mod = explode(".", $module);
                if(count($mod) !== 2) {
                    continue;
                }
                $file = $mod[0] . "/dist/" . $mod[1] . "/" . $mod[0] . ($minified ? ".min" : "") . "." . $mod[1];
                if(!file_exists($npmDir . $file)) {
                    $file =  $mod[0] . "/dist/" . $mod[0] . ($minified ? ".min" : "") . "." . $mod[1];
                    if(!file_exists($npmDir . $file)) {
                        continue;
                    }
                }
                $dependancies[$mod[1]][] = $npmHttpDir . $file;
            }
        }
        return $dependancies;
    }
}