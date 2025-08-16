<?php 
//update.php

// header("Access-Control-Allow-Origin: {$_SERVER['HTTP_ORIGIN']}");
// header('Access-Control-Allow-Credentials: true');
// header("Access-Control-Allow-Methods: GET, POST, OPTIONS");
// header("Access-Control-Allow-Headers: {$_SERVER['HTTP_ACCESS_CONTROL_REQUEST_HEADERS']}");

if ($_SERVER['REQUEST_METHOD'] === 'POST') {

    require_once __DIR__ . '/../../database/DbConnection.php';
    $selecao = json_decode($_POST['payload'] ?? '', true);
    $dia = $_POST['dia'] ?? null;
    if (!is_array($selecao)) $selecao = []; // trata JSON inválido

    try {
        $pdo = DbConnection();
        if ($pdo === null) {
            echo json_encode(['status' => 'error', 'message' => 'Não foi possível conectar ao banco de dados.']);
            exit();
        }
        //limpa a tabela antes  de salvar
        $sqldel = $pdo->prepare('DELETE FROM arranchados WHERE data_refeicao = :dia');
        $sqldel->execute([':dia' => $dia]);
        // Prepara a consulta para inserir ou atualizar os dados
        $stmt = $pdo->prepare(  'INSERT INTO arranchados (user_id, data_refeicao, refeicao)
            VALUES (:user_id, :data_refeicao, :refeicao)
            ON CONFLICT(user_id, data_refeicao, refeicao) DO NOTHING'
        );

        foreach ($selecao as $entrada) {
            $user_id = $entrada['user_id'];
            $data = $entrada['data_refeicao'];
            $refeicao = $entrada['refeicao'];
            // Executa a inserção ou atualização
            $stmt->execute([
                ':user_id' => $user_id,
                ':data_refeicao' => $data,
                ':refeicao' => $refeicao
            ]);
        }

        echo json_encode(['status' => 'success', 'message' => 'Dados salvos com sucesso.']);
    } catch (PDOException $e) {
        echo json_encode(['status' => 'error', 'message' => 'Erro ao salvar os dados: ' . $e->getMessage()]);
    }
    exit();
}



?>