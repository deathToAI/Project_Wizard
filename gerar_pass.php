<?php
// SCRIPT DE CONFIGURAÇÃO INICIAL - Execução única
// Modo de uso: php gerar_hash.php

// Define o caminho para o seu banco de dados SQLite
$dbPath = __DIR__ . '/database/refeicoes.sqlite';

// --- PASSO 1: DADOS DO NOVO USUÁRIO ADMIN E FURRIEL ---
$userData = [
    'username' => 'admin',
    'senha'    => 'AdminPass123', // <--- MUDE A SENHA AQUI!!!!
    'nome_pg'  => 'Admin',
    'role'     => 'admin',
    'grupo'    => 1001,
];
$furrielData = [
    'username' => 'furriel',
    'senha'    => 'furriel', // <--- MUDE A SENHA AQUI!!!!
    'nome_pg'  => 'Furriel',
    'role'     => 'furriel',
    'grupo'    => 1001,
];

// Gera o hash da senha para o admin
$userData['hash'] = password_hash($userData['senha'], PASSWORD_DEFAULT);
unset($userData['senha']);

// Gera o hash da senha para o furriel
$furrielData['hash'] = password_hash($furrielData['senha'], PASSWORD_DEFAULT);
unset($furrielData['senha']);

echo "Senha definida (Admin): " . $userData['senha'] . "\n";
echo "Hash gerado (Admin): " . $userData['hash'] . "\n\n";
echo "Senha definida (Furriel): " . $furrielData['senha'] . "\n";
echo "Hash gerado (Furriel): " . $furrielData['hash'] . "\n\n";

// --- PASSO 2: CONEXÃO COM O BANCO DE DADOS E CRIAÇÃO DAS TABELAS ---
try {
    $pdo = new PDO("sqlite:" . $dbPath);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    echo "Conectado ao banco de dados.\n";

    // CRIAÇÃO DA TABELA 'users'
    $sqlUsers = "
        CREATE TABLE IF NOT EXISTS users (
            id INTEGER PRIMARY KEY,
            username TEXT NOT NULL UNIQUE,
            password TEXT NOT NULL,
            nome_pg TEXT NOT NULL,
            role TEXT NOT NULL,
            grupo INTEGER NOT NULL
        );
    ";
    $pdo->exec($sqlUsers);
    echo "Tabela 'users' verificada/criada.\n";

    // CRIAÇÃO DA TABELA 'arranchados'
    $sqlArranchados = "
        CREATE TABLE IF NOT EXISTS arranchados (
            user_id INTEGER NOT NULL,
            data_refeicao TEXT NOT NULL,
            refeicao TEXT NOT NULL CHECK (refeicao IN ('cafe', 'almoco', 'janta')),
            PRIMARY KEY (user_id, data_refeicao, refeicao),
            FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE
        );
    ";
    $pdo->exec($sqlArranchados);
    echo "Tabela 'arranchados' verificada/criada.\n";
    
    // --- PASSO 3: INSERÇÃO DOS USUÁRIOS (APENAS SE NÃO EXISTIREM) ---
    // Prepara a consulta para inserção
    $sqlInsert = "
        INSERT INTO users (username, password, nome_pg, role, grupo)
        VALUES (:username, :password, :nome_pg, :role, :grupo);
    ";
    $stmtInsert = $pdo->prepare($sqlInsert);

    // Array de usuários a serem inseridos
    $usersToInsert = [$userData, $furrielData];
    
    foreach ($usersToInsert as $user) {
        $stmtCheck = $pdo->prepare("SELECT COUNT(*) FROM users WHERE username = :username");
        $stmtCheck->bindValue(':username', $user['username']);
        $stmtCheck->execute();
        $userCount = $stmtCheck->fetchColumn();

        if ($userCount > 0) {
            echo "\nO usuário '{$user['username']}' já existe. Nenhuma ação de inserção foi tomada.\n";
        } else {
            $stmtInsert->bindValue(':username', $user['username']);
            $stmtInsert->bindValue(':password', $user['hash']);
            $stmtInsert->bindValue(':nome_pg', $user['nome_pg']);
            $stmtInsert->bindValue(':role', $user['role']);
            $stmtInsert->bindValue(':grupo', $user['grupo']);
            $stmtInsert->execute();
            echo "\nSucesso: O usuário '{$user['username']}' foi inserido no banco de dados.\n";
        }
    }

} catch (PDOException $e) {
    echo "\nErro no banco de dados: " . $e->getMessage() . "\n";
} finally {
    $pdo = null;
}
?>