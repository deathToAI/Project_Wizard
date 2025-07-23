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
if (empty($_SESSION['token'])) {
    $_SESSION['token'] = bin2hex(random_bytes(32));
}
// Variável para armazenar a mensagem para o usuário
$feedback_message = '';
$feedback_type = '';


function listUsers(){
    echo '<h2>Usuários Cadastrados</h2>';
    try {
        $pdo = DbConnection();
        if ($pdo === null) {
            echo "<strong>ERRO:</strong> Não foi possível conectar ao banco de dados.";
            exit();
        }
        $stmt = $pdo->query("SELECT id, username, nome_pg, role, grupo FROM users");
        $users = $stmt->fetchAll(PDO::FETCH_ASSOC);
        echo "<table border='1'>
            <tr>
                <th>ID(database id)</th>
                <th>Usuario(username)</th>
                <th>Nome de Guerra(nome_pg)</th>
                <th>Tipo(role)</th>
                <th>Grupo(grupo)</th>
                <th>Ações</th>           
            </tr>";
        foreach ($users as $user) {
            echo "<tr>";
            echo "<td>" . htmlspecialchars($user['id']) . "</td>";
            echo "<td>" . htmlspecialchars($user['username']) . "</td>";
            echo "<td>" . htmlspecialchars($user['nome_pg']) . "</td>";
            echo "<td>" . htmlspecialchars($user['role']) . "</td>";
            echo "<td>" . htmlspecialchars($user['grupo']) . "</td>";
            echo "<td>
                    <a href='edit_user.php?action=edit&id=" . $user['id'] . "'>Editar</a> |
                    <a href='delete_user.php?action=delete&id=" . $user['id'] . "' onclick='return confirm(\"Tem certeza?\");'>Deletar</a>
                  </td>";
            echo "</tr>";

            echo "</tr>";
        }
        echo "</table>";

    } catch (PDOException $e) {
        echo "<strong>ERRO:</strong> " . htmlspecialchars($e->getMessage());
    }
}

?>
<!-- ===================-->
<!-- HTML (FRONT-END)   -->
<!-- ===================-->
<!DOCTYPE html>
<html>
<head>
    <title>Painel Admin</title>
    <link rel="stylesheet" href="../public/styles.css">
</head>
<body>
    <?php
    echo "<h1>Bem vindo,". htmlspecialchars($_SESSION['auth_data']['nome_pg']).",ao painel de Admin</h1>";
     ?>
    <h2>Gerenciamento de usuários</h2>
    <?php if ($feedback_message): ?>
        <div class="feedback <?php echo $feedback_type; ?>">
            <?php echo htmlspecialchars($feedback_message); ?>
        </div>
    <?php endif; ?>
<div id="Criar Usuario" class="tabcontent">
        <h3>Criar Usuário</h3>
        <p>Preencha os campos abaixo para criar um novo usuário:</p>
        <form action="create_user.php" method="GET">
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
        <input type="hidden" name="token" value="<?php $_SESSION['token']?>">
        <button type="submit">Criar Usuário</button>
    </form>
    </div>

    
<?php
// Chama a função para listar os usuários
listUsers();

?>
</body>
</html>