<?php
// /config/furriel/auto_print_report.php
// Este script é projetado para ser executado via cron job para gerar e salvar
// o relatório de arranchamento diariamente.

// --- CONFIGURAÇÃO E DEPENDÊNCIAS ---

// Garante que o script não seja acessado via navegador e tenha um tempo de execução adequado.
if (php_sapi_name() !== 'cli') {
    http_response_code(403);
    die("Acesso negado. Este script deve ser executado a partir da linha de comando.");
}
set_time_limit(300); // 5 minutos de tempo de execução

require __DIR__ .'/../../vendor/autoload.php';
require __DIR__ .'/../../database/DbConnection.php';

date_default_timezone_set('America/Sao_Paulo');

// Importação de dependências do PhpSpreadsheet
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;

// --- LÓGICA PRINCIPAL ---

try {
    // --- 1. DEFINIR DATA E CAMINHOS ---
    $hoje = new DateTime('now');
    $diaSel = $hoje->format('Y-m-d');
    
    // Diretório base para os relatórios
    $baseReportDir = '/var/www/html/relatorios';
    // Diretório específico para o dia atual, conforme solicitado
    $dailyReportDir = $baseReportDir . '/' . $diaSel;

    // Cria o diretório do dia se ele não existir
    if (!is_dir($dailyReportDir)) {
        if (!mkdir($dailyReportDir, 0755, true)) {
            throw new Exception("Falha ao criar o diretório de relatórios: {$dailyReportDir}. Verifique as permissões.");
        }
    }

    // Define o nome e o caminho completo do arquivo
    $fileName = "Arranchamento-" . $hoje->format('d-m-Y') . ".xlsx";
    $filePath = $dailyReportDir . '/' . $fileName;

    echo "Iniciando a geração do relatório para {$diaSel}..." . PHP_EOL;
    echo "Salvando em: {$filePath}" . PHP_EOL;

    // --- 2. CONEXÃO COM O BANCO DE DADOS ---
    $pdo = DbConnection();
    if ($pdo === null) {
        throw new Exception("Não foi possível conectar ao banco de dados.");
    }

    // --- 3. LÓGICA DE GERAÇÃO DA PLANILHA (COPIADA DE report.php) ---
    
    $spreadsheet = new Spreadsheet();
    $sheet = $spreadsheet->getActiveSheet();
    $sheet->setTitle("Arranchados_" . $diaSel);

    $sheet->mergeCells("B1:D1");
    $spreadsheet->getDefaultStyle()->getFont()->setSize(12);
    $sheet->setCellValue("B1", "Arranchados para " . $hoje->format('d/m/Y'));
    $sheet->getStyle("B1")->getFont()->setSize(16)->setBold(true);
    $sheet->mergeCells("A1:A5");

    $brasaoPath = __DIR__ . '/../../public/img/brasao.png';
    if (file_exists($brasaoPath)) {
        $brasao = new \PhpOffice\PhpSpreadsheet\Worksheet\Drawing();
        $brasao->setName('BrasaoCiaCom');
        $brasao->setPath($brasaoPath);
        $brasao->setHeight(72)->setWidth(72)->setCoordinates('A1')->setWorksheet($sheet);
    }

    function recRef($dia, $refeicao, $pdo) {
        try {
            $stmt = $pdo->prepare("SELECT u.nome_pg FROM arranchados as a INNER JOIN users as u ON u.id = a.user_id WHERE a.data_refeicao = :dia AND a.refeicao = :refeicao AND u.role = 'comum'");
            $stmt->bindParam(':dia', $dia);
            $stmt->bindParam(':refeicao', $refeicao);
            $stmt->execute();
            return array_column($stmt->fetchAll(PDO::FETCH_ASSOC), 'nome_pg');
        } catch (PDOException $e) {
            throw new PDOException("Erro ao buscar dados para {$refeicao}: " . $e->getMessage());
        }
    }

    $cafe = recRef($diaSel, 'cafe', $pdo);
    $almoco = recRef($diaSel, 'almoco', $pdo);
    $janta = recRef($diaSel, 'janta', $pdo);

    $row = 2;
    function escreveRef(&$funcrow, $ref, $arrayRef, $funcsheet) {
        $funcSetRed = ["font" => ["bold" => true, "italic" => true, "color" => ["argb" => "FFFF0000"]]];
        $funcTitle = ["font" => ["bold" => true, "color" => ["argb" => "FF000000"], "size" => 16]];
        $funcsheet->mergeCells("B".$funcrow.":". "D".$funcrow);
        $funcsheet->setCellValue("B".$funcrow, $ref);
        $funcsheet->getStyle("B".$funcrow)->applyFromArray($funcTitle);
        $funcrow++;
        if (!$arrayRef) {
            $funcsheet->setCellValue("B".$funcrow, "Não há arranchados.");
            $funcsheet->getStyle("B".$funcrow)->applyFromArray($funcSetRed);
        } else {
            $data = array_chunk($arrayRef, 3, true);
            $funcsheet->fromArray($data, null, "B".$funcrow);
        }
        $funcrow = $funcsheet->getHighestRow() + 1;
    }

    escreveRef($row, 'Cafe(Total: ' . count($cafe) . ')', $cafe, $sheet);
    escreveRef($row, 'Almoço(Total: ' . count($almoco) . ')', $almoco, $sheet);
    escreveRef($row, 'Janta(Total: ' . count($janta) . ')', $janta, $sheet);

    $styleSet = [
        "font" => ["name" => "Times New Roman"],
        "alignment" => ["horizontal" => \PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER, "vertical" => \PhpOffice\PhpSpreadsheet\Style\Alignment::VERTICAL_CENTER],
        "borders" => ["allBorders" => ["borderStyle" => \PhpOffice\PhpSpreadsheet\Style\Border::BORDER_THIN, "color" => ["argb" => "FF000000"]]]
    ];
    $sheet->setCellValue("E1", "_____")->setCellValue("E2", "Visto")->getStyle("E1:E2")->applyFromArray($styleSet);
    $sheet->getStyle("B1:D".$row)->applyFromArray($styleSet);
    $sheet->getColumnDimension('B')->setAutoSize(true);
    $sheet->getColumnDimension('C')->setAutoSize(true);
    $sheet->getColumnDimension('D')->setAutoSize(true);
    $total = count($cafe) + count($almoco) + count($janta);
    if ($row == $sheet->getHighestRow()) {
        $sheet->mergeCells("B".($sheet->getHighestRow()+1) . ":D".($sheet->getHighestRow()+1));
        $sheet->setCellValue("B".$sheet->getHighestRow(), "Total: $total");
        $sheet->getStyle("B".$sheet->getHighestRow())->applyFromArray($styleSet);
    }
    $spreadsheet->getActiveSheet()->setPrintGridlines(true);

    // --- 4. CRIAÇÃO DA SEGUNDA ABA "VALE DIARIO" ---
    $spreadsheet->createSheet();
    $vale = $spreadsheet->setActiveSheetIndex(1);
    $vale->setTitle('Vale Diario');
    
    $vale->setCellValue('C1', 'visto')->setCellValue('H1', '3ª Cia Com Bld')->setCellValue('B3', 'Fisc Adm');
    $vale->setCellValue('F4', 'Vale diário para o dia')->setCellValue('I4', $hoje->format('d/m/Y'));
    $vale->setCellValue('B5', '')->setCellValue('C5', 'café')->setCellValue('D5', 'almoço')->setCellValue('E5', 'jantar');
    $vale->setCellValue('F5', 'etapas completas')->setCellValue('G5', 'a alimentar')->setCellValue('H5', 'A alimentar O OM');
    $vale->setCellValue('I5', 'Soma')->setCellValue('J5', 'tipo')->setCellValue('K5', 'quantidade');
    $vale->setCellValue('B6', 'oficiais')->setCellValue('B7', 'subten/sgt')->setCellValue('B8', 'cb/sd');
    $vale->mergeCells('C1:D1')->mergeCells('H1:I1')->mergeCells('B3:D3')->mergeCells('F4:H4')->mergeCells('I4:J4');

    $vale->setCellValue('C6', count($cafe)/3)->setCellValue('D6', count($almoco)/3)->setCellValue('E6', count($janta)/3);
    $vale->setCellValue('C7', count($cafe)/3)->setCellValue('D7', count($almoco)/3)->setCellValue('E7', count($janta)/3);
    $vale->setCellValue('C8', count($cafe)/3)->setCellValue('D8', count($almoco)/3)->setCellValue('E8', count($janta)/3);
    $vale->setCellValue('G6', count($cafe)/3)->setCellValue('G7', count($almoco)/3)->setCellValue('G8', count($janta)/3);

    $vale->setCellValue('F6', '=LARGE(C6:E6, 1)')->setCellValue('F7', '=LARGE(C7:E7, 1)')->setCellValue('F8', '=LARGE(C8:E8, 1)');
    $vale->setCellValue('I6', '=SUM(F6:H6)')->setCellValue('I7', '=SUM(F7:H7)')->setCellValue('I8', '=SUM(F8:H8)');
    $vale->setCellValue('B10', 'SOMA')->setCellValue('C10', '=SUM(C6:C8)')->setCellValue('D10', '=SUM(D6:D8)');
    $vale->setCellValue('E10', '=SUM(E6:E8)')->setCellValue('F10', '=SUM(F6:F8)')->setCellValue('G10', '=SUM(G6:G8)');
    $vale->setCellValue('H10', '=SUM(H6:H8)')->setCellValue('I10', '=SUM(I6:I8)');

    $vale->getStyle('H1')->getAlignment()->setHorizontal(\PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER);
    $vale->getStyle('B3')->getAlignment()->setHorizontal(\PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER);
    $vale->getStyle('F4')->getAlignment()->setHorizontal(\PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER);
    $vale->getStyle('C1')->getAlignment()->setHorizontal(\PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER);
    $styleArray = ['borders' => ['allBorders' => ['borderStyle' => \PhpOffice\PhpSpreadsheet\Style\Border::BORDER_THIN, 'color' => ['argb' => 'FF000000']]]];
    $vale->getStyle('B5:I8')->applyFromArray($styleArray);
    $vale->getStyle('B10:I10')->applyFromArray($styleArray);

    // --- 5. SALVAR O ARQUIVO ---
    $spreadsheet->setActiveSheetIndex(0); // Volta para a primeira aba antes de salvar
    $writer = new Xlsx($spreadsheet);
    $writer->save($filePath);

    echo "Relatório gerado e salvo com sucesso!" . PHP_EOL;

} catch (Exception $e) {
    // Em caso de erro, registra a mensagem no stderr para que possa ser capturada pelos logs do cron
    $timestamp = date('Y-m-d H:i:s');
    file_put_contents('php://stderr', "[{$timestamp}] ERRO: " . $e->getMessage() . PHP_EOL, FILE_APPEND);
    exit(1); // Sai com um código de erro
}

exit(0); // Sai com sucesso

?>