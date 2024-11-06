<?php
session_start();

if (!isset($_SESSION['user'])) {
    header('Location: ../../index.php');
    exit();
}

require '../../Config/Database.php';
require '../Controller/IncidentListController.php';

$incidentListController = new IncidentListController();

try {
    $incidencias = $incidentListController->getAllIncidencias();
    echo json_encode(['success' => true, 'incidencias' => $incidencias]);
} catch (Exception $e) {
    echo json_encode(['success' => false, 'message' => $e->getMessage()]);
}
?>
