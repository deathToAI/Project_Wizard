<?php 
//config/furriel.php

// //Verifica se há dados de autenticação
// if (empty($_SESSION['auth_data']['role']) || $_SESSION['auth_data']['role'] !== 'furriel') {
//         $_SESSION["erro"] = "Acesso negado. Você não tem permissão para acessar esta página.";
//         header("Location:../index.php");
//         exit();
// }

// Inclui os arquivos necessários
require_once __DIR__ . '/../lib/DbConnection.php';

// if (empty($_SESSION['token'])) {
//     $_SESSION['token'] = bin2hex(random_bytes(32));
// }

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
$interval = new DateInterval('P1D'); // Intervalo de 1 dia
$dates = new DatePeriod(
    $start, // Data de início
    $interval, // Intervalo de 1 dia
    $end // Data de fim
);
$today = $formatter->format($start);
echo $today;

echo '<h2>Usuários Arranchados</h2>';
    try {
        $pdo = DbConnection();
        if ($pdo === null) {
            echo "<strong>ERRO:</strong> Não foi possível conectar ao banco de dados.";
            exit();
        }
        $stmt = $pdo->query("SELECT id, username, nome_pg, role, grupo FROM users");
        $users = $stmt->fetchAll(PDO::FETCH_ASSOC);
        echo "<Selecione a data>
        <select>";
        foreach($dates as $date){
            echo "<option value=". $date->format('Y-m-d') .">" . $formatter->format($date) . "</option>";
        }
        echo "</select>";

        echo "<table border='1'>
            <tr>
                <tr>
                    <th colspan=\"5\" align=\"center\">Data</th>
                </tr>
                <th align=\"center\>Usuario(username)</th>
                <th align=\"center\>Nome de Guerra</th>
                <th align=\"center\">Cafe</th>
                <th align=\"center\">Almoço</th>
                <th align=\"center\">Janta</th>
        </tr>       
            </tr>";
        //Recuperar as datas de refeições daquele usuario e marcar as checkbox nas datas que estiverem arranchados
        
        foreach ($users as $user) {
            echo "<tr>";
            echo "<td>" . htmlspecialchars($user['username']) . "</td>";
            echo "<td>" . htmlspecialchars($user['nome_pg']) . "</td>";
            echo "<td>" . htmlspecialchars($user['cafe']) . "</td>";
            echo "<td>" . htmlspecialchars($user['grupo']) . "</td>";
            echo "<td>
                    
                  </td>";
            echo "</tr>";

        }
        echo "</table>";

    } catch (PDOException $e) {
        echo "<strong>ERRO:</strong> " . htmlspecialchars($e->getMessage());
    }



?>