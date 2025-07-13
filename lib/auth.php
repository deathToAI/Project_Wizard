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
$usercheck = $pdo->prepare("SELECT COUNT(*) FROM users WHERE username = :username");
$usercheck->bindParam(':username', $usuario);
$usercheck->execute();
if (!$usercheck->fetchColumn()) {
    $_SESSION["erro"] = "Usuário não existe";
    // Redireciona para a página de login
    header("Location:../index.php");
    exit();
} else {
    // Verifica se a senha está correta
    $stmt = $pdo->prepare("SELECT password FROM users WHERE username = :username");
    $stmt->bindParam(':username', $usuario);
    $stmt->execute();
    //Retorna array associativo com os dados em uma array associativa(ex:['password' => 'hash_da_senha_do_banco'] )
    $user = $stmt->fetch(PDO::FETCH_ASSOC);
    
    if ($user && password_verify($senha, $user['password'])) {
        // Senha correta, inicia sessão
        $_SESSION["usuario"] = htmlspecialchars($usuario);
        $_SESSION["role"] = $user['role'];
        $_SESSION["grupo"] = $user['grupo'];
        if($usuario === 'admin'){
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
}


?>



?>