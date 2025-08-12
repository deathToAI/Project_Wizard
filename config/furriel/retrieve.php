<?php


if ($_SERVER['REQUEST_METHOD'] === 'GET'){

    require_once __DIR__.'/../../database/DbConnection.php';
    try {
        $date = $_GET['date'];
        $pdo = DbConnection();
        $stmt = $pdo->prepare("SELECT * FROM arranchados WHERE data_refeicao = :date");
        $stmt->bindParam(':date', $date);
        $stmt->execute();
        $result = $stmt->fetchAll(PDO::FETCH_ASSOC);
        echo json_encode($result);
    } catch (PDOException $e) {
        echo "<strong>ERRO:</strong> " . htmlspecialchars($e->getMessage());
    }

}



?>