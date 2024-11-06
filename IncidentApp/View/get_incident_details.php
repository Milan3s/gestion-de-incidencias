<?php
require_once '../../Config/Database.php';
require_once '../Controller/IncidentListController.php';

$incidentListController = new IncidentListController();

header('Content-Type: application/json');

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $data = json_decode(file_get_contents('php://input'), true);
    $id = $data['id'] ?? null;

    if ($id) {
        $incidencia = $incidentListController->getIncidenciaById($id);

        if ($incidencia) {
            echo json_encode([
                'success' => true,
                'incident' => $incidencia
            ]);
        } else {
            echo json_encode([
                'success' => false,
                'message' => 'Incidencia no encontrada.'
            ]);
        }
    } else {
        echo json_encode([
            'success' => false,
            'message' => 'ID de incidencia no proporcionado.'
        ]);
    }
} else {
    echo json_encode([
        'success' => false,
        'message' => 'Método de solicitud no permitido.'
    ]);
}
?>
