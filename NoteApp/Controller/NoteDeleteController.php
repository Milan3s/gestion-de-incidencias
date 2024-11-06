<?php
require_once '../../Config/Database.php';

class BorrarNotas extends Database {

    public function deleteNota($id) {
        $sql = 'DELETE FROM notas WHERE id = :id';
        $stmt = $this->connect()->prepare($sql);
        $stmt->bindParam(':id', $id);
        return $stmt->execute();
    }
}
?>
