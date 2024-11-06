<?php
session_start();
require_once '../../Login/Controller/EditProfileDetailsController.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $editController = new EditProfileDetailsController();
    
    $userId = $_POST['user_id'];
    $username = $_POST['username'];
    $email = $_POST['email'];
    $firstName = $_POST['first_name'];
    $lastName = $_POST['last_name'];
    $address = $_POST['address'];
    $phoneNumber = $_POST['phone_number'];
    $birthDate = $_POST['birth_date'];
    $roleId = $_POST['role_id'];
    $seccionId = $_POST['seccion_id'];

    // Obtener la foto actual del usuario
    $currentPhoto = $_POST['current_photo_user'];

    // Manejo de la foto de perfil
    if (isset($_FILES['photo_user']) && $_FILES['photo_user']['error'] === UPLOAD_ERR_OK) {
        $photoUser = basename($_FILES['photo_user']['name']);
        $uploadDir = '../../Login/fotos_users/';
        $uploadFile = $uploadDir . $photoUser;
        
        if (move_uploaded_file($_FILES['photo_user']['tmp_name'], $uploadFile)) {
            // Archivo subido exitosamente
        } else {
            // Error al subir el archivo
            $photoUser = $currentPhoto;
        }
    } else {
        $photoUser = $currentPhoto;
    }

    $userDetails = [
        'user_id' => $userId,
        'username' => $username,
        'email' => $email,
        'first_name' => $firstName,
        'last_name' => $lastName,
        'address' => $address,
        'phone_number' => $phoneNumber,
        'birth_date' => $birthDate,
        'role_id' => $roleId,
        'seccion_id' => $seccionId,
        'photo_user' => $photoUser
    ];
    
    $result = $editController->updateUserDetails($userDetails);
    
    if ($result) {
        // Redirigir a la vista del perfil con un mensaje de éxito
        $_SESSION['message'] = "Perfil actualizado exitosamente.";
        header('Location: ProfileView.php?status=success');
    } else {
        // Redirigir a la vista del perfil con un mensaje de error
        $_SESSION['message'] = "Error al actualizar el perfil.";
        header('Location: ProfileView.php?status=error');
    }
}
?>
