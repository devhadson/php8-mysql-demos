<?php
/**
 * Página principal - Listado de usuarios
 * 
 * Muestra una tabla con todos los usuarios registrados en el sistema.
 * Incluye funcionalidades de búsqueda y paginación.
 */

require_once 'database/conexion.php';

// Iniciar sesión para mensajes flash
session_start();

// Conectar a la base de datos
$pdo = conectarBD();

// Verificar conexión
if (!$pdo) {
    die("Error al conectar con la base de datos");
}

// Manejar mensajes flash
$mensaje = '';
$tipo_mensaje = 'success'; // success, danger, warning, info

if (isset($_SESSION['mensaje'])) {
    $mensaje = $_SESSION['mensaje'];
    unset($_SESSION['mensaje']);
}

if (isset($_SESSION['error'])) {
    $mensaje = $_SESSION['error'];
    $tipo_mensaje = 'danger';
    unset($_SESSION['error']);
}

// Configuración de paginación
$registrosPorPagina = 5;
$pagina = isset($_GET['pagina']) ? (int)$_GET['pagina'] : 1;
if ($pagina < 1) $pagina = 1;
$offset = ($pagina - 1) * $registrosPorPagina;

// Manejar búsqueda
$busqueda = isset($_GET['busqueda']) ? trim($_GET['busqueda']) : '';

// Obtener total de registros
if (!empty($busqueda)) {
    $sqlTotal = "SELECT COUNT(*) as total FROM usuarios WHERE (nombre LIKE ? OR email LIKE ?)";
    $stmtTotal = $pdo->prepare($sqlTotal);
    $stmtTotal->execute(["%$busqueda%", "%$busqueda%"]);
} else {
    $sqlTotal = "SELECT COUNT(*) as total FROM usuarios";
    $stmtTotal = $pdo->prepare($sqlTotal);
    $stmtTotal->execute();
}

$totalRegistros = $stmtTotal->fetch()['total'];
$totalPaginas = ceil($totalRegistros / $registrosPorPagina);

// Ajustar página si es necesario
if ($pagina > $totalPaginas && $totalPaginas > 0) {
    $pagina = $totalPaginas;
    $offset = ($pagina - 1) * $registrosPorPagina;
}

// Obtener usuarios para la página actual
if (!empty($busqueda)) {
    $sql = "SELECT * FROM usuarios WHERE (nombre LIKE ? OR email LIKE ?) ORDER BY fecha_registro DESC LIMIT ? OFFSET ?";
    $stmt = $pdo->prepare($sql);
    $stmt->execute(["%$busqueda%", "%$busqueda%", $registrosPorPagina, $offset]);
} else {
    $sql = "SELECT * FROM usuarios ORDER BY fecha_registro DESC LIMIT ? OFFSET ?";
    $stmt = $pdo->prepare($sql);
    $stmt->execute([$registrosPorPagina, $offset]);
}

$usuarios = $stmt->fetchAll();

// Obtener estadísticas
$sqlActivos = "SELECT COUNT(*) as activos FROM usuarios WHERE activo = 1";
$activos = $pdo->query($sqlActivos)->fetch()['activos'];

$sqlRecientes = "SELECT COUNT(*) as recientes FROM usuarios 
                 WHERE fecha_registro >= DATE_SUB(NOW(), INTERVAL 30 DAY)";
$recientes = $pdo->query($sqlRecientes)->fetch()['recientes'];
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Sistema CRUD de Usuarios</title>
    
    <!-- Bootstrap 5 CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    
    <!-- Bootstrap Icons -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.0/font/bootstrap-icons.css">
    
    <!-- CSS Personalizado -->
    <link rel="stylesheet" href="assets/css/estilo.css">
