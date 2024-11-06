<?php
require_once '../../Config/Database.php';

class Usuario extends Database {

    public function getUsuarios() {
        $sql = 'SELECT * FROM usuarios';
        $stmt = $this->connect()->prepare($sql);
        $stmt->execute();

        $result = $stmt->fetchAll(PDO::FETCH_ASSOC);

        return $result;
    }

    public function createUsuario($username, $password, $email, $role_id = null) {
        if ($role_id === null) {
            // Obtener el role_id del rol "pendiente de aprobar"
            $sql = 'SELECT id FROM roles WHERE nombre = :nombre';
            $stmt = $this->connect()->prepare($sql);
            $nombre = 'pendiente de aprobar';
            $stmt->bindParam(':nombre', $nombre);
            $stmt->execute();

            $role = $stmt->fetch(PDO::FETCH_ASSOC);
            $role_id = $role['id'];
        }

        $sql = 'INSERT INTO usuarios (username, password, email, role_id) VALUES (:username, :password, :email, :role_id)';
        $stmt = $this->connect()->prepare($sql);
        
        // Encriptar la contraseña usando md5
        $hashed_password = md5($password);

        $stmt->bindParam(':username', $username);
        $stmt->bindParam(':password', $hashed_password);
        $stmt->bindParam(':email', $email);
        $stmt->bindParam(':role_id', $role_id);

        if ($stmt->execute()) {
            return true;
        } else {
            return false;
        }
    }

    public function getRoles() {
        $sql = 'SELECT * FROM roles';
        $stmt = $this->connect()->prepare($sql);
        $stmt->execute();

        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
}
?>
