<?php
require_once '../../Config/Database.php';

class NoteReportController extends Database {

    public function getReport($limit, $offset, $orderBy = 'fecha_creacion', $orderDir = 'ASC') {
        $validColumns = ['fecha_creacion'];
        $orderBy = in_array($orderBy, $validColumns) ? $orderBy : 'fecha_creacion';
        $orderDir = strtoupper($orderDir) === 'ASC' ? 'ASC' : 'DESC';

        $sql = "SELECT notas.id, notas.titulo, notas.contenido, usuarios.username, notas.fecha_creacion, notas.estado
                FROM notas
                JOIN usuarios ON notas.user_id = usuarios.id
                ORDER BY notas.$orderBy $orderDir
                LIMIT :limit OFFSET :offset";
        $stmt = $this->connect()->prepare($sql);
        $stmt->bindParam(':limit', $limit, PDO::PARAM_INT);
        $stmt->bindParam(':offset', $offset, PDO::PARAM_INT);
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function getTotalNotas() {
        $sql = 'SELECT COUNT(*) as total FROM notas';
        $stmt = $this->connect()->prepare($sql);
        $stmt->execute();
        $result = $stmt->fetch(PDO::FETCH_ASSOC);
        return $result['total'];
    }
}
?>
