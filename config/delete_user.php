<?php
// config/delete_user.php
require_once __DIR__ . '/../lib/DbConnection.php';
$id = filter_input(INPUT_GET, 'id', FILTER_VALIDATE_INT);
function deleteUser($userId) {
    $pdo = DbConnection(); // Conexão PDO

    if ($pdo === null) {
        return ['success' => false, 'message' => 'Erro na conexão com o banco de dados'];
    }

    try {
        $sql = "DELETE FROM users WHERE id = :id";
        $stmt = $pdo->prepare($sql);
        $stmt->bindParam(':id', $userId, PDO::PARAM_INT);
        $stmt->execute();

        return ['success' => true, 'message' => 'Usuário deletado com sucesso!'];

        exit();
    } catch (PDOException $e) {
        return ['success' => false, 'message' => 'Erro ao deletar usuário: ' . $e->getMessage()];
    }
}
$result = deleteUser($id);
$_SESSION['deleteUserResult'] = $result;
header('Location: admin.php');

?>