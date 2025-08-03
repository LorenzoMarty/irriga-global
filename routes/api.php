<?php
require_once __DIR__ . '/../vendor/autoload.php';

use Firebase\JWT\JWT;
use Firebase\JWT\Key;
use Dotenv\Dotenv;

header('Content-Type: application/json');

// Carregar variáveis de ambiente
$dotenv = Dotenv::createImmutable(__DIR__ . '/../');
$dotenv->load();

$secretKey = $_ENV['JWT_KEY'];

function readJson($file)
{
    if (!file_exists($file)) return [];
    return json_decode(file_get_contents($file), true);
}

function saveJson($file, $data)
{
    $dir = dirname($file);
    if (!is_dir($dir)) {
        mkdir($dir, 0777, true);
    }
    file_put_contents($file, json_encode($data, JSON_PRETTY_PRINT));
}

function authenticate()
{
    global $secretKey;
    $headers = getallheaders();
    if (!isset($headers['Authorization'])) {
        http_response_code(401);
        echo json_encode(['error' => 'Token não fornecido.']);
        exit;
    }

    $authHeader = $headers['Authorization'];
    $token = str_replace('Bearer ', '', $authHeader);

    try {
        return JWT::decode($token, new Key($secretKey, 'HS256'));
    } catch (Exception $e) {
        http_response_code(401);
        echo json_encode(['error' => 'Token inválido: ' . $e->getMessage()]);
        exit;
    }
}

$uri = parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH);
$method = $_SERVER['REQUEST_METHOD'];

if ($uri === '/auth/register' && $method === 'POST') {
    $data = json_decode(file_get_contents('php://input'), true);
    $users = readJson(__DIR__ . '/../storage/data/users.json');
    foreach ($users as $user) {
        if ($user['username'] === $data['username']) {
            echo json_encode(['error' => 'Usuário já existe.']);
            exit;
        }
    }

    $data['id'] = uniqid();
    $data['password'] = password_hash($data['password'], PASSWORD_DEFAULT);
    $users[] = $data;
    saveJson(__DIR__ . '/../storage/data/users.json', $users);

    echo json_encode(['message' => 'Usuário registrado com sucesso.']);
    exit;
}

if ($uri === '/auth/login' && $method === 'POST') {
    $data = json_decode(file_get_contents('php://input'), true);
    $users = readJson(__DIR__ . '/../storage/data/users.json');
    foreach ($users as $user) {
        if ($user['username'] === $data['username'] && password_verify($data['password'], $user['password'])) {
            $payload = [
                'sub' => $user['id'],
                'username' => $user['username'],
                'iat' => time(),
                'exp' => time() + 3600
            ];
            $jwt = JWT::encode($payload, $secretKey, 'HS256');
            echo json_encode(['token' => $jwt]);
            exit;
        }
    }

    http_response_code(401);
    echo json_encode(['error' => 'Credenciais inválidas.']);
    exit;
}

$authUser = authenticate();

$pivotFile = __DIR__ . '/../storage/data/pivots.json';
if ($uri === '/pivots' && $method === 'GET') {
    echo json_encode(readJson($pivotFile));
    exit;
}

if ($uri === '/pivots' && $method === 'POST') {
    $data = json_decode(file_get_contents('php://input'), true);
    $data['id'] = uniqid();
    $pivots = readJson($pivotFile);
    $pivots[] = $data;
    saveJson($pivotFile, $pivots);
    echo json_encode(['message' => 'Pivô criado.']);
    exit;
}

$irrigationFile = __DIR__ . '/../storage/data/irrigations.json';
if ($uri === '/irrigations' && $method === 'GET') {
    echo json_encode(readJson($irrigationFile));
    exit;
}

if ($uri === '/irrigations' && $method === 'POST') {
    $data = json_decode(file_get_contents('php://input'), true);
    $data['id'] = uniqid();
    $data['timestamp'] = date('Y-m-d H:i:s');
    $irrigations = readJson($irrigationFile);
    $irrigations[] = $data;
    saveJson($irrigationFile, $irrigations);
    echo json_encode(['message' => 'Registro de irrigação salvo.']);
    exit;
}

http_response_code(404);
echo json_encode(['error' => 'Rota não encontrada.']);
