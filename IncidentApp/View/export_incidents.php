<?php
session_start();

if (!isset($_SESSION['user'])) {
    header('Location: ../../index.php');
    exit();
}

require '../../Framework/vendor/autoload.php'; // Carga el autoload de Composer

require_once '../../Config/Database.php';
require_once '../Controller/IncidentListController.php';

use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;
use PhpOffice\PhpSpreadsheet\Style\Color;
use PhpOffice\PhpSpreadsheet\Style\Fill;
use PhpOffice\PhpSpreadsheet\Style\Alignment;

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $selectedIds = json_decode($_POST['selected_ids'], true);
    $exportAll = $_POST['export_all'] === '1';
    $incidentListController = new IncidentListController();
    
    if ($exportAll) {
        // Obtener todas las incidencias
        $incidencias = $incidentListController->getAllIncidencias();
    } else {
        // Obtener incidencias seleccionadas
        $incidencias = [];
        foreach ($selectedIds as $id) {
            $incidencia = $incidentListController->getIncidenciaById($id);
            if ($incidencia) {
                $incidencias[] = $incidencia;
            }
        }
    }

    if (!empty($incidencias)) {
        $spreadsheet = new Spreadsheet();
        $sheet = $spreadsheet->getActiveSheet();

        // Configurar la cabecera de la tabla
        $sheet->setCellValue('A1', 'ID')
              ->setCellValue('B1', 'N° Ref. Reclamación')
              ->setCellValue('C1', 'Asunto')
              ->setCellValue('D1', 'Persona que reclama')
              ->setCellValue('E1', 'Estado')
              ->setCellValue('F1', 'Fecha de creación')
              ->setCellValue('G1', 'Fecha de resolución')
              ->setCellValue('H1', 'Ver Resultados')
              ->setCellValue('I1', 'Archivos Adjuntos');

        // Aplicar estilo a la cabecera
        $headerStyleArray = [
            'font' => [
                'bold' => true,
                'color' => ['argb' => Color::COLOR_WHITE],
            ],
            'fill' => [
                'fillType' => Fill::FILL_SOLID,
                'startColor' => ['argb' => Color::COLOR_BLACK],
            ],
            'alignment' => [
                'horizontal' => Alignment::HORIZONTAL_CENTER,
                'vertical' => Alignment::VERTICAL_CENTER,
            ],
        ];
        $sheet->getStyle('A1:I1')->applyFromArray($headerStyleArray);

        // Ajustar la altura de la fila de la cabecera a 35px
        $sheet->getRowDimension(1)->setRowHeight(35);

        // Añadir datos
        $row = 2;
        foreach ($incidencias as $incidencia) {
            $sheet->setCellValue('A' . $row, $incidencia['id'])
                  ->setCellValue('B' . $row, $incidencia['numero_ref_reclamacion'])
                  ->setCellValue('C' . $row, $incidencia['asunto'])
                  ->setCellValue('D' . $row, $incidencia['persona_que_reclama'])
                  ->setCellValue('E' . $row, $incidencia['estado_nombre'])
                  ->setCellValue('F' . $row, date('d/m/Y', strtotime($incidencia['fecha_de_creacion'])))
                  ->setCellValue('G' . $row, $incidencia['fecha_resolucion'] ? date('d/m/Y', strtotime($incidencia['fecha_resolucion'])) : 'N/A')
                  ->setCellValue('H' . $row, $incidencia['ver_resultados'])
                  ->setCellValue('I' . $row, $incidencia['archivos_adjuntos']);

            // Añadir enlaces
            if (!empty($incidencia['ver_resultados'])) {
                $sheet->getCell('H' . $row)->getHyperlink()->setUrl($incidencia['ver_resultados']);
            }
            if (!empty($incidencia['archivos_adjuntos'])) {
                $sheet->getCell('I' . $row)->getHyperlink()->setUrl('../recursos_incidencias/' . $incidencia['archivos_adjuntos']);
            }

            // Ajustar la altura de la fila a 35px
            $sheet->getRowDimension($row)->setRowHeight(35);
            $row++;
        }

        // Aplicar estilo a todas las celdas de datos
        $dataStyleArray = [
            'alignment' => [
                'horizontal' => Alignment::HORIZONTAL_CENTER,
                'vertical' => Alignment::VERTICAL_CENTER,
            ],
        ];
        $sheet->getStyle('A2:I' . ($row - 1))->applyFromArray($dataStyleArray);

        // Ajustar el tamaño de las columnas
        foreach (range('A', 'I') as $columnID) {
            $sheet->getColumnDimension($columnID)->setAutoSize(true);
        }

        // Crear el archivo Excel y enviarlo al navegador para descarga
        header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
        header('Content-Disposition: attachment;filename="incidencias.xlsx"');
        header('Cache-Control: max-age=0');

        $writer = new Xlsx($spreadsheet);
        $writer->save('php://output');
        exit();
    } else {
        $_SESSION['error'] = 'No se encontraron incidencias para exportar.';
        header('Location: IncidentListView.php');
        exit();
    }
} else {
    header('HTTP/1.1 405 Method Not Allowed');
    exit();
}
?>
