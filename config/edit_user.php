<?php
// config/edit_user.php

$id = filter_input(INPUT_GET, 'id', FILTER_VALIDATE_INT);
if (!$id) {
    die("ID do usuário não fornecido.");
    header("Location: admin.php");
}
require_once __DIR__ . '/../lib/DbConnection.php';

$pdo = DbConnection(); // Conexão PDO

if ($pdo === null) {
    return null;
}

try {
    $sql = "SELECT username, password, nome_pg, role, grupo FROM users WHERE id = :id";
    $stmt = $pdo->prepare($sql);
    $stmt->bindParam(':id', $id, PDO::PARAM_INT);
    $stmt->execute();
    $userdata = $stmt->fetch(PDO::FETCH_ASSOC);
} catch (PDOException $e) {
    return null;
}



function editUser($username, $password, $nome_pg, $role,$grupo){
    $pdo = DbConnection(); // Conexão PDO

    if ($pdo === null) {
        return ['success' => false, 'message' => 'Erro na conexão com o banco de dados'];
    }

    if (empty($username) || empty($password) || empty($nome_pg) || empty($role) || !isset($grupo)) {
        return ['success' => false, 'message' => 'Todos os campos são obrigatórios.'];
    }

    try {
        $enc_password = password_hash($password, PASSWORD_DEFAULT);
        $sql = "UPDATE users SET password = :password, nome_pg = :nome_pg, role = :role, grupo = :grupo WHERE username = :username";
        $stmt = $pdo->prepare($sql);
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
?>
<!DOCTYPE html>
<html>
<head>
<link rel="stylesheet" href="../public/styles.css">
<title>Editar Usuário - <?php echo $username ?></title>
</head>
<body>
<h2>Editar Usuário - <?php echo htmlspecialchars($userdata['username']); ?></h2>
<form action="edit_user.php" method="POST">
    <input type="hidden" name="id" value="<?php echo htmlspecialchars($id); ?>">
    <p>
        <label for="username">Usuário:</label>
        <input type="text" id="username" name="username" value="<?php echo htmlspecialchars($userdata['username']); ?>" required>
    </p>
    <p>
        <label for="password">Senha:</label>
        <input type="password" id="password" name="password" value="<?php echo htmlspecialchars($userdata['password']); ?>" required>
    </p>
    <p>
        <label for="nome_pg">Nome de Guerra:</label>
        <input type="text" id="nome_pg" name="nome_pg" value="<?php echo htmlspecialchars($userdata['nome_pg']); ?>" required>
    </p>
    <p>
        <label for="role">Função:</label>
        <select id="role" name="role">
            <option value="comum" <?php echo $userdata['role'] === 'comum' ? 'selected' : ''; ?>>Comum</option>
            <option value="admin" <?php echo $userdata['role'] === 'admin' ? 'selected' : ''; ?>>Admin</option>
            <option value="furriel" <?php echo $userdata['role'] === 'furriel' ? 'selected' : ''; ?>>Furriel</option>
        </select>
    </p>
    <p>
        <label for="grupo">Grupo:</label>
        <select id="grupo" name="grupo">
            <option value="1" <?php echo $userdata['grupo'] == 1 ? 'selected' : ''; ?>>Of/Sgt</option>
            <option value="2" <?php echo $userdata['grupo'] == 2 ? 'selected' : ''; ?>>Cb/Sd</option>
        </select>
    </p>
    <p>
        <input type="submit" value="Salvar">
    </p>
</form>
<?php
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $username = trim($_POST['username'] ?? '');
    if (!preg_match('/^[a-zA-Z0-9_]{3,20}$/', $username)) {
        echo "<div class='error'>Username inválido</div>";
    } else {
        $password = $_POST['password'] ?? '';
        $nome_pg = $_POST['nome_pg'] ?? '';
        $role = $_POST['role'] ?? 'comum';
        $grupo = (int)$_POST['grupo']; // Converte para inteiro
        if ($grupo !== 1 && $grupo !== 2) {
            die("Grupo inválido!");
        }
        // Chama a função para editar o usuário
        $result = editUser($username, $password, $nome_pg, $role, $grupo);
        // Verifica o resultado e define a mensagem de feedback
        if ($result['success']) {
            $feedback_type = 'success';
        } else {
            $feedback_type = 'error';
        }
        $feedback_message = $result['message'];
        echo "<div class='feedback $feedback_type'>" . htmlspecialchars($feedback_message) . "</div>";
        // Redireciona para evitar reenvio do formulário
        header("Location: admin.php");
        exit();
    }
}
?>

</body>

</html>