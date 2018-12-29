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
    public static function setDependancies(bool $minified = false) {
        $dependancies = [];
        $config = new Config();
        $modules = explode(",", $config->getConfigurationData("modules"));
        $npmDir = LIBRARY_DIR . "/node_modules/";
        $npmHttpDir = $config->getBaseUrl(false);
        if(is_dir($npmDir)) {
            foreach($modules as $module) {
                $mod = explode(".", $module);
                if(count($mod) !== 2) {
                    continue;
                }
                $extension = $minified ? "min." . $mod[1] : $mod[1];

                $result = [];
                self::getDependancyFile($mod[0], $extension, $npmDir . $mod[0], $result);
                self::treatDependancyList($result);
                if(count($result) > 0) {
                    $dependancies[$mod[1]][] = $npmHttpDir . $result[0];
                }
            }
        }
        return $dependancies;
    }

    /**
     * Retraite la liste des dépendances trouvées - élimination des doublons, tri des résultats
     * @param array $result
     */
    private static function treatDependancyList(array &$result) {
        $result = array_unique($result);
        sort($result);
    }

    /**
     * Renvoie la liste des fichiers trouvés correspondant au fichier de dépendance souhaité
     * @param string $dependancy
     * @param string $extension
     * @param string $dir
     * @param array $result
     * @return array
     */
    private static function getDependancyFile(string $dependancy, string $extension, string $dir, array &$result) {
        if(!is_dir($dir)) {
            return [];
        }
        $files = scandir($dir);
        foreach ( $files as $file) {
            if(in_array($file, [".",".."])) {
                continue;
            }
            if(is_dir($dir . "/" . $file)) {
                self::getDependancyFile($dependancy, $extension, $dir . "/" . $file, $result);
            } else {
                if($file === $dependancy . "." . $extension) {
                    $result[] = substr($dir . "/" . $file, strpos($dir, "library"));
                }
            }
        }
        return $result;
    }
}