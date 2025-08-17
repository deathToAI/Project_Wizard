<?php 
//user_info.php
require_once __DIR__ . '/../database/DbConnection.php';
if (session_status() !== PHP_SESSION_ACTIVE) {
    session_start();
}
header('Content-Type: application/json');
if ($_SERVER['REQUEST_METHOD'] == 'GET'){
//Preencher checkboxes
    try {
        if (!isset($_GET['dias'])) {
            throw new Exception('Parâmetro "dia" não informado');
        }
        $dia = $_GET['dias'];
        if (!isset($_SESSION["auth_data"]["id"])) {
            throw new Exception('Usuário não autenticado');
        }
        $id = $_GET['id'] ?? ($_SESSION["auth_data"]["id"] ?? null);
            if (!$id) {
                throw new Exception('Usuário não autenticado');
            }
        $dias = json_decode($_GET['dias'] ?? '', true);
            if (!is_array($dias) || empty($dias)) {
                throw new Exception('Parâmetro "dias" ausente ou inválido');
            }

        $pdo = DbConnection();
        $ph = implode(',', array_fill(0, count($dias), '?'));
        $sql = "SELECT user_id, data_refeicao, refeicao
                FROM arranchados
                WHERE user_id = ? AND data_refeicao IN ($ph)";
        $stmt = $pdo->prepare($sql);
        $stmt->execute(array_merge([$id], $dias));
        $result = $stmt->fetchAll(PDO::FETCH_ASSOC);

        if (empty($result)) {
            $result = [];
        }
        echo json_encode($result);

    }//Fim try
    catch (PDOException $e) {
    header('Content-Type: application/json');
    echo json_encode(['status' => 'error', 'message' => 'Erro ao salvar os dados: ' . $e->getMessage()]);
    exit;
    }//Fim catch

}//Fim if GET


?>