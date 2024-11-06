<?php
require_once '../../Config/Database.php';

class IncidentDeleteController extends Database {
    public function eliminarIncidencia($id) {
        $sql = 'DELETE FROM incidencias WHERE id = :id';
        $stmt = $this->connect()->prepare($sql);
        $stmt->bindParam(':id', $id, PDO::PARAM_INT);
        return $stmt->execute();
    }
}
