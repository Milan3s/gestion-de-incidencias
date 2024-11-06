<?php
require_once '../../Config/Database.php';

class DeleteUsersController extends Database {

    public function eliminarUsuario($id) {
        try {
            $conn = $this->connect();
            $conn->beginTransaction();

            // Desactivar restricciones de clave foránea
            $conn->exec('SET FOREIGN_KEY_CHECKS=0');

            // Eliminar el usuario
            $query = 'DELETE FROM usuarios WHERE id = :id';
            $stmt = $conn->prepare($query);
            $stmt->bindParam(':id', $id, PDO::PARAM_INT);
            $stmt->execute();

            // Volver a activar las restricciones de clave foránea
            $conn->exec('SET FOREIGN_KEY_CHECKS=1');

            $conn->commit();
            return true;
        } catch (Exception $e) {
            $conn->rollBack();
            return false;
        }
    }
}
?>
