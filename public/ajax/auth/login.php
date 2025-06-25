<?php
require_once dirname(__DIR__, 3) . '/config/config.php';
require_once BASE_PATH . '/src/controllers/AuthController.php';

use Firebase\JWT\JWT;

header('Content-Type: application/json');

try {
    $input = json_decode(file_get_contents('php://input'), true);

    $email = trim($input['email'] ?? '');
    $password = $input['password'] ?? '';

    if (!$email || !$password) {
        echo json_encode(['success' => false, 'message' => 'Email y contraseña requeridos.']);
        exit;
    }

    $pdo = getConnection('nom');
    $result = AuthController::login($email, $password, $pdo);

    //fragmento para agregar mas informacion al inicar sesion con conexion al TRESS
    // if ($result['success'] && isset($result['region'])) {
    //     // Obtener base de datos según la región
    //     $regionDbKey = match (strtoupper($result['region'])) {
    //         'CENTRAL' => 'tecmaCENTRAL',
    //         'WEST' => 'tecmaWEST',
    //         default => null,
    //     };

    //     if ($regionDbKey) {
    //         $regionPdo = getConnection($regionDbKey);

    //         // Llamar una nueva función que haga la consulta extra
    //         $extraData = AuthController::getExtraData($result['user_id'], $regionPdo); // Ejemplo: otra función

    //         // Agregar los datos al resultado final
    //         $result['region_data'] = $extraData;
    //     }
    // }

    echo json_encode($result);
} catch (Throwable $e) {
    http_response_code(500);
    echo json_encode([
        'success' => false,
        'message' => 'Excepción: ' . $e->getMessage()
    ]);
}
