<?php
try {
    require_once dirname(__DIR__, 3) . '/config/config.php';
    require_once BASE_PATH . '/src/controllers/EmployeeController.php';

    header('Content-Type: application/json');

    $input = file_get_contents('php://input');
    $data = json_decode($input, true);

    $cb_codigo = $data['cb_codigo'] ?? null;
    $region = $data['region'] ?? null;

    if ($cb_codigo) {
        
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

        $response = EmployeeController::validateEmployee($cb_codigo, $pdo);
        echo json_encode($response);
    } else {
        echo json_encode([
            'success' => false,
            'message' => 'Código de empleado no recibido'
        ]);
    }
} catch (Throwable $e) {
    http_response_code(500);
    echo json_encode([
        'success' => false,
        'message' => 'Excepción: ' . $e->getMessage()
    ]);
}
