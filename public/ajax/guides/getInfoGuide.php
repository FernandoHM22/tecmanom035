<?php
try {
    require_once dirname(__DIR__, 3) . '/config/config.php';
    require_once BASE_PATH . '/src/controllers/GuideController.php';

    header('Content-Type: application/json');

    $input = file_get_contents('php://input');
    $data = json_decode($input, associative: true);

    $guide_type  = $data['guide_type'] ?? null;

    if ($guide_type ) {
        $pdo = getConnection(key: 'nom');
        $response = GuideController::guideInfo($guide_type ,$pdo);
        echo json_encode(value: $response);
    } else {
        echo json_encode([
            'success' => false,
            'message' => 'Falta el parameto guide_type'
        ]);
    }
} catch (Throwable $e) {
    http_response_code(500);
    echo json_encode([
        'success' => false,
        'message' => 'Excepción: ' . $e->getMessage()
    ]);
}
