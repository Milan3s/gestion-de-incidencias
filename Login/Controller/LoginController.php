<?php
require_once '../../Config/Database.php';

class LoginController extends Database {
    public function login($username, $password) {
        $sql = 'SELECT * FROM usuarios WHERE username = :username AND password = :password';
        $stmt = $this->connect()->prepare($sql);
        
        $hashed_password = md5($password);

        $stmt->bindParam(':username', $username);
        $stmt->bindParam(':password', $hashed_password);
        $stmt->execute();

        $user = $stmt->fetch(PDO::FETCH_ASSOC);

        if ($user) {
            // Iniciar sesión y guardar datos del usuario en la sesión
            session_start();
            $_SESSION['user'] = $user;

            // Registrar entrada en informe_status_usuarios
            $this->registerLogin($user['id']);
            
            return true;
        } else {
            return false;
        }
    }

    private function registerLogin($userId) {
        // Verificar si ya existe un registro para el usuario en el día actual
        $sql = 'SELECT * FROM informe_status_usuarios 
                WHERE usuario_id = :usuario_id 
                AND DATE(hora_entrada) = CURDATE()';
        $stmt = $this->connect()->prepare($sql);
        $stmt->bindParam(':usuario_id', $userId);
        $stmt->execute();
        $result = $stmt->fetch(PDO::FETCH_ASSOC);

        if ($result) {
            // Si ya existe un registro para el día actual, actualizarlo
            $sql = 'UPDATE informe_status_usuarios 
                    SET status = :status, veces_on = veces_on + 1, hora_entrada = CURRENT_TIMESTAMP 
                    WHERE id = :id';
            $stmt = $this->connect()->prepare($sql);

            $status = 'on';

            $stmt->bindParam(':status', $status);
            $stmt->bindParam(':id', $result['id']);
            $stmt->execute();
        } else {
            // Si no existe un registro para el día actual, insertarlo
            $sql = 'INSERT INTO informe_status_usuarios (usuario_id, status, ipaddress, veces_on)
                    VALUES (:usuario_id, :status, :ipaddress, 1)';
            $stmt = $this->connect()->prepare($sql);

            $status = 'on';
            $ipaddress = $_SERVER['REMOTE_ADDR'];

            $stmt->bindParam(':usuario_id', $userId);
            $stmt->bindParam(':status', $status);
            $stmt->bindParam(':ipaddress', $ipaddress);
            $stmt->execute();
        }
    }

    public function logout() {
        session_start();
        if (isset($_SESSION['user'])) {
            $userId = $_SESSION['user']['id'];
            $this->registerLogout($userId);
            session_destroy();
        }
    }

    public function registerLogout($userId) {
        $sql = 'UPDATE informe_status_usuarios 
                SET status = :status, hora_salida = CURRENT_TIMESTAMP 
                WHERE usuario_id = :usuario_id AND status = "on" ORDER BY hora_entrada DESC LIMIT 1';
        $stmt = $this->connect()->prepare($sql);

        $status = 'off';

        $stmt->bindParam(':status', $status);
        $stmt->bindParam(':usuario_id', $userId);
        $stmt->execute();

        // Verificar si la actualización fue exitosa
        if ($stmt->rowCount() > 0) {
            error_log("Logout registrado correctamente para el usuario_id: $userId");
        } else {
            error_log("Error al registrar logout para el usuario_id: $userId");
        }
    }
}
?>
