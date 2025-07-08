<?php 
require_once '../config/database.php';

class DbConnection {
    private static $instance = null;
    private $pdo;

    private function __construct() {
        $config = require __DIR__.'../config/database.php';
        $driver = $config['driver'];
        $database_path = $config['database_path'];

    }


}



?>
