<?php
try {
    require_once dirname(__DIR__, 3) . '/config/config.php';
    require_once BASE_PATH . '/src/controllers/ProjectController.php';

    header('Content-Type: application/json');

    $input = file_get_contents('php://input');
    $data = json_decode($input, true);

    $projectId = $data['projectId'] ?? null;
    $region = $data['region'] ?? null;

    if (!$projectId) {
        http_response_code(400);
        echo json_encode(value: [
            'success' => false,
            'message' => 'ID de proyecto no especificado'
        ]);
        exit;
    }
    
    switch ($region) {
        case 'CENTRAL':
            $pdo = getConnection(key: 'tecmaCENTRAL');
            break;
        case 'WEST':
            $pdo = getConnection(key: 'tecmaWEST');
            break;
        default:
            break;
    }

    $response = ProjectController::projectShifts($projectId, $region, $pdo);
    echo json_encode(value: $response);
} catch (Throwable $e) {
    http_response_code(500);
    echo json_encode([
        'success' => false,
        'message' => 'Excepción: ' . $e->getMessage()
    ]);
}
