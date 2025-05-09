<?php

require_once __DIR__ . '/../vendor/autoload.php';

use SQLiteCloud\SQLiteCloudClient;
use Dotenv\Dotenv;

$dotenv = Dotenv::createImmutable(__DIR__ . '/../');
$dotenv->load();

function conectarBD() {
    static $sqlite = null;
    if ($sqlite === null) {
        try {
            $DB_URL = $_ENV['DB_URL'] ?? throw new RuntimeException("DB_URL no definido");
            $sqlite = new SQLiteCloudClient();
            $sqlite->connectWithString($DB_URL);
        } catch (Exception $e) {
            error_log("[BD] Error de conexión: " . $e->getMessage());
            throw $e; 
        }
    }
    return $sqlite; 
}
?>