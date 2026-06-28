<?php

require_once __DIR__ . '/../lib/JwtHandler.php';

class AuthController
{
    private JwtHandler $jwt;
    private array $users;

    public function __construct()
    {
        $this->jwt = new JwtHandler();
        $this->users = [
            ['username' => 'admin', 'password' => 'password123'],
        ];
    }

    public function login(array $data): void
    {
        if (empty($data['username']) || empty($data['password'])) {
            $this->sendJson(['error' => 'Nom d’utilisateur ou mot de passe manquant'], 422);
            return;
        }

        foreach ($this->users as $user) {
            if ($user['username'] === $data['username'] && $user['password'] === $data['password']) {
                $token = $this->jwt->generateToken(['username' => $user['username']]);
                $this->sendJson(['token' => $token]);
                return;
            }
        }

        $this->sendJson(['error' => 'Authentification échouée'], 401);
    }

    public function sendJson($data, int $status = 200): void
    {
        http_response_code($status);
        header('Content-Type: application/json; charset=utf-8');
        echo json_encode($data, JSON_UNESCAPED_UNICODE);
    }
}
