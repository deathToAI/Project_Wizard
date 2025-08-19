<?php 
//config/furriel/furriel.php

if (session_status() !== PHP_SESSION_ACTIVE) {
    session_start();
}
//Verifica se há dados de autenticação
if (empty($_SESSION['auth_data']['role']) || $_SESSION['auth_data']['role'] !== 'furriel') {
        $_SESSION["erro"] = "Acesso negado. Você não tem permissão para acessar esta página.";
        header("Location:../../index.php");
        exit();
}
//Insere o cabbeçalho
include __DIR__ . '/../../lib/header.php';
// Inclui os arquivos necessários
require_once __DIR__ . '/../../database/DbConnection.php';

if (empty($_SESSION['token'])) {
    $_SESSION['token'] = bin2hex(random_bytes(32));
}

// Definir periodo de datas
$start = new DateTime('now');
$end = clone $start; // Clona o objeto para não alterar o original
$end->add(new DateInterval('P10D')); 
// Formatação de data usando IntlDateFormatter
$formatter = new IntlDateFormatter(
    'pt_BR',                      // Idioma/localidade
    IntlDateFormatter::FULL,     // Formato da data
    IntlDateFormatter::NONE,   // Formato da hora
    'America/Sao_Paulo',         // Fuso horário
    IntlDateFormatter::GREGORIAN // Calendário
);
//Array com datas
$interval = new DateInterval('P1D'); // Intervalo de 1 dia
$dates = new DatePeriod(
    $start, // Data de início
    $interval, // Intervalo de 1 dia
    $end // Data de fim
);
$today = $formatter->format($start);

echo "Data: ". $today;
echo '<h2>Usuários Arranchados</h2>';
    try {
        $pdo = DbConnection();
        if ($pdo === null) {
            echo "<strong>ERRO:</strong> Não foi possível conectar ao banco de dados.";
            exit();
        }
        //Array com usuarios
        $stmt = $pdo->query("SELECT id, username, nome_pg, role, grupo FROM users");
        $users = $stmt->fetchAll(PDO::FETCH_ASSOC);
        //Array com refeições
        $stmt_arranchados = $pdo->query("SELECT user_id, data_refeicao, refeicao FROM arranchados");
        $arranchados= $stmt_arranchados->fetchAll(PDO::FETCH_ASSOC);
        echo "<form>";
        echo "<div class=\"card\" align='center' id=\"formulario\">";
        echo "Selecione a data:<br>

        
        <select name=\"dia\" id=\"dia\" >";
        foreach($dates as $index => $date){
            if($index == 0){
                echo "<option value=\"" . $date->format('Y-m-d') . "\" selected>" . $formatter->format($date) . "</option>";
            } else {
                echo "<option value=\"" . $date->format('Y-m-d') . "\">" . $formatter->format($date) . "</option>";
            }
        }
        echo "</select>";
        echo "
            <div id='seltudo'>
            <div class='switch-container'>
                <label class='switch'>
                <input type='checkbox' id='tudoCafe'>
                <span class='slider round'></span>
                </label>
                <div>Todos: Café</div>
            </div>

            <div class='switch-container'>
                <label class='switch'>
                <input type='checkbox' id='tudoAlmoco'>
                <span class='slider round'></span>
                </label>
                <div>Todos: Almoço</div>
            </div>

            <div class='switch-container'>
                <label class='switch'>
                <input type='checkbox' id='tudoJanta'>
                <span class='slider round'></span>
                </label>
                <div>Todos: Janta</div>
            </div>
            </div>";
 
        echo "<table id=\"tabela\" align='center' width='80%' border='1'>

                <tr>
                    <th id=\"diaselecionado\" colspan=\"4\" align=\"center\">  </th> 
                     
                </tr>
                <th align=\"center\>Usuario(username)</th>
                <th align=\"center\>Nome de Guerra</th>
                <th align=\"center\">Cafe</th>
                <th align=\"center\">Almoço</th>
                <th align=\"center\">Janta</th>
     
            </tr>";
        //Recuperar as datas de refeições daquele usuario e marcar as checkbox nas datas que estiverem arranchados

        foreach ($users as $user) {
            echo "<tr class=\"user-row\" data-id=\"" . $user['id'] . "\">";
            
            echo "<td align=\"center\">" . htmlspecialchars($user['nome_pg']) . "</td>";
            echo "<td align=\"center\">" . "<input class=\"ck\" type=checkbox data-refeicao=\"cafe\" >" . "</td>";
            echo "<td align=\"center\">" . "<input class=\"ck\" type=checkbox data-refeicao=\"almoco\" >" . "</td>";
            echo "<td align=\"center\">" . "<input class=\"ck\" type=checkbox data-refeicao=\"janta\" >" . "</td>";
            echo "</tr>";

        }
        echo "</table>";
        echo "<br><button align=\"center\" id=\"btn_enviar\" type=\"button\" >Enviar</button>"; // Botao de save
        echo "</form>";
        echo "</div>";
    } catch (PDOException $e) {
        echo "<strong>ERRO:</strong> " . htmlspecialchars($e->getMessage());
    }
echo "<p id=\"resposta_bd\"></p>";


include __DIR__ . '/../../lib/footer.php';
?>