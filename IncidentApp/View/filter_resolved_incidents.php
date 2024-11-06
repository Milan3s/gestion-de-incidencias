<?php
session_start();

if (!isset($_SESSION['user'])) {
    echo json_encode(['success' => false]);
    exit();
}

require '../../Framework/vendor/autoload.php';
require_once '../../Config/Database.php';
require_once '../Controller/IncidentListController.php';

$incidentListController = new IncidentListController();

$incidencias = $incidentListController->getIncidenciasResueltas();

if (!empty($incidencias)) {
    echo json_encode(['success' => true, 'incidencias' => $incidencias]);
} else {
    echo json_encode(['success' => false]);
}
?>
