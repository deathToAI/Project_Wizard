<?php

require __DIR__ .'/../../vendor/autoload.php';
date_default_timezone_set('America/Sao_Paulo');

use PhpOffice\PhpSpreadsheet\Calculation\DateTimeExcel\Date;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;
// (B)CRIA NOVA PLANILHA
$spreadsheet = new Spreadsheet();
$sheet = $spreadsheet->getActiveSheet();

$sheet->setCellValue("A1", "Hello World!");

$writer = new Xlsx($spreadsheet);
// (D) SEND DOWNLOAD HEADERS
// ob_clean();
// ob_start();
//Define a data a ser baixada:
$today = new DateTime('now');

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
?>