
<?php

require_once __DIR__ . '/../vendor/autoload.php';

use SQLiteCloud\SQLiteCloudClient;

try {
    
    $sqlite = new SQLiteCloudClient();
    $sqlite->connectWithString("sqlitecloud://clppbta2nz.g4.sqlite.cloud:8860/Inventory?apikey=Ue5HiXcbMqnpBrOfYF0B1Pdyqxoenax1zmzJahWjle4");

    
    $tableName = 'categorias';  
    $query = "SELECT * FROM $tableName";

    
    $rowset = $sqlite->execute($query);

    foreach ($rowset as $row) {
        print_r($row);
    }

  
    $sqlite->disconnect();
} catch (Exception $e) {

    echo "Error: " . $e->getMessage();
}

?>