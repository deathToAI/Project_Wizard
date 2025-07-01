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

?>
<?php 
echo "<h1> area de testes </h1>";
$start = new DateTime('now');
$end = new DateTime('now + 7 days');
//Create a DateInterval object with a 1 day interval
$interval = new DateInterval('P1D');
$dates = [];
for($i = $start; $i <= $end; $i->add($interval)){
  $dates[] = $i->format('d-m-Y');
}
// Formatação de data usando IntlDateFormatter
$formatter = new IntlDateFormatter(
    'pt_BR',                      // Idioma/localidade
    IntlDateFormatter::FULL,     // Formato da data
    IntlDateFormatter::NONE,   // Formato da hora
    'America/Sao_Paulo',         // Fuso horário
    IntlDateFormatter::GREGORIAN // Calendário
);
foreach ($dates as $date) {
    echo $formatter->format($date) . "<br>";
    print_r( $date );
    
}


echo "<h1> fim area de testes </h1>";



$hoje = new DateTime();
$hoje = $formatter ->format($hoje);
echo "<h2>Usando formatter hoje é: $hoje</h2>";


?>

<body>
    <h2>Dashboard</h2>
    <p>Esta é a página do dashboard, onde você pode acessar as funcionalidades do sistema.</p>
    <a class="logout" href="logout.php" > Sair </a>
</body>
</html>