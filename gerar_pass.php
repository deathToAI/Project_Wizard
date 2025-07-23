<?php
// gerar_hash.php da senha do admin
// modo de uso: php gerar_hash.php
$senha_admin = 'C@mole'; // <--- MUDE A SENHA DE ADMIN!!!!
$hash = password_hash($senha_admin, PASSWORD_DEFAULT);
echo "Senha: " . $senha_admin . "\n";
echo "Hash gerado: " . $hash . "\n";
?>
