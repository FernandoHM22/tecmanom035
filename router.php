<?php
require_once __DIR__ . '/src/middlewares/jwt.php';

$token = getToken();
$user = $token ? verifyToken($token) : null;

// Redirige si está en root sin login
if (!isset($_GET['page']) && !$user) {
    header("Location: " . route('login'));
    exit;
}

// Normaliza URL quitando múltiples slashes excepto después del protocolo
$requestUri = $_SERVER['REQUEST_URI'];
$normalizedUri = preg_replace('#/+#', '/', $requestUri);
$normalizedUri = preg_replace('#^(/)#', '/', $normalizedUri);

// Si hay diferencia, redirige
if ($requestUri !== $normalizedUri) {
    $protocol = (!empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off') ? 'https' : 'http';
    $host = $_SERVER['HTTP_HOST'];
    header("Location: $protocol://$host$normalizedUri", true, 301);
    exit();
}


$request = $_GET['page'] ?? 'home';  // ← Aquí está la clave

$request = strtolower(trim($request));

if ($request === 'login') {
    $viewPath = "src/views/auth/login.php";
}else if($request === 'register'){
    $viewPath = "src/views/auth/register.php";
}
else if($request === 'error404'){
    $viewPath = "error404.php";
}
 else {
    $viewPath = "src/views/" . $request . ".php";
}


if (file_exists($viewPath)) {
    require $viewPath;
} else {
    http_response_code(404);
    require 'error404.php';
}

require_once __DIR__ . '/src/views/partials/footer.php';