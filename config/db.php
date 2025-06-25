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

    // Conexión a la base de datos TECMA CENTRAL
    $hostTECMAJRZ = $_ENV['DBTECMAJRZ_HOST'];
    $portTECMAJRZ = $_ENV['DBTECMAJRZ_PORT'];
    $dbTECMAJRZ   = $_ENV['DBTECMAJRZ_DATABASE'];
    $userTECMAJRZ   = $_ENV['DBTECMAJRZ_USERNAME'];
    $passwordTECMAJRZ   = $_ENV['DBTECMAJRZ_PASSWORD'];

    $dsnTECMAJRZ  = "sqlsrv:Server=$hostTECMAJRZ,$portTECMAJRZ;Database=$dbTECMAJRZ";

    $pdoTECMAJRZ = new PDO($dsnTECMAJRZ, $userTECMAJRZ, $passwordTECMAJRZ, [
        PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
        PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
    ]);

    // Conexión a la base de datos TECMA WEST
    $hostTECMAWEST = $_ENV['DBTECWEST_HOST'];
    $portTECMAWEST = $_ENV['DBTECWEST_PORT'];
    $dbTECMAWEST   = $_ENV['DBTECWEST_DATABASE'];
    $userTECMAWEST   = $_ENV['DBTECWEST_USERNAME'];
    $passwordTECMAWEST   = $_ENV['DBTECWEST_PASSWORD'];

    $dsnTECMAWEST  = "sqlsrv:Server=$hostTECMAWEST,$portTECMAWEST;Database=$dbTECMAWEST";

    $pdoTECMAWEST = new PDO($dsnTECMAWEST, $userTECMAWEST, $passwordTECMAWEST, options: [
        PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
        PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
    ]);

    // Retornar las conexiones en un array
    return [
        'nom' => $pdoNOM,
        'tecmaCENTRAL' => $pdoTECMAJRZ,
        'tecmaWEST' => $pdoTECMAWEST,
    ];
} catch (PDOException $e) {
    die("Error al conectar a la base de datos: " . $e->getMessage());
}
