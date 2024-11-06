<?php
session_start();

if (!isset($_SESSION['user'])) {
    header('Location: ../index.php');
    exit();
}

require_once '../Controller/UsersReportController.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['selectedUsers'])) {
    $ids = $_POST['selectedUsers'];

    $reportController = new UsersReportController();
    $usuarios = $reportController->obtenerUsuariosPorIds($ids);
    $reportController->exportarUsuarios($usuarios, 'usuarios_seleccionados_' . date('YmdHis') . '.xlsx');
}
?>
