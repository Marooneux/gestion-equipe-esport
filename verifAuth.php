<?php
require_once("jwt_utils.php");

class verifAuth {

    function verifAuth($linkpdo, $login, $password) {
        if (!empty($login) && !empty($password)) {
            $sql = "SELECT * FROM user WHERE login = :login";
            $stmt = $linkpdo->prepare($sql);
            $stmt->execute(['login' => $login]);
            $data = $stmt->fetch(PDO::FETCH_ASSOC);
            if ($data) {
                if (password_verify($password, $data['password'])) {
                    $header = array('alg' => 'HS256', 'typ' => 'JWT');
                    $payload = array('role' => $data['role'], 'user_id' => $data['id'], 'exp' => time() + 3600);
                    $signature = "";
                    $token = generate_jwt($header, $payload, $signature);
                    return $token;
                }
            }
            return false;
        }
    }

}