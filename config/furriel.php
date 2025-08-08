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
echo "<script> 
        function updateDateHeader(dateValue) {
            var formatter = new Intl.DateTimeFormat('pt-BR', {
                weekday: 'long',
                year: 'numeric',
                month: 'long',
                day: 'numeric'
            });
            
            // CORREÇÃO: Força a interpretação da data como local
            // Em vez de usar new Date(dateValue) diretamente
            var dateParts = dateValue.split('-'); // ['2025', '07', '05']
            var year = parseInt(dateParts[0]);
            var month = parseInt(dateParts[1]) - 1; // Mês em JavaScript é 0-indexado
            var day = parseInt(dateParts[2]);
            
            // Cria a data usando o construtor local
            var date = new Date(year, month, day);
            
            var formattedDate = formatter.format(date);
            document.getElementById('diaselecionado').innerHTML = 'Data: ' + formattedDate;
        }
        function initializeDateHeader() {
        var selectElement = document.getElementById('dia');
        updateDateHeader(selectElement.value);
    }
    
    document.addEventListener('DOMContentLoaded', initializeDateHeader);
    </script>";
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
        <select name=\"dia\" id=\"dia\" onchange=\"updateDateHeader(this.value)\">";
        foreach($dates as $index => $date){
            if($index == 0){
                echo "<option value=\"" . $date->format('Y-m-d') . "\" selected>" . $formatter->format($date) . "</option>";
            } else {
                echo "<option value=\"" . $date->format('Y-m-d') . "\">" . $formatter->format($date) . "</option>";
            }
        }
        echo "</select>";
       
        echo "<table align='center' width='80%' border='1'>
            <tr>
                <tr>
                    <th id=\"diaselecionado\" colspan=\"4\" align=\"center\">  </th> 
                     
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
            
            echo "<td>" . htmlspecialchars($user['nome_pg']) . "</td>";
            echo "<td>" . htmlspecialchars($user['cafe']) . "</td>";
            echo "<td>" . htmlspecialchars($user['almoco']) . "</td>";
            echo "<td>" . htmlspecialchars($user['janta']) . "</td>";
            echo "</tr>";

        }
        echo "</table>";
    } catch (PDOException $e) {
        echo "<strong>ERRO:</strong> " . htmlspecialchars($e->getMessage());
    }



?>