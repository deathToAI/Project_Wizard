<?php
// lib/Logger.php

/**
 * Escreve uma mensagem em um arquivo de log centralizado.
 *
 * @param string $message A mensagem a ser registrada.
 * @param string $level O nível do log (ex: INFO, WARNING, ERROR, ACCESS).
 * @return void
 */
function log_message(string $message, string $level = 'INFO'): void
{
    // Garante que o fuso horário esteja definido
    date_default_timezone_set('America/Sao_Paulo');

    // Define o caminho para o arquivo de log
    $log_directory = __DIR__ . '/../logs';
    if (!is_dir($log_directory)) {
        mkdir($log_directory, 0755, true);
    }
    $log_file = $log_directory . '/app.log';

    // Formata a mensagem de log: [Data e Hora] [NÍVEL] Mensagem
    $formatted_message = sprintf(
        "[%s] [%s] %s" . PHP_EOL,
        date('Y-m-d H:i:s'),
        strtoupper($level),
        $message
    );

    // Adiciona a mensagem ao final do arquivo de forma segura
    file_put_contents($log_file, $formatted_message, FILE_APPEND | LOCK_EX);
}