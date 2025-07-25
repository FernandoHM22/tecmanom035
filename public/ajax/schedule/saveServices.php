<?php
try {
    require_once dirname(__DIR__, 3) . '/config/config.php';
    require_once BASE_PATH . '/src/controllers/AdminController.php';

    header('Content-Type: application/json');

    $input = file_get_contents('php://input');
    $data = json_decode($input, true);

    $selectedServices = $data['selectedServices'] ?? null;

    if (!$selectedServices) {
        http_response_code(400);
        echo json_encode([
            'success' => false,
            'message' => 'Servicios no especificados'
        ]);
        exit;
    }
    
    $pdo = getConnection(key: 'nom');
    $response = AdminController::servicesProjectData($selectedServices, $pdo);
    echo json_encode(value: $response);
} catch (Throwable $e) {
    http_response_code(500);
    echo json_encode([
        'success' => false,
        'message' => 'Excepción: ' . $e->getMessage()
    ]);
}
