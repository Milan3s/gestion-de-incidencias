<?php
require_once '../../Config/Database.php';

class ListarNotas extends Database {

    public function getNotas($limit, $offset, $orderBy = 'fecha_creacion', $orderDir = 'ASC') {
        $validColumns = ['id', 'titulo', 'fecha_creacion'];
        $orderBy = in_array($orderBy, $validColumns) ? $orderBy : 'fecha_creacion';
        $orderDir = strtoupper($orderDir) === 'ASC' ? 'ASC' : 'DESC';

        $sql = "SELECT n.id, n.titulo, n.contenido, n.fecha_creacion, 
                       n.user_id, n.estado, u.username
                FROM notas n 
                LEFT JOIN usuarios u ON n.user_id = u.id
                ORDER BY n.$orderBy $orderDir
                LIMIT :limit OFFSET :offset";
        $stmt = $this->connect()->prepare($sql);
        $stmt->bindParam(':limit', $limit, PDO::PARAM_INT);
        $stmt->bindParam(':offset', $offset, PDO::PARAM_INT);
        $stmt->execute();
        
        $result = $stmt->fetchAll(PDO::FETCH_ASSOC);

        foreach ($result as &$nota) {
            $nota['fecha_creacion'] = $this->formatDate($nota['fecha_creacion']);
        }
        
        return $result;
    }

    public function getNotasByUser($user_id, $limit, $offset, $orderBy = 'fecha_creacion', $orderDir = 'ASC') {
        $validColumns = ['id', 'titulo', 'fecha_creacion'];
        $orderBy = in_array($orderBy, $validColumns) ? $orderBy : 'fecha_creacion';
        $orderDir = strtoupper($orderDir) === 'ASC' ? 'ASC' : 'DESC';

        $sql = "SELECT n.id, n.titulo, n.contenido, n.fecha_creacion, 
                       n.user_id, n.estado, u.username
                FROM notas n 
                LEFT JOIN usuarios u ON n.user_id = u.id
                WHERE n.user_id = :user_id
                ORDER BY n.$orderBy $orderDir
                LIMIT :limit OFFSET :offset";
        $stmt = $this->connect()->prepare($sql);
        $stmt->bindParam(':limit', $limit, PDO::PARAM_INT);
        $stmt->bindParam(':offset', $offset, PDO::PARAM_INT);
        $stmt->bindParam(':user_id', $user_id, PDO::PARAM_INT);
        $stmt->execute();
        
        $result = $stmt->fetchAll(PDO::FETCH_ASSOC);

        foreach ($result as &$nota) {
            $nota['fecha_creacion'] = $this->formatDate($nota['fecha_creacion']);
        }
        
        return $result;
    }

    public function getTotalNotas() {
        $sql = 'SELECT COUNT(*) as total FROM notas';
        $stmt = $this->connect()->prepare($sql);
        $stmt->execute();
        $result = $stmt->fetch(PDO::FETCH_ASSOC);
        return $result['total'];
    }

    public function updateEstado($id, $estado) {
        $sql = 'UPDATE notas SET estado = :estado WHERE id = :id';
        $stmt = $this->connect()->prepare($sql);
        $stmt->bindParam(':estado', $estado);
        $stmt->bindParam(':id', $id);
        return $stmt->execute();
    }

    private function formatDate($date) {
        $timestamp = strtotime($date);
        return date('d/m/Y H:i', $timestamp);
    }
}
?>
