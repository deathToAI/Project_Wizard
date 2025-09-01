<?php

require __DIR__ .'/../../vendor/autoload.php';
require __DIR__ .'/../../database/DbConnection.php';

date_default_timezone_set('America/Sao_Paulo');
//Importação de dependências
use PhpOffice\PhpSpreadsheet\Calculation\DateTimeExcel\Date;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use \PhpOffice\PhpSpreadsheet\Style\Style;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;

//Define a data 
if (empty($_POST['dia'])) {
    // Se nenhuma data for enviada, usa a data de hoje como padrão.
    $diaSel = (new DateTime('now'))->format('Y-m-d');
} else {
    // Usa a data enviada pelo formulário.
    $diaSel = $_POST['dia'];
}
// Cria o objeto DateTime a partir do formato correto (AAAA-MM-DD)
$userDate = DateTime::createFromFormat('Y-m-d', $diaSel);

// CRIA NOVA PLANILHA
$spreadsheet = new Spreadsheet();
//Cria a aba e define o nome
$sheet = $spreadsheet->getActiveSheet();
$sheet->setTitle("Arranchados_"."$diaSel");
//Prepara cabeçalho e atributos da planilha
$sheet->mergeCells("B1:D1");
$spreadsheet->getDefaultStyle()->getFont()->setSize(12);
$sheet->setCellValue("B1", "Arranchados para ".$userDate->format('d/m/Y'));
$sheet->getStyle("B1")->getFont()->setBold(true);
$sheet->getStyle("B1")->getFont()->setSize(16)->setBold(true);
$sheet->mergeCells("A1:A5");

//Coloca o brasão
$brasaoPath=__DIR__.'/../../public/img/brasao.png';
$brasao = new \PhpOffice\PhpSpreadsheet\Worksheet\Drawing();
$brasao->setName('BrasaoCiaCom'); // Nome
$brasao->setPath($brasaoPath); // Caminho
$brasao->setHeight(72);// Altura
$brasao->setWidth(72);// Largura
$brasao->setCoordinates('A1');//Celula inseria
$brasao->setWorksheet($sheet);//Em qual aba vai ser colocada

//Função para puxar as pessoas arranchadas
function recRef($dia, $refeicao, $pdo){
    //Array com refeiçoes
  try { 
    $stmt = $pdo->prepare("SELECT u.nome_pg FROM arranchados as a INNER JOIN users as u  ON u.id =a.user_id 
    WHERE a.data_refeicao = :dia AND a.refeicao = :refeicao AND u.role = 'comum'" );
    $stmt->bindParam(':dia', $dia);
    $stmt->bindParam(':refeicao', $refeicao);
    $stmt->execute();
    $refeicao = $stmt->fetchAll(PDO::FETCH_ASSOC);
    $resultado = array_column($refeicao ,'nome_pg');
    return $resultado;
  }//Fim try 
  catch (PDOException $e) {
        echo "<strong>ERRO:</strong> " . htmlspecialchars($e->getMessage());
  } // Fim Catch
} 

//Conexão com o banco de dados
try {
        $pdo = DbConnection();
        if ($pdo === null) {
            echo "<strong>ERRO:</strong> Não foi possível conectar ao banco de dados.";
            exit();
        }// Cria as arrays de arranchados
        $cafe = recRef($diaSel,'cafe',$pdo);
        $almoco = recRef($diaSel,'almoco',$pdo); 
        $janta = recRef($diaSel,'janta',$pdo); 

      } catch (PDOException $e) {
        echo "<strong>ERRO:</strong> " . htmlspecialchars($e->getMessage());
        }


$row = 2;
//Define a função de escrever os arranhcados
function escreveRef(&$funcrow, $ref,$arrayRef,$funcsheet){
    $funcSetRed = [//Formatação de texto de array vazio
        "font" => [
            "bold" => true,
            "italic" => true,
            "color" => ["argb" => "FFFF0000"]
        ]
    ];
    $funcTitle = [ // Estilo do título
        "font" => [
            "bold" => true,
            "color" => ["argb" => "FF000000"],
            "size" => 16
        ]
    ];
    //Escreve o titulo da refeicao
    $funcsheet->mergeCells("B".$funcrow.":". "D".$funcrow);
    $funcsheet->setCellValue("B".$funcrow, $ref);
    $funcsheet->getStyle("B".$funcrow)->applyFromArray($funcTitle);
    $funcrow++;//Proxima linha
    if (!$arrayRef){//Se não houver ninguem escreve em italico e vermelho
        $funcsheet->setCellValue("B".$funcrow, "Não há arranchados.");
        $funcstyle = $funcsheet->getStyle("B".$funcrow);
        $funcstyle->applyFromArray($funcSetRed);
    }
    else{//Escreve os arranchados
        $data = array_chunk($arrayRef, 3, true);
        $funcsheet->fromArray($data, null, "B".$funcrow);
    }
    //Passa para a próxima linha para ser chamad a função da próxima refeição
    $funcrow = $funcsheet->getHighestRow()+1;
}
// //Arranchados p/ café
escreveRef($row, 'Cafe(Total: ' . count($cafe) . ')', $cafe, $sheet);
escreveRef($row, 'Almoço(Total: ' . count($almoco) . ')', $almoco, $sheet);
escreveRef($row, 'Janta(Total: ' . count($janta) . ')', $janta, $sheet);

//Formatação final da planilha

$styleSet = [
  // (C1) FONT
  "font" => [
    // "bold" => true,
    // "italic" => true,
    "underline" => false,
    "strikethrough" => false,
    // "color" => ["argb" => "FF000000"],
    "name" => "Times New Roman",
    // "size" => 12
  ],
 
  // (C2) Alinhamento
  "alignment" => [
    "horizontal" => \PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER,
    "vertical" => \PhpOffice\PhpSpreadsheet\Style\Alignment::VERTICAL_CENTER
  ],
 
  // (C3) Bordas
  "borders" => [
    // Definir todas bordas da célula
    "allBorders" => [
      "borderStyle" => \PhpOffice\PhpSpreadsheet\Style\Border::BORDER_THIN,
      "color" => ["argb" => "FF000000"]
    ]
  ]
];
$sheet->setCellValue("E1", "_____");
$sheet->setCellValue("E2", "Visto");
$sheet->getStyle("E1:E2")->applyFromArray($styleSet);
$style = $sheet->getStyle("B1:D".$row);
$style->applyFromArray($styleSet);
$sheet->getColumnDimension('B')->setAutoSize(true);
$sheet->getColumnDimension('C')->setAutoSize(true);
$sheet->getColumnDimension('D')->setAutoSize(true);
$total= count($cafe)+count($almoco)+count($janta);
if ($row == $sheet->getHighestRow()){
  $sheet->mergeCells("B".$sheet->getHighestRow()+1 . ":"."D".$sheet->getHighestRow()+1);
  $sheet->setCellValue("B".$sheet->getHighestRow(), "Total: $total");
  $sheet->getStyle("B".$sheet->getHighestRow())->applyFromArray($styleSet);
}
$spreadsheet->getActiveSheet()->setPrintGridlines(true);
//Define nova aba
$spreadsheet->createSheet();
$spreadsheet->setActiveSheetIndex(1); // Mudar o índice da aba ativa para o novo

$vale = $spreadsheet->getActiveSheet();
$vale->setTitle('Vale Diario');
  // --- TÍTULOS E CABEÇALHOS ---
$vale->setCellValue('C1', 'visto');
$vale->setCellValue('H1', '3ª Cia Com Bld');
$vale->setCellValue('B3', 'Fisc Adm');
$vale->setCellValue('F4', 'Vale diário para o dia');
$vale->setCellValue('I4', $userDate->format('d/m/Y'));
$vale->setCellValue('J5', 'tipo');
$vale->setCellValue('K5', 'quantidade');

// Subcabeçalhos da tabela
$vale->setCellValue('B5', '');
$vale->setCellValue('C5', 'café');
$vale->setCellValue('D5', 'almoço');
$vale->setCellValue('E5', 'jantar');
$vale->setCellValue('F5', 'etapas completas');
$vale->setCellValue('G5', 'a alimentar');
$vale->setCellValue('H5', 'A alimentar O OM');
$vale->setCellValue('I5', 'Soma');
$vale->setCellValue('J5', 'tipo');
$vale->setCellValue('K5', 'quantidade');

// Nomes das linhas da tabela
$vale->setCellValue('B6', 'oficiais');
$vale->setCellValue('B7', 'subten/sgt');
$vale->setCellValue('B8', 'cb/sd');

// --- CÉLULAS MESCLADAS ---
$vale->mergeCells('C1:D1');
$vale->mergeCells('H1:I1');
$vale->mergeCells('B3:D3');
$vale->mergeCells('F4:H4');
$vale->mergeCells('I4:J4');

// --- DADOS DA TABELA ---
$vale->setCellValue('C6', count($cafe)/3);
$vale->setCellValue('D6', count($almoco)/3);
$vale->setCellValue('E6', count($janta)/3);

$vale->setCellValue('C7', count($cafe)/3);
$vale->setCellValue('D7', count($almoco)/3);
$vale->setCellValue('E7', count($janta)/3);

$vale->setCellValue('C8', count($cafe)/3);
$vale->setCellValue('D8', count($almoco)/3);
$vale->setCellValue('E8', count($janta)/3);

$vale->setCellValue('G6', count($cafe)/3);
$vale->setCellValue('G7', count($almoco)/3);
$vale->setCellValue('G8', count($janta)/3);

// --- FÓRMULAS ---
// Coluna 'etapas completas' (o LARGE na sua planilha está com erro, corrigido aqui)
$vale->setCellValue('F6', '=LARGE(C6:E6, 1)');
$vale->setCellValue('F7', '=LARGE(C7:E7, 1)');
$vale->setCellValue('F8', '=LARGE(C8:E8, 1)');

// Coluna 'Soma'
$vale->setCellValue('I6', '=SUM(F6:H6)');
$vale->setCellValue('I7', '=SUM(F7:H7)');
$vale->setCellValue('I8', '=SUM(F8:H8)');

// Linha de totais
$vale->setCellValue('B10', 'SOMA');
$vale->setCellValue('C10', '=SUM(C6:C8)');
$vale->setCellValue('D10', '=SUM(D6:D8)');
$vale->setCellValue('E10', '=SUM(E6:E8)');
$vale->setCellValue('F10', '=SUM(F6:F8)');
$vale->setCellValue('G10', '=SUM(G6:G8)');
$vale->setCellValue('H10', '=SUM(H6:H8)');
$vale->setCellValue('I10', '=SUM(I6:I8)');

// --- ESTILOS ---
// Centraliza títulos principais
$vale->getStyle('H1')->getAlignment()->setHorizontal(\PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER);
$vale->getStyle('B3')->getAlignment()->setHorizontal(\PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER);
$vale->getStyle('F4')->getAlignment()->setHorizontal(\PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER);
$vale->getStyle('C1')->getAlignment()->setHorizontal(\PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER);

// Estilos de borda
$styleArray = [
    'borders' => [
        'allBorders' => [
            'borderStyle' => \PhpOffice\PhpSpreadsheet\Style\Border::BORDER_THIN,
            'color' => ['argb' => 'FF000000'],
        ],
    ],
];

$vale->getStyle('B5:I8')->applyFromArray($styleArray);
$vale->getStyle('B10:I10')->applyFromArray($styleArray);


//Escreve na planilha
$writer = new Xlsx($spreadsheet);

//cabeçalhos para serem baixadas
header("Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet");
header("Content-Disposition: attachment;filename=\"Arranchamento-".$userDate->format('d-m-Y').".xlsx\"");
header("Cache-Control: max-age=0");
header("Last-Modified: ". gmdate("D, d M Y H:i:s") ." GMT");
header("Cache-Control: cache, must-revalidate");
header("Pragma: public");
$writer->save("php://output");
// ob_end_flush();
// header("Location: {$_SERVER['HTTP_REFERER']}");
exit;
?>