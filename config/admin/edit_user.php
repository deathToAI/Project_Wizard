<?php
// config/edit_user.php
if (session_status() !== PHP_SESSION_ACTIVE) {
    session_start();
}

// Verifica se há dados de autenticação
if (empty($_SESSION['auth_data']['role']) || $_SESSION['auth_data']['role'] !== 'admin') {
        $_SESSION["erro"] = "Acesso negado. Você não tem permissão para acessar esta página.";
        header("Location:../index.php");
        exit();
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    error_log("Dados recebidos: " . print_r($_POST, true));
    
    if (!isset($_POST['token']) || $_POST['token'] !== $_SESSION['token']) {
        error_log("Token inválido: Sessão=".$_SESSION['token']." Recebido=".($_POST['token']??'vazio'));
        $_SESSION['editUserResult'] = ['success' => false, 'message' => 'Token inválido'];
        header("Location: admin.php");
        exit();
    }
    $id = filter_input(INPUT_POST, 'id', FILTER_VALIDATE_INT);
    if (!$id) {
        $_SESSION['editUserResult'] = ['success' => false, 'message' => 'ID do usuário não fornecido'];
        header("Location: admin.php");
        exit();
}
}

// Conexão PDO
require_once __DIR__ . '/../../database/DbConnection.php';
$pdo = DbConnection(); 

if ($pdo === null) {
    return null;
}
// Função para editar usuário
function editUser($id,$username, $password, $nome_pg, $role,$grupo){
    $pdo = DbConnection(); // Conexão PDO

    if ($pdo === null) {
        return ['success' => false, 'message' => 'Erro na conexão com o banco de dados'];
    }

    if (empty($username) || empty($password) || empty($nome_pg) || empty($role) || !isset($grupo)) {
        return ['success' => false, 'message' => 'Todos os campos são obrigatórios.'];
    }

    try {
        $enc_password = password_hash($password, PASSWORD_DEFAULT);
        $sql = "UPDATE users SET username = :username, password = :password, 
                nome_pg = :nome_pg, role = :role, grupo = :grupo WHERE id = :id";
        $stmt = $pdo->prepare($sql);
        $stmt->bindParam(':id', $id, PDO::PARAM_INT);
        $stmt->bindParam(':username', $username);
        $stmt->bindParam(':password', $enc_password);
        $stmt->bindParam(':nome_pg', $nome_pg);
        $stmt->bindParam(':role', $role);
        $stmt->bindParam(':grupo', $grupo);
        $stmt->execute();

        return ['success' => true, 'message' => 'Usuário editado com sucesso!'];
    } catch (PDOException $e) {
        return ['success' => false, 'message' => 'Erro ao editar usuário: ' . $e->getMessage()];
    }
}
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $id = filter_input(INPUT_POST, 'id', FILTER_VALIDATE_INT);
    $username = trim($_POST['username'] ?? '');
    
    if (!preg_match('/^[a-zA-Z0-9_]{3,20}$/', $username)) {
        $_SESSION['editUserResult'] = ['success' => false, 'message' => 'Username inválido'];
    } else {
        $password = $_POST['password'] ?? '';
        $nome_pg = $_POST['nome_pg'] ?? '';
        $role = $_POST['role'] ?? 'comum';
        $grupo = (int)$_POST['grupo'];
        
        if ($grupo !== 1 && $grupo !== 2) {
            $_SESSION['editUserResult'] = ['success' => false, 'message' => 'Grupo inválido!'];
        } else {
            $result = editUser($id, $username, $password, $nome_pg, $role, $grupo);
            $_SESSION['editUserResult'] = $result;
        }
    }
    header("Location: admin.php");
    exit();
}
?>