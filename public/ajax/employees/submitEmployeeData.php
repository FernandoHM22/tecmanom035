<?php
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

try {
    require_once dirname(__DIR__, 3) . '/config/config.php';
    require_once BASE_PATH . '/src/controllers/EmployeeController.php';

    header('Content-Type: application/json');

    $input = json_decode(file_get_contents('php://input'), true);

    if (!$input || !isset($input['CB_CODIGO'])) {
        echo json_encode([
            'success' => false,
            'message' => 'Faltan datos clave del empleado (CB_CODIGO).'
        ]);
        return;
    }

    $pdo = getConnection(key: 'nom');

    $response = EmployeeController::submitEmployeeData($input, $pdo);
    
     echo json_encode(value: $response);
} catch (Throwable $e) {
    http_response_code(500);
    echo json_encode([
        'success' => false,
        'message' => 'Excepción: ' . $e->getMessage()
    ]);
}
