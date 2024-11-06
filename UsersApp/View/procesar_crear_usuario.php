<?php
require_once '../../Config/Database.php';
require_once '../../Login/Controller/RegisterController.php';

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $username = $_POST['username'];
    $email = $_POST['email'];
    $password = $_POST['password'];
    $role_id = $_POST['role_id'];

    $registerController = new Usuario();

    $result = $registerController->createUsuario($username, $password, $email, $role_id);

    if ($result) {
        echo json_encode(['success' => '1']);
    } else {
        echo json_encode(['success' => '0']);
    }
}
?>
