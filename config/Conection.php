
<?php

require_once __DIR__ . '/../vendor/autoload.php';

use SQLiteCloud\SQLiteCloudClient;
use Dotenv\Dotenv;

function conectarBD() {
    try {
        $dotenv = Dotenv::createImmutable(__DIR__ . '/../');
        $dotenv->load();

        $DB_URL = $_ENV['DB_URL'];
        $API_KEY = $_ENV['API_KEY'];
        $sqlite = new SQLiteCloudClient();
        $sqlite->connectWithString("$DB_URL/$API_KEY");

        return $sqlite;
    
    } catch (Exception $e) {
        die("Error de conexión: " . $e->getMessage());
    }
}
    ?>