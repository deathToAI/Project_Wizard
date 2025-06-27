<html>
<body>
<?php 

$usuario = $_POST["usuario"];
$senha = $_POST["senha"];

if (empty($usuario)) {
    echo "Insira o usuario";

}elseif(empty($senha)){
    echo "Insira sua senha";

}else{
    echo "Seu usuario: $usuario <br>
    Sua senha: $senha";
}

 ?>
</body>
</html>
