<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);
// Cargar la conexión a la base de datos
$pdo = require '../../config/db.php';

// Conexión a la base de datos TECMA
$pdoTEC = $pdo['tecmajrz'];
$stmtTEC = $pdoTEC->query("SELECT TOP 1 * FROM COLABORA WHERE CB_CODIGO = 900209");
$resultTEC = $stmtTEC->fetch();
echo "Desde TECMA: " . print_r($resultTEC, true);