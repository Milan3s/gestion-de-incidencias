<?php
require_once '../../Config/Database.php';

class ListarUsuarios extends Database {
    public function obtenerUsuarios($offset, $limit, $search = '') {
        if ($limit == -1) {
            $query = '
                SELECT usuarios.id, usuarios.username, usuarios.email, roles.nombre AS rol
                FROM usuarios
                INNER JOIN roles ON usuarios.role_id = roles.id
                WHERE usuarios.username LIKE :search OR usuarios.email LIKE :search
            ';
            $stmt = $this->connect()->prepare($query);
        } else {
            $query = '
                SELECT usuarios.id, usuarios.username, usuarios.email, roles.nombre AS rol
                FROM usuarios
                INNER JOIN roles ON usuarios.role_id = roles.id
                WHERE usuarios.username LIKE :search OR usuarios.email LIKE :search
                LIMIT :offset, :limit
            ';
            $stmt = $this->connect()->prepare($query);
            $stmt->bindParam(':offset', $offset, PDO::PARAM_INT);
            $stmt->bindParam(':limit', $limit, PDO::PARAM_INT);
        }
        $searchTerm = '%' . $search . '%';
        $stmt->bindParam(':search', $searchTerm, PDO::PARAM_STR);
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function contarUsuarios($search = '') {
        $query = 'SELECT COUNT(*) as total FROM usuarios WHERE username LIKE :search OR email LIKE :search';
        $stmt = $this->connect()->prepare($query);
        $searchTerm = '%' . $search . '%';
        $stmt->bindParam(':search', $searchTerm, PDO::PARAM_STR);
        $stmt->execute();
        $result = $stmt->fetch(PDO::FETCH_ASSOC);
        return $result['total'];
    }
}
?>
