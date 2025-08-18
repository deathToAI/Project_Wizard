<?php
// Medida de segurança: Garante que o script não seja executado em produção.
// Esta verificação restringe o acesso ao localhost.
if (!in_array($_SERVER['REMOTE_ADDR'], ['127.0.0.1', '::1'])) {
    header('Location:index.php');
    die('Acesso restrito ao ambiente de desenvolvimento.');
}

require_once 'database/DbConnection.php';

try {
    // Mostra configurações
    $config = require './database/database.php';
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
?>