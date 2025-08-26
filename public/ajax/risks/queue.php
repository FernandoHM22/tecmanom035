<?php
try {
    require_once dirname(__DIR__, 3) . '/config/config.php';
    require_once BASE_PATH . '/src/controllers/RiskController.php';

    header('Content-Type: application/json; charset=UTF-8');

    $data = json_decode(file_get_contents('php://input'), true) ?: [];

    $employee  = $data['employeeNumber'] ?? null;
    $scopeType = $data['scopeType'] ?? 'category';  // 'category' | 'domain'
    $answerSet = $data['answerSet'] ?? 'main';
    $guideId   = isset($data['guideId']) ? (int)$data['guideId'] : null;

    if (!$employee) {
        http_response_code(400);
        echo json_encode(['success'=>false, 'message'=>'employeeNumber es requerido']); exit;
    }
    if (!in_array($scopeType, ['category','domain'], true)) {
        http_response_code(400);
        echo json_encode(['success'=>false, 'message'=>'scopeType inválido']); exit;
    }

    $pdo = getConnection(key: 'nom');
    $resp = RiskController::enqueueJob($employee, $scopeType, $answerSet, $guideId, $pdo);
    echo json_encode($resp);
} catch (Throwable $e) {
    http_response_code(500);
    echo json_encode(['success'=>false, 'message'=>'Excepción: '.$e->getMessage()]);
}
