<?php
/**
 * Created by PhpStorm.
 * User: Swann
 * Date: 08/12/2018
 * Time: 17:17
 */

namespace Core;

class Query
{
    /**
     * @var string
     */
    private $request;
    /**
     * @var Model\Query_result
     */
    private $result;

    /**
     * @var string
     */
    private $host;
    /**
     * @var string
     */
    private $user;
    /**
     * @var string
     */
    private $password;
    /**
     * @var string
     */
    private $dbName;
    /**
     * @var \mysqli
     */
    private $db;
    /**
     * @var int
     */
    private $typeResult;
    /**
     * @var string
     */
    private $apiAuthenticateUsername;
    /**
     * @var string
     */
    private $apiAuthenticateField;
    /**
     * @var string
     */
    private $apiAuthenticateTable;

    /**
     * Query constructor. Initialise l'objet en lien avec la base de données
     * @param Config $config
     */
    public function __construct(\Core\Config $config) {
        $this->host = $config->getConfigurationData("db_host");
        $this->user = $config->getConfigurationData("db_user");
        $this->password = $config->getConfigurationData("db_password");
        $this->dbName = $config->getConfigurationData("db_name");
        $this->typeResult = $config->getConfigurationData("db_result_type") === "assoc" ? MYSQLI_ASSOC : MYSQLI_NUM;

        $this->apiAuthenticateTable = $config->getConfigurationData("api_authenticate_table");
        $this->apiAuthenticateField = $config->getConfigurationData("api_authenticate_field");
        $this->apiAuthenticateUsername = $config->getConfigurationData("api_authenticate_username");

        $this->db = new \mysqli($this->host, $this->user, $this->password, $this->dbName);
        if(!$this->db) {
            exit("Incorrect DB credentials");
        } else {
            $this->db->set_charset("utf8");
        }

        $this->request = "";
        $this->result = new \Core\Model\Query_result();
    }

    /**
     * Définit le type de requête passé en paramètre
     * @return string
     */
    private function defineTypeQuery() {
        if(strpos($this->request, "SELECT") !== false) {
            return SELECT;
        } elseif(strpos($this->request, "INSERT") !== false) {
            return INSERT;
        } elseif(strpos($this->request, "UPDATE") !== false) {
            return UPDATE;
        } elseif(strpos($this->request, "DELETE") !== false) {
            return DELETE;
        } else {
            return "";
        }
    }

    /**
     * Popule un résultat avec le statement passé en paramètre
     * @param $result
     * @param string $type
     */
    private function affectResult($result, string $type) {
        $this->result->setQuery($this->request);

        switch($type) {
            case SELECT:
                $this->result->setResultCount($result->num_rows);
                $this->result->setResult($result->fetch_all($this->typeResult));
                $this->result->setStatus(STATUS_QUERY_OK);
                break;
            case INSERT:
                $this->result->setStatus(STATUS_QUERY_CREATED);
                break;
            case UPDATE:
                $this->result->setStatus(STATUS_QUERY_UPDATED);
                break;
            case DELETE:
            $this->result->setResultCount($this->db->affected_rows);
            $this->result->setResult(null);
                $this->result->setStatus(STATUS_QUERY_DELETED);
            break;
            default:
                $this->result->setStatus(STATUS_QUERY_KO);
                break;
        }
        if($this->result->getResultCount() <= 0) {
            $this->result->setStatus(STATUS_QUERY_KO);
        }
    }

    /**
     * Crée un résultat null après retour d'une ERREUR par exemple
     */
    private function affectNullResult() {
        $this->result->setQuery(NO_QUERY);
        $this->result->setResultCount(-1);
        $this->result->setResult([]);
        $this->result->setStatus(STATUS_QUERY_KO);
    }

    /**
     * Lancer une requête textuelle
     * @param string $query
     * @return Model\Query_result
     */
    public function run(string $query) {
        if(strlen($query) < 7) {
            $this->affectNullResult();
            $this->result->setMessage(QUERY_SELECT_TOO_SHORT);
            $this->result->setQuery($query);
            return $this->result;
        }

        $this->request = $this->db->real_escape_string($query);
        $result = $this->db->query($this->request);

        $this->affectResult($result, $this->defineTypeQuery());
        return $this->result;
    }

