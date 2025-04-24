<?php
require_once __DIR__ . '/../vendor/autoload.php';

use Dotenv\Dotenv;

// Cargar las variables de entorno
$dotenv = Dotenv::createImmutable(__DIR__ . '/../');
$dotenv->load();

try {
    // Conexión a la base de datos NOM035
    $hostNOM = $_ENV['DBNOM_HOST'];
    $portNOM = $_ENV['DBNOM_PORT'];
    $dbNOM   = $_ENV['DBNOM_DATABASE'];
    $userNOM   = $_ENV['DBNOM_USERNAME'];
    $passwordNOM   = $_ENV['DBNOM_PASSWORD'];
    $dsnNOM  = "sqlsrv:Server=$hostNOM,$portNOM;Database=$dbNOM";

    $pdoNOM = new PDO($dsnNOM, $userNOM, $passwordNOM, [
        PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
        PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
    ]);

    // Conexión a la base de datos TECMA
    $hostTEC = $_ENV['DBTEC_HOST'];
    $portTEC = $_ENV['DBTEC_PORT'];
    $dbTEC   = $_ENV['DBTEC_DATABASE'];
    $userTEC   = $_ENV['DBTEC_USERNAME'];
    $passwordTEC   = $_ENV['DBTEC_PASSWORD'];

    $dsnTEC  = "sqlsrv:Server=$hostTEC,$portTEC;Database=$dbTEC";

    $pdoTEC = new PDO($dsnTEC, $userTEC, $passwordTEC, [
        PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
        PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
    ]);

    // Retornar las conexiones en un array
    return [
        'nom' => $pdoNOM,
        'tecma' => $pdoTEC,
    ];
} catch (PDOException $e) {
    die("Error al conectar a la base de datos: " . $e->getMessage());
}
