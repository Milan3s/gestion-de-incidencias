<?php
require_once '../../Config/Database.php'; 

class Nota extends Database {
    
    public function createNota($titulo, $contenido, $user_id) {
        $sql = 'INSERT INTO notas (titulo, contenido, user_id, estado) VALUES (:titulo, :contenido, :user_id, :estado)';
        $stmt = $this->connect()->prepare($sql);
        $stmt->bindParam(':titulo', $titulo);
        $stmt->bindParam(':contenido', $contenido);
        $stmt->bindParam(':user_id', $user_id);
        
        // Estado por defecto
        $estado = 'Pendiente';
        $stmt->bindParam(':estado', $estado);

        if ($stmt->execute()) {
            return true;
        } else {
            return false;
        }
    }
}
?>
