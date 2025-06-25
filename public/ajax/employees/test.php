<?php
require_once dirname(__DIR__, 3) . '/config/config.php';

$user = requireAuth(); // <- Valida y obtiene usuario autenticado desde el helper middleware de jwt validando el token y enviando por authentication bearer

echo json_encode([
  'success' => true,
  'region' => $user['region'],
  'id' => $user['id'],
  'role' => $user['role']
]);