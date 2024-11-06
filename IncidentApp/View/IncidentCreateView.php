<?php
session_start();

if (!isset($_SESSION['user'])) {
    header('Location: ../../index.php');
    exit();
}


$user = $_SESSION['user'];

require_once '../../Config/Database.php';
require_once '../Controller/IncidentCreateController.php';

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $numeroRef = $_POST['numero_ref_reclamacion'];
    $asunto = $_POST['asunto'];
    $personaQueReclama = $_POST['persona_que_reclama'];
    $estadoId = $_POST['estado_id'];
    $fechaResolucion = $_POST['fecha_resolucion'];
    $verResultados = $_POST['ver_resultados'];
    $archivoAdjunto = $_FILES['archivo_adjunto'];

    // Procesar el archivo subido
    $uploadDir = '../../incidentApp/recursos_incidencias/';
    $uploadFile = $uploadDir . basename($archivoAdjunto['name']);
    if (move_uploaded_file($archivoAdjunto['tmp_name'], $uploadFile)) {
        $usuarioId = $_SESSION['user']['id']; // Asegúrate de que la sesión contiene el ID del usuario

        $incidentCreateController = new IncidentCreateController();
        $incidentCreateController->crearIncidencia($numeroRef, $asunto, $personaQueReclama, $estadoId, $fechaResolucion, $archivoAdjunto['name'], $usuarioId, $verResultados);
        
        $_SESSION['message'] = 'Incidencia creada correctamente'; // Añadir mensaje de éxito a la sesión
        header('Location: IncidentCreateView.php');
        exit();
    } else {
        $_SESSION['error'] = 'Error al subir el archivo.';
        header('Location: IncidentCreateView.php');
        exit();
    }
}

$incidentCreateController = new IncidentCreateController();
$estados = $incidentCreateController->getEstados();
$user = $_SESSION['user'];
?>

<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="../../Framework/css/bootstrap.min.css">
    <link rel="stylesheet" href="../../Framework/custom/createincidentview.css">
    <link rel="stylesheet" href="../../Framework/custom/menu.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@10"></script>
    <title>Crear Incidencia Individual</title>
</head>

<body>
    <header>
        <nav class="navbar navbar-expand-lg navbar-custom">
            <div class="container-fluid">
                <a class="navbar-brand" href="#">Incidencias</a>
                <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav" aria-controls="navbarNav" aria-expanded="false" aria-label="Toggle navigation">
                    <span class="navbar-toggler-icon"></span>
                </button>
                <div class="collapse navbar-collapse" id="navbarNav">
                    <ul class="navbar-nav me-auto mb-2 mb-lg-0">
                        <li class="nav-item">
                            <a class="nav-link active" aria-current="page" href="../../Login/View/ProfileView.php">Mi Perfil</a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link active" aria-current="page" href="../index.php">Volver a Gestion de Incidencias</a>
                        </li>
                    </ul>
                    <span class="navbar-text me-3">
                        Bienvenido, <?php echo htmlspecialchars($user['username']); ?>
                    </span>
                    <a href="../../Login/Logout/exit.php" class="btn btn-outline-danger">Cerrar Sesión</a>
                </div>
            </div>
        </nav>
    </header>

    <div class="container main-container">
        <div class="row">
            <div class="col-lg-12">
                <h2 class="crear-inc">Crear Incidencia Individual</h2>
            </div>
        </div>
        <div class="form-container">        
            <form action="IncidentCreateView.php" method="POST" enctype="multipart/form-data">
                <div class="row">
                    <div class="col-md-6">
                        <div class="mb-3">
                            <label for="fechaCreacion" class="form-label">Fecha de Creación</label>
                            <input type="date" class="form-control" id="fechaCreacion" name="fecha_creacion" value="<?= date('Y-m-d') ?>" disabled>
                        </div>
                        <div class="mb-3">
                            <label for="numeroRef" class="form-label">N° Ref. Reclamación</label>
                            <input type="text" class="form-control" id="numeroRef" name="numero_ref_reclamacion" required>
                        </div>
                        <div class="mb-3">
                            <label for="asunto" class="form-label">Asunto</label>
                            <input type="text" class="form-control" id="asunto" name="asunto" required>
                        </div>
                        <div class="mb-3">
                            <label for="personaQueReclama" class="form-label">Persona que reclama</label>
                            <input type="text" class="form-control" id="personaQueReclama" name="persona_que_reclama" required>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="mb-3">
                            <label for="fechaResolucion" class="form-label">Fecha de Resolución</label>
                            <input type="date" class="form-control" id="fechaResolucion" name="fecha_resolucion" required>
                        </div>
                        <div class="mb-3">
                            <label for="estado" class="form-label">Estado</label>
                            <select class="form-select" id="estado" name="estado_id" required>
                                <?php foreach ($estados as $estado) : ?>
                                    <option value="<?= $estado['id'] ?>"><?= $estado['estado_nombre'] ?></option>
                                <?php endforeach; ?>
                            </select>
                        </div>
                        <div class="mb-3">
                            <label for="verResultados" class="form-label">Ver Resultados (URL)</label>
                            <input type="text" class="form-control" id="verResultados" name="ver_resultados">
                        </div>
                        <div class="mb-3">
                            <label for="archivoAdjunto" class="form-label">Archivo Adjunto (ZIP)</label>
                            <input type="file" class="form-control" id="archivoAdjunto" name="archivo_adjunto" accept=".zip" required>
                        </div>
                    </div>
                </div>
                <div class="text-end">
                    <button type="submit" class="btn btn-primary">Crear Incidencia</button>
                </div>
            </form>
        </div>
    </div>

    <script src="../../Framework/js/bootstrap.bundle.min.js"></script>
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            <?php if (isset($_SESSION['message'])) : ?>
                Swal.fire({
                    icon: 'success',
                    title: 'Éxito',
                    text: '<?= $_SESSION['message'] ?>'
                });
                <?php unset($_SESSION['message']); ?>
            <?php elseif (isset($_SESSION['error'])) : ?>
                Swal.fire({
                    icon: 'error',
                    title: 'Error',
                    text: '<?= $_SESSION['error'] ?>'
                });
                <?php unset($_SESSION['error']); ?>
            <?php endif; ?>
        });
    </script>
</body>

</html>
