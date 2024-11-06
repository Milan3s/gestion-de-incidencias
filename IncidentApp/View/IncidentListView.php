
<?php
session_start();

if (!isset($_SESSION['user'])) {
    header('Location: ../../index.php');
    exit();
}

$user = $_SESSION['user'];

require '../../Framework/vendor/autoload.php'; // Carga el autoload de Composer
require_once '../../Config/Database.php';
require_once '../Controller/IncidentListController.php';
require_once '../Controller/IncidentCreateController.php';
require_once '../Controller/IncidentEditController.php';

$incidentListController = new IncidentListController();
$incidentCreateController = new IncidentCreateController();
$incidentEditController = new IncidentEditController();

// Paginación
$limit = 12; // Número de registros por página
$page = isset($_GET['page']) ? (int)$_GET['page'] : 1;
$offset = ($page - 1) * $limit;

$incidencias = $incidentListController->getIncidencias($limit, $offset);
$totalIncidencias = $incidentListController->getTotalIncidencias();
$totalPages = ceil($totalIncidencias / $limit);

$user = $_SESSION['user'];
$estados = $incidentCreateController->getEstados();

// Manejo de la creación, visualización y edición de incidencias
$incidencia = null;
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    if (isset($_POST['crear_incidencia'])) {
        $numeroRef = $incidentCreateController->getNextRefNumber(); // Obtener el siguiente número de referencia
        $asunto = $_POST['asunto'];
        $personaQueReclama = $_POST['persona_que_reclama'];
        $estadoId = $_POST['estado_id'];
        $fechaResolucion = $_POST['fecha_resolucion'];
        $verResultados = $_POST['ver_resultados'];
        $solucion = $_POST['solucion'];
        $archivoAdjunto = $_FILES['archivo_adjunto'];

        // Procesar el archivo subido
        $uploadDir = '../../incidentApp/recursos_incidencias/' . $numeroRef . '/';
        if (!is_dir($uploadDir)) {
            mkdir($uploadDir, 0777, true);
        }
        $uploadFile = $uploadDir . basename($archivoAdjunto['name']);
        if (move_uploaded_file($archivoAdjunto['tmp_name'], $uploadFile)) {
            $usuarioId = $_SESSION['user']['id']; // Asegúrate de que la sesión contiene el ID del usuario

            $incidentCreateController->crearIncidencia($numeroRef, $asunto, $personaQueReclama, $estadoId, $fechaResolucion, $archivoAdjunto['name'], $usuarioId, $verResultados, $solucion);

            $_SESSION['message'] = 'Incidencia creada correctamente'; // Añadir mensaje de éxito a la sesión
            header('Location: IncidentListView.php');
            exit();
        } else {
            $_SESSION['error'] = 'Error al subir el archivo.';
            header('Location: IncidentListView.php');
            exit();
        }
    } elseif (isset($_POST['ver_incidencia'])) {
        $incidenciaId = $_POST['incidencia_id'];
        $incidencia = $incidentListController->getIncidenciaById($incidenciaId);
    } elseif (isset($_POST['editar_incidencia'])) {
        $id = $_POST['id'];
        $asunto = $_POST['asunto'];
        $personaQueReclama = $_POST['persona_que_reclama'];
        $estadoId = $_POST['estado_id'];
        $fechaResolucion = $_POST['fecha_resolucion'];
        $verResultados = $_POST['ver_resultados'];
        $solucion = $_POST['solucion'];
        $archivoAdjunto = isset($_FILES['archivo_adjunto']) ? $_FILES['archivo_adjunto'] : null;

        $archivoAdjuntoNombre = null;
        if ($archivoAdjunto && $archivoAdjunto['tmp_name']) {
            $numeroRef = $incidentCreateController->getNextRefNumber(); // Obtener el siguiente número de referencia
            $uploadDir = '../../incidentApp/recursos_incidencias/' . $numeroRef . '/';
            if (!is_dir($uploadDir)) {
                mkdir($uploadDir, 0777, true);
            }
            $uploadFile = $uploadDir . basename($archivoAdjunto['name']);
            if (move_uploaded_file($archivoAdjunto['tmp_name'], $uploadFile)) {
                $archivoAdjuntoNombre = $archivoAdjunto['name'];
            }
        }

        $success = $incidentEditController->actualizarIncidencia($id, $asunto, $personaQueReclama, $estadoId, $fechaResolucion, $archivoAdjuntoNombre, $verResultados, $solucion);
        
        if ($success) {
            $_SESSION['message'] = 'Incidencia actualizada correctamente';
        } else {
            $_SESSION['error'] = 'Error al actualizar la incidencia';
        }
        header('Location: IncidentListView.php');
        exit();
    }
}
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="../../Framework/css/bootstrap.min.css">
    <link rel="stylesheet" href="../../Framework/custom/incidentlistview.css">
    <link rel="stylesheet" href="../../Framework/custom/menu.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@10"></script>
    <title>Las incidencias</title>
    <style>
        table tbody td {
            text-align: center; /* Centrar texto en las celdas de la tabla */
            vertical-align: middle; /* Alinear verticalmente al centro */
        }
    </style>
