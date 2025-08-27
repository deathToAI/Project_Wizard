<?php
// config/admin.php

if (session_status() !== PHP_SESSION_ACTIVE) {
    session_start();
}

// 2. Verifica se há dados de autenticação
if (empty($_SESSION['auth_data']['role']) || $_SESSION['auth_data']['role'] !== 'admin') {
        $_SESSION["erro"] = "Acesso negado. Você não tem permissão para acessar esta página.";
        header("Location:../../index.php");
        exit();
}

// Inclui o cabeçalho da página. Isso já inicia o HTML, head, e o body.
include __DIR__ . '/../../lib/header.php';

// Inclui os arquivos necessários
require_once __DIR__ . '/../../database/DbConnection.php';

// // CSRF Token
if (empty($_SESSION['token'])) {
    $_SESSION['token'] = bin2hex(random_bytes(32));
}
// Variável para armazenar a mensagem para o usuário
$feedback_message = '';
$feedback_type = '';

function listUsers(){
    echo '<div class="card"><h2>Usuários Cadastrados</h2>';
    try {
        $pdo = DbConnection();
        if ($pdo === null) {
            echo "<strong>ERRO:</strong> Não foi possível conectar ao banco de dados.";
            exit();
        }
        $stmt = $pdo->query("SELECT id, username, nome_pg, role, grupo FROM users WHERE username NOT IN ('admin', 'furriel')");
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
                    <a href='#' data-action='edit' data-user='".htmlspecialchars(json_encode($user), ENT_QUOTES, 'UTF-8')."'>✏️Editar</a> |
                    <a href='delete_user.php?action=delete&id=" . $user['id'] . "' onclick='return confirm(\"Tem certeza?\");'>🗑️ Deletar</a>
                  </td>";
            echo "</tr>";

        }
        echo "</table> </div>";

    } catch (PDOException $e) {
        echo "<strong>ERRO:</strong> " . htmlspecialchars($e->getMessage());
    }
}

?>
<!-- ===================-->
<!-- HTML (FRONT-END)   -->
<!-- ===================-->
<?php
echo "<h1>Bem vindo,". htmlspecialchars($_SESSION['auth_data']['nome_pg']).",ao painel de Admin</h1>";
?>
    <h2>Gerenciamento de usuários</h2>
    <?php if ($feedback_message): ?>
        <div class="card feedback <?php echo $feedback_type; ?>">
            <?php echo htmlspecialchars($feedback_message); ?>
        </div>
    <?php endif; ?>
<div id="create_user" class="card painel">
        <h3>Criar Usuário</h3>
        <p>Preencha os campos abaixo para criar um novo usuário:</p>
        <form action="create_user.php" method="POST">
        <input type="hidden" name="create_user" value="1">
        <p>
            <label for="username">Username:</label><br>
            <input type="text" id="username" name="username" placeholder="sgtfulano" required>
        </p>
        <p>
            <label for="password">Password:</label><br>
            <input type="password" id="password" name="password" required>
        </p>
        <p>
            <label for="nome_pg">Nome de Guerra:</label><br>
            <input type="text" id="nome_pg" name="nome_pg" placeholder="Sgt Fulano" required>
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
        <input type="hidden" name="token" value="<?php echo $_SESSION['token'];?>">
        <button type="submit">Criar Usuário</button>
    </form>
    </div>

    
<?php
// Verifica o feedback da criação do usuário
if (isset($_SESSION['createUserResult'])) {
    echo "<p>" . htmlspecialchars($_SESSION['createUserResult']['message']) . "</p>";
    unset($_SESSION['createUserResult']); // Limpa a mensagem após exibi-la
}
// Verifica o feedback da deleção do usuário
if (isset($_SESSION['deleteUserResult'])) {
    echo "<p>" . htmlspecialchars($_SESSION['deleteUserResult']['message']) . "</p>";
    unset($_SESSION['deleteUserResult']); // Limpa a mensagem após exibi-la
}
if (isset($_SESSION['editUserResult'])) {
    echo "<p>" . htmlspecialchars($_SESSION['editUserResult']['message']) . "</p>";
    unset($_SESSION['editUserResult']); // Limpa a mensagem após exibi-la
}
function define_modal(){
    echo '<div id="editModal" class="editModal">
        <div class="modal-content">
            <span class="close-btn">&times;</span>
            <form id="editForm" method="POST" action="edit_user.php">
                <input type="hidden" name="id" id="editId"><br>
                <input type="hidden" name="token" value="'.$_SESSION['token'].'">
                
                <div class="form-group">
                    <label for="username">Usuário:</label>
                    <input type="text" id="username" name="username" required>
                </div>
                
                <div class="form-group">
                    <label for="nome_pg">Nome de Guerra:</label>
                    <input type="text" id="nome_pg" name="nome_pg" required>
                </div>
                
                <div class="form-group">
                    <label for="password">Senha:</label>
                    <input type="password" id="password" name="password" required>
                </div>
                
                <div class="form-group">
                    <label for="role">Função:</label>
                    <select id="role" name="role" required>
                        <option value="comum">Comum</option>
                        <option value="admin">Admin</option>
                        <option value="furriel">Furriel</option>
                    </select>
                </div>
                
                <div class="form-group">
                    <label for="grupo">Grupo:</label>
                    <select id="grupo" name="grupo" required>
                        <option value="1">Of/Sgt</option>
                        <option value="2">Cb/Sd</option>
                    </select>
                </div>
                
                <div class="form-actions">
                    <button type="submit" class="btn btn-primary">Salvar</button>
                    <button type="button" class="btn btn-secondary">Cancelar</button>
                </div>
            </form>
        </div>
    </div>';
}
// Chama a função para listar os usuários

define_modal();
listUsers();

// Inclui o rodapé da página
include __DIR__ . '/../../lib/footer.php';
?>
