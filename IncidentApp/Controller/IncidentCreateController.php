<?php
require_once '../../Config/Database.php';

class IncidentCreateController extends Database {

    public function getEstados() {
        $sql = 'SELECT id, estado_nombre FROM incidencia_estados';
        $stmt = $this->connect()->prepare($sql);
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function crearIncidencia($numeroRef, $asunto, $personaQueReclama, $estadoId, $fechaResolucion, $archivoAdjunto, $usuarioId, $verResultados) {
        $sql = 'INSERT INTO incidencias (numero_ref_reclamacion, asunto, persona_que_reclama, estado_id, fecha_de_creacion, fecha_resolucion, archivos_adjuntos, usuario_id, ver_resultados) 
                VALUES (:numeroRef, :asunto, :personaQueReclama, :estadoId, NOW(), :fechaResolucion, :archivoAdjunto, :usuarioId, :verResultados)';
        $stmt = $this->connect()->prepare($sql);
        $stmt->bindParam(':numeroRef', $numeroRef);
        $stmt->bindParam(':asunto', $asunto);
        $stmt->bindParam(':personaQueReclama', $personaQueReclama);
        $stmt->bindParam(':estadoId', $estadoId);
        $stmt->bindParam(':fechaResolucion', $fechaResolucion);
        $stmt->bindParam(':archivoAdjunto', $archivoAdjunto);
        $stmt->bindParam(':usuarioId', $usuarioId);
        $stmt->bindParam(':verResultados', $verResultados);
        $stmt->execute();
    }

    public function getNextRefNumber() {
        $sql = 'SELECT MAX(id) as max_id FROM incidencias';
        $stmt = $this->connect()->prepare($sql);
        $stmt->execute();
        $result = $stmt->fetch(PDO::FETCH_ASSOC);
        $nextId = $result['max_id'] + 1;
        return sprintf('REF%02d', $nextId);
    }
}
?>
