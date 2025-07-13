<?php
/**
 * Estabelece conexão com o banco de dados SQLite
 * 
 * @return PDO Objeto de conexão com o banco de dados
 * @throws Exception Se o arquivo de configuração não for encontrado ou houver erro de conexão
 */
function DbConnection() {
    // Caminho completo para o arquivo de configuração
    $configFile = __DIR__ . '/../config/database.php';
    
    // Verifica se o arquivo de configuração existe
    if (!file_exists($configFile)) {
        throw new Exception("Arquivo de configuração não encontrado em: " . $configFile);
    }

    // Carrega as configurações do arquivo
    $config = require $configFile;
    
    // Monta a string de conexão (DSN) no formato "sqlite:/caminho/para/arquivo.sqlite"
    $dsn = $config['driver'] . ':' . $config['database_path'];
    
    try {
        // Cria a conexão PDO
        $pdo = new PDO($dsn);
        
        // Configura o PDO para lançar exceções em caso de erros
        $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
        
        // Retorna o objeto de conexão
        return $pdo;
        
    } catch (PDOException $e) {
        // Captura e relata erros de conexão
        throw new Exception("Erro de conexão: " . $e->getMessage());
    }
}