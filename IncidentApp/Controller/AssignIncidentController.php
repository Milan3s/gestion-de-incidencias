<?php
require_once '../../Config/Database.php';

class AssignIncidentController extends Database {

    public function getAllUsers() {
        $sql = "SELECT id, username FROM usuarios";
        $stmt = $this->connect()->prepare($sql);
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function getAllIncidents() {
        $sql = "SELECT id, asunto FROM incidencias";
        $stmt = $this->connect()->prepare($sql);
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function assignIncidentsToUser($userId, $incidenciaIds) {
        $conn = $this->connect();
        try {
            $conn->beginTransaction();

            foreach ($incidenciaIds as $incidenciaId) {
                // Verificar si la incidencia ya está en la tabla user_incidencias
                $sqlCheck = "SELECT COUNT(*) as count FROM user_incidencias WHERE incidencia_id = :incidencia_id";
                $stmtCheck = $conn->prepare($sqlCheck);
                $stmtCheck->bindParam(':incidencia_id', $incidenciaId, PDO::PARAM_INT);
                $stmtCheck->execute();
                $result = $stmtCheck->fetch(PDO::FETCH_ASSOC);

                if ($result['count'] > 0) {
                    // Si ya existe, actualizar el user_id en user_incidencias
                    $sqlUpdateUserIncidencias = "UPDATE user_incidencias SET user_id = :user_id WHERE incidencia_id = :incidencia_id";
                } else {
                    // Si no existe, insertar una nueva entrada en user_incidencias
                    $sqlUpdateUserIncidencias = "INSERT INTO user_incidencias (user_id, incidencia_id) VALUES (:user_id, :incidencia_id)";
                }

                $stmtUpdateUserIncidencias = $conn->prepare($sqlUpdateUserIncidencias);
                $stmtUpdateUserIncidencias->bindParam(':user_id', $userId, PDO::PARAM_INT);
                $stmtUpdateUserIncidencias->bindParam(':incidencia_id', $incidenciaId, PDO::PARAM_INT);
                $stmtUpdateUserIncidencias->execute();
            }

            $conn->commit();
            return true;
        } catch (Exception $e) {
            $conn->rollBack();
            throw new Exception("Error al asignar las incidencias: " . $e->getMessage());
        }
    }
}
?>
