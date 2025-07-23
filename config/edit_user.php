<?php
// edit_user.php
$id = $_GET['id'] ?? null;
if (!$id) {
    die("ID do usuário não fornecido.");
    header("Location: admin.php");
}

$username = trim($_POST['username'] ?? '');

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

</body>

</html>