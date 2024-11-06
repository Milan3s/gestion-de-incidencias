<?php
session_start();
require_once '../Controller/IncidentDeleteController.php';

if (!isset($_SESSION['user'])) {
    header('Location: ../../index.php');
    exit();
}

$response = ['success' => false];

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $data = json_decode(file_get_contents('php://input'), true);
    if (isset($data['id'])) {
        $incidentDeleteController = new IncidentDeleteController();
        if ($incidentDeleteController->eliminarIncidencia($data['id'])) {
            $response['success'] = true;
        }
    }
}

echo json_encode($response);
?>
