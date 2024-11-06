<?php
require_once '../../Config/Database.php';

class IncidentListController extends Database {

    public function getIncidencias($limit, $offset) {
        $sql = 'SELECT i.id, i.numero_ref_reclamacion, i.asunto, i.persona_que_reclama, i.fecha_de_creacion, i.fecha_resolucion, i.archivos_adjuntos, e.estado_nombre, i.ver_resultados, i.solucion
                FROM incidencias i
                JOIN incidencia_estados e ON i.estado_id = e.id
                ORDER BY i.id ASC
                LIMIT :limit OFFSET :offset';
        $stmt = $this->connect()->prepare($sql);
        $stmt->bindParam(':limit', $limit, PDO::PARAM_INT);
        $stmt->bindParam(':offset', $offset, PDO::PARAM_INT);
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function getTotalIncidencias() {
        $sql = 'SELECT COUNT(*) as total FROM incidencias';
        $stmt = $this->connect()->prepare($sql);
        $stmt->execute();
        return $stmt->fetch(PDO::FETCH_ASSOC)['total'];
    }

    public function getIncidenciaById($id) {
        $sql = 'SELECT i.id, i.numero_ref_reclamacion, i.asunto, i.persona_que_reclama, i.fecha_de_creacion, i.fecha_resolucion, i.archivos_adjuntos, e.estado_nombre, i.ver_resultados, i.solucion
                FROM incidencias i
                JOIN incidencia_estados e ON i.estado_id = e.id
                WHERE i.id = :id';
        $stmt = $this->connect()->prepare($sql);
        $stmt->bindParam(':id', $id, PDO::PARAM_INT);
        $stmt->execute();
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    public function getAllIncidencias() {
        $sql = 'SELECT i.id, i.numero_ref_reclamacion, i.asunto, i.persona_que_reclama, i.fecha_de_creacion, i.fecha_resolucion, i.archivos_adjuntos, e.estado_nombre, i.ver_resultados, i.solucion
                FROM incidencias i
                JOIN incidencia_estados e ON i.estado_id = e.id
                ORDER BY i.id ASC';
        $stmt = $this->connect()->prepare($sql);
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function getIncidenciasResueltas() {
        $sql = 'SELECT i.id, i.numero_ref_reclamacion, i.asunto, i.persona_que_reclama, i.fecha_de_creacion, i.fecha_resolucion, i.archivos_adjuntos, e.estado_nombre, i.ver_resultados, i.solucion
                FROM incidencias i
                JOIN incidencia_estados e ON i.estado_id = e.id
                WHERE e.estado_nombre LIKE "%resuelta%"
                ORDER BY i.id ASC';
        $stmt = $this->connect()->prepare($sql);
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function buscarIncidenciasPorReferencia($referencia) {
        $sql = 'SELECT i.id, i.numero_ref_reclamacion, i.asunto, i.persona_que_reclama, i.fecha_de_creacion, i.fecha_resolucion, i.archivos_adjuntos, e.estado_nombre, i.ver_resultados, i.solucion
                FROM incidencias i
                JOIN incidencia_estados e ON i.estado_id = e.id
                WHERE i.numero_ref_reclamacion LIKE :referencia
                ORDER BY i.id ASC';
        $stmt = $this->connect()->prepare($sql);
        $referencia = "%$referencia%";
        $stmt->bindParam(':referencia', $referencia, PDO::PARAM_STR);
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
}
?>
