<?php
$modal = $_GET['modal'] ?? '';
$modal = str_replace(['..', '//'], '', $modal); // seguridad básica

$path = dirname(__DIR__, 2) . '/src/views/modals/' . $modal . '.php';

echo "Cargando modal desde: $path"; // depuración, eliminar en producción
if (file_exists($path)) {
    include $path;
} else {
    http_response_code(404);
    echo "<div class='p-3'>Modal no encontrado.</div>";
}
