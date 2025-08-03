<?php
require_once __DIR__ . '/../utils/jwt.php';

class JwtMiddleware {
    public static function authenticate() {
        $headers = getallheaders();
        if (!isset($headers['Authorization'])) {
            http_response_code(401);
            echo json_encode(['message' => 'Missing Authorization header']);
            exit;
        }

        $token = str_replace('Bearer ', '', $headers['Authorization']);
        $decoded = JwtHandler::validateToken($token); // Corrigido o método

        if (!$decoded) {
            http_response_code(401);
            echo json_encode(['message' => 'Invalid or expired token']);
            exit;
        }

        return $decoded['user_id'] ?? null;
    }
}
