<?php
// config/admin.php

if (session_status() !== PHP_SESSION_ACTIVE) {
    session_start();
}

// 2. Verifica se há dados de autenticação
if (empty($_SESSION['auth_data']['role']) || $_SESSION['auth_data']['role'] !== 'admin') {
        $_SESSION["erro"] = "Acesso negado. Você não tem permissão para acessar esta página.";
        header("Location:../index.php");
        exit();
}
// Inclui os arquivos necessários
require_once __DIR__ . '/../lib/DbConnection.php';

header("X-Frame-Options: DENY");
header("Content-Security-Policy: default-src 'self'");


// // CSRF Token
// if (empty($_SESSION['csrf_token'])) {
//     $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
// }

//CRIAR USUARIO
function createUser($username, $password, $nome_pg, $role,$grupo) {
    $pdo = DbConnection(); // Conexão PDO

    if ($pdo === null) {
    $_SESSION["erro"] = "Erro na conexão com o banco de dados";
    header("Location:../index.php");
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
    
    }catch (PDOException $e){
            if ($e->getCode()==='23000'){
                return ['success' => false, 'message' => 'Erro: O nome de usuário já existe.'];

            }else{
                // Em produção, você logaria o erro detalhado em vez de exibi-lo
                return ['success' => false, 'message' => 'Erro de banco de dados: ' . $e->getMessage()];
            }
    }
}

// Variável para armazenar a mensagem para o usuário
$feedback_message = '';
$feedback_type = ''; // 'success' ou 'error'

// Verifica se o formulário de criação de usuário foi enviado
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['create_user'])) {
    // Coleta os dados do formulário
    $username = trim($_POST['username'] ?? '');
    if (!preg_match('/^[a-zA-Z0-9_]{3,20}$/', $username)) {
        return ['success' => false, 'message' => 'Username inválido'];
    }
    $password = $_POST['password'] ?? '';
    $nome_pg = $_POST['nome_pg'] ?? '';
    $role = $_POST['role'] ?? 'comum';
    $grupo = (int)$_POST['grupo']; // Converte para inteiro
    if ($grupo !== 1 && $grupo !== 2) {
        die("Grupo inválido!");
    }
    // Chama a função para criar o usuário
    $result = createUser($username, $password, $nome_pg, $role, $grupo);

    // Verifica o resultado e define a mensagem de feedback
    if ($result['success']) {
        $feedback_type = 'success';
    } else {
        $feedback_type = 'error';
    }
    $feedback_message = $result['message'];
}

?>
<!-- ===================-->
<!-- HTML (FRONT-END)   -->
<!-- ===================-->
<!DOCTYPE html>
<html>
<head>
    <title>Painel Admin</title>
</head>
<body>
    <?php
    echo "<h1>Bem vindo,". $_SESSION['auth_data']['nome_pg'].",ao painel de Admin</h1>";
     ?>
    <h1>Criar Usuário</h1>

    <?php if ($feedback_message): ?>
        <div class="feedback <?php echo $feedback_type; ?>">
            <?php echo htmlspecialchars($feedback_message); ?>
        </div>
    <?php endif; ?>

    <form action="admin.php" method="POST">
        <input type="hidden" name="create_user" value="1">
        <p>
            <label for="username">Username:</label><br>
            <input type="text" id="username" name="username" required>
        </p>
        <p>
            <label for="password">Password:</label><br>
            <input type="password" id="password" name="password" required>
        </p>
        <p>
            <label for="nome_pg">Nome de Guerra:</label><br>
            <input type="text" id="nome_pg" name="nome_pg" required>
        </p>
        <p>
            <label for="grupo">Grupo:</label><br>
            <select type="number" id="grupo" name="grupo" required >
                <option value=1> Of/Sgt </option>
                <option value=2> Cb/Sd </option>
            </select>
        </p>
        <p>
            <label for="role">Tipo:</label><br>
            <select id="role" name="role" required>
                <option value="comum">Comum</option>
                <option value="furriel">Furriel</option>
                <option value="admin">Admin</option>
            </select>
        </p>
        <button type="submit">Criar Usuário</button>
    </form>

</body>
</html>