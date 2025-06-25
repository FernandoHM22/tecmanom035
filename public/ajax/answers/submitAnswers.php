<?php
try {
    require_once dirname(__DIR__, 3) . '/config/config.php';
    require_once BASE_PATH . '/src/controllers/AnswerController.php';

    header('Content-Type: application/json');


    $input = json_decode(file_get_contents('php://input'), true);

    if (!$input || !isset($input['employeeNumber']) || !isset($input['answers'])) {
        echo json_encode(['success' => false, 'message' => 'Datos incompletos.']);
        return;
    }

    $employeeNumber = $input['employeeNumber'];
    $answers = $input['answers'];

    if ($answers) {
        $pdo = getConnection(key: 'nom');
        $response = AnswerController::submitAnswers($answers, $employeeNumber,  $pdo);
        echo json_encode(value: $response);
    } else {
        echo json_encode([
            'success' => false,
            'message' => 'No se encontraron respuestas para la evaluacion que estas intentando enviar.'
        ]);
    }
} catch (Throwable $e) {
    http_response_code(500);
    echo json_encode([
        'success' => false,
        'message' => 'Excepción: ' . $e->getMessage()
    ]);
}
