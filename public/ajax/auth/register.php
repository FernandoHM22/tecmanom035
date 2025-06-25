<?php
try {
    require_once dirname(__DIR__, 3) . '/config/config.php';
    require_once BASE_PATH . '/src/controllers/AuthController.php';

    header('Content-Type: application/json');

    $input = json_decode(file_get_contents('php://input'), true);

    // Validar estructura
    $required = ['fullName', 'email', 'password', 'region'];
    foreach ($required as $field) {
        if (empty($input[$field])) {
            echo json_encode([
                'success' => false,
                'message' => "El campo $field es obligatorio."
            ]);
            exit;
        }
    }

    $email = trim($input['email']);
    $password = $input['password'];

    if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        http_response_code(400);
        echo json_encode(['success' => false, 'message' => 'Correo inválido']);
        exit;
    }

    if (strlen($password) < 6) {
        http_response_code(400);
        echo json_encode(['success' => false, 'message' => 'La contraseña debe tener al menos 6 caracteres']);
        exit;
    }

    $pdo = getConnection('nom');

    // Verificar si ya existe el email
    $stmt = $pdo->prepare("SELECT COUNT(*) FROM Users WHERE Email = ?");
    $stmt->execute([$email]);

    if ($stmt->fetchColumn() > 0) {
        echo json_encode(['success' => false, 'message' => 'Ya existe una cuenta con ese correo']);
        exit;
    }

    // Llamar al controlador con todos los datos
    $response = AuthController::register($input, $pdo);
    echo json_encode($response);

} catch (Throwable $e) {
    http_response_code(500);
    echo json_encode([
        'success' => false,
        'message' => 'Excepción: ' . $e->getMessage()
    ]);
}
