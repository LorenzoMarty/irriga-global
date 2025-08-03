<?php
require_once __DIR__ . '/../../utils/jwt.php';

class AuthController {
    private $usersFile = __DIR__ . '/../../storage/data/users.json';

    public function register($data) {
        if (!isset($data['username'], $data['password'])) {
            http_response_code(400);
            echo json_encode(['message' => 'Missing username or password']);
            return;
        }

        if (!file_exists(dirname($this->usersFile))) {
            mkdir(dirname($this->usersFile), 0777, true);
        }

        $users = file_exists($this->usersFile) ? json_decode(file_get_contents($this->usersFile), true) : [];

        foreach ($users as $user) {
            if ($user['username'] === $data['username']) {
                http_response_code(400);
                echo json_encode(['message' => 'User already exists']);
                return;
            }
        }

        $newUser = [
            'id' => uniqid(),
            'username' => $data['username'],
            'password' => password_hash($data['password'], PASSWORD_DEFAULT)
        ];
        $users[] = $newUser;

        file_put_contents($this->usersFile, json_encode($users, JSON_PRETTY_PRINT));
        echo json_encode(['message' => 'User registered successfully']);
    }

    public function login($data) {
        if (!isset($data['username'], $data['password'])) {
            http_response_code(400);
            echo json_encode(['message' => 'Missing username or password']);
            return;
        }

        $users = file_exists($this->usersFile) ? json_decode(file_get_contents($this->usersFile), true) : [];

        foreach ($users as $user) {
            if ($user['username'] === $data['username'] && password_verify($data['password'], $user['password'])) {
                $jwt = JwtHandler::generateToken(['user_id' => $user['id']]);
                echo json_encode(['token' => $jwt]);
                return;
            }
        }

        http_response_code(401);
        echo json_encode(['message' => 'Invalid credentials']);
    }
}
