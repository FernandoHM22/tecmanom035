<?php
// Ruta física del proyecto (para operaciones internas si lo necesitas)
if (!defined('BASE_PATH')) {
    define('BASE_PATH', dirname(__DIR__));
}

$protocol = (!empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off') ||
            (isset($_SERVER['HTTP_X_FORWARDED_PROTO']) && $_SERVER['HTTP_X_FORWARDED_PROTO'] === 'https')
            ? 'https'
            : 'http';

$host = $_SERVER['HTTP_HOST'];

// Solo incluye ruta base si existe (por ejemplo en localhost)
$scriptName = str_replace('\\', '/', $_SERVER['SCRIPT_NAME']);
$basePath = explode('/public', $scriptName)[0];

// Elimina index.php si aparece
$basePath = str_replace('/index.php', '', $basePath);
$basePath = rtrim($basePath, '/');

define('BASE_URL', "$protocol://$host$basePath");



// 🔧 Función para rutas a archivos estáticos
function asset($path)
{
    $url = rtrim(BASE_URL, '/\\') . '/' . ltrim(str_replace('\\', '/', $path), '/');
    return $url;
}

// 🔧 Función para rutas controladas por router.php
function route($path = '')
{
    $url = rtrim(BASE_URL, '/\\') . '/' . ltrim(str_replace('\\', '/', $path), '/');
    return $url;
}

// Conexión a base de datos (puedes mantenerlo igual)
require_once __DIR__ . '/db.php';
require_once BASE_PATH . '/vendor/autoload.php';
require_once BASE_PATH . '/src/middlewares/jwt.php';

return [
    'db' => require __DIR__ . '/db.php', // Conexión a la base de datos
    'app_name' => 'Tecmanom035',
    'debug' => true,
];

function getConnection(string $key = 'nom'): PDO
{
    static $connections = null;

    if ($connections === null) {
        $connections = require __DIR__ . '/db.php';
    }

    if (!isset($connections[$key])) {
        throw new Exception("No se encontró la conexión '$key'");
    }

    return $connections[$key];
}
