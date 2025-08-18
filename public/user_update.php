
<?php
// user_update.php
// Script responsável por atualizar as refeições arranchadas de um usuário

// Verifica se o método da requisição é POST
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    // Redireciona de volta para a dashboard
    header("Location: ../public/dashboard.php");
    exit();
}
// Recupera as datas selecionadas e as refeições arranchadas
//forma: Função savetable enviará JSON:{"cafe":["2025-08-20"],"almoco":["2025-08-23"],"janta":["2025-08-21"]} dashboard.js:112:17
$json = file_get_contents('php://input');
$data = json_decode($json, true);
$id = $data['id']; // ID do usuário
$cafe = $data['cafe'] ;
$almoco = $data['almoco'];
$janta = $data ['janta'];


// Conecta-se ao banco de dados
require_once __DIR__ . '/../database/DbConnection.php';
$pdo = DbConnection();

// Verifica se a conexão com o banco foi bem-sucedida
if ($pdo === null) {
    // Redireciona de volta para a dashboard
    header("Location: ../public/dashboard.php");
    exit();
}

try {
    // Deleta as refeições existentes para o usuário
    $stmt = $pdo->prepare("DELETE FROM arranchados WHERE user_id = ?");
    $stmt->execute([$id]);

    // Inserir dados de café
    foreach ($cafe as $data) {
        $stmt = $pdo->prepare("INSERT INTO arranchados (user_id, data_refeicao, refeicao) VALUES (?, ?, 'cafe')");
        $stmt->execute([$id, $data]);
    }

    // Inserir dados de almoço
    foreach ($almoco as $data) {
        $stmt = $pdo->prepare("INSERT INTO arranchados (user_id, data_refeicao, refeicao) VALUES (?, ?, 'almoco')");
        $stmt->execute([$id, $data]);
    }

    // Inserir dados de janta
    foreach ($janta as $data) {
        $stmt = $pdo->prepare("INSERT INTO arranchados (user_id, data_refeicao, refeicao) VALUES (?, ?, 'janta')");
        $stmt->execute([$id, $data]);
    }

    // Redireciona de volta para a dashboard
    header("Location: ../public/dashboard.php");
    exit();
} catch (PDOException $e) {
    ob_clean();
    $erro = array('mensagem' => 'Erro ao enviar formulário: ' . $e->getMessage());
    header('Content-Type: application/json');
    echo json_encode($erro);
    exit();
}
