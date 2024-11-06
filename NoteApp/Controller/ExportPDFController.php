<?php
require_once '../../Framework/vendor/autoload.php'; // Asegúrate de que esta ruta sea correcta para tu instalación de Composer
require_once '../../Config/Database.php';
require_once '../Controller/NoteReportController.php';

use Dompdf\Dompdf;
use Dompdf\Options;

$reportController = new NoteReportController();

// Definir los valores de limit y offset según tus necesidades
$limit = 100; // o cualquier valor que consideres adecuado
$offset = 0; // o cualquier valor que consideres adecuado

$notas = $reportController->getReport($limit, $offset);

exportPDF($notas);

function exportPDF($notas) {
    // Configurar opciones de Dompdf
    $options = new Options();
    $options->set('isHtml5ParserEnabled', true);
    $options->set('isRemoteEnabled', true);

    // Crear una instancia de Dompdf con las opciones
    $dompdf = new Dompdf($options);

    // Crear el contenido HTML para el PDF
    $html = '<html><body>';
    $html .= '<h1>Reporte de Notas</h1>';
    $html .= '<table border="1" cellpadding="5" cellspacing="0" style="width: 100%;">';
    $html .= '<thead>';
    $html .= '<tr>';
    $html .= '<th>ID</th>';
    $html .= '<th>Título</th>';
    $html .= '<th>Contenido</th>';
    $html .= '<th>Usuario</th>';
    $html .= '<th>Fecha de Creación</th>';
    $html .= '<th>Estado</th>';
    $html .= '</tr>';
    $html .= '</thead>';
    $html .= '<tbody>';

    foreach ($notas as $nota) {
        $html .= '<tr>';
        $html .= '<td>' . htmlspecialchars($nota['id']) . '</td>';
        $html .= '<td>' . htmlspecialchars($nota['titulo']) . '</td>';
        $html .= '<td>' . htmlspecialchars($nota['contenido']) . '</td>';
        $html .= '<td>' . htmlspecialchars($nota['username']) . '</td>';
        $html .= '<td>' . htmlspecialchars($nota['fecha_creacion']) . '</td>';
        $html .= '<td>' . htmlspecialchars($nota['estado']) . '</td>';
        $html .= '</tr>';
    }

    $html .= '</tbody>';
    $html .= '</table>';
    $html .= '</body></html>';

    // Cargar el contenido HTML en Dompdf
    $dompdf->loadHtml($html);

    // (Opcional) Configurar el tamaño del papel y la orientación
    $dompdf->setPaper('A4', 'portrait');

    // Renderizar el PDF
    $dompdf->render();

    // Enviar el PDF al navegador
    $dompdf->stream("reporte_notas.pdf", ["Attachment" => true]);

    exit;
}
?>
