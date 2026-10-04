<?php
require_once(__DIR__ . "/../src/utils/jwt_utils.php");

$http_method = $_SERVER['REQUEST_METHOD'];

switch ($http_method){
    case "POST" :
        $postedData = file_get_contents('php://input');
        $data = json_decode($postedData, true);

        $token = $data["token"];
        $isValid = is_jwt_valid($token, "sportteam");
        if (!$isValid) {
            deliver_response(400, "Le token n'est pas valide");
        } else {
            deliver_response(200, "Le token est valide", true);
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