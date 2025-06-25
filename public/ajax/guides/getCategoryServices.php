<?php
try {
    require_once dirname(__DIR__, 3) . '/config/config.php';
    require_once BASE_PATH . '/src/controllers/GuideController.php';

    header('Content-Type: application/json');

    $pdo = getConnection(key: 'nom');
    $response = GuideController::getCategoryServices($pdo);
    echo json_encode(value: $response);
} catch (Throwable $e) {
    http_response_code(500);
    echo json_encode([
        'success' => false,
        'message' => 'Excepción: ' . $e->getMessage()
    ]);
}
