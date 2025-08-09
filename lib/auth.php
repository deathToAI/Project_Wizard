<?php 
// File: lib/auth.php
//AUTENTICADOR

if (session_status() !== PHP_SESSION_ACTIVE) {
    session_start();
}

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    $_SESSION['erro'] = "Método de requisição inválido";
    // Redireciona para a página de login
    header("HTTP/1.1 405 Method Not Allowed");
    header("Location:../index.php");
    exit();
}

//User Vazio
if (empty($_POST['usuario']) || empty($_POST['senha'])) {
    $_SESSION["erro"] = "Usuario ou senha não informados";
    header("Location: ../index.php");
}
// Caso o usuário já esteja logado e não é admin, redireciona para a dashboard
require_once '../database/DbConnection.php';
$pdo = DbConnection();
if ($pdo === null) {
    $_SESSION["erro"] = "Erro na conexão com o banco de dados";
    header("Location:../index.php");
    exit();
}
$username = htmlspecialchars($_POST['usuario']);
$userpass = $_POST['senha'];

//User nao existe
$usercheck = $pdo->prepare("SELECT id, username, password, role, grupo, nome_pg FROM users WHERE username = :username");
$usercheck->bindParam(':username', $username, PDO::PARAM_STR);
$usercheck->execute();
$user = $usercheck->fetch(PDO::FETCH_ASSOC);

if (!$user) {
    $_SESSION["erro"] = "Credenciais não encontradas";
    // Redireciona para a página de login
    header("Location:../index.php");
    exit();
}
// Verifica se a senha está correta
if (password_verify($userpass, $user['password'])) {
        // Senha correta, inicia sessão
        session_regenerate_id(true); // Gera um novo ID de sessão para segurança
        $_SESSION["auth_data"] = [
            'id' => $user['id'],
            'username' => htmlspecialchars($user['username']),
            'role' => $user['role'],
            'grupo' => $user['grupo'],
            'nome_pg' => $user['nome_pg']

        ];

// 8. Redirecionamento seguro
        switch ($user['role']) {
            case 'admin':
                $_SESSION["admin"] = true;
                $redirect = '../config/admin/admin.php';
                break;
            case 'furriel':
                $_SESSION["furriel"] = true;
                $redirect = '../config/furriel/furriel.php';
                break;
            case 'comum':
                $_SESSION["comum"] = true;
                $redirect = '../public/dashboard.php';

                break;
        }
        header("Location: " . $redirect);
        exit();
        
    }else {
        $_SESSION["erro"] = "Senha incorreta";
        header("Location:../index.php");
        exit();
    }


?>


