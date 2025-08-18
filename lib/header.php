
<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <!-- O título pode ser dinâmico, mas deixaremos um padrão por enquanto -->
    <title>Sistema de Arranchamento</title>
    <!-- Caminho absoluto para o CSS para garantir que funcione em todas as páginas -->
    <link rel="stylesheet" href="/public/css/styles.css">
</head>
<body>
    <header>
        <nav class="navbar">
            <!-- Links com caminhos absolutos para robustez -->
            <a href="/index.php" class="logo">Arranchamento</a>
            <div class="navbar-right">
                <ul class="menu">
                    <li><a href="/index.php">Início</a></li>
                    <li><a href="/public/dashboard.php">Minhas Seleções</a></li>
                    <?php if (isset($_SESSION['auth_data']['role']) && $_SESSION['auth_data']['role'] === 'furriel'): ?>
                        <li><a href="/config/furriel/furriel.php">Painel Furriel</a></li>
                    <?php endif; ?>
                    <?php if (isset($_SESSION['auth_data']['role']) && $_SESSION['auth_data']['role'] === 'admin'): ?>
                        <li><a href="/config/admin/admin.php">Painel Admin</a></li>
                    <?php endif; ?>
                    <li><a href="/lib/logout.php">Sair</a></li>
                </ul>
                <span id="relogio"></span>
            </div>
        </nav>
    </header>
    <main>
