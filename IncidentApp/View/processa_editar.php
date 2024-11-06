<?php
session_start();
require_once '../../Config/Database.php';
require_once '../Controller/IncidentEditController.php';

$incidentEditController = new IncidentEditController();

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $id = $_POST['id'];
    $asunto = $_POST['asunto'];
    $personaQueReclama = $_POST['persona_que_reclama'];
    $estadoId = $_POST['estado_id'];
    $fechaResolucion = $_POST['fecha_resolucion'];
    $verResultados = $_POST['ver_resultados'];
    $archivoAdjunto = isset($_FILES['archivo_adjunto']) && $_FILES['archivo_adjunto']['error'] == 0 ? $_FILES['archivo_adjunto']['name'] : null;

    if ($archivoAdjunto) {
        $uploadDir = '../../incidentApp/recursos_incidencias/' . $_POST['numero_ref_reclamacion'] . '/';
        if (!is_dir($uploadDir)) {
            mkdir($uploadDir, 0777, true);
        }
        $uploadFile = $uploadDir . basename($_FILES['archivo_adjunto']['name']);
        move_uploaded_file($_FILES['archivo_adjunto']['tmp_name'], $uploadFile);
    }

    $success = $incidentEditController->actualizarIncidencia($id, $asunto, $personaQueReclama, $estadoId, $fechaResolucion, $archivoAdjunto, $verResultados);

    if ($success) {
        $_SESSION['message'] = 'Incidencia actualizada correctamente';
    } else {
        $_SESSION['error'] = 'Error al actualizar la incidencia';
    }

    header('Location: IncidentListView.php');
    exit();
}
?>
