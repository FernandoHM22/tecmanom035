<?php

// Define ruta absoluta del proyecto (una sola vez)
if (!defined('BASE_PATH')) {
    define('BASE_PATH', dirname(__DIR__));
}

require_once __DIR__ . '/db.php'; // Importar conexión a la base de datos

return [
    'db' => require __DIR__ . '/db.php', // Conexión a la base de datos
    'app_name' => 'Tecmanom035',         // Nombre de la aplicación
    'debug' => true,                     // Activar/desactivar modo debug
];
