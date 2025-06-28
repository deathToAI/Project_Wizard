<html>
<body>
<?php 
if(!isset($_SESSION)) {
    session_start();
}
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    $_SESSION['erro'] = "Método de requisição inválido";
    // Redireciona para a página de login
    header("HTTP/1.1 405 Method Not Allowed");
    header("Location:../index.php");
    exit();
}

$usuario = htmlspecialchars($_POST["usuario"]);
$senha = htmlspecialchars($_POST["senha"]);

if (empty($usuario)) {
    // echo "Insira o usuario";
    $_SESSION["erro"] = "Insira o usuario";
    // Redireciona para a página de login
    header("Location:../index.php");

}elseif(empty($senha)){
    // echo "Insira sua senha";
    $_SESSION["erro"] = "Insira a senha";
    // Redireciona para a página de login
    header("Location:../index.php");

}else{
    echo "Redirecionando para o dashboard...<br>";
    echo "Usuario: $usuario <br>";
    
    $_SESSION["usuario"] = $usuario;
    $_SESSION["senha"] = $senha;
    // Redireciona para a página de dashboard
    header("Location:dashboard.php");
    exit();
}

 ?>
</body>
</html>
