<?php
// Ruta física del proyecto (para operaciones internas si lo necesitas)
if (!defined('BASE_PATH')) {
    define('BASE_PATH', dirname(__DIR__));
}

// Detectar dinámicamente la BASE_URL
$scriptDir = dirname($_SERVER['SCRIPT_NAME']); // Ejemplo: /tecmanom035/public/views/forms
$base = explode('/public', $scriptDir)[0];      // Cortamos hasta /public
define('BASE_URL', $base ?: '');                 // Si no hay base, será la raíz ('')

// 🔧 Función para rutas a archivos estáticos
function asset($path)
{
    return rtrim(BASE_URL, '/') . '/' . ltrim($path, '/');
}

// 🔧 Función para rutas controladas por router.php
function route($path = '')
{
    return rtrim(BASE_URL, '/') . '/' . ltrim($path, '/');
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
