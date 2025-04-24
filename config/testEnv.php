<?php
error_reporting(E_ALL);
require_once __DIR__ . '/../vendor/autoload.php';

use Dotenv\Dotenv;

// Cargar el archivo .env desde la raíz del proyecto
$dotenv = Dotenv::createImmutable(__DIR__ . '/../');
$dotenv->load();

// Probar si las variables están accesibles
echo "DB_HOST: " . $_ENV['DBNOM_HOST'] . "<br>";
echo "DB_PORT: " . $_ENV['DBNOM_PORT'] . "<br>";
echo "DB_DATABASE: " . $_ENV['DBNOM_DATABASE'] . "<br>";
echo "DB_USERNAME: " . $_ENV['DBNOM_USERNAME'] . "<br>";
echo "DB_PASSWORD: " . $_ENV['DBNOM_PASSWORD'] . "<br>";
