<?php
require_once(__DIR__ . "/../utils/jwt_utils.php");

class verifAuth {

    private PDO $pdo;

    public function __construct(PDO $pdo) {
        $this->pdo = $pdo;
    }

    function verifAuth($user, $password) {
        if (!empty($user) && !empty($password)) {
            $sql = "SELECT * FROM users WHERE user = :user";
            $stmt = $this->pdo->prepare($sql);
            $stmt->execute(['user' => $user]);
            $data = $stmt->fetch(PDO::FETCH_ASSOC);
            if ($data) {
                if ($password == $data['password']) {
                    $header = array('alg' => 'HS256', 'typ' => 'JWT');
                    $payload = array('role' => $data['role'], 'user_id' => $data['id'], 'exp' => time() + 3600);
                    $env = parse_ini_file(__DIR__ . "/../../.env");
                    $signature = $env["JWT_SECRET"];
                    $token = generate_jwt($header, $payload, $signature);
                    return $token;
                }
            }
            return false;
        }
    }

}