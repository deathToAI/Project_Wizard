
<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Arranchamento</title>
    <link rel="stylesheet" href="style.css"> <!-- Se tiver CSS -->
</head>
<body>
    <header>
        <nav class="navbar">
            <a href="index.php" class="logo">Arranchamento</a>
            <ul class="menu">
                <li><a href="../index.php">Início</a></li>
                <li><a href="selecionar.php">Seleções</a></li>
                <?php if ($_SESSION['auth_data']['role'] === 'furriel'):
                    echo '<li><a href="relatorios.php">Relatórios</a></li>'; ?>
                <?php endif; ?>
                <li><a href="logout.php">Sair</a></li>
            </ul>
        </nav>
    </header>
    <main>
