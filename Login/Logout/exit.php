<?php
session_start();

require_once '../../Config/Database.php';
require_once '../Controller/LoginController.php';

// Crear una instancia del controlador
$loginController = new LoginController();

if (isset($_SESSION['user'])) {
    $userId = $_SESSION['user']['id'];
    // Registrar logout
    $loginController->registerLogout($userId);
}

// Destruir la sesión
session_unset();
session_destroy();

// Redireccionar al index
header('Location: ../../index.php');
exit();
?>
