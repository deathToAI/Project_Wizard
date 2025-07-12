<?php 

function DbConnection() {
    if (file_exists(__DIR__ . '../config/database.php')) {
        try{
            $config = require __DIR__ . '/../config/database.php';
            $dsn = $config ['driver']. ':' . $config['database_path'];

            $pdo = new PDO($dsn);

        }catch(PDOException $e){
            echo "Não foi possivel conectar ao banco de dados:" . $e->getMessage();
        }
    return $pdo;
    }
}

?>