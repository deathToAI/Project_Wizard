<?php
// config/delete_user.php
if (session_status() !== PHP_SESSION_ACTIVE) {
    session_start();
}
require_once __DIR__ . '/../../lib/Logger.php';
require_once __DIR__ . '/../../database/DbConnection.php';
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

        $admin_username = $_SESSION['auth_data']['username'] ?? 'sistema';
        log_message("Admin '{$admin_username}' deletou o usuário com ID '{$userId}'.", 'INFO');
        return ['success' => true, 'message' => 'Usuário deletado com sucesso!'];

        exit();
    } catch (PDOException $e) {
        return ['success' => false, 'message' => 'Erro ao deletar usuário: ' . $e->getMessage()];
    }
}
$result = deleteUser($id);
$_SESSION['deleteUserResult'] = $result;
header('Location: admin.php');
exit();

?>