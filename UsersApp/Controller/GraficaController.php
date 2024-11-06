<?php
require_once '../../Config/Database.php';

class GraficaUsuarios extends Database {
    
    public function obtenerDatosGrafica() {
        $sql = "SELECT u.username, SUM(i.veces_on) as total_veces_on
                FROM usuarios u
                JOIN informe_status_usuarios i ON u.id = i.usuario_id
                WHERE MONTH(i.fecha_de_creacion) = MONTH(CURDATE()) AND YEAR(i.fecha_de_creacion) = YEAR(CURDATE())
                GROUP BY u.username";
        $stmt = $this->connect()->prepare($sql);
        $stmt->execute();
        
        $result = $stmt->fetchAll(PDO::FETCH_ASSOC);
        return $result;
    }
}
?>
