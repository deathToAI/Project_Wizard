<?php 
//update.php

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
        $pdo->beginTransaction();
        //limpa a tabela antes  de salvar
        $sqldel = $pdo->prepare('DELETE FROM arranchados WHERE data_refeicao = :dia');
        $sqldel->execute([':dia' => $dia]);
        // Prepara a consulta para inserir ou atualizar os dados
        $stmt = $pdo->prepare(  'INSERT INTO arranchados (user_id, data_refeicao, refeicao)
            VALUES (:user_id, :data_refeicao, :refeicao)
            ON CONFLICT(user_id, data_refeicao, refeicao) DO NOTHING'
        );
        $start = microtime(true);
        if ($selecao) {
        // constrói placeholders (?,?,?),(?,?,?),...
        $values = [];
        $params = [];
        foreach ($selecao as $r) {
            // segurança mínima de tipos
            $uid = (int)$r['user_id'];
            $ref = (string)$r['refeicao']; // 'cafe'|'almoco'|'janta'
            $values[] = '(?, ?, ?)';
            $params[] = $uid;
            $params[] = $dia;
            $params[] = $ref;
        }
        $sql = 'INSERT INTO arranchados (user_id, data_refeicao, refeicao) VALUES '.implode(',', $values);

        // Para Postgres/SQLite com UNIQUE(user_id,data_refeicao,refeicao):
        //  - Postgres: append "ON CONFLICT (user_id, data_refeicao, refeicao) DO NOTHING"
        //  - SQLite:  append "ON CONFLICT(user_id, data_refeicao, refeicao) DO NOTHING"
        // Se você já faz DELETE do dia inteiro, o CONFLICT é desnecessário.
        $stmt = $pdo->prepare($sql);
        $stmt->execute($params);
        }
        $pdo->commit();
        $elapsed = round((microtime(true)-$start)*1000,1);
        echo json_encode(['status' => 'success', 'message' => "OK em {$elapsed} ms"]);
    } catch (PDOException $e) {
        echo json_encode(['status' => 'error', 'message' => 'Erro ao salvar os dados: ' . $e->getMessage()]);
    }
    exit();
}



?>