    /**
     * Met à jour les paramètres dans le Query Statement
     * @param $stmt
     * @param $params
     */
    private function bindParameters(&$stmt, &$params) {
        $args = [];
        $args[] = implode("", array_values($params));

        foreach($params as $name => $type) {
            $args[] = &$params[$name];
            $params[$name] = null;
        }

        call_user_func_array(array(&$stmt, 'bind_param'), $args);
    }

    /**
     * Ajoute une ligne dans une table
     * @param array $item Données de la ligne à rajouter
     * @param string $table Nom de la table
     * @return Model\Query_result Résultat de la requête
     */
    public function insert(array $item, string $table) {
        if(!isset($item) || count($item) === 0) {
            $this->affectNullResult();
            $this->result->setMessage(QUERY_NO_PARAMETERS_UPDATE);
            return $this->result;
        }

        $keys = implode(", ", array_keys($item));

        $nbItem = [];
        $params = [];
        foreach($item as $key => $it) {
            $nbItem[] = "?";
            if(is_int($it)) {
                $params[$key] = "i";
            } elseif(is_float($it)) {
                $params[$key] =  "d";
            } else {
                $params[$key] =  "s";
            }
        }

        $request = "INSERT INTO $table ($keys) VALUES (" . implode(", ", $nbItem) . ")";
        if($stmt = $this->db->prepare($request)) {
            $this->bindParameters($stmt, $params);
            foreach ($item as $key => $it) {
                $params[$key] = $it;
            }
            if ($stmt->execute()) {
                $this->result->setStatus(STATUS_QUERY_CREATED);
                $this->result->setResult($stmt->insert_id);
                $this->result->setResultCount(1);
                $this->result->setMessage(QUERY_EXECUTE_OK);
            } else {
                $this->affectNullResult();
                $this->result->setMessage(QUERY_EXECUTE_KO . "<br/>" . $stmt->error);
            }
            $stmt->close();
        } else {
            $this->affectNullResult();
            $this->result->setMessage(QUERY_PREPARE_KO . "<br/>" . $stmt->error);
        }
        $this->result->setQuery($request);
        $this->result->setParametres($item);

        return $this->result;
    }

    /**
     * Supprime une ligne d'une table passée en paramètre
     * @param string $table Nom de la table
     * @param string $idName Nom de l'identifiant
     * @param int $id Valeur de l'identifiant à supprimer
     * @return Model\Query_result Résultat de la requête
     */
    public function delete(string $table, string $idName, int $id) {
        if(!isset($id) || !is_int($id)) {
            $this->affectNullResult();
            $this->result->setMessage(QUERY_UPDATE_DELETE_ID_EMPTY_UPDATE);
            return $this->result;
        }

        $request = sprintf("DELETE FROM %s WHERE %s = ?", $table, $idName);
        if($stmt = $this->db->prepare($request)) {
            $stmt->bind_param("i", $id);
            if($stmt->execute()) {
                $this->result->setStatus(STATUS_QUERY_DELETED);
                $this->result->setResult($stmt->affected_rows);
                $this->result->setResultCount(1);
                $this->result->setMessage(QUERY_EXECUTE_OK);
            } else {
                $this->affectNullResult();
                $this->result->setMessage(QUERY_EXECUTE_KO . "<br/>" . $stmt->error);

            }
            $stmt->close();
        } else {
            $this->affectNullResult();
            $this->result->setMessage(QUERY_PREPARE_KO . "<br/>" . $stmt->error);
        }
        $this->result->setQuery($request);
        $this->result->setParametres([$idName => $id]);

        return $this->result;
    }

