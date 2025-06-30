<!DOCTYPE html>
<html>
<?php

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
<body>
    <h2>Dashboard</h2>
    <p>Esta é a página do dashboard, onde você pode acessar as funcionalidades do sistema.</p>
    <a class="logout" href="logout.php" > Sair </a>
</body>
</html>