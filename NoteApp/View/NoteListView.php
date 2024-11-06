<?php
session_start();

if (!isset($_SESSION['user'])) {
    header('Location: ../index.php');
    exit();
}

require_once '../Controller/NoteListController.php';
require_once '../Controller/NoteEditController.php';
require_once '../Controller/NoteDeleteController.php';
require_once '../Controller/NoteCreateController.php';

$listarNotas = new ListarNotas();
$editarNotas = new EditarNotas();
$borrarNotas = new BorrarNotas();
$nota = new Nota();

$limit = 6; // Número de notas por página
$page = isset($_GET['page']) ? $_GET['page'] : 1; // Página actual
$orderBy = isset($_GET['orderby']) ? $_GET['orderby'] : 'fecha_creacion'; // Campo de ordenación
$orderDir = isset($_GET['orderdir']) ? $_GET['orderdir'] : 'ASC'; // Dirección de ordenación
$offset = ($page - 1) * $limit;

$totalNotas = $listarNotas->getTotalNotas(); // Número total de notas
$totalPages = ceil($totalNotas / $limit); // Número total de páginas

$notas = $listarNotas->getNotas($limit, $offset, $orderBy, $orderDir);

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    if (isset($_POST['update_estado'])) {
        $id = $_POST['nota_id'];
        $estado = $_POST['estado'];
        if ($listarNotas->updateEstado($id, $estado)) {
            $mensaje = "El estado de la nota se ha actualizado correctamente.";
            $tipoMensaje = 'success';
        } else {
            $mensaje = "Error al actualizar el estado de la nota.";
            $tipoMensaje = 'error';
        }
    } elseif (isset($_POST['edit_nota'])) {
        $id = $_POST['nota_id'];
        $titulo = $_POST['titulo'];
        $contenido = $_POST['contenido'];
        if ($editarNotas->updateNota($id, $titulo, $contenido)) {
            $mensaje = "La nota se ha actualizado correctamente.";
            $tipoMensaje = 'success';
        } else {
            $mensaje = "Error al actualizar la nota.";
            $tipoMensaje = 'error';
        }
    } elseif (isset($_POST['delete_nota'])) {
        $id = $_POST['nota_id'];
        if ($borrarNotas->deleteNota($id)) {
            $mensaje = "La nota se ha eliminado correctamente.";
            $tipoMensaje = 'success';
        } else {
            $mensaje = "Error al eliminar la nota.";
            $tipoMensaje = 'error';
        }
    } elseif (isset($_POST['create_nota'])) {
        $titulo = $_POST['titulo'];
        $contenido = $_POST['contenido'];
        $user_id = $_SESSION['user']['id'];
        if ($nota->createNota($titulo, $contenido, $user_id)) {  // Pasar el user_id aquí
            $mensaje = "Nota creada exitosamente.";
            $tipoMensaje = 'success';
            // Actualizar la lista de notas después de crear una nueva nota
            $totalNotas = $listarNotas->getTotalNotas();
            $totalPages = ceil($totalNotas / $limit);
            $notas = $listarNotas->getNotas($limit, $offset, $orderBy, $orderDir); // Obtener todas las notas
        } else {
            $mensaje = "Error al crear la nota.";
            $tipoMensaje = 'error';
        }
    }
}
?>
<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="../../Framework/css/bootstrap.css">
    <link rel="stylesheet" href="../../Framework/custom/menu.css">
    <link rel="stylesheet" href="../../Framework/custom/notelistview.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
    <title>Tablero de Notas</title>
</head>

