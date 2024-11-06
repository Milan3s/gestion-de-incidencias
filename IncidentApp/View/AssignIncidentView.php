<?php
session_start();

if (!isset($_SESSION['user'])) {
    header('Location: ../index.php');
    exit();
}

$user = $_SESSION['user'];

require_once '../Controller/AssignIncidentController.php';

$assignIncidentController = new AssignIncidentController();
$users = $assignIncidentController->getAllUsers();
$incidencias = $assignIncidentController->getAllIncidents();

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    if (isset($_POST['assign_incidencias'])) {
        $userId = $_POST['user_id'];
        $incidenciaIds = $_POST['incidencia_ids'];
        if ($assignIncidentController->assignIncidentsToUser($userId, $incidenciaIds)) {
            $mensaje = "Las incidencias se han asignado correctamente.";
            $tipoMensaje = 'success';
        } else {
            $mensaje = "Error al asignar las incidencias.";
            $tipoMensaje = 'error';
        }
    }
}

$status = isset($tipoMensaje) ? $tipoMensaje : '';
?>

<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="../../Framework/css/bootstrap.css">
    <link rel="stylesheet" href="../../Framework/custom/assignincident.css">
    <link rel="stylesheet" href="../../Framework/custom/menu.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/sweetalert2@11/dist/sweetalert2.min.css">
    <title>Asignar Incidencias a Usuarios</title>
</head>

<body>
    <header>
        <nav class="navbar navbar-expand-lg navbar-custom fixed-top">
            <div class="container-fluid">
                <a class="navbar-brand" href="#">Aplicación de Incidencias</a>
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
                        Bienvenido, <?php echo htmlspecialchars($_SESSION['user']['username']); ?>
                    </span>
                    <a href="../../Login/Logout/exit.php" class="btn btn-outline-danger">Cerrar Sesión</a>
                </div>
            </div>
        </nav>
    </header>

    <div class="container-centered">
        <div>
            <h3 class="tit-global-asign">Asignar Incidencias a Usuarios</h3>
            <div class="form-container">
                <?php if (isset($mensaje)) : ?>
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
                        <div class="select-arrow">
                            <select class="form-control" id="user_id" name="user_id" required>
                                <option value="">Selecciona un usuario...</option>
                                <?php foreach ($users as $user) : ?>
                                    <option value="<?php echo $user['id']; ?>"><?php echo htmlspecialchars($user['username']); ?></option>
                                <?php endforeach; ?>
                            </select>
                        </div>
                    </div>
                    <div class="search-box">
                        <input type="text" id="search_incidencias" class="form-control" placeholder="Buscar incidencias...">
                    </div>
                    <div class="select-incidencias-container">
                        <select multiple class="form-control" id="incidencia_ids" name="incidencia_ids[]" required>
                            <?php foreach ($incidencias as $incidencia) : ?>
                                <option value="<?php echo $incidencia['id']; ?>"><?php echo htmlspecialchars($incidencia['asunto']); ?></option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                    <button type="submit" name="assign_incidencias" class="btn btn-primary w-100 btn-submit">Asignar Incidencias</button>
                </form>
            </div>
        </div>
    </div>


    <script src="../../Framework/js/jquery-3.7.1.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/popper.js/2.11.6/umd/popper.min.js"></script>
    <script src="../../Framework/js/bootstrap.bundle.min.js"></script>
    <script src="../../Framework/js/bootstrap.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/js/all.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

    <script>
        $(document).ready(function() {
            $('#search_incidencias').on('input', function() {
                var search = $(this).val().toLowerCase();
                $('#incidencia_ids option').each(function() {
                    var text = $(this).text().toLowerCase();
                    if (text.indexOf(search) === -1) {
                        $(this).hide();
                    } else {
                        $(this).show();
                    }
                });
            });

            const status = "<?php echo $status; ?>";
            if (status === 'success') {
                Swal.fire({
                    icon: 'success',
                    title: 'Éxito',
                    text: 'Las incidencias se han asignado correctamente.'
                });
            } else if (status === 'error') {
                Swal.fire({
                    icon: 'error',
                    title: 'Error',
                    text: 'Hubo un problema al asignar las incidencias. Inténtalo de nuevo.'
                });
            }
        });
    </script>
</body>

</html>
