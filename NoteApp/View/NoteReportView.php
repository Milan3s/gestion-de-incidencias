<?php
session_start();

if (!isset($_SESSION['user'])) {
    header('Location: ../index.php');
    exit();
}

$user = $_SESSION['user'];

require_once '../Controller/NoteReportController.php';

$reportController = new NoteReportController();

$perPageOptions = [5, 10, 15, 20, 'all'];
$limit = isset($_GET['limit']) ? $_GET['limit'] : 6;
$page = isset($_GET['page']) ? $_GET['page'] : 1;
$orderBy = isset($_GET['orderby']) ? $_GET['orderby'] : 'fecha_creacion';
$orderDir = isset($_GET['orderdir']) ? $_GET['orderdir'] : 'DESC';

if ($limit === 'all') {
    $totalNotas = $reportController->getTotalNotas();
    $totalPages = 1;
    $notas = $reportController->getReport($totalNotas, 0, $orderBy, $orderDir);
} else {
    $limit = (int)$limit;
    $offset = ($page - 1) * $limit;

    $totalNotas = $reportController->getTotalNotas();
    $totalPages = ceil($totalNotas / $limit);

    $notas = $reportController->getReport($limit, $offset, $orderBy, $orderDir);
}
?>

<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="../../Framework/css/bootstrap.min.css">
    <link rel="stylesheet" href="../../Framework/custom/notereport.css">
    <link rel="stylesheet" href="../../Framework/custom/menu.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
    <title>Reporte de Notas</title>
    <style>
        .estado-activo {
            background-color: #d4edda;
        }

        .estado-inactivo {
            background-color: #f8d7da;
        }

        .estado-pendiente {
            background-color: #fff3cd;
        }
    </style>
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
                            <a class="nav-link active" aria-current="page" href="../index.php">Volver a Gestión de Notas</a>
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
        <h3 class="mb-4">Reporte de Notas</h3>

        <div class="mb-4">
            <button class="btn btn-primary" onclick="exportReportPDF()">Exportar PDF</button>
            <button class="btn btn-secondary" onclick="exportReportCSV()">Exportar CSV</button>
        </div>

        <div class="mb-3">
            <span>Ordenar por: </span>
            <a href="?orderby=fecha_creacion&orderdir=ASC&limit=<?php echo $limit; ?>" class="btn btn-outline-secondary btn-sm">Fecha Ascendente</a>
            <a href="?orderby=fecha_creacion&orderdir=DESC&limit=<?php echo $limit; ?>" class="btn btn-outline-secondary btn-sm">Fecha Descendente</a>
        </div>

        <div class="mb-3">
            <label for="entriesPerPage">Mostrar:</label>
            <select id="entriesPerPage" class="form-select form-select-sm" onchange="changeEntriesPerPage()">
                <?php foreach ($perPageOptions as $option) : ?>
                    <option value="<?php echo $option; ?>" <?php echo ($limit == $option || ($limit === 'all' && $option === 'all')) ? 'selected' : ''; ?>>
                        <?php echo ($option === 'all') ? 'Todas' : $option; ?>
                    </option>
                <?php endforeach; ?>
            </select>
        </div>

        <table class="table table-striped">
            <thead>
                <tr>
                    <th>ID</th>
                    <th>Título</th>
                    <th>Contenido</th>
                    <th>Usuario</th>
                    <th>Fecha de Creación</th>
                    <th>Estado</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($notas as $nota) : ?>
                    <tr class="<?php echo 'estado-' . strtolower(htmlspecialchars($nota['estado'])); ?>">
                        <td><?php echo htmlspecialchars($nota['id']); ?></td>
                        <td><?php echo htmlspecialchars($nota['titulo']); ?></td>
                        <td><?php echo htmlspecialchars($nota['contenido']); ?></td>
                        <td><?php echo htmlspecialchars($nota['username']); ?></td>
                        <td><?php echo htmlspecialchars($nota['fecha_creacion']); ?></td>
                        <td><?php echo htmlspecialchars($nota['estado']); ?></td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>

        <!-- Paginación -->
        <?php if ($totalNotas > 12 && $limit !== 'all') : ?>
            <nav aria-label="Page navigation">
                <ul class="pagination justify-content-center">
                    <?php if ($page > 1) : ?>
                        <li class="page-item">
                            <a class="page-link" href="?page=1&orderby=<?php echo $orderBy; ?>&orderdir=<?php echo $orderDir; ?>&limit=<?php echo $limit; ?>" aria-label="Primera">
                                <span aria-hidden="true">&laquo;&laquo;</span>
                            </a>
                        </li>
                        <li class="page-item">
                            <a class="page-link" href="?page=<?php echo $page - 1; ?>&orderby=<?php echo $orderBy; ?>&orderdir=<?php echo $orderDir; ?>&limit=<?php echo $limit; ?>" aria-label="Anterior">
                                <span aria-hidden="true">&laquo;</span>
                            </a>
                        </li>
                    <?php endif; ?>

                    <?php for ($i = 1; $i <= $totalPages; $i++) : ?>
                        <li class="page-item <?php echo $i == $page ? 'active' : ''; ?>">
                            <a class="page-link" href="?page=<?php echo $i; ?>&orderby=<?php echo $orderBy; ?>&orderdir=<?php echo $orderDir; ?>&limit=<?php echo $limit; ?>"><?php echo $i; ?></a>
                        </li>
                    <?php endfor; ?>

                    <?php if ($page < $totalPages) : ?>
                        <li class="page-item">
                            <a class="page-link" href="?page=<?php echo $page + 1; ?>&orderby=<?php echo $orderBy; ?>&orderdir=<?php echo $orderDir; ?>&limit=<?php echo $limit; ?>" aria-label="Siguiente">
                                <span aria-hidden="true">&raquo;</span>
                            </a>
                        </li>
                        <li class="page-item">
                            <a class="page-link" href="?page=<?php echo $totalPages; ?>&orderby=<?php echo $orderBy; ?>&orderdir=<?php echo $orderDir; ?>&limit=<?php echo $limit; ?>" aria-label="Última">
                                <span aria-hidden="true">&raquo;&raquo;</span>
                            </a>
                        </li>
                    <?php endif; ?>
                </ul>
            </nav>
        <?php endif; ?>
    </div>

    <script src="../../Framework/js/jquery-3.7.1.js"></script>
    <script src="../../Framework/js/bootstrap.bundle.min.js"></script>
    <script src="../../Framework/js/bootstrap.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/js/all.min.js"></script>
    <script>
        function changeEntriesPerPage() {
            const limit = document.getElementById('entriesPerPage').value;
            const params = new URLSearchParams(window.location.search);
            params.set('limit', limit);
            params.set('page', 1); // Reiniciar a la primera página
            window.location.search = params.toString();
        }

        function exportReportPDF() {
            window.location.href = '../Controller/ExportPDFController.php';
        }

        function exportReportCSV() {
            window.location.href = '../Controller/ExportCSVController.php';
        }
    </script>

</body>

</html>