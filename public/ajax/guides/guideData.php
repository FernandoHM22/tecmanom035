<?php
try {
    require_once dirname(__DIR__, 3) . '/config/config.php';
    require_once BASE_PATH . '/src/controllers/GuideController.php';

    header('Content-Type: application/json');

    $input = file_get_contents('php://input');
    $data = json_decode($input, associative: true);

    $guideValue = $data['guideValue'] ?? null;

    if ($guideValue) {
        $pdo = getConnection(key: 'nom');
        $response = GuideController::dataGuide($guideValue, $pdo);
        echo json_encode(value: $response);
    } else {
        echo json_encode([
            'success' => false,
            'message' => 'Numero de guia no recibido'
        ]);
    }
} catch (Throwable $e) {
    http_response_code(500);
    echo json_encode([
        'success' => false,
        'message' => 'Excepción: ' . $e->getMessage()
    ]);
}
