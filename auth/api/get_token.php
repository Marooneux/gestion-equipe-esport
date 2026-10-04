<?php
require_once(__DIR__ . "/../src/modele/verifAuth.php");
require_once(__DIR__ . "/../src/modele/DatabaseHandler.php");
$linkpdo = DatabaseHandler::getInstance()->pdo();
$verifAuth = new verifAuth($linkpdo);


$http_method = $_SERVER['REQUEST_METHOD'];

switch ($http_method){
    case "POST" :
        $postedData = file_get_contents('php://input');
        $data = json_decode($postedData, true);

        $login = $data["login"] ?? null;
        $password = $data["password"] ?? null;

        if (empty($login) || empty($password)) {
            deliver_response(400, "Le login et le mot de passe sont obligatoires");
            break;
        }

        $utilisateurValide = $verifAuth->verifAuth($login, $password);
        if ($utilisateurValide == false) {
            deliver_response(403, "L'utilisateur et/ou le mot de passe sont incorrectes");
        } else {
            deliver_response(200, "Le token a été créer", $utilisateurValide);
        }
        break;



    default:
        deliver_response(400, "Il faut utiliser la méthode POST");
}


    function deliver_response($status_code, $status_message, $data=null){
        http_response_code($status_code);
        header("HTTP/1.1 $status_code $status_message");
        header("Content-Type: application/json; charset=utf-8");
        
        header("Access-Control-Allow-Origin: *");
        
        $response['status_code'] = $status_code;
        $response['status_message'] = $status_message;
        $response['data'] = $data;

        $json_response = json_encode($response);
        if($json_response === false)
            die('json encode ERROR : '.json_last_error_msg());
        echo $json_response;
    } 
?>