<?php

require __DIR__ .'/../../vendor/autoload.php';
date_default_timezone_set('America/Sao_Paulo');
//Importação de dependências
use PhpOffice\PhpSpreadsheet\Calculation\DateTimeExcel\Date;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use \PhpOffice\PhpSpreadsheet\Style\Style;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;

//Define a data 
$today = new DateTime('now');

// (B)CRIA NOVA PLANILHA
$spreadsheet = new Spreadsheet();
//Cria a aba e define o nome
$sheet = $spreadsheet->getActiveSheet();
$sheet->setTitle("Arranchados_".$today->format('d_m_Y'));
//Prepara cabeçalho e atributos da planilha
$sheet->mergeCells("B1:D1");
$spreadsheet->getDefaultStyle()->getFont()->setSize(12);
$sheet->setCellValue("B1", "Arranchados para ".$today->format('d/m/Y'));
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

//Define a data a ser baixada:
$dataSelecionada = $_POST['dia'];

//Definição de nomes (buscar na BD)
$cafe = [
   'Ana Clara Silva',
   'Bruno Mendes Oliveira',
   'Gabriela Pereira Martins',
   'Hugo Ribeiro Correia',
   'Isabela Souza Castro',
   'Júlio Santos Pereira',
    'Ana Clara Silva',
   'Bruno Mendes Oliveira',
   'Gabriela Pereira Martins',
   'Hugo Ribeiro Correia',
   'Isabela Souza Castro',
   'Júlio Santos Pereira'
];

$almoco =[
    'Carla de Souza Lima',
    'Daniel Fernandes Costa',
    'Elisabeth Rodrigues Santos',
    'Fábio Goncalves Almeida'
]; 

$janta = []; 

$row = 2;
//Escreve quem está no café
// $sheet->mergeCells("B".$row.":". "D".$row);
// $sheet->setCellValue("B".$row, "Café");
// $row++;
// $data = array_chunk($cafe, 3, true);
// $sheet->fromArray($data, null, "B".$row);
// $row = $sheet->getHighestRow()+1;
// $sheet->mergeCells("B".$row.":". "D".$row);

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
escreveRef($row, 'Cafe', $cafe, $sheet);
escreveRef($row, 'Almoço', $almoco, $sheet);
escreveRef($row, 'Janta', $janta, $sheet);

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

$style = $sheet->getStyle("B1:D".$row);
$style->applyFromArray($styleSet);
$sheet->getColumnDimension('B')->setAutoSize(true);
$sheet->getColumnDimension('C')->setAutoSize(true);
$sheet->getColumnDimension('D')->setAutoSize(true);


//Escreve na planilha
$writer = new Xlsx($spreadsheet);

//cabeçalhos para serem baixadas
header("Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet");
header("Content-Disposition: attachment;filename=\"Arranchamento-".$today->format('d_m_Y')."\"");
header("Cache-Control: max-age=0");
header("Last-Modified: ". gmdate("D, d M Y H:i:s") ." GMT");
header("Cache-Control: cache, must-revalidate");
header("Pragma: public");
$writer->save("php://output");
// ob_end_flush();
header("Location: {$_SERVER['HTTP_REFERER']}");
exit;
?>