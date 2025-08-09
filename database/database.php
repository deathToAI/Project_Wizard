<?php
/**
 * Configurações do banco de dados SQLite
 * 
 * Retorna um array com as configurações de conexão:
 * - driver: Tipo de banco de dados (sqlite)
 * - database_path: Caminho completo para o arquivo do banco de dados
 */
return [
    'driver' => 'sqlite',                      // Tipo do banco de dados
    'database_path' => __DIR__ . '/refeicoes.sqlite'  // Caminho absoluto para o arquivo SQLite
];