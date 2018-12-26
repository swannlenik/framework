<?php
// DEFINITION DES REPERTOIRES
define('ROOT_DIR', dirname(__DIR__));
define('LIBRARY_DIR', ROOT_DIR . "/library");
define('THIRD_PARTY_DIR', LIBRARY_DIR . "/third_party");

// DEFINITION DES TYPES DE REQUETES HTTP
define('GET_REQUEST', 'GET');
define('POST_REQUEST', 'POST');
define('PUT_REQUEST', 'PUT');
define('DELETE_REQUEST', 'DELETE');

// DEFINITION DES CONSTANTES POUR REQUETES SQL
define('SELECT', 'SELECT');
define('INSERT', 'INSERT');
define('UPDATE', 'UPDATE');
define('DELETE', 'DELETE');
define('NO_QUERY', 'NO_QUERY');
define('STATUS_QUERY_OK', 200);
define('STATUS_QUERY_CREATED', 201);
define('STATUS_QUERY_UPDATED', 202);
define('STATUS_QUERY_DELETED', 203);
define('STATUS_QUERY_KO', 500);
define('STATUS_QUERY_UNAUTHORIZED_ACCESS', 403);

define('QUERY_PREPARE_KO', 'Erreur lors de la préparation de la requête. Vérifiez les paramètres fournis.');
define('QUERY_EXECUTE_KO', 'Erreur lors de l\'éxécution de la requête. Vérifiez les paramètres fournis.');
define('QUERY_EXECUTE_OK', 'Exécution de la requête OK');
define('QUERY_NO_PARAMETERS_UPDATE', 'Aucun paramètre fourni pour la mise à jour.');
define('QUERY_UPDATE_DELETE_ID_EMPTY_UPDATE', 'Identifiant de l\'enregistrement non fourni.');
define('QUERY_SELECT_TOO_SHORT', 'La requête fournie est incorrecte');

define('API_AUTHENTICATE_NONE', 'NONE');
define('API_AUTHENTICATE_KEY', 'KEY');
define('API_AUTHENTICATE_TOKEN', 'TOKEN');

/**
 * Created by PhpStorm.
 * User: S0078595
 * Date: 02/11/2018
 * Time: 11:17
 */