<?php

require_once __DIR__ . '/controllers/AuthController.php';
require_once __DIR__ . '/controllers/StudentController.php';
require_once __DIR__ . '/lib/JwtHandler.php';

header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: GET, POST, PUT, PATCH, DELETE, OPTIONS');
header('Access-Control-Allow-Headers: Content-Type, Authorization');

if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
    http_response_code(204);
    exit;
}

$scriptName = dirname($_SERVER['SCRIPT_NAME']);
$uri = parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH);
$uri = str_replace('\u0000', '', $uri);
if ($scriptName !== '/' && strpos($uri, $scriptName) === 0) {
    $uri = substr($uri, strlen($scriptName));
}
$uri = trim($uri, '/');
$segments = array_values(array_filter(explode('/', $uri)));

$authController = new AuthController();
$studentController = new StudentController();
$jwtHandler = new JwtHandler();

$body = json_decode(file_get_contents('php://input'), true) ?: [];

function respondNotFound(): void
{
    http_response_code(404);
    header('Content-Type: application/json; charset=utf-8');
    echo json_encode(['error' => 'Route introuvable']);
    exit;
}

function requireAuth(JwtHandler $jwtHandler): void
{
    $headers = getallheaders();
    $authHeader = $headers['Authorization'] ?? $headers['authorization'] ?? '';
    if (!preg_match('/Bearer\s+(.*)$/i', $authHeader, $matches)) {
        http_response_code(401);
        header('Content-Type: application/json; charset=utf-8');
        echo json_encode(['error' => 'Token manquant ou invalide']);
        exit;
    }
    $token = $matches[1];
    if (!$jwtHandler->validateToken($token)) {
        http_response_code(401);
        header('Content-Type: application/json; charset=utf-8');
        echo json_encode(['error' => 'Token expiré ou invalide']);
        exit;
    }
}

if ($segments[0] === 'login' && $_SERVER['REQUEST_METHOD'] === 'POST') {
    $authController->login($body);
    exit;
}

if ($segments[0] === 'students') {
    if ($_SERVER['REQUEST_METHOD'] === 'GET' && count($segments) === 1) {
        requireAuth($jwtHandler);
        $studentController->index();
        exit;
    }

    if ($_SERVER['REQUEST_METHOD'] === 'GET' && count($segments) === 2 && is_numeric($segments[1])) {
        requireAuth($jwtHandler);
        $studentController->show((int)$segments[1]);
        exit;
    }

    if ($_SERVER['REQUEST_METHOD'] === 'POST' && count($segments) === 1) {
        requireAuth($jwtHandler);
        $studentController->store($body);
        exit;
    }

    if (in_array($_SERVER['REQUEST_METHOD'], ['PUT', 'PATCH'], true) && count($segments) === 2 && is_numeric($segments[1])) {
        requireAuth($jwtHandler);
        $studentController->update((int)$segments[1], $body);
        exit;
    }

    if ($_SERVER['REQUEST_METHOD'] === 'DELETE' && count($segments) === 2 && is_numeric($segments[1])) {
        requireAuth($jwtHandler);
        $studentController->delete((int)$segments[1]);
        exit;
    }
}

respondNotFound();