    /**
     * Met à jour une ligne d'une table passée en paramètre. Si l'identifiant est passé dans la liste des données, il ne sera pas pris en compte
     * @param string $table Nom de la table
     * @param array $item Données de la ligne à modifier
     * @param string $idName Nom de l'ID dans la table
     * @param int $id Valeur de l'ID dans la table
     * @return Model\Query_result Résultat de la requête
     */
    public function update(string $table, array $item, string $idName, int $id) {
        if(!isset($id) || !is_int($id)) {
            $this->affectNullResult();
            $this->result->setMessage(QUERY_UPDATE_DELETE_ID_EMPTY_UPDATE);
            return $this->result;
        }

        if(count($item) > 0 ) {

            $keys = implode(", ", array_keys($item));

            $nbItem = [];
            $params = [];
            foreach ($item as $key => $it) {
                if ($key == $idName) {
                    echo "OUI";
                    continue;
                }

                $nbItem[] = "?";
                if (is_int($it)) {
                    $params[$key] = "i";
                } elseif (is_float($it)) {
                    $params[$key] = "d";
                } else {
                    $params[$key] = "s";
                }
            }

            $request = "UPDATE $table SET " . implode(" = ?, ", array_keys($params)) . " = ? WHERE $idName = ?";
            $params[$idName] = "i";
            if ($stmt = $this->db->prepare($request)) {
                $this->bindParameters($stmt, $params);
                foreach ($item as $key => $it) {
                    $params[$key] = $it;
                }
                $params[$idName] = $id;
                if ($stmt->execute()) {
                    $this->result->setStatus(STATUS_QUERY_UPDATED);
                    $this->result->setResult($stmt->affected_rows);
                    $this->result->setResultCount(1);
                    $this->result->setMessage(QUERY_EXECUTE_OK);
                } else {
                    $this->affectNullResult();
                    $this->result->setMessage(QUERY_EXECUTE_KO . "<br/>" . $stmt->error);
                }
                $stmt->close();
            } else {
                $this->affectNullResult();
                $this->result->setMessage(QUERY_PREPARE_KO . "<br/>" . $stmt->error);
            }
            $this->result->setQuery($request);
            $this->result->setParametres($item);

        } else {
            $this->affectNullResult();
            $this->result->setMessage(QUERY_NO_PARAMETERS_UPDATE);
        }
        return $this->result;
    }

    /**
     * Renvoie le résultat de demande d'accès à l'API par TOKEN
     * @param string $token
     * @return Model\Query_result Résultat de la requête d'authentification
     */
    public function grantAccessToken(&$token = "") {
        $query = "SELECT `" . $this->apiAuthenticateField . "` FROM `" . $this->apiAuthenticateTable . "` WHERE `" . $this->apiAuthenticateField . "` = ?";
        if($stmt = $this->db->prepare($query)) {
            $stmt->bind_param("s", $token);
            if ($stmt->execute()) {
                $stmt->store_result();
                $this->result->setStatus(STATUS_QUERY_OK);
                $this->result->setResult($stmt->num_rows);
                $this->result->setResultCount($stmt->num_rows);
                $this->result->setMessage(QUERY_EXECUTE_OK);
            } else {
                $this->affectNullResult();
                $this->result->setMessage(QUERY_EXECUTE_KO . "<br/>" . $stmt->error);
            }
            $stmt->close();
        } else {
            $this->affectNullResult();
            $this->result->setMessage(QUERY_EXECUTE_KO . "<br/>" . $stmt->error);
        }
        return $this->result;
    }

    /**
     * Renvoie le résultat de demande d'accès à l'API par Username / Key
     * @param string $username
     * @param string $key
     * @return Model\Query_result Résultat de la requête d'authentification
     */
    public function grantAccessUsernameKey($username = "", $key = "") {
        $query = "SELECT `" . $this->apiAuthenticateField . "` FROM `" . $this->apiAuthenticateTable . "` WHERE `" . $this->apiAuthenticateField . "` = ? AND `" . $this->apiAuthenticateUsername . "` = ?";
        if($stmt = $this->db->prepare($query)) {
            $stmt->bind_param("ss", $key, $username);
            if ($stmt->execute()) {
                $stmt->store_result();
                $this->result->setStatus(STATUS_QUERY_OK);
                $this->result->setResult($stmt->num_rows);
                $this->result->setResultCount($stmt->num_rows);
                $this->result->setMessage(QUERY_EXECUTE_OK);
            } else {
                $this->affectNullResult();
                $this->result->setMessage(QUERY_EXECUTE_KO . "<br/>" . $stmt->error);
            }
            $stmt->close();
        }
        return $this->result;
    }
}