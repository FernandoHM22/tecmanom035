<?php
require_once BASE_PATH . '/src/controllers/AuthController.php';

function requireAuth(): array
{
    $headers = getallheaders();
    $authHeader = $headers['Authorization'] ?? '';

    if (!preg_match('/Bearer\\s(\\S+)/', $authHeader, $matches)) {
        http_response_code(401);
        echo json_encode(['success' => false, 'message' => 'Token no proporcionado.']);
        exit;
    }

    $token = $matches[1];
    $user = AuthController::getUserInfoFromToken($token);

    if (!$user) {
        http_response_code(401);
        echo json_encode(['success' => false, 'message' => 'Token inválido o expirado.']);
        exit;
    }

    return $user;
}

function getToken(): ?string
{
    $headers = getallheaders();

    // Buscar en Authorization
    if (isset($headers['Authorization']) && preg_match('/Bearer\s(\S+)/', $headers['Authorization'], $matches)) {
        return $matches[1];
    }

    // Buscar en cookie opcional (si usas cookies para sesiones persistentes)
    if (isset($_COOKIE['jwt'])) {
        return $_COOKIE['jwt'];
    }

    return null;
}

function verifyToken(?string $token): ?array
{
    if (!$token) return null;
    $user = AuthController::getUserInfoFromToken($token);
    return $user ?: null;
}
