<?php
try {
    require_once dirname(__DIR__, 3) . '/config/config.php';
    require_once BASE_PATH . '/src/controllers/RiskController.php';

    header('Content-Type: application/json; charset=UTF-8');

    $employee  = $_GET['employee'] ?? null;
    $scopeType = $_GET['scopeType'] ?? 'category';
    $answerSet = $_GET['answerSet'] ?? 'main';

    if (!$employee) {
        http_response_code(400);
        echo json_encode(['success'=>false, 'message'=>'employee es requerido']); exit;
    }

    $pdo = getConnection(key: 'nom');
    $resp = RiskController::getResults($employee, $scopeType, $answerSet, $pdo);
    echo json_encode($resp);
} catch (Throwable $e) {
    http_response_code(500);
    echo json_encode(['success'=>false, 'message'=>'Excepción: '.$e->getMessage()]);
}
