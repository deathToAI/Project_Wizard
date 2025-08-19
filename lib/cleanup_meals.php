<?php
// /lib/cleanup_meals.php
// Script para ser executado via Cron Job para limpar registros antigos da tabela 'arranchados'.
//LEMBRAR DE:
//crontab -e
//1 0 1 * * /usr/bin/php /home/dtai/Projects/Tutorials/Project_Wizard/lib/cleanup_meals.php
// Define o fuso horário para garantir que a data seja calculada corretamente
date_default_timezone_set('America/Sao_Paulo');

// O script está fora do diretório web, então o caminho para a conexão precisa ser ajustado
require_once __DIR__ . '/../database/DbConnection.php';
require_once __DIR__ . '/Logger.php';

try {
    $pdo = DbConnection();
    if ($pdo === null) {
        throw new Exception("Não foi possível conectar ao banco de dados.");
    }

    log_message("Iniciando job de limpeza de refeições antigas.", 'JOB');

    // Calcula a data de corte: 30 dias atrás a partir de hoje
    $cutoff_date = new DateTime();
    $cutoff_date->sub(new DateInterval('P30D'));
    $formatted_date = $cutoff_date->format('Y-m-d');

    // Prepara a query para deletar os registros antigos
    $sql = "DELETE FROM arranchados WHERE data_refeicao < ?";
    $stmt = $pdo->prepare($sql);
    
    $stmt->execute([$formatted_date]);

    $deleted_count = $stmt->rowCount();

    log_message("Limpeza concluída. {$deleted_count} registros anteriores a {$formatted_date} foram deletados.", 'JOB');

} catch (Exception $e) {
    log_message("ERRO durante a limpeza: " . $e->getMessage(), 'ERROR');
    exit(1); // Retorna um código de erro
}

exit(0); // Retorna um código de sucesso