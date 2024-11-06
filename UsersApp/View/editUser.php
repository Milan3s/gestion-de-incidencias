<?php
session_start();

if (!isset($_SESSION['user'])) {
    header('Location: ../index.php');
    exit();
}

require_once '../Controller/EditUsersController.php';

$editUsersController = new EditUsersController();

if ($_SERVER['REQUEST_METHOD'] === 'GET' && isset($_GET['id'])) {
    $userId = $_GET['id'];
    $usuario = $editUsersController->obtenerUsuarioPorId($userId);
    $roles = $editUsersController->obtenerRoles();
    echo json_encode(['usuario' => $usuario, 'roles' => $roles]);
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $userId = $_POST['id'];
    $username = $_POST['username'];
    $email = $_POST['email'];
    $role_id = $_POST['role_id'];
    $success = $editUsersController->actualizarUsuario($userId, $username, $email, $role_id);
    echo json_encode(['success' => $success]);
}
?>
