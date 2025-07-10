<?php 

function DbConnection() {
    if (file_exists(__DIR__ . '/../config/database.php')) {
        try{
            $config = require __DIR__ . '/../config/database.php';
            $dsn = $config ['driver']. ':' . $config['database_path'];

            $pdo = new PDO($dsn);

        }catch(PDOException $e){
            echo "Fail:" . $e->getMessage();
        }
        $criaRef = "CREATE TABLE IF NOT EXISTS refeicoes 
            (
            id INTEGER PRIMARY KEY AUTOINCREMENT,
            user_id INTEGER NOT NULL,
            data_refeicao DATE NOT NULL,
            tipo_ref TEXT CHECK( tipo_ref IN ('cafe','almoco','janta') ) NOT NULL DEFAULT 'almoco', 
            FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE
            );";
        $criaUsers = "CREATE TABLE IF NOT EXISTS users
            (id INTEGER PRIMARY KEY AUTOINCREMENT,
            username TEXT NOT NULL UNIQUE,
            password TEXT NOT NULL,
            nome_pg TEXT NOT NULL,
            role TEXT CHECK(role IN ('admin','furriel','comum')) NOT NULL DEFAULT 'comum',
            grupo INTEGER NOT NULL
            );";
        $pdo->exec($criaUsers);           
        $pdo->exec($criaRef);
        return $pdo;
    }
}

?>