<?php 
// File: lib/auth.php
//AUTENTICADOR

if (!isset($_SESSION)) {
    session_start();
}


// Caso o usuário já esteja logado e não é admin, redireciona para a dashboard
require_once '../lib/DbConnection.php';
$pdo = DbConnection();
if ($pdo === null) {
    $_SESSION["erro"] = "Erro na conexão com o banco de dados";
    header("Location:../index.php");
    exit();
}
$usuario = htmlspecialchars($_POST["usuario"]) ?? '';
$senha = htmlspecialchars($_POST["senha"]) ?? '';

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    $_SESSION['erro'] = "Método de requisição inválido";
    // Redireciona para a página de login
    header("HTTP/1.1 405 Method Not Allowed");
    header("Location:../index.php");
    exit();
}
//User Vazio
if (empty($usuario) || empty($senha)) {
    $_SESSION["erro"] = "Usuario ou senha não informados";
    header('../index.php');
}
//User nao existe
$usercheck = $pdo->prepare("SELECT id, username, password, role, grupo, nome_pg FROM users WHERE username = :username");
$usercheck->bindParam(':username', $usuario, PDO::PARAM_STR);
$usercheck->execute();
$user = $usercheck->fetch(PDO::FETCH_ASSOC);

if (!$user) {
    $_SESSION["erro"] = "Credenciais não encontradas";
    // Redireciona para a página de login
    header("Location:../index.php");
    exit();
}
// Verifica se a senha está correta
if ($user && password_verify($senha, $user['password'])) {
        // Senha correta, inicia sessão
        session_regenerate_id(true); // Gera um novo ID de sessão para segurança
        $_SESSION["usuario"] = [
            'id' => $user['id'],
            'username' => htmlspecialchars($user['username']),
            'role' => $user['role'],
            'grupo' => $user['grupo'],
            'nome_pg' => $user['nome_pg']

        ];

        if($role === 'admin'){
            header("Location:admin.php");
        }else{
            header("Location:dashboard.php");
        }
        exit();
    } else {
        $_SESSION["erro"] = "Senha incorreta";
        header("Location:../index.php");
        exit();
    }


?>



?>