<?php
require_once '../../Config/Database.php';

class AssignNotesController extends Database {

    public function getAllUsers() {
        $sql = "SELECT id, username FROM usuarios";
        $stmt = $this->connect()->prepare($sql);
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function getAllNotes() {
        $sql = "SELECT id, titulo FROM notas";
        $stmt = $this->connect()->prepare($sql);
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function assignNotesToUser($userId, $noteIds) {
        $conn = $this->connect();
        try {
            $conn->beginTransaction();

            foreach ($noteIds as $noteId) {
                // Actualizar el user_id en la tabla notas
                $sqlUpdateNotas = "UPDATE notas SET user_id = :user_id WHERE id = :note_id";
                $stmtUpdateNotas = $conn->prepare($sqlUpdateNotas);
                $stmtUpdateNotas->bindParam(':user_id', $userId, PDO::PARAM_INT);
                $stmtUpdateNotas->bindParam(':note_id', $noteId, PDO::PARAM_INT);
                $stmtUpdateNotas->execute();

                // Verificar si la nota ya está en la tabla user_notes
                $sqlCheck = "SELECT COUNT(*) as count FROM user_notes WHERE note_id = :note_id";
                $stmtCheck = $conn->prepare($sqlCheck);
                $stmtCheck->bindParam(':note_id', $noteId, PDO::PARAM_INT);
                $stmtCheck->execute();
                $result = $stmtCheck->fetch(PDO::FETCH_ASSOC);

                if ($result['count'] > 0) {
                    // Si ya existe, actualizar el user_id en user_notes
                    $sqlUpdateUserNotes = "UPDATE user_notes SET user_id = :user_id WHERE note_id = :note_id";
                } else {
                    // Si no existe, insertar una nueva entrada en user_notes
                    $sqlUpdateUserNotes = "INSERT INTO user_notes (user_id, note_id) VALUES (:user_id, :note_id)";
                }

                $stmtUpdateUserNotes = $conn->prepare($sqlUpdateUserNotes);
                $stmtUpdateUserNotes->bindParam(':user_id', $userId, PDO::PARAM_INT);
                $stmtUpdateUserNotes->bindParam(':note_id', $noteId, PDO::PARAM_INT);
                $stmtUpdateUserNotes->execute();
            }

            $conn->commit();
            return true;
        } catch (Exception $e) {
            $conn->rollBack();
            throw new Exception("Error al asignar las notas: " . $e->getMessage());
        }
    }
}
?>
