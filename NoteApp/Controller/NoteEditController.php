<?php
require_once '../../Config/Database.php';

class EditarNotas extends Database {

    public function getNota($id) {
        $sql = 'SELECT * FROM notas WHERE id = :id';
        $stmt = $this->connect()->prepare($sql);
        $stmt->bindParam(':id', $id);
        $stmt->execute();
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    public function updateNota($id, $titulo, $contenido) {
        $sql = 'UPDATE notas SET titulo = :titulo, contenido = :contenido WHERE id = :id';
        $stmt = $this->connect()->prepare($sql);
        $stmt->bindParam(':titulo', $titulo);
        $stmt->bindParam(':contenido', $contenido);
        $stmt->bindParam(':id', $id);
        return $stmt->execute();
    }
}
?>
