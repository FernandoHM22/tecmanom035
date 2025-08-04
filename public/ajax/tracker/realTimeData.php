<?php
try {
    require_once dirname(__DIR__, 3) . '/config/config.php';
    require_once BASE_PATH . '/src/controllers/AdminController.php';

    header('Content-Type: application/json');

    $input = file_get_contents('php://input');
    $data = json_decode($input, true);

    $type = $data['type'] ?? null;
    $project = $data['project'] ?? null;
    $selectYears = $data['selectYears'] ?? null;

    if (!$project || !$selectYears) {
        http_response_code(400);
        echo json_encode([
            'success' => false,
            'message' => 'ID de proyecto o años no especificados'
        ]);
        exit;
    }
    
    $pdo = getConnection(key: 'nom');
    if($type === 'general') {
        $response = AdminController::projectRealTimeGeneralData($project, $selectYears, $pdo);
    } elseif ($type === 'area') {
        $response = AdminController::projectRealTimeAreaData($project, $selectYears, $pdo);
    } elseif ($type === 'supervisor') {
        $response = AdminController::projectRealTimeSupervisorData($project, $selectYears, $pdo);
    } elseif ($type === 'shift') {
        $response = AdminController::projectRealTimeShiftData($project, $selectYears, $pdo);
    }
    
    // $response = AdminController::projectRealTimeGeneralData($project, $selectYears, $pdo);
    echo json_encode(value: $response);
} catch (Throwable $e) {
    http_response_code(500);
    echo json_encode([
        'success' => false,
        'message' => 'Excepción: ' . $e->getMessage()
    ]);
}
