<?php
session_start();

if (!isset($_SESSION['user'])) {
    header('Location: ../index.php');
    exit();
}
$user = $_SESSION['user'];

require_once '../Controller/NoteCreateController.php';

$nota = new Nota();
$mensaje = '';
$tipoMensaje = '';

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $titulo = $_POST['titulo'];
    $contenido = $_POST['contenido'];

    if ($nota->createNota($titulo, $contenido, $user['id'])) {
        $mensaje = "Nota creada exitosamente.";
        $tipoMensaje = 'success';
    } else {
        $mensaje = "Error al crear la nota.";
        $tipoMensaje = 'error';
    }
}
?>

<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="../../Framework/css/bootstrap.min.css">
    <link rel="stylesheet" href="../../Framework/custom/noteapp.css">
    <link rel="stylesheet" href="../../Framework/custom/menu.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
    <title>Crear Nota</title>
</head>

<body>
    <nav class="navbar navbar-expand-lg navbar-custom">
        <div class="container-fluid">
            <a class="navbar-brand" href="#">App De Notas</a>
            <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav" aria-controls="navbarNav" aria-expanded="false" aria-label="Toggle navigation">
                <span class="navbar-toggler-icon"></span>
            </button>
            <div class="collapse navbar-collapse" id="navbarNav">
                <ul class="navbar-nav me-auto mb-2 mb-lg-0">
                    <li class="nav-item">
                        <a class="nav-link active" aria-current="page" href="../../Login/View/ProfileView.php">Mi Perfil</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link active" aria-current="page" href="../index.php">Volver a Gestion de Notas</a>
                    </li>
                </ul>
                <span class="navbar-text me-3">
                    Bienvenido, <?php echo htmlspecialchars($user['username']); ?>
                </span>
                <a href="../Login/Logout/exit.php" class="btn btn-outline-danger">Cerrar Sesión</a>
            </div>
        </div>
    </nav>

    <div class="container mt-5">
        <h3 class="mb-4">Crear Nueva Nota</h3>

        <?php if (!empty($mensaje)) : ?>
            <script src="//cdn.jsdelivr.net/npm/sweetalert2@11"></script>
            <script>
                Swal.fire({
                    icon: '<?php echo $tipoMensaje; ?>',
                    title: '<?php echo $tipoMensaje === 'success' ? 'Éxito' : 'Error'; ?>',
                    text: '<?php echo $mensaje; ?>'
                });
            </script>
        <?php endif; ?>

        <form action="" method="POST">
            <div class="mb-3">
                <label for="titulo" class="form-label">Título</label>
                <input type="text" class="form-control" id="titulo" name="titulo" required>
            </div>
            <div class="mb-3">
                <label for="contenido" class="form-label">Contenido</label>
                <textarea class="form-control" id="contenido" name="contenido" rows="4" required></textarea>
            </div>
            <input type="hidden" name="update_estado" value="1">
            <button type="submit" class="btn btn-primary w-100">Crear Nota</button>
        </form>
    </div>

    <script src="../../Framework/js/jquery-3.7.1.js"></script>
    <script src="../../Framework/js/bootstrap.bundle.min.js"></script>
    <script src="../../Framework/js/bootstrap.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/js/all.min.js"></script>

</body>

</html>