<?php
require_once '../../Config/Database.php';

class EditProfileDetailsController extends Database {

    public function getRoles() {
        $query = "SELECT id, nombre FROM roles";
        $stmt = $this->connect()->query($query);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function getSecciones() {
        $query = "SELECT id, nombre FROM seccion";
        $stmt = $this->connect()->query($query);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function updateUserDetails($userDetails) {
        $query = "UPDATE user_details SET 
                    photo_user = :photo_user, 
                    first_name = :first_name, 
                    last_name = :last_name, 
                    address = :address, 
                    phone_number = :phone_number, 
                    birth_date = :birth_date, 
                    role_id = :role_id, 
                    seccion_id = :seccion_id
                  WHERE user_id = :user_id";
        $stmt = $this->connect()->prepare($query);
        $stmt->bindParam(':photo_user', $userDetails['photo_user']);
        $stmt->bindParam(':first_name', $userDetails['first_name']);
        $stmt->bindParam(':last_name', $userDetails['last_name']);
        $stmt->bindParam(':address', $userDetails['address']);
        $stmt->bindParam(':phone_number', $userDetails['phone_number']);
        $stmt->bindParam(':birth_date', $userDetails['birth_date']);
        $stmt->bindParam(':role_id', $userDetails['role_id']);
        $stmt->bindParam(':seccion_id', $userDetails['seccion_id']);
        $stmt->bindParam(':user_id', $userDetails['user_id']);

        return $stmt->execute();
    }
}
?>
