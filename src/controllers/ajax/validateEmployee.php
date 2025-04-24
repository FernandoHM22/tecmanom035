<?php

declare(strict_types=1);
error_reporting(E_ALL);

// Incluir archivo de configuración y obtener los parámetros
$config = require __DIR__ . '/../../../config/config.php';

// Obtener la conexión PDO desde la configuración
$pdo = $config['db']['tecma'];

// Asegurar cabeceras para respuesta JSON y evitar cache
header('Content-Type: application/json');
header('Cache-Control: no-store, no-cache, must-revalidate');

// Validar que sea una petición POST con el parámetro esperado
if ($_SERVER['REQUEST_METHOD'] !== 'POST' || empty($_POST['cb_codigo'])) {
    echo json_encode([
        'success' => false,
        'message' => 'Petición inválida o parámetro faltante.',
    ]);
    exit;
}

// Sanitizar la entrada (esto depende del tipo de dato que esperas, aquí como string)
$cb_codigo = trim(string: $_POST['cb_codigo'] ?? '');

// Validar mínimo 2 caracteres
if (strlen($cb_codigo) < 2) {
    echo json_encode([
        'success' => false,
        'message' => 'Debe ingresar al menos 2 caracteres.',
    ]);
    exit;
}

try {
    // Preparar y ejecutar consulta segura
    $stmt = $pdo->prepare("SELECT
            CASE
            WHEN C.CB_SEXO = 'F' THEN 'Mujer' 
            ELSE 'Hombre' END  AS GENERO,
            DATEDIFF(YEAR, C.CB_FEC_NAC, GETDATE()) 
            - CASE 
            WHEN MONTH(C.CB_FEC_NAC) > MONTH(GETDATE()) 
                OR (MONTH(C.CB_FEC_NAC) = MONTH(GETDATE()) AND DAY(C.CB_FEC_NAC) > DAY(GETDATE()))
            THEN 1 ELSE 0 END AS Edad,  
            EC.TB_ELEMENT AS EDOCIVIL,
            EST.TB_ELEMENT AS ESTUDIO,
            T.TU_DESCRIP,
            DATEDIFF(YEAR, C.CB_FEC_ANT, GETDATE()) 
            - CASE 
            WHEN MONTH(C.CB_FEC_ANT) > MONTH(GETDATE()) 
                OR (MONTH(C.CB_FEC_ANT) = MONTH(GETDATE()) AND DAY(C.CB_FEC_ANT) > DAY(GETDATE()))
            THEN 1 ELSE 0 END AS ANTIGUEDAD,  
            A.TB_ELEMENT AS AREA,
            S.TB_ELEMENT AS SUPERVISOR,
            C.PRETTYNAME AS NOMBRE,
            C.CB_CODIGO,
            P.TB_ELEMENT AS CLIENTE,
            E.TB_ELEMENT AS PLANTA
            FROM COLABORA C 
            INNER JOIN V_NIVEL0 P ON C.CB_NIVEL0 = P.TB_CODIGO
            INNER JOIN NIVEL3 S ON C.CB_NIVEL3 = S.TB_CODIGO
            INNER JOIN NIVEL5 A ON C.CB_NIVEL5 = A.TB_CODIGO
            INNER JOIN NIVEL7 E ON C.CB_NIVEL7 = E.TB_CODIGO
            INNER JOIN TURNO T ON C.CB_TURNO = T.TU_CODIGO
            INNER JOIN EDOCIVIL EC ON C.CB_EDO_CIV = EC.TB_CODIGO
            INNER JOIN ESTUDIOS EST ON C.CB_ESTUDIO = EST.TB_CODIGO
         WHERE C.CB_CODIGO = ?");
         
    $stmt->execute([$cb_codigo]);
    $result = $stmt->fetch(PDO::FETCH_ASSOC);

    if ($result) {
        echo json_encode([
            'success' => true,
            'data' => $result
        ]);
    } else {
        echo json_encode([
            'success' => false,
            'message' => 'Empleado no encontrado.'
        ]);
    }
} catch (PDOException $e) {
    error_log("DB error: " . $e->getMessage());
    echo json_encode([
        'success' => false,
        'message' => 'Error al consultar la base de datos.'
    ]);
}
