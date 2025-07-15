<?php
try {
    require_once dirname(__DIR__, 3) . '/config/config.php';
    require_once BASE_PATH . '/src/controllers/AdminController.php';

    header('Content-Type: application/json');

    $input = file_get_contents('php://input');
    $data = json_decode($input, true);

    $dataHR = $data['dataHR'] ?? null;

    if (!$dataHR) {
        http_response_code(400);
        echo json_encode([
            'success' => false,
            'message' => 'Información de recursos humanos no especificada'
        ]);
        exit;
    }
    
    $pdo = getConnection(key: 'nom');
    $response = AdminController::RHData($dataHR, $pdo);
    echo json_encode(value: $response);
} catch (Throwable $e) {
    http_response_code(500);
    echo json_encode([
        'success' => false,
        'message' => 'Excepción: ' . $e->getMessage()
    ]);
}