<body>
    <header>
        <nav class="navbar navbar-expand-lg navbar-custom">
            <div class="container-fluid">
                <a class="navbar-brand" href="#">Aplicacion de Notas</a>
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

    <div class="container mt-5">
        <h3 class="mb-4">Tablero de Notas</h3>

        <div class="bloque-botones">
            <button type="button" class="btn btn-success" data-bs-toggle="modal" data-bs-target="#createModal">Crear Nota</button>
        </div>

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

        <div class="mb-3 ordenar-fechas">
            <span>Ordenar por: </span>
            <a href="?orderby=fecha_creacion&orderdir=ASC" class="btn btn-outline-secondary btn-sm">Fecha Ascendente</a>
            <a href="?orderby=fecha_creacion&orderdir=DESC" class="btn btn-outline-secondary btn-sm">Fecha Descendente</a>
        </div>

        <div class="mb-3">
            <input type="text" id="searchInput" class="form-control" placeholder="Buscar nota...">
        </div>

        <div class="board" id="noteBoard">
            <?php if (!empty($notas)) : ?>
                <?php foreach ($notas as $nota) : ?>
                    <div class="note-card">
                        <h5 class="note-title"><?php echo htmlspecialchars($nota['titulo']); ?></h5>
                        <p class="note-content"><?php echo htmlspecialchars($nota['contenido']); ?></p>
                        <p class="note-meta">
                            <span class="note-user"><i class="fas fa-user"></i> <span class="username"><?php echo htmlspecialchars($nota['username']); ?></span></span>
                            <span class="note-date"><i class="fas fa-calendar"></i><span class="fecha_creacion"> <?php echo htmlspecialchars($nota['fecha_creacion']); ?></span></span>
                        </p>
                        <form action="" method="POST">
                            <input type="hidden" name="nota_id" value="<?php echo $nota['id']; ?>">
                            <div class="btn-group estado-buttons" role="group">
                                <button type="submit" name="estado" value="Vista" class="btn btn-vista" data-bs-toggle="tooltip" data-bs-placement="top" title="Vista"><i class="fas fa-eye"></i></button>
                                <button type="submit" name="estado" value="En Proceso" class="btn btn-info" data-bs-toggle="tooltip" data-bs-placement="top" title="En Proceso"><i class="fas fa-spinner"></i></button>
                                <button type="submit" name="estado" value="Completada" class="btn btn-success" data-bs-toggle="tooltip" data-bs-placement="top" title="Completada"><i class="fas fa-check"></i></button>
                            </div>
                            <input type="hidden" name="update_estado" value="1">
                        </form>
                        <p class="estado-nota">Estado: <?php echo htmlspecialchars($nota['estado']); ?></p>

                        <div class="btn-group edit-delete-buttons" role="group">
                            <button type="button" class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#editModal-<?php echo $nota['id']; ?>"><i class="fas fa-pencil-alt"></i></button>
                            <button type="button" class="btn btn-danger" data-bs-toggle="modal" data-bs-target="#deleteModal-<?php echo $nota['id']; ?>"><i class="fas fa-trash-alt"></i></button>
                        </div>

                        <!-- Modal para editar -->
                        <div class="modal fade" id="editModal-<?php echo $nota['id']; ?>" tabindex="-1" aria-labelledby="editModalLabel-<?php echo $nota['id']; ?>" aria-hidden="true">
                            <div class="modal-dialog">
                                <div class="modal-content">
                                    <div class="modal-header">
                                        <h5 class="modal-title" id="editModalLabel-<?php echo $nota['id']; ?>">Editar Nota</h5>
                                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                                    </div>
                                    <div class="modal-body">
                                        <form action="" method="POST">
                                            <div class="mb-3">
                                                <label for="titulo-<?php echo $nota['id']; ?>" class="form-label">Título</label>
                                                <input type="text" class="form-control" id="titulo-<?php echo $nota['id']; ?>" name="titulo" value="<?php echo htmlspecialchars($nota['titulo']); ?>" required>
                                            </div>
                                            <div class="mb-3">
                                                <label for="contenido-<?php echo $nota['id']; ?>" class="form-label">Contenido</label>
                                                <textarea class="form-control" id="contenido-<?php echo $nota['id']; ?>" name="contenido" rows="4" required><?php echo htmlspecialchars($nota['contenido']); ?></textarea>
                                            </div>
                                            <input type="hidden" name="nota_id" value="<?php echo $nota['id']; ?>">
                                            <button type="submit" name="edit_nota" class="btn btn-primary w-100">Guardar Cambios</button>
                                        </form>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Modal para eliminar -->
                        <div class="modal fade" id="deleteModal-<?php echo $nota['id']; ?>" tabindex="-1" aria-labelledby="deleteModalLabel-<?php echo $nota['id']; ?>" aria-hidden="true">
                            <div class="modal-dialog">
                                <div class="modal-content">
                                    <div class="modal-header">
                                        <h5 class="modal-title" id="deleteModalLabel-<?php echo $nota['id']; ?>">Eliminar Nota</h5>
                                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                                    </div>
                                    <div class="modal-body">
                                        <p>¿Estás seguro de que deseas eliminar esta nota?</p>
                                    </div>
                                    <div class="modal-footer">
                                        <form action="" method="POST">
                                            <input type="hidden" name="nota_id" value="<?php echo $nota['id']; ?>">
                                            <button type="submit" name="delete_nota" class="btn btn-danger">Eliminar</button>
                                            <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
                                        </form>
                                    </div>
                                </div>
                            </div>
                        </div>

                    </div>
                <?php endforeach; ?>
            <?php else : ?>
                <p>No hay notas disponibles.</p>
            <?php endif; ?>
        </div>

        <!-- Modal para crear nueva nota -->
        <div class="modal fade" id="createModal" tabindex="-1" aria-labelledby="createModalLabel" aria-hidden="true">
            <div class="modal-dialog">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title" id="createModalLabel">Crear Nueva Nota</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
                    <div class="modal-body">
                        <form action="" method="POST">
                            <div class="mb-3">
                                <label for="titulo" class="form-label">Título</label>
                                <input type="text" class="form-control" id="titulo" name="titulo" required>
                            </div>
                            <div class="mb-3">
                                <label for="contenido" class="form-label">Contenido</label>
                                <textarea class="form-control" id="contenido" name="contenido" rows="4" required></textarea>
                            </div>
                            <input type="hidden" name="user_id" value="<?php echo $_SESSION['user']['id']; ?>"> <!-- Aquí se pasa el user_id -->
                            <input type="hidden" name="create_nota" value="1">
                            <button type="submit" class="btn btn-primary w-100">Crear Nota</button>
                        </form>
                    </div>
                </div>
            </div>
        </div>

        <!-- Paginación -->
        <nav aria-label="Page navigation">
            <ul class="pagination justify-content-center">
                <?php if ($page > 1) : ?>
                    <li class="page-item">
                        <a class="page-link" href="?page=1&orderby=<?php echo $orderBy; ?>&orderdir=<?php echo $orderDir; ?>" aria-label="Primera">
                            <span aria-hidden="true">&laquo;&laquo;</span>
                        </a>
                    </li>
                    <li class="page-item">
                        <a class="page-link" href="?page=<?php echo $page - 1; ?>&orderby=<?php echo $orderBy; ?>&orderdir=<?php echo $orderDir; ?>" aria-label="Anterior">
                            <span aria-hidden="true">&laquo;</span>
                        </a>
                    </li>
                <?php endif; ?>

                <?php for ($i = 1; $i <= $totalPages; $i++) : ?>
                    <li class="page-item <?php echo $i == $page ? 'active' : ''; ?>">
                        <a class="page-link" href="?page=<?php echo $i; ?>&orderby=<?php echo $orderBy; ?>&orderdir=<?php echo $orderDir; ?>"><?php echo $i; ?></a>
                    </li>
                <?php endfor; ?>

                <?php if ($page < $totalPages) : ?>
                    <li class="page-item">
                        <a class="page-link" href="?page=<?php echo $page + 1; ?>&orderby=<?php echo $orderBy; ?>&orderdir=<?php echo $orderDir; ?>" aria-label="Siguiente">
                            <span aria-hidden="true">&raquo;</span>
                        </a>
                    </li>
                    <li class="page-item">
                        <a class="page-link" href="?page=<?php echo $totalPages; ?>&orderby=<?php echo $orderBy; ?>&orderdir=<?php echo $orderDir; ?>" aria-label="Última">
                            <span aria-hidden="true">&raquo;&raquo;</span>
                        </a>
                    </li>
                <?php endif; ?>
            </ul>
        </nav>
    </div>

    <script src="//cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/popper.js/2.11.6/umd/popper.min.js"></script>
    <script src="../../Framework/js/bootstrap.bundle.min.js"></script>
    <script src="../../Framework/js/bootstrap.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/js/all.min.js"></script>
    <script>
        var tooltipTriggerList = [].slice.call(document.querySelectorAll('[data-bs-toggle="tooltip"]'))
        var tooltipList = tooltipTriggerList.map(function(tooltipTriggerEl) {
            return new bootstrap.Tooltip(tooltipTriggerEl)
        })

        // Buscador de notas
        document.getElementById('searchInput').addEventListener('input', function() {
            var searchValue = this.value.toLowerCase().split(' ');
            var notes = document.getElementsByClassName('note-card');

            var filteredNotes = [];

            for (var i = 0; i < notes.length; i++) {
                var noteTitle = notes[i].getElementsByClassName('note-title')[0].innerText.toLowerCase();
                var noteContent = notes[i].getElementsByClassName('note-content')[0].innerText.toLowerCase();
                var noteUser = notes[i].getElementsByClassName('username')[0].innerText.toLowerCase();
                var noteEstado = notes[i].getElementsByClassName('estado-nota')[0].innerText.toLowerCase();

                var match = searchValue.every(function(val) {
                    return noteTitle.includes(val) || noteContent.includes(val) || noteUser.includes(val) || noteEstado.includes(val);
                });

                if (match) {
                    filteredNotes.push(notes[i]);
                }
            }

            // Ocultar todas las notas
            for (var j = 0; j < notes.length; j++) {
                notes[j].style.display = 'none';
            }

            // Mostrar notas filtradas en la primera página
            var limit = <?php echo $limit; ?>;
            for (var k = 0; k < filteredNotes.length; k++) {
                if (k < limit) {
                    filteredNotes[k].style.display = '';
                }
            }

            // Actualizar paginación
            updatePagination(filteredNotes.length, limit);
        });

        // Función para actualizar la paginación
        function updatePagination(totalFilteredNotes, limit) {
            var pagination = document.querySelector('.pagination');
            pagination.innerHTML = '';

            var totalPages = Math.ceil(totalFilteredNotes / limit);

            if (totalPages > 1) {
                var currentPage = 1;

                if (currentPage > 1) {
                    pagination.innerHTML += `
                        <li class="page-item">
                            <a class="page-link" href="javascript:void(0)" aria-label="Primera" onclick="showPage(1)">
                                <span aria-hidden="true">&laquo;&laquo;</span>
                            </a>
                        </li>
                        <li class="page-item">
                            <a class="page-link" href="javascript:void(0)" aria-label="Anterior" onclick="showPage(${currentPage - 1})">
                                <span aria-hidden="true">&laquo;</span>
                            </a>
                        </li>
                    `;
                }

                for (var i = 1; i <= totalPages; i++) {
                    pagination.innerHTML += `
                        <li class="page-item ${i == currentPage ? 'active' : ''}">
                            <a class="page-link" href="javascript:void(0)" onclick="showPage(${i})">${i}</a>
                        </li>
                    `;
                }

                if (currentPage < totalPages) {
                    pagination.innerHTML += `
                        <li class="page-item">
                            <a class="page-link" href="javascript:void(0)" aria-label="Siguiente" onclick="showPage(${currentPage + 1})">
                                <span aria-hidden="true">&raquo;</span>
                            </a>
                        </li>
                        <li class="page-item">
                            <a class="page-link" href="javascript:void(0)" aria-label="Última" onclick="showPage(${totalPages})">
                                <span aria-hidden="true">&raquo;&raquo;</span>
                            </a>
                        </li>
                    `;
                }
            }
        }

        // Función para mostrar las notas en la página seleccionada
        function showPage(page) {
            var notes = document.getElementsByClassName('note-card');
            var searchValue = document.getElementById('searchInput').value.toLowerCase().split(' ');
            var limit = <?php echo $limit; ?>;
            var offset = (page - 1) * limit;
            var filteredNotes = [];

            for (var i = 0; i < notes.length; i++) {
                var noteTitle = notes[i].getElementsByClassName('note-title')[0].innerText.toLowerCase();
                var noteContent = notes[i].getElementsByClassName('note-content')[0].innerText.toLowerCase();
                var noteUser = notes[i].getElementsByClassName('username')[0].innerText.toLowerCase();
                var noteEstado = notes[i].getElementsByClassName('estado-nota')[0].innerText.toLowerCase();

                var match = searchValue.every(function(val) {
                    return noteTitle.includes(val) || noteContent.includes(val) || noteUser.includes(val) || noteEstado.includes(val);
                });

                if (match) {
                    filteredNotes.push(notes[i]);
                }
            }

            // Ocultar todas las notas
            for (var j = 0; j < notes.length; j++) {
                notes[j].style.display = 'none';
            }

            // Mostrar notas filtradas en la página seleccionada
            for (var k = offset; k < offset + limit && k < filteredNotes.length; k++) {
                filteredNotes[k].style.display = '';
            }

            // Actualizar paginación
            updatePagination(filteredNotes.length, limit);
        }

        // Restaurar paginación y notas originales cuando se borra el texto del buscador
        document.getElementById('searchInput').addEventListener('input', function() {
            if (this.value === '') {
                var notes = document.getElementsByClassName('note-card');

                // Mostrar todas las notas
                for (var i = 0; i < notes.length; i++) {
                    notes[i].style.display = '';
                }

                // Restaurar paginación original
                updatePagination(<?php echo $totalNotas; ?>, <?php echo $limit; ?>);
            }
        });
    </script>
</body>

</html>