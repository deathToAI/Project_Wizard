<?php
// config/create_user.php

if (session_status() !== PHP_SESSION_ACTIVE) {
    session_start();
}
if (empty($_SESSION['token'])) {
    $_SESSION['token'] = bin2hex(random_bytes(32));
}
if (!isset($_GET['create_user'])) {
    die("Ação inválida.");
}
require_once __DIR__ . '/../lib/DbConnection.php';
function createUser($username, $password, $nome_pg, $role,$grupo) {
    $pdo = DbConnection(); // Conexão PDO

    if ($pdo === null) {
    $_SESSION["erro"] = "Erro na conexão com o banco de dados";
    exit();
   }
    
    if (empty($username) || empty($password) || empty($nome_pg) || empty($role) || !isset($grupo)) {
            return ['success' => false, 'message' => 'Todos os campos são obrigatórios.'];
        }
    try {
    $pdo = DbConnection();//Conexao PDO

    //Criptografa o password
    $enc_password = password_hash($password, PASSWORD_DEFAULT);
    $sql="INSERT INTO users (username, password, nome_pg, role, grupo) VALUES (:username, :password, :nome_pg, :role, :grupo);";
    $stmt = $pdo->prepare($sql);
    $stmt->bindParam(':username', $username);
    $stmt->bindParam(':password', $enc_password);
    $stmt->bindParam(':nome_pg', $nome_pg);
    $stmt->bindParam(':role', $role);
    $stmt->bindParam(':grupo', $grupo);   
    $stmt->execute();

    return ['success' => true, 'message' => 'Usuário criado com sucesso!'];
    exit();
    
    }catch (PDOException $e){
            if ($e->getCode()==='23000'){
                return ['success' => false, 'message' => 'Erro: O nome de usuário já existe.'];

            }else{
                // Em produção, você logaria o erro detalhado em vez de exibi-lo
                return ['success' => false, 'message' => 'Erro de banco de dados: ' . $e->getMessage()];
            }
    }
}
$username = trim($_GET['username'] ?? '');
if (!preg_match('/^[a-zA-Z0-9_]{3,20}$/', $username)) {
    return ['success' => false, 'message' => 'Username inválido'];
}
$password = $_GET['password'] ?? '';
$nome_pg = $_GET['nome_pg'] ?? '';
$role = $_GET['role'] ?? 'comum';
$grupo = (int)$_GET['grupo']; // Converte para inteiro
if ($grupo !== 1 && $grupo !== 2) {
    die("Grupo inválido!");
}
// Chama a função para criar o usuário
$result = createUser($username, $password, $nome_pg, $role, $grupo);
// Armazena o resultado na sessão
$_SESSION['createUserResult']= $result;
// Redireciona para a página de administração
header('Location: admin.php');

?>