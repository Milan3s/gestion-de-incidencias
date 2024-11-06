<?php
require_once '../../Config/Database.php';

class ProfileDetailsController extends Database {

    // Método para obtener los detalles del perfil del usuario
    public function getUserProfileDetails($userId) {
        $sql = 'SELECT u.username, u.email, d.first_name, 
            d.last_name, d.address, d.phone_number, d.birth_date, d.photo_user, r.nombre AS role, s.nombre AS seccion
                FROM usuarios u
                LEFT JOIN user_details d ON u.id = d.user_id
                LEFT JOIN roles r ON d.role_id = r.id
                LEFT JOIN seccion s ON d.seccion_id = s.id
                WHERE u.id = :user_id';
        
        $stmt = $this->connect()->prepare($sql);
        $stmt->bindParam(':user_id', $userId, PDO::PARAM_INT);
        $stmt->execute();

        $userDetails = $stmt->fetch(PDO::FETCH_ASSOC);

        return $userDetails;
    }
}
?>
