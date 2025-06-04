<?php
require_once __DIR__ . '/../vendor/autoload.php';

function conectarBD()
{
    $dotenv = Dotenv\Dotenv::createImmutable(__DIR__ . '/../');
    $dotenv->load();

    $host = $_ENV['DB_HOST'];
    $port = $_ENV['DB_PORT'];
    $user = $_ENV['DB_USER'];
    $pass = $_ENV['DB_PASS'];
    $dbname = $_ENV['DB_NAME'];

    try {
        $pdo = new PDO("pgsql:host=$host;port=$port;dbname=$dbname", $user, $pass);
        $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
        return $pdo;
    } catch (PDOException $e) {
        error_log("Error de conexión: " . $e->getMessage());
        echo "Error al conectar: " . $e->getMessage();
        return null;
    }
}
 