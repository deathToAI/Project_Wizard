<!DOCTYPE html>
<html>
    <head>
        <meta charset="UTF-8">
        
        <!-- <link rel="stylesheet" href="css/style.css">
        <link rel="stylesheet" href="css/bootstrap.min.css"> -->
</head>
<?php
date_default_timezone_set('America/Sao_Paulo');
setlocale(LC_TIME, 'pt_BR.UTF-8');

session_start();
if (!isset($_SESSION["usuario"]) || !isset($_SESSION["senha"])) {
    header("Location: login.php");
    exit();
}  
else {
    $usuario = $_SESSION["usuario"];
    echo "<h1>Bem vindo ao sistema, $usuario</h1>";
}
echo "<title>$usuario Dashboard</title>";


$start = new DateTime('now');
$end = new DateTime('now + 7 days');
// Formatação de data usando IntlDateFormatter
$formatter = new IntlDateFormatter(
    'pt_BR',                      // Idioma/localidade
    IntlDateFormatter::FULL,     // Formato da data
    IntlDateFormatter::NONE,   // Formato da hora
    'America/Sao_Paulo',         // Fuso horário
    IntlDateFormatter::GREGORIAN // Calendário
);

$interval = new DateInterval('P1D'); // Intervalo de 1 dia
$dates = [];
for($i = $start; $i <= $end; $i->add($interval)){
//   $dates[] = $i->format('d-m-Y l');
  $dates[] = $formatter->format($i);
}
?>

<body>
    <h2>Dashboard</h2>
    <p>Esta é a página do dashboard, onde você pode acessar as funcionalidades do sistema.</p>
    <a class="logout" href="logout.php" > Sair </a> <br>
    <form name="refeicoes" action="dashboard.php" method="post">
    <table border="1">
        <tr>
            <th align="center">Data</th>
            <th align="center">Cafe</th>
            <th align="center">Almoço</th>
            <th align="center">Janta</th>
        </tr>
        <?php
        $start = new DateTime('now');
        
        foreach ($dates as $index => $date) {
            $dataDMA = $start->format('d-m-Y');
            echo "<tr>";
            echo  '<td align="center">'.   $date . "</td>";   
            echo  '<td align="center">'. '<input name="cafe['.$dataDMA.']"  value="1" type="checkbox">'.    "</td>";
            echo  '<td align="center">'. '<input name="almoco['.$dataDMA.']" value="1" type="checkbox">'.    "</td>"; 
            echo  '<td align="center">'. '<input name="janta['.$dataDMA.']" value="1" type="checkbox">'.    "</td>"; 
            echo "</tr>";
            $start->add($interval); // Incrementa o dia
        }  
         ?>
    </table>
      <input type="submit" value="Enviar">
    </form>  
</body>
</html>

<?php 
echo "<h1>Área de testes</h1>";
echo "<table border='1'>";
echo "<tr><th>Data</th><th>Café</th><th>Almoço</th><th>Janta</th></tr>";

foreach ($dates as $index => $date) {
    echo "<tr>";
    echo "<td>$date</td>";
    
    
    // Verifica se o café foi marcado
    $cafeMarcado = isset($_POST['cafe'][$index]) ? 'Sim' : 'Não';
    echo "<td>$cafeMarcado</td>";
    
    // Verifica se o almoço foi marcado
    $almocoMarcado = isset($_POST['almoco'][$index]) ? 'Sim' : 'Não';
    echo "<td>$almocoMarcado</td>";
    
    // Verifica se a janta foi marcada
    $jantaMarcada = isset($_POST['janta'][$index]) ? 'Sim' : 'Não';
    echo "<td>$jantaMarcada</td>";
    
    echo "</tr>";
}

echo "</table>";
echo "<h1>Fim área de testes</h1>";
?>