<?php
require_once __DIR__ . '/../vendor/autoload.php';

use Firebase\JWT\JWT;
use Firebase\JWT\Key;

class JwtHandler
{
    private static $config;

    public static function init()
    {
        if (!self::$config) {
            self::$config = require __DIR__ . '/../config/jwt.php';
        }
    }

    public static function generateToken($payloadData)
    {
        self::init();
        $issuedAt = time();
        $exp = $issuedAt + self::$config['ttl'];

        $payload = array_merge($payloadData, [
            'iat' => $issuedAt,
            'exp' => $exp
        ]);

        return JWT::encode($payload, self::$config['secret'], self::$config['algo']);
    }

    public static function validateToken($token)
    {
        self::init();
        try {
            $decoded = JWT::decode($token, new Key(self::$config['secret'], self::$config['algo']));
            return (array) $decoded;
        } catch (Exception $e) {
            return false;
        }
    }
}