</head>
<body>
    <div class="container-fluid">
        <!-- Header -->
        <header class="bg-primary text-white py-4 mb-4">
            <div class="container">
                <div class="d-flex justify-content-between align-items-center">
                    <h1 class="display-5 mb-0">
                        <i class="bi bi-people-fill"></i>
                        Sistema de Gestión de Usuarios
                    </h1>
                    <span class="badge bg-light text-primary fs-6">
                        <i class="bi bi-database"></i> <?php echo $totalRegistros; ?> usuarios
                    </span>
                </div>
                <p class="lead mb-0 mt-2">
                    PoC sobre conexión PDO, MySQL  y front end con Bootstrap 5<br>
                    <strong>Administra tus usuarios de forma sencilla y eficiente</strong>
                </p>
            </div>
        </header>

        <main class="container">
            <!-- Mensajes de estado -->
            <?php if ($mensaje): ?>
                <div class="alert alert-<?php echo $tipo_mensaje; ?> alert-dismissible fade show" role="alert">
                    <div class="d-flex align-items-center">
                        <?php if ($tipo_mensaje == 'success'): ?>
                            <i class="bi bi-check-circle-fill me-2 fs-4"></i>
                        <?php elseif ($tipo_mensaje == 'danger'): ?>
                            <i class="bi bi-exclamation-triangle-fill me-2 fs-4"></i>
                        <?php elseif ($tipo_mensaje == 'warning'): ?>
                            <i class="bi bi-exclamation-circle-fill me-2 fs-4"></i>
                        <?php else: ?>
                            <i class="bi bi-info-circle-fill me-2 fs-4"></i>
                        <?php endif; ?>
                        <div>
                            <?php echo $mensaje; ?>
                        </div>
                    </div>
                    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                </div>
            <?php endif; ?>

            <!-- Barra de herramientas -->
            <div class="card shadow-sm mb-4">
                <div class="card-body">
                    <div class="row align-items-center">
                        <div class="col-md-6 mb-3 mb-md-0">
                            <a href="crear.php" class="btn btn-success">
                                <i class="bi bi-plus-circle"></i> Nuevo Usuario
                            </a>
                        </div>
                        <div class="col-md-6">
                            <form method="GET" class="d-flex">
                                <input type="text" name="busqueda" class="form-control me-2" 
                                       placeholder="Buscar por nombre o email..." 
                                       value="<?php echo htmlspecialchars($busqueda); ?>">
                                <button type="submit" class="btn btn-primary">
                                    <i class="bi bi-search"></i>
                                </button>
                                <?php if (!empty($busqueda)): ?>
                                    <a href="index.php" class="btn btn-outline-secondary ms-2">
                                        <i class="bi bi-x-circle"></i>
                                    </a>
                                <?php endif; ?>
                            </form>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Tabla de usuarios -->
            <div class="card shadow-sm">
                <div class="card-header bg-light">
                    <h5 class="mb-0">
                        <i class="bi bi-list-ul"></i> Lista de Usuarios
                    </h5>
                </div>
                <div class="card-body p-0">
                    <?php if (count($usuarios) > 0): ?>
                        <div class="table-responsive">
                            <table class="table table-hover mb-0">
                                <thead class="table-light">
                                    <tr>
                                        <th>ID</th>
                                        <th>Nombre</th>
                                        <th>Email</th>
                                        <th>Teléfono</th>
                                        <th>Fecha Nacimiento</th>
                                        <th>Estado</th>
                                        <th>Acciones</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php foreach ($usuarios as $usuario): ?>
                                        <tr>
                                            <td class="fw-bold">#<?php echo $usuario['id']; ?></td>
                                            <td><?php echo htmlspecialchars($usuario['nombre']); ?></td>
                                            <td>
                                                <a href="mailto:<?php echo $usuario['email']; ?>" class="text-decoration-none">
                                                    <?php echo htmlspecialchars($usuario['email']); ?>
                                                </a>
                                            </td>
                                            <td><?php echo $usuario['telefono'] ?: 'No especificado'; ?></td>
                                            <td>
                                                <?php 
                                                if ($usuario['fecha_nacimiento']) {
                                                    $fecha = new DateTime($usuario['fecha_nacimiento']);
                                                    echo $fecha->format('d/m/Y');
                                                } else {
                                                    echo 'No especificada';
                                                }
                                                ?>
                                            </td>
                                            <td>
                                                <span class="badge <?php echo $usuario['activo'] ? 'bg-success' : 'bg-danger'; ?>">
                                                    <?php echo $usuario['activo'] ? 'Activo' : 'Inactivo'; ?>
                                                </span>
                                            </td>
                                            <td>
                                                <div class="btn-group btn-group-sm">
                                                    <a href="editar.php?id=<?php echo $usuario['id']; ?>" 
                                                       class="btn btn-outline-primary" title="Editar">
                                                        <i class="bi bi-pencil-square"></i>
                                                    </a>
                                                    <a href="eliminar.php?id=<?php echo $usuario['id']; ?>" 
                                                       class="btn btn-outline-danger" 
                                                       onclick="return confirm('¿Estás seguro de eliminar este usuario?')"
                                                       title="Eliminar">
                                                        <i class="bi bi-trash"></i>
                                                    </a>
                                                </div>
                                            </td>
                                        </tr>
                                    <?php endforeach; ?>
                                </tbody>
                            </table>
                        </div>
                    <?php else: ?>
                        <div class="text-center py-5">
                            <i class="bi bi-people display-1 text-muted"></i>
                            <h4 class="mt-3">No hay usuarios registrados</h4>
                            <p class="text-muted">
                                <?php echo empty($busqueda) ? 'Comienza agregando tu primer usuario.' : 'No se encontraron resultados para tu búsqueda.'; ?>
                            </p>
                            <?php if (empty($busqueda)): ?>
                                <a href="crear.php" class="btn btn-primary">
                                    <i class="bi bi-plus-circle"></i> Crear primer usuario
                                </a>
                            <?php endif; ?>
                        </div>
                    <?php endif; ?>
                </div>
                
                <!-- Paginación -->
                <?php if ($totalPaginas > 1): ?>
                    <div class="card-footer">
                        <nav aria-label="Paginación">
                            <ul class="pagination justify-content-center mb-0">
                                <?php if ($pagina > 1): ?>
                                    <li class="page-item">
                                        <a class="page-link" href="?pagina=<?php echo $pagina-1; ?>&busqueda=<?php echo urlencode($busqueda); ?>">
                                            <i class="bi bi-chevron-left"></i>
                                        </a>
                                    </li>
                                <?php endif; ?>
                                
                                <?php for ($i = 1; $i <= $totalPaginas; $i++): ?>
                                    <?php if ($i == 1 || $i == $totalPaginas || ($i >= $pagina - 2 && $i <= $pagina + 2)): ?>
                                        <li class="page-item <?php echo $i == $pagina ? 'active' : ''; ?>">
                                            <a class="page-link" href="?pagina=<?php echo $i; ?>&busqueda=<?php echo urlencode($busqueda); ?>">
                                                <?php echo $i; ?>
                                            </a>
                                        </li>
                                    <?php elseif ($i == $pagina - 3 || $i == $pagina + 3): ?>
                                        <li class="page-item disabled">
                                            <span class="page-link">...</span>
                                        </li>
                                    <?php endif; ?>
                                <?php endfor; ?>
                                
                                <?php if ($pagina < $totalPaginas): ?>
                                    <li class="page-item">
                                        <a class="page-link" href="?pagina=<?php echo $pagina+1; ?>&busqueda=<?php echo urlencode($busqueda); ?>">
                                            <i class="bi bi-chevron-right"></i>
                                        </a>
                                    </li>
                                <?php endif; ?>
                            </ul>
                        </nav>
                    </div>
                <?php endif; ?>
            </div>
            
            <!-- Estadísticas -->
            <div class="row mt-4">
                <div class="col-md-4">
                    <div class="card text-white bg-info">
                        <div class="card-body">
                            <h5 class="card-title">
                                <i class="bi bi-person-plus"></i> Total Usuarios
                            </h5>
                            <p class="card-text display-6"><?php echo $totalRegistros; ?></p>
                        </div>
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="card text-white bg-success">
                        <div class="card-body">
                            <h5 class="card-title">
                                <i class="bi bi-check-circle"></i> Activos
                            </h5>
                            <p class="card-text display-6"><?php echo $activos; ?></p>
                        </div>
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="card text-white bg-secondary">
                        <div class="card-body">
                            <h5 class="card-title">
                                <i class="bi bi-clock-history"></i> Último Mes
                            </h5>
                            <p class="card-text display-6"><?php echo $recientes; ?></p>
                        </div>
                    </div>
                </div>
            </div>
        </main>

        <footer class="mt-5 py-3 bg-light text-center">
            <div class="container">
                <p class="mb-0">
                    Sistema CRUD de Usuarios &copy; <?php echo date('Y'); ?> 
                    | Desarrollado con PHP 8, MySQL, Bootstrap 5
                </p>
            </div>
        </footer>
    </div>

    <!-- Bootstrap 5 JS Bundle -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    
    <!-- JS Personalizado -->
    <script src="assets/js/scripts.js"></script>
</body>
</html>