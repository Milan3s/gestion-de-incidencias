<?php
session_start();
require_once '../../Config/Database.php';

class IncidentDeleteController extends Database {
    public function eliminarIncidencia($id) {
        $sql = 'DELETE FROM incidencias WHERE id = :id';
        $stmt = $this->connect()->prepare($sql);
        $stmt->bindParam(':id', $id, PDO::PARAM_INT);
        return $stmt->execute();
    }
}

$incidentDeleteController = new IncidentDeleteController();

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $id = $_POST['id'];
    if ($incidentDeleteController->eliminarIncidencia($id)) {
        $_SESSION['message'] = 'Incidencia eliminada correctamente';
    } else {
        $_SESSION['error'] = 'Error al eliminar la incidencia';
    }
    header('Location: IncidentListView.php');
    exit();
}
?>
