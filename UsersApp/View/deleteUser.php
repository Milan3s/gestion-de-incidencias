<?php
session_start();

if (!isset($_SESSION['user'])) {
    header('Location: ../index.php');
    exit();
}

require_once '../Controller/DeleteUsersController.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['id'])) {
    $userId = $_POST['id'];

    $borrarUsuarios = new DeleteUsersController();
    $success = $borrarUsuarios->eliminarUsuario($userId);

    echo json_encode(['success' => $success]);
}
?>
