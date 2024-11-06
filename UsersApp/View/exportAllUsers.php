<?php
session_start();

if (!isset($_SESSION['user'])) {
    header('Location: ../index.php');
    exit();
}

require_once '../Controller/UsersReportController.php';

$reportController = new UsersReportController();
$usuarios = $reportController->obtenerUsuarios(0, -1, '');
$reportController->exportarUsuarios($usuarios, 'todos_usuarios_' . date('YmdHis') . '.xlsx');
?>
