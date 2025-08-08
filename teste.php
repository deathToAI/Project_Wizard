<?php
require_once 'lib/DbConnection.php';

try {
    // Mostra configurações
    $config = require 'config/database.php';
    echo "Driver: " . $config['driver'] . "<br>";
    echo "Caminho: " . $config['database_path'] . "<br>";
    
    // Testa conexão
    $pdo = DbConnection();
    echo "Conexão OK!<br>";
    
    // Testa consulta
    $tables = $pdo->query("SELECT name FROM sqlite_master WHERE type='table'")->fetchAll();
    echo "Tabelas encontradas: " . count($tables);
    
} catch (Exception $e) {
    echo "<strong>ERRO:</strong> " . $e->getMessage();
}

phpinfo();

?>