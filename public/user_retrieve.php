<?php 
//user_info.php
require_once __DIR__ . '/../database/DbConnection.php';
if (session_status() !== PHP_SESSION_ACTIVE) {
    session_start();
}
if ($_SERVER['REQUEST_METHOD'] == 'GET'){
//Preencher checkboxes
    try {
        if (!isset($_GET['dias'])) {
            throw new Exception('Parâmetro "dia" não informado');
        }
        $dia = $_GET['dia'];
        if (!isset($_SESSION["auth_data"]["id"])) {
            throw new Exception('Usuário não autenticado');
        }
        $id = $_SESSION["auth_data"]["id"];
        $dias = json_decode($_GET['dias'] ?? '', true);

        $pdo = DbConnection();
        $stmt = $pdo->prepare('SELECT * from arranchados WHERE user_id = :id AND data_refeicao = :dia');
        $stmt->execute([':id' => $id,':dia' => $dia]);
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