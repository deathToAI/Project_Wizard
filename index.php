<html>
<body>
<?php
if (!isset($_SESSION)) {
    session_start();
}
if (empty($_SESSION["token"])) {
    $_SESSION["token"] = bin2hex(random_bytes(32));
}
// Verifica se o formulário foi enviado
$erro = '';
if (isset($_SESSION["erro"])) {
    $erro = $_SESSION["erro"];
    echo "$_SESSION[erro]";
    unset($_SESSION["erro"]); // Limpa o erro após exibir
}
?>

<h1>Bem vindo ao sistema </h1>
<form action="lib/auth.php" method="POST"> 
    
<label for="usuario">Usuario</label>
<input type="text" name="usuario" placeholder="sgtfulano"><br>


<label for="password" >Senha</label>
<input type="password" name="senha" placeholder="Senha"> <br>
<input type="hidden" name="token" value="<?php $_SESSION['token']?>">

<input type="submit" value="Login" name="login">
<?php if (!empty($erro)): ?>
<div style="color:red;"> <?php echo $erro; ?>  </div>
<?php endif; ?>

</form>


<?php 
echo "Teste de sistema: <br>" ;
echo "token: $_SESSION[token]";
echo "<br>Role: " . (isset($_SESSION['auth_data']['role']) ? $_SESSION['auth_data']['role'] : 'não autenticado') . "<br>";

?>
</body>
</html>
