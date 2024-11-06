<?php
session_start();

if (!isset($_SESSION['user'])) {
    header('Location: ../index.php');
    exit();
}

require_once '../Controller/AssignNotesController.php';

$assignNotesController = new AssignNotesController();
$users = $assignNotesController->getAllUsers();
$notes = $assignNotesController->getAllNotes();

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    if (isset($_POST['assign_notes'])) {
        $userId = $_POST['user_id'];
        $noteIds = $_POST['note_ids'];
        if ($assignNotesController->assignNotesToUser($userId, $noteIds)) {
            $mensaje = "Las notas se han asignado correctamente.";
            $tipoMensaje = 'success';
        } else {
            $mensaje = "Error al asignar las notas.";
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
    <link rel="stylesheet" href="../../Framework/custom/assignnotes.css">
    <link rel="stylesheet" href="../../Framework/custom/menu.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/sweetalert2@11/dist/sweetalert2.min.css">
    <title>Asignar Notas a Usuarios</title>
</head>

<body>
    <header>
        <nav class="navbar navbar-expand-lg navbar-custom fixed-top">
            <div class="container-fluid">
                <a class="navbar-brand" href="#">Aplicación de Notas</a>
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
                        Bienvenido, <?php echo htmlspecialchars($_SESSION['user']['username']); ?>
                    </span>
                    <a href="../../Login/Logout/exit.php" class="btn btn-outline-danger">Cerrar Sesión</a>
                </div>
            </div>
        </nav>
    </header>

    <div class="container-centered">
        <div>
            <h3 class="tit-global-asign">Asignar Notas a Usuarios</h3>
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
                        <input type="text" id="search_notes" class="form-control" placeholder="Buscar notas...">
                    </div>
                    <div class="select-notes-container">
                        <select multiple class="form-control" id="note_ids" name="note_ids[]" required>
                            <?php foreach ($notes as $note) : ?>
                                <option value="<?php echo $note['id']; ?>"><?php echo htmlspecialchars($note['titulo']); ?></option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                    <button type="submit" name="assign_notes" class="btn btn-primary w-100 btn-submit">Asignar Notas</button>
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
            $('#search_notes').on('input', function() {
                var search = $(this).val().toLowerCase();
                $('#note_ids option').each(function() {
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
                    text: 'Las notas se han asignado correctamente.'
                });
            } else if (status === 'error') {
                Swal.fire({
                    icon: 'error',
                    title: 'Error',
                    text: 'Hubo un problema al asignar las notas. Inténtalo de nuevo.'
                });
            }
        });
    </script>
</body>

</html>