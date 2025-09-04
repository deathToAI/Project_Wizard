<?php
// Garante que a sessão seja iniciada em todas as páginas que incluem este cabeçalho.
// A verificação `session_status()` evita erros se a sessão já foi iniciada.
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
?>
<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <!-- O título pode ser dinâmico, mas deixaremos um padrão por enquanto -->
    <title>Sistema de Arranchamento</title>
    <!-- Caminho absoluto para o CSS para garantir que funcione em todas as páginas -->
    <link rel="stylesheet" href="/public/css/styles.css">
    <link rel="icon" type="image/x-icon" href="/public/img/favicon.ico">
</head>
<body>
    <header>
        
        <nav class="navbar">
            <!-- Links com caminhos absolutos para robustez -->
            <a href="/index.php" class="logo"><img class="header-icon" src="/public/img/header-icon.png" alt="Logo"> Arranchamento</a>
            <div class="navbar-right">
                <span id="relogio"></span>
                <ul class="menu">
                    <li><a href="/index.php">Início</a></li>
                    <?php if (isset($_SESSION['auth_data'])): // Verifica se o usuário está autenticado ?>
                        <?php if ($_SESSION['auth_data']['role'] === 'comum'): ?>
                            <li><a href="/public/dashboard.php">Minhas Seleções</a></li>
                        <?php elseif ($_SESSION['auth_data']['role'] === 'furriel'): ?>
                            <li><a href="/config/furriel/furriel.php">Painel Furriel</a></li>
                        <?php elseif ($_SESSION['auth_data']['role'] === 'admin'): ?>
                            <li><a href="/config/admin/admin.php">Painel Admin</a></li>
                        <?php endif; ?>
                        <li><a href="/lib/logout.php">Sair</a></li>
                    <?php endif; // Fim da verificação de autenticação ?>
                </ul>
                
            </div>
        </nav>
    </header>
    <main>
