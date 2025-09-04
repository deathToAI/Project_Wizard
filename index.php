
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
<div class="card">
<h1 style="text-align: center;">Bem vindo ao sistema</h1>
<div class="login-container">
    <img src="/public/img/giuseppe256.png" alt="Logo do Sistema" class="login-logo">
    <form action="lib/auth.php" method="POST"> 
        
        <label for="usuario">Usuario</label>
        <input type="text" id="usuario" name="usuario" placeholder="sgtfulano"><br>

        <label for="password" >Senha</label>
        <input type="password" id="password" name="senha" placeholder="Senha"> <br>
        <input type="hidden" name="token" value="<?php echo htmlspecialchars($_SESSION['token']); ?>">

        <input type="submit" value="Login" name="login">
    </form>
</div>
<?php if (!empty($erro)): ?>
<div class="login-error"> <?php echo $erro; ?>  </div>
<?php endif; ?>
</div>

<?php 

include __DIR__ . '/lib/footer.php';

?>
