<?php
require_once '../../Config/Database.php';
require_once '../Controller/IncidentListController.php';

header('Content-Type: application/json');

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $data = json_decode(file_get_contents('php://input'), true);
    $referencia = $data['referencia'] ?? '';

    $incidentListController = new IncidentListController();
    $incidencias = $incidentListController->buscarIncidenciasPorReferencia($referencia);

    if ($incidencias) {
        echo json_encode(['success' => true, 'incidencias' => $incidencias]);
    } else {
        echo json_encode(['success' => false, 'message' => 'No se encontraron incidencias con esa referencia.']);
    }
} else {
    echo json_encode(['success' => false, 'message' => 'Método no permitido.']);
}
?>
