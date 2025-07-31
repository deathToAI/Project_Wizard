<?php
require_once 'lib/DbConnection.php';

try {
    // Mostra configurações
    $config = require 'config/database.php';
    echo "Driver: " . $config['driver'] . "<br>";
    echo "Caminho: " . $config['database_path'] . "<br>";
    
    // Testa conexão
    $pdo = DbConnection();
    echo "Conexão OK!<br>";
    
    // Testa consulta
    $tables = $pdo->query("SELECT name FROM sqlite_master WHERE type='table'")->fetchAll();
    echo "Tabelas encontradas: " . count($tables);
    
} catch (Exception $e) {
    echo "<strong>ERRO:</strong> " . $e->getMessage();
}
$html = "<!DOCTYPE html>
<html>
<head>
    <title>Painel Admin</title>
</head>
<body>
    <h1>Bem vindo, " . htmlspecialchars($_SESSION['auth_data']['nome_pg']) . ", ao painel de Admin</h1>
    <h2>Gerenciamento de usuários</h2>
    " . ($feedback_message ? "
        <div class=\"feedback $feedback_type\">
            " . htmlspecialchars($feedback_message) . "
        </div>
    " : "") . "
    <div id=\"Criar Usuario\" class=\"painel\">
        <h3>Criar Usuário</h3>
        <p>Preencha os campos abaixo para criar um novo usuário:</p>
        <form action=\"create_user.php\" method=\"POST\">
            <input type=\"hidden\" name=\"create_user\" value=\"1\">
            <p>
                <label for=\"username\">Username:</label><br>
                <input type=\"text\" id=\"username\" name=\"username\" placeholder=\"sgtfulano\" required>
            </p>
            <p>
                <label for=\"password\">Password:</label><br>
                <input type=\"password\" id=\"password\" name=\"password\" required>
            </p>
            <p>
                <label for=\"nome_pg\">Nome de Guerra:</label><br>
                <input type=\"text\" id=\"nome_pg\" name=\"nome_pg\" placeholder=\"Sgt Fulano\" required>
            </p>
            <p>
                <label for=\"grupo\">Grupo:</label><br>
                <select id=\"grupo\" name=\"grupo\" required>
                    <option value=\"1\">Of/Sgt</option>
                    <option value=\"2\">Cb/Sd</option>
                </select>
            </p>
            <p>
                <label for=\"role\">Tipo:</label><br>
                <select id=\"role\" name=\"role\" required>
                    <option value=\"comum\">Comum</option>
                    <option value=\"furriel\">Furriel</option>
                    <option value=\"admin\">Admin</option>
                </select>
            </p>
            <input type=\"hidden\" name=\"token\" value=\"" . $_SESSION['token'] . "\">
            <button type=\"submit\">Criar Usuário</button>
        </form>
    </div>";

echo $html;
phpinfo();

?>