<?php
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
    require 'src/views/404.php';
}
