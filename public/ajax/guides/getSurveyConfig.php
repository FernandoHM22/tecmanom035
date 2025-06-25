<?php
try {
    require_once dirname(__DIR__, 3) . '/config/config.php';
    require_once BASE_PATH . '/src/controllers/GuideController.php';

    header('Content-Type: application/json');

    $input = file_get_contents('php://input');
    $data = json_decode($input, associative: true);

    $projectId  = $data['project_id'] ?? null;

    if ($projectId ) {
        $pdo = getConnection(key: 'nom');
        $response = GuideController::surveyConfig($projectId ,$pdo);
        echo json_encode(value: $response);
    } else {
        echo json_encode([
            'success' => false,
            'message' => 'Falta el parameto project_id'
        ]);
    }
} catch (Throwable $e) {
    http_response_code(500);
    echo json_encode([
        'success' => false,
        'message' => 'Excepción: ' . $e->getMessage()
    ]);
}