</head>
<body>
    <?php if (isset($_SESSION['message'])): ?>
        <script>
            Swal.fire({
                icon: 'success',
                title: 'Éxito',
                text: '<?php echo $_SESSION['message']; ?>'
            });
        </script>
        <?php unset($_SESSION['message']); ?>
    <?php endif; ?>

    <?php if (isset($_SESSION['error'])): ?>
        <script>
            Swal.fire({
                icon: 'error',
                title: 'Error',
                text: '<?php echo $_SESSION['error']; ?>'
            });
        </script>
        <?php unset($_SESSION['error']); ?>
    <?php endif; ?>

    <header>
        <nav class="navbar navbar-expand-lg navbar-custom navbar-light bg-light">
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
    <div class="container-fluid mt-4">
        <div class="row">
            <div class="col-12">
                <div class="action-buttons">
                    <button class="btn btn-dark" data-bs-toggle="modal" data-bs-target="#crearIncidenciaModal">Crear Incidencia</button>
                    <button class="btn btn-dark" id="btnVerIncidencia">Ver I. completa</button>
                    <button class="btn btn-dark" id="btnExportarIncidencias">Exportar I. seleccionadas</button>
                    <button class="btn btn-dark" id="btnExportarTodasIncidencias">Exportar Todas las I.</button>
                    <button class="btn btn-dark" id="btnIncidenciasResueltas">I. Resueltas</button>
                    <button class="btn btn-dark" id="btnMostrarTodasIncidencias" style="display: none;">Mostrar Todas las Incidencias</button>
                </div>

                <form id="exportForm" action="export_incidents.php" method="POST" style="display:none;">
                    <input type="hidden" name="selected_ids" id="selectedIds">
                    <input type="hidden" name="export_all" id="exportAll" value="0">
                </form>

                <div class="input-group">
                    <input type="text" class="form-control" placeholder="Buscar incidencia..." aria-label="Buscar incidencia..." aria-describedby="search-icon" id="searchInput">
                </div>

                <div class="table-container" id="tableContainer">
                    <table class="table table-bordered incident-table">
                        <thead class="table-light">
                            <tr>
                                <th><input type="checkbox" id="select-all"></th>
                                <th>N° Ref. Reclamación</th>
                                <th>Asunto</th>
                                <th>Persona que reclama</th>
                                <th>Estado</th>
                                <th>Ver Resultado</th>
                                <th>Fecha de creación</th>
                                <th>Fecha de resolución</th>
                                <th>Solución</th>
                                <th>Backup Archivos</th>
                                <th class="action-column">Acciones</th>
                            </tr>
                        </thead>
                        <tbody id="incidentTableBody">
                            <?php foreach ($incidencias as $incidencia) : ?>
                                <tr class="incident-item">
                                    <td><input type="checkbox" class="incident-checkbox" value="<?= htmlspecialchars($incidencia['id']) ?>"></td>
                                    <td><?= htmlspecialchars($incidencia['numero_ref_reclamacion']) ?></td>
                                    <td><?= htmlspecialchars($incidencia['asunto']) ?></td>
                                    <td><?= htmlspecialchars($incidencia['persona_que_reclama']) ?></td>
                                    <td><?= htmlspecialchars($incidencia['estado_nombre']) ?></td>
                                    <td>
                                        <?php if (!empty($incidencia['ver_resultados'])) : ?>
                                            <a href="<?= htmlspecialchars($incidencia['ver_resultados']) ?>" class="link-boton" target="_blank">Link</a>
                                        <?php else : ?>
                                            No hay link asignado
                                        <?php endif; ?>
                                    </td>
                                    <td><?= htmlspecialchars(date('d/m/Y', strtotime($incidencia['fecha_de_creacion']))) ?></td>
                                    <td><?= htmlspecialchars($incidencia['fecha_resolucion'] ? date('d/m/Y', strtotime($incidencia['fecha_resolucion'])) : 'N/A') ?></td>
                                    <td><?= htmlspecialchars($incidencia['solucion']) ?></td>
                                    <td><a href="../recursos_incidencias/<?= htmlspecialchars($incidencia['numero_ref_reclamacion']) . '/' . htmlspecialchars($incidencia['archivos_adjuntos']) ?>" download><span class="zip-icon">ZIP</span></a></td>
                                    <td class="action-buttons-table action-column">
                                        <button class="btn btn-sm btn-outline-primary btn-edit" data-id="<?= htmlspecialchars($incidencia['id']) ?>"><i class="fas fa-edit"></i></button>
                                        <button class="btn btn-sm btn-outline-danger btn-delete" data-id="<?= htmlspecialchars($incidencia['id']) ?>"><i class="fas fa-trash"></i></button>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>

                <form id="deleteForm" action="processa_eliminar.php" method="POST" style="display:none;">
                    <input type="hidden" name="id" id="deleteIncidentId">
                </form>

                <nav>
                    <ul class="pagination justify-content-center" id="pagination">
                        <?php if ($page > 1) : ?>
                            <li class="page-item"><a class="page-link" href="?page=<?= $page - 1 ?>">Anterior</a></li>
                        <?php endif; ?>
                        <?php for ($i = 1; $i <= $totalPages; $i++) : ?>
                            <li class="page-item <?= $i == $page ? 'active' : '' ?>"><a class="page-link" href="?page=<?= $i ?>"><?= $i ?></a></li>
                        <?php endfor; ?>
                        <?php if ($page < $totalPages) : ?>
                            <li class="page-item"><a class="page-link" href="?page=<?= $page + 1 ?>">Siguiente</a></li>
                        <?php endif; ?>
                    </ul>
                </nav>
            </div>
        </div>
    </div>

    <!-- Modal para Crear Incidencia -->
    <div class="modal fade" id="crearIncidenciaModal" tabindex="-1" aria-labelledby="crearIncidenciaModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered modal-lg">
            <div class="modal-content" style="color: #000; background-color: rgba(255, 255, 255, 0.9);">
                <div class="modal-header">
                    <h5 class="modal-title" id="crearIncidenciaModalLabel">Crear Nueva Incidencia</h5>
                </div>
                <form action="IncidentListView.php" method="POST" enctype="multipart/form-data">
                    <div class="modal-body">
                        <div class="container">
                            <div class="row">
                                <div class="col-md-6">
                                    <div class="mb-3">
                                        <label for="fechaCreacion" class="form-label">Fecha de Creación</label>
                                        <input type="date" class="form-control" id="fechaCreacion" name="fecha_creacion" value="<?= date('Y-m-d') ?>" disabled>
                                    </div>
                                    <div class="mb-3">
                                        <label for="numeroRef" class="form-label">N° Ref. Reclamación</label>
                                        <input type="text" class="form-control" id="numeroRef" name="numero_ref_reclamacion" value="<?= htmlspecialchars($incidentCreateController->getNextRefNumber()) ?>" disabled>
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
                                        <label for="solucion" class="form-label">Solución</label>
                                        <input type="text" class="form-control" id="solucion" name="solucion" required>
                                    </div>
                                    <div class="mb-3">
                                        <label for="archivoAdjunto" class="form-label">Archivo Adjunto (ZIP)</label>
                                        <input type="file" class="form-control" id="archivoAdjunto" name="archivo_adjunto" accept=".zip" required>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-darkwhite1" data-bs-dismiss="modal">Cerrar</button>
                        <button type="submit" class="btn btn-darkwhite2" name="crear_incidencia">Crear Incidencia</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <!-- Modal para Ver Incidencia -->
    <div class="modal fade" id="verIncidenciaModal" tabindex="-1" aria-labelledby="verIncidenciaModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered modal-lg">
            <div class="modal-content" style="color: #000; background-color: rgba(255, 255, 255, 0.9);">
                <div class="modal-header">
                    <h5 class="modal-title" id="verIncidenciaModalLabel">Detalles de la Incidencia</h5>
                </div>
                <div class="modal-body">
                    <div class="container">
                        <div class="row">
                            <div class="col-md-12">
                                <p><strong>N° Ref. Reclamación:</strong> <span id="modalNumeroRef"></span></p>
                                <p><strong>Asunto:</strong> <span id="modalAsunto"></span></p>
                                <p><strong>Persona que reclama:</strong> <span id="modalPersonaQueReclama"></span></p>
                                <p><strong>Estado:</strong> <span id="modalEstado"></span></p>
                                <p><strong>Fecha de Creación:</strong> <span id="modalFechaCreacion"></span></p>
                                <p><strong>Fecha de Resolución:</strong> <span id="modalFechaResolucion"></span></p>
                                <p><strong>Ver Resultados (URL):</strong> <span id="modalVerResultados"></span></p>
                                <p><strong>Solución:</strong> <span id="modalSolucion"></span></p>
                                <p><strong>Archivo Adjunto (ZIP):</strong> <span id="modalArchivoAdjunto"></span></p>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-darkwhite1" data-bs-dismiss="modal">Cerrar</button>
                </div>
            </div>
        </div>
    </div>

    <!-- Modal para Editar Incidencia -->
    <div class="modal fade" id="editarIncidenciaModal" tabindex="-1" aria-labelledby="editarIncidenciaModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered modal-lg">
            <div class="modal-content" style="color: #000; background-color: rgba(255, 255, 255, 0.9);">
                <div class="modal-header">
                    <h5 class="modal-title" id="editarIncidenciaModalLabel">Editar Incidencia</h5>
                </div>
                <form id="editarIncidenciaForm" action="IncidentListView.php" method="POST" enctype="multipart/form-data">
                    <div class="modal-body">
                        <div class="container">
                            <div class="row">
                                <div class="col-md-6">
                                    <div class="mb-3">
                                        <label for="editarFechaCreacion" class="form-label">Fecha de Creación</label>
                                        <input type="date" class="form-control" id="editarFechaCreacion" name="fecha_creacion" disabled>
                                    </div>
                                    <div class="mb-3">
                                        <label for="editarNumeroRef" class="form-label">N° Ref. Reclamación</label>
                                        <input type="text" class="form-control" id="editarNumeroRef" name="numero_ref_reclamacion" disabled>
                                    </div>
                                    <div class="mb-3">
                                        <label for="editarAsunto" class="form-label">Asunto</label>
                                        <input type="text" class="form-control" id="editarAsunto" name="asunto" required>
                                    </div>
                                    <div class="mb-3">
                                        <label for="editarPersonaQueReclama" class="form-label">Persona que reclama</label>
                                        <input type="text" class="form-control" id="editarPersonaQueReclama" name="persona_que_reclama" required>
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="mb-3">
                                        <label for="editarFechaResolucion" class="form-label">Fecha de Resolución</label>
                                        <input type="date" class="form-control" id="editarFechaResolucion" name="fecha_resolucion" required>
                                    </div>
                                    <div class="mb-3">
                                        <label for="editarEstado" class="form-label">Estado</label>
                                        <select class="form-select" id="editarEstado" name="estado_id" required>
                                            <?php foreach ($estados as $estado) : ?>
                                                <option value="<?= $estado['id'] ?>"><?= $estado['estado_nombre'] ?></option>
                                            <?php endforeach; ?>
                                        </select>
                                    </div>
                                    <div class="mb-3">
                                        <label for="editarVerResultados" class="form-label">Ver Resultados (URL)</label>
                                        <input type="text" class="form-control" id="editarVerResultados" name="ver_resultados">
                                    </div>
                                    <div class="mb-3">
                                        <label for="editarSolucion" class="form-label">Solución</label>
                                        <input type="text" class="form-control" id="editarSolucion" name="solucion" required>
                                    </div>
                                    <div class="mb-3">
                                        <label for="editarArchivoAdjunto" class="form-label">Archivo Adjunto (ZIP)</label>
                                        <input type="file" class="form-control" id="editarArchivoAdjunto" name="archivo_adjunto" accept=".zip">
                                    </div>
                                    <input type="hidden" name="id" id="editarId">
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-darkwhite1" data-bs-dismiss="modal">Cerrar</button>
                        <button type="submit" class="btn btn-darkwhite2" name="editar_incidencia">Guardar Cambios</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <script src="../../Framework/js/jquery-3.7.1.js"></script>
    <script src="../../Framework/js/bootstrap.min.js"></script>
    <script src="../../Framework/js/bootstrap.bundle.min.js"></script>

    <script>
        document.addEventListener('DOMContentLoaded', function() {
            document.getElementById('btnVerIncidencia').addEventListener('click', function() {
                const selectedCheckbox = document.querySelector('.incident-checkbox:checked');
                if (selectedCheckbox) {
                    const incidentId = selectedCheckbox.value;
                    fetchIncidentDetails(incidentId);
                } else {
                    Swal.fire({
                        icon: 'warning',
                        title: 'Advertencia',
                        text: 'Por favor, selecciona una incidencia para ver los detalles.'
                    });
                }
            });

            document.getElementById('btnExportarIncidencias').addEventListener('click', function() {
                const selectedCheckboxes = document.querySelectorAll('.incident-checkbox:checked');
                if (selectedCheckboxes.length > 0) {
                    const selectedIds = Array.from(selectedCheckboxes).map(cb => cb.value);
                    document.getElementById('selectedIds').value = JSON.stringify(selectedIds);
                    document.getElementById('exportAll').value = '0';
                    document.getElementById('exportForm').submit();
                } else {
                    Swal.fire({
                        icon: 'warning',
                        title: 'Advertencia',
                        text: 'Por favor, selecciona al menos una incidencia para exportar.'
                    });
                }
            });

            document.getElementById('btnExportarTodasIncidencias').addEventListener('click', function() {
                document.getElementById('selectedIds').value = '';
                document.getElementById('exportAll').value = '1';
                document.getElementById('exportForm').submit();
            });

            document.getElementById('btnIncidenciasResueltas').addEventListener('click', function() {
                fetch('filter_resolved_incidents.php')
                    .then(response => response.json())
                    .then(data => {
                        if (data.success) {
                            updateTable(data.incidencias);
                            document.getElementById('btnIncidenciasResueltas').style.display = 'none';
                            document.getElementById('btnMostrarTodasIncidencias').style.display = 'inline-block';
                            document.querySelectorAll('.action-column').forEach(column => {
                                column.style.display = 'none';
                            });
                        } else {
                            Swal.fire({
                                icon: 'error',
                                title: 'Error',
                                text: 'No se pudieron cargar las incidencias resueltas.'
                            });
                        }
                    })
                    .catch(error => {
                        console.error('Error fetching resolved incidents:', error);
                        Swal.fire({
                            icon: 'error',
                            title: 'Error',
                            text: 'Ocurrió un error al intentar obtener las incidencias resueltas.'
                        });
                    });
            });

            document.getElementById('btnMostrarTodasIncidencias').addEventListener('click', function() {
                fetch('get_all_incidents.php')
                    .then(response => response.json())
                    .then(data => {
                        if (data.success) {
                            updateTable(data.incidencias);
                            document.getElementById('btnMostrarTodasIncidencias').style.display = 'none';
                            document.getElementById('btnIncidenciasResueltas').style.display = 'inline-block';
                            document.querySelectorAll('.action-column').forEach(column => {
                                column.style.display = 'table-cell';
                            });
                        } else {
                            Swal.fire({
                                icon: 'error',
                                title: 'Error',
                                text: 'No se pudieron cargar todas las incidencias.'
                            });
                        }
                    })
                    .catch(error => {
                        console.error('Error fetching all incidents:', error);
                        Swal.fire({
                            icon: 'error',
                            title: 'Error',
                            text: 'Ocurrió un error al intentar obtener todas las incidencias.'
                        });
                    });
            });

            document.getElementById('searchInput').addEventListener('input', function() {
                const searchValue = this.value;
                fetch('search_incidents.php', {
                        method: 'POST',
                        headers: {
                            'Content-Type': 'application/json'
                        },
                        body: JSON.stringify({ referencia: searchValue })
                    })
                    .then(response => response.json())
                    .then(data => {
                        if (data.success) {
                            updateTable(data.incidencias);
                        } else {
                            Swal.fire({
                                icon: 'error',
                                title: 'Error',
                                text: 'No se encontraron incidencias con esa referencia.'
                            });
                        }
                    })
                    .catch(error => {
                        console.error('Error searching incidents:', error);
                        Swal.fire({
                            icon: 'error',
                            title: 'Error',
                            text: 'Ocurrió un error al intentar buscar las incidencias.'
                        });
                    });
            });

            document.getElementById('select-all').addEventListener('change', function() {
                const checkboxes = document.querySelectorAll('.incident-checkbox');
                checkboxes.forEach(checkbox => {
                    checkbox.checked = this.checked;
                });
            });

            document.querySelectorAll('.btn-edit').forEach(button => {
                button.addEventListener('click', function() {
                    const incidentId = this.getAttribute('data-id');
                    fetchIncidentDetails(incidentId, true);
                });
            });

            document.querySelectorAll('.btn-delete').forEach(button => {
                button.addEventListener('click', function() {
                    const incidentId = this.getAttribute('data-id');
                    Swal.fire({
                        title: '¿Estás seguro?',
                        text: "¡No podrás revertir esto!",
                        icon: 'warning',
                        showCancelButton: true,
                        confirmButtonColor: '#3085d6',
                        cancelButtonColor: '#d33',
                        confirmButtonText: 'Sí, eliminarla'
                    }).then((result) => {
                        if (result.isConfirmed) {
                            document.getElementById('deleteIncidentId').value = incidentId;
                            document.getElementById('deleteForm').submit();
                        }
                    });
                });
            });

            function fetchIncidentDetails(id, isEdit = false) {
                fetch('get_incident_details.php', {
                        method: 'POST',
                        headers: {
                            'Content-Type': 'application/json'
                        },
                        body: JSON.stringify({ id })
                    })
                    .then(response => response.json())
                    .then(data => {
                        if (data.success) {
                            if (isEdit) {
                                document.getElementById('editarFechaCreacion').value = data.incident.fecha_de_creacion;
                                document.getElementById('editarNumeroRef').value = data.incident.numero_ref_reclamacion;
                                document.getElementById('editarAsunto').value = data.incident.asunto;
                                document.getElementById('editarPersonaQueReclama').value = data.incident.persona_que_reclama;
                                document.getElementById('editarFechaResolucion').value = data.incident.fecha_resolucion;
                                document.getElementById('editarEstado').value = data.incident.estado_id;
                                document.getElementById('editarVerResultados').value = data.incident.ver_resultados;
                                document.getElementById('editarSolucion').value = data.incident.solucion;
                                document.getElementById('editarId').value = data.incident.id;

                                var editarIncidenciaModal = new bootstrap.Modal(document.getElementById('editarIncidenciaModal'));
                                editarIncidenciaModal.show();
                            } else {
                                document.getElementById('modalNumeroRef').textContent = data.incident.numero_ref_reclamacion;
                                document.getElementById('modalAsunto').textContent = data.incident.asunto;
                                document.getElementById('modalPersonaQueReclama').textContent = data.incident.persona_que_reclama;
                                document.getElementById('modalEstado').textContent = data.incident.estado_nombre;
                                document.getElementById('modalFechaCreacion').textContent = new Date(data.incident.fecha_de_creacion).toLocaleDateString('es-ES', {
                                    day: '2-digit',
                                    month: '2-digit',
                                    year: 'numeric'
                                });
                                document.getElementById('modalFechaResolucion').textContent = data.incident.fecha_resolucion ? new Date(data.incident.fecha_resolucion).toLocaleDateString('es-ES', {
                                    day: '2-digit',
                                    month: '2-digit',
                                    year: 'numeric'
                                }) : 'N/A';
                                document.getElementById('modalVerResultados').textContent = data.incident.ver_resultados || 'No hay link asignado';
                                document.getElementById('modalSolucion').textContent = data.incident.solucion || 'No hay solución asignada';
                                document.getElementById('modalArchivoAdjunto').textContent = data.incident.archivos_adjuntos || 'N/A';

                                var verIncidenciaModal = new bootstrap.Modal(document.getElementById('verIncidenciaModal'));
                                verIncidenciaModal.show();
                            }
                        } else {
                            Swal.fire({
                                icon: 'error',
                                title: 'Error',
                                text: 'No se pudo cargar la información de la incidencia.'
                            });
                        }
                    })
                    .catch(error => {
                        console.error('Error fetching incident details:', error);
                        Swal.fire({
                            icon: 'error',
                            title: 'Error',
                            text: 'Ocurrió un error al intentar obtener los detalles de la incidencia.'
                        });
                    });
            }

            function updateTable(incidencias) {
                let tableBodyHtml = '';
                incidencias.forEach(incidencia => {
                    tableBodyHtml += `<tr class="incident-item">
                                        <td><input type="checkbox" class="incident-checkbox" value="${incidencia.id}"></td>
                                        <td>${incidencia.numero_ref_reclamacion}</td>
                                        <td>${incidencia.asunto}</td>
                                        <td>${incidencia.persona_que_reclama}</td>
                                        <td>${incidencia.estado_nombre}</td>
                                        <td>${incidencia.ver_resultados ? `<a href="${incidencia.ver_resultados}" class="link-boton" target="_blank">Link</a>` : 'No hay link asignado'}</td>
                                        <td>${new Date(incidencia.fecha_de_creacion).toLocaleDateString('es-ES', { day: '2-digit', month: '2-digit', year: 'numeric' })}</td>
                                        <td>${incidencia.fecha_resolucion ? new Date(incidencia.fecha_resolucion).toLocaleDateString('es-ES', { day: '2-digit', month: '2-digit', year: 'numeric' }) : 'N/A'}</td>
                                        <td>${incidencia.solucion}</td>
                                        <td><a href="../recursos_incidencias/${incidencia.numero_ref_reclamacion}/${incidencia.archivos_adjuntos}" download><span class="zip-icon">ZIP</span></a></td>
                                        <td class="action-buttons-table action-column">
                                            <button class="btn btn-sm btn-outline-primary btn-edit" data-id="${incidencia.id}"><i class="fas fa-edit"></i></button>
                                            <button class="btn btn-sm btn-outline-danger btn-delete" data-id="${incidencia.id}"><i class="fas fa-trash"></i></button>
                                        </td>
                                      </tr>`;
                });
                document.getElementById('incidentTableBody').innerHTML = tableBodyHtml;
            }
        });
    </script>
</body>
</html>
