<!DOCTYPE html>
<html>
    <head>
        <meta charset="UTF-8">
        <!-- Relogio -->
<script>
   document.addEventListener('DOMContentLoaded', function() {
            // Todo o código JavaScript que interage com o DOM vai aqui dentro
            function atualizarRelogio() {
                const agora = new Date();
                let horas = agora.getHours();
                let minutos = agora.getMinutes();
                let segundos = agora.getSeconds();

                horas = horas < 10 ? '0' + horas : horas;
                minutos = minutos < 10 ? '0' + minutos : minutos;
                segundos = segundos < 10 ? '0' + segundos : segundos;

                const horaFormatada = `${horas}:${minutos}:${segundos}`;
                document.getElementById('relogio').innerHTML = horaFormatada;
            }

            atualizarRelogio();
            setInterval(atualizarRelogio, 1000);
        });

</script>
</head>
<?php
date_default_timezone_set('America/Sao_Paulo');
setlocale(LC_TIME, 'pt_BR.UTF-8');
if (session_status() !== PHP_SESSION_ACTIVE) {
    session_start();
}
if (!isset($_SESSION["auth_data"]['nome_pg']) || empty($_SESSION["auth_data"]['nome_pg'])) {
    $_SESSION["erro"] = "Usuario ou senha nao informados";
    header("Location: ../index.php");
    exit();
}  
else {
    $usuario = $_SESSION["auth_data"]['nome_pg'];
    echo "<h1 id=\"idusuario\" data-id=\"".$_SESSION["auth_data"]['id']. "\">Bem vindo ao sistema, $usuario</h1>";

    echo "<title>$usuario Dashboard</title>";
}

//Verifica se já passou do meio dia
// Se já passou do meio dia, inicia o intervalo a partir do dia seguinte
// Se não passou do meio dia, inicia o intervalo a partir de hoje
$limite = new DateTime('today 12:00:00');
$momento = new DateTime('now');
$start = null;
if ($momento > $limite) {
    $start = new DateTime('now + 1 day');
} else {
    $start = new DateTime('now');
} ;
$end = clone $start; // Clona o objeto para não alterar o original
$end->add(new DateInterval('P7D')); 
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

?>

<body>
    <h2>Arranchamento</h2>
    <?php echo "Hoje é ".$formatter->format($momento); ?>
    <div id="relogio">
        <!-- A hora será exibida aqui -->
        Carregando relógio...
    </div>
    <a class="logout" href="../lib/logout.php" > Sair </a> <br>

    <h3 align="center">Selecione as datas de arranchamento abaixo: </h3>
    <form name="refeicoes" action="dashboard.php" method="post">
    <table id="tabela" align="center" width="50%" border="1">
        <tr>
            <th align="center">Data</th>
            <th align="center">Cafe</th>
            <th align="center">Almoço</th>
            <th align="center">Janta</th>
        </tr>
        <?php
      
        foreach ($dates as $index => $d) {
            
            echo "<tr>";
            echo  '<td id="dia" align="center" value="'.$d->format('Y-m-d').' ">'.   $formatter->format($d) . "</td>";   
            echo  '<td align="center">'. '<input name="cafe['.$d->format('Y-m-d').']"   type="checkbox">'.    "</td>";
            echo  '<td align="center">'. '<input name="almoco['.$d->format('Y-m-d').']"  type="checkbox">'.    "</td>"; 
            echo  '<td align="center">'. '<input name="janta['.$d->format('Y-m-d').']"  type="checkbox">'.    "</td>"; 
            echo "</tr>";

        }  
         ?>
    </table>
      <input type="submit" value="Enviar">
    </form>  
</body>
</html>

<?php 
//DEPURANDO AQUI
echo "<h1>Área de testes</h1>";

echo "$usuario arranchado para:<br>";
echo "<ul>";
echo "Café:";
    foreach($_POST['cafe'] as $data => $valor) {
        echo "<li>$data" . "</li>";
    } ;
echo "Almoço:";
    foreach($_POST['almoco'] as $data => $valor) {
        echo "<li>$data" . "</li>";
    } ;
echo "Janta:";
    foreach($_POST['janta'] as $data => $valor) {
        echo "<li>$data" . "</li>";
    } ;
echo "</ul>";
echo "<h1>Fim área de testes</h1>";
?>