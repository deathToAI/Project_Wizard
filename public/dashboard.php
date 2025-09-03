<?php
date_default_timezone_set('America/Sao_Paulo');
setlocale(LC_TIME, 'pt_BR.UTF-8');
if (session_status() !== PHP_SESSION_ACTIVE) {
    session_start();
}

// Proteção de página: verifica se o usuário está autenticado
if (!isset($_SESSION["auth_data"]['id']) || empty($_SESSION["auth_data"]['nome_pg'])) {
    $_SESSION["erro"] = "Acesso negado. Por favor, faça o login.";
    header("Location: ../index.php");
    exit();
}

$usuario = htmlspecialchars($_SESSION["auth_data"]['nome_pg']);
$id = $_SESSION["auth_data"]['id'];

// Inclui o cabeçalho da página
include __DIR__ . '/../lib/header.php';


//Verifica se já passou do meio dia
// Se já passou do meio dia, inicia o intervalo a partir do dia seguinte
// Se não passou do meio dia, inicia o intervalo a partir de hoje
$limite = new DateTime('today 12:00:00');
$momento = new DateTime('now');
$start = null;
if ($momento > $limite) {
    $start = new DateTime('now + 1 day');
} else {
    $start = new DateTime('now');
} ;
$end = clone $start; // Clona o objeto para não alterar o original
$end->add(new DateInterval('P7D')); 
// Formatação de data usando IntlDateFormatter
$formatter = new IntlDateFormatter(
    'pt_BR',                      // Idioma/localidade
    IntlDateFormatter::FULL,     // Formato da data
    IntlDateFormatter::NONE,   // Formato da hora
    'America/Sao_Paulo',         // Fuso horário
    IntlDateFormatter::GREGORIAN // Calendário
);
$interval = new DateInterval('P1D'); // Intervalo de 1 dia
$dates = new DatePeriod(
    $start, // Data de início
    $interval, // Intervalo de 1 dia
    $end // Data de fim
);

?>

<h1 id="idusuario" data-id="<?php echo $id; ?>">Bem vindo ao sistema, <?php echo $usuario; ?></h1>

<h2>Arranchamento</h2>
<p>
    <?php echo "Hoje é ".$formatter->format($momento); ?>
</p>

<div id="selecao" class="card" style="margin-top: 20px;">
    <h3 align="center">Selecione as datas de arranchamento abaixo: </h3>
    <!-- Formulário  -->
    <form name="refeicoes">
        <table id="tabela" align="center" width="50%" border="1">
            <thead>
                <tr>
                    <th align="center">Data</th>
                    <th align="center">Café</th>
                    <th align="center">Almoço</th>
                    <th align="center">Janta</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($dates as $d): ?>
                    <tr>
                        <td class="dia" align="center" data-date="<?php echo $d->format('Y-m-d'); ?>"><?php echo $formatter->format($d); ?></td>
                        <td align="center"><input name="cafe" value="<?php echo $d->format('Y-m-d'); ?>" type="checkbox"></td>
                        <td align="center"><input name="almoco" value="<?php echo $d->format('Y-m-d'); ?>" type="checkbox"></td>
                        <td align="center"><input name="janta" value="<?php echo $d->format('Y-m-d'); ?>" type="checkbox"></td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
        <div style="text-align: center; margin-top: 20px;">
            <input id="enviar" class="btn_enviar" type="submit" value="Enviar" name="salvar">
        </div>
    </form> 
</div>

<h2 style="margin-top: 40px;">Mudar Senha</h2>
<div id="change-password" class="card" style="margin-top: 20px;">
    <h3 align="center">Altere sua senha</h3>
    <?php if (isset($_SESSION['password_change_status'])): ?>
        <p align="center" style="color: <?= strpos($_SESSION['password_change_status'], 'sucesso') !== false ? 'green' : 'red'; ?>;">
            <?= htmlspecialchars($_SESSION['password_change_status']); ?>
        </p>
        <?php unset($_SESSION['password_change_status']); ?>
    <?php endif; ?>
    <form action="../lib/change_password.php" method="POST" name="changePasswordForm">
        <table align="center" width="50%" border="0" style="border-collapse: separate; border-spacing: 0 10px;">
            <tr>
                <td align="right" style="padding-right: 10px;"><label for="current_password">Senha Atual:</label></td>
                <td><input type="password" id="current_password" name="current_password" required></td>
            </tr>
            <tr>
                <td align="right" style="padding-right: 10px;"><label for="new_password">Nova Senha:</label></td>
                <td><input type="password" id="new_password" name="new_password" required></td>
            </tr>
            <tr>
                <td align="right" style="padding-right: 10px;"><label for="confirm_password">Confirmar Nova Senha:</label></td>
                <td><input type="password" id="confirm_password" name="confirm_password" required></td>
            </tr>
        </table>
        <div style="text-align: center; margin-top: 20px;">
            <input id="change_password_submit" class="btn_enviar" type="submit" value="Mudar Senha">
        </div>
    </form> 
</div>
<?php include __DIR__ . '/../lib/footer.php'; ?>