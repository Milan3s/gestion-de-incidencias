<?php
require_once '../../Config/Database.php';

class EditUsersController extends Database {

    public function obtenerUsuarioPorId($id) {
        $query = 'SELECT * FROM usuarios WHERE id = :id';
        $stmt = $this->connect()->prepare($query);
        $stmt->bindParam(':id', $id, PDO::PARAM_INT);
        $stmt->execute();
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    public function obtenerRoles() {
        $query = 'SELECT id, nombre FROM roles';
        $stmt = $this->connect()->prepare($query);
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function actualizarUsuario($id, $username, $email, $role_id) {
        try {
            $query = 'UPDATE usuarios SET username = :username, email = :email, role_id = :role_id WHERE id = :id';
            $stmt = $this->connect()->prepare($query);
            $stmt->bindParam(':id', $id, PDO::PARAM_INT);
            $stmt->bindParam(':username', $username, PDO::PARAM_STR);
            $stmt->bindParam(':email', $email, PDO::PARAM_STR);
            $stmt->bindParam(':role_id', $role_id, PDO::PARAM_INT);
            return $stmt->execute();
        } catch (Exception $e) {
            return false;
        }
    }
}
?>
