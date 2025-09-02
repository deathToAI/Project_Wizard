
<?php
// session_start() must be called before any output.
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

include __DIR__ . '/lib/header.php';

if (empty($_SESSION["token"])) {
    $_SESSION["token"] = bin2hex(random_bytes(32));
}
// Verifica se há uma mensagem de erro na sessão.
if (isset($_SESSION["erro"])) {
    $erro = $_SESSION["erro"];
    unset($_SESSION["erro"]); // Limpa o erro após exibir
}
?>
<div class="card" align="center">
<h1>Bem vindo ao sistema </h1>
<form action="lib/auth.php" method="POST"> 
    
<label for="usuario">Usuario</label>
<input type="text" name="usuario" placeholder="sgtfulano"><br>


<label for="password" >Senha</label>
<input type="password" name="senha" placeholder="Senha"> <br>
<input type="hidden" name="token" value="<?php echo htmlspecialchars($_SESSION['token']); ?>">

<input type="submit" value="Login" name="login">
<?php if (!empty($erro)): ?>
<div style="color:red;"> <?php echo $erro; ?>  </div>
<?php endif; ?>

</form>
<div>

<?php 

include __DIR__ . '/lib/footer.php';

?>
