<?php
// lib/change_password.php

if (session_status() !== PHP_SESSION_ACTIVE) {
    session_start();
}

// Inclui dependências
require_once __DIR__ . '/../database/DbConnection.php';
require_once __DIR__ . '/Logger.php';

// 1. Verificações de Segurança
// Verifica se o usuário está autenticado
if (!isset($_SESSION["auth_data"]['id'])) {
    $_SESSION["erro"] = "Acesso negado. Por favor, faça o login.";
    header("Location: ../index.php");
    exit();
}

// Verifica se o método da requisição é POST
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header("Location: ../public/dashboard.php");
    exit();
}

// 2. Obtém dados do POST e da sessão
$userId = $_SESSION["auth_data"]['id'];
$username = $_SESSION["auth_data"]['username'];
$currentPassword = $_POST['current_password'] ?? '';
$newPassword = $_POST['new_password'] ?? '';
$confirmPassword = $_POST['confirm_password'] ?? '';

// 3. Validação de Entrada
if (empty($currentPassword) || empty($newPassword) || empty($confirmPassword)) {
    $_SESSION['password_change_status'] = "Todos os campos são obrigatórios.";
    header("Location: ../public/dashboard.php");
    exit();
}

if ($newPassword !== $confirmPassword) {
    $_SESSION['password_change_status'] = "A nova senha e a confirmação não coincidem.";
    header("Location: ../public/dashboard.php");
    exit();
}

// 4. Lógica do Banco de Dados
$pdo = DbConnection();
if ($pdo === null) {
    $_SESSION['password_change_status'] = "Erro: Não foi possível conectar ao banco de dados.";
    log_message("Falha na conexão com o banco de dados ao tentar mudar a senha para o usuário ID: {$userId}", 'ERROR');
    header("Location: ../public/dashboard.php");
    exit();
}

try {
    // Busca o hash da senha atual do usuário
    $stmt = $pdo->prepare("SELECT password FROM users WHERE id = ?");
    $stmt->execute([$userId]);
    $user = $stmt->fetch(PDO::FETCH_ASSOC);

    if (!$user || !password_verify($currentPassword, $user['password'])) {
        $_SESSION['password_change_status'] = "A senha atual está incorreta.";
        log_message("Tentativa de mudança de senha falhou para o usuário '{$username}'. Senha atual incorreta.", 'WARNING');
        header("Location: ../public/dashboard.php");
        exit();
    }

    // Gera o hash da nova senha
    $newPasswordHash = password_hash($newPassword, PASSWORD_DEFAULT);

    // Atualiza a senha no banco de dados
    $updateStmt = $pdo->prepare("UPDATE users SET password = ? WHERE id = ?");
    $updateStmt->execute([$newPasswordHash, $userId]);

    $_SESSION['password_change_status'] = "Senha alterada com sucesso!";
    log_message("Senha alterada com sucesso para o usuário '{$username}'.", 'INFO');

} catch (PDOException $e) {
    $_SESSION['password_change_status'] = "Erro no banco de dados ao tentar alterar a senha.";
    log_message("PDOException ao alterar senha para o usuário '{$username}': " . $e->getMessage(), 'ERROR');
}

// 5. Redireciona de volta para o dashboard
header("Location: ../public/dashboard.php");
exit();
