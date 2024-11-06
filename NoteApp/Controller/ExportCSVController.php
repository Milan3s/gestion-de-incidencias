<?php
require_once '../../Framework/vendor/autoload.php';
require_once 'NoteReportController.php';

use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;
use PhpOffice\PhpSpreadsheet\Style\Fill;
use PhpOffice\PhpSpreadsheet\Style\Alignment;
use PhpOffice\PhpSpreadsheet\Style\Border;

$reportController = new NoteReportController();

// Definir los valores de limit y offset según tus necesidades
$limit = 100; // o cualquier valor que consideres adecuado
$offset = 0; // o cualquier valor que consideres adecuado

$notas = $reportController->getReport($limit, $offset);

// Crear una nueva hoja de cálculo
$spreadsheet = new Spreadsheet();
$sheet = $spreadsheet->getActiveSheet();

// Poner los datos en las celdas
$sheet->setCellValue('A1', 'ID');
$sheet->setCellValue('B1', 'Título');
$sheet->setCellValue('C1', 'Contenido');
$sheet->setCellValue('D1', 'Usuario');
$sheet->setCellValue('E1', 'Fecha de Creación');
$sheet->setCellValue('F1', 'Estado');

$row = 2;
foreach ($notas as $nota) {
    $sheet->setCellValue('A' . $row, $nota['id']);
    $sheet->setCellValue('B' . $row, $nota['titulo']);
    $sheet->setCellValue('C' . $row, $nota['contenido']);
    $sheet->setCellValue('D' . $row, $nota['username']);
    $sheet->setCellValue('E' . $row, $nota['fecha_creacion']);
    $sheet->setCellValue('F' . $row, $nota['estado']);
    $row++;
}

// Aplicar estilos al encabezado
$headerStyle = [
    'font' => [
        'bold' => true,
        'color' => ['rgb' => 'FFFFFF'],
    ],
    'fill' => [
        'fillType' => Fill::FILL_SOLID,
        'startColor' => ['rgb' => '000000'],
    ],
    'alignment' => [
        'horizontal' => Alignment::HORIZONTAL_CENTER,
        'vertical' => Alignment::VERTICAL_CENTER,
    ],
];
$sheet->getStyle('A1:F1')->applyFromArray($headerStyle);

// Aplicar bordes, centrar el texto y dar tamaño a todas las celdas
$cellStyle = [
    'alignment' => [
        'horizontal' => Alignment::HORIZONTAL_CENTER,
        'vertical' => Alignment::VERTICAL_CENTER,
    ],
    'borders' => [
        'allBorders' => [
            'borderStyle' => Border::BORDER_THIN,
            'color' => ['rgb' => '000000'],
        ],
    ],
];
$sheet->getStyle('A1:F' . ($row - 1))->applyFromArray($cellStyle);

// Ajustar el ancho de las columnas y la altura de las filas
foreach (range('A', 'F') as $columnID) {
    $sheet->getColumnDimension($columnID)->setAutoSize(true);
}
for ($r = 1; $r <= $row; $r++) {
    $sheet->getRowDimension($r)->setRowHeight(20);
}

// Crear el archivo Excel
$writer = new Xlsx($spreadsheet);

// Enviar el archivo al navegador para descarga
header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
header('Content-Disposition: attachment;filename="reporte_notas.xlsx"');
header('Cache-Control: max-age=0');
$writer->save('php://output');

exit;
?>
