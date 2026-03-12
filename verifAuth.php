<?php
require_once("jwt_utils.php");

class verifAuth {

    function verifAuth($linkpdo, $user, $password) {
        if (!empty($user) && !empty($password)) {
            $sql = "SELECT * FROM users WHERE user = :user";
            $stmt = $linkpdo->prepare($sql);
            $stmt->execute(['user' => $user]);
            $data = $stmt->fetch(PDO::FETCH_ASSOC);
            if ($data) {
                if ($password == $data['password']) {
                    $header = array('alg' => 'HS256', 'typ' => 'JWT');
                    $payload = array('role' => $data['role'], 'user_id' => $data['id'], 'exp' => time() + 3600);
                    $signature = "sportteam";
                    $token = generate_jwt($header, $payload, $signature);
                    return $token;
                }
            }
            return false;
        }
    }

}