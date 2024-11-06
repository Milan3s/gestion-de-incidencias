<?php
require_once '../../Config/Database.php';

class IncidentEditController extends Database {

    public function actualizarIncidencia($id, $asunto, $personaQueReclama, $estadoId, $fechaResolucion, $archivoAdjunto, $verResultados, $solucion) {
        if ($archivoAdjunto) {
            $sql = 'UPDATE incidencias SET asunto = :asunto, persona_que_reclama = :personaQueReclama, estado_id = :estadoId, fecha_resolucion = :fechaResolucion, archivos_adjuntos = :archivoAdjunto, ver_resultados = :verResultados, solucion = :solucion WHERE id = :id';
            $stmt = $this->connect()->prepare($sql);
            $stmt->bindParam(':archivoAdjunto', $archivoAdjunto);
        } else {
            $sql = 'UPDATE incidencias SET asunto = :asunto, persona_que_reclama = :personaQueReclama, estado_id = :estadoId, fecha_resolucion = :fechaResolucion, ver_resultados = :verResultados, solucion = :solucion WHERE id = :id';
            $stmt = $this->connect()->prepare($sql);
        }

        $stmt->bindParam(':asunto', $asunto);
        $stmt->bindParam(':personaQueReclama', $personaQueReclama);
        $stmt->bindParam(':estadoId', $estadoId);
        $stmt->bindParam(':fechaResolucion', $fechaResolucion);
        $stmt->bindParam(':verResultados', $verResultados);
        $stmt->bindParam(':solucion', $solucion);
        $stmt->bindParam(':id', $id, PDO::PARAM_INT);

        return $stmt->execute();
    }
}
?>
