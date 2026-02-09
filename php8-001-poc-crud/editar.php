<?php
/**
 * Formulario para editar usuario existente
 * 
 * Recibe el ID del usuario por GET, carga sus datos y los muestra
 * en un formulario para su edición.
 */

require_once 'database/conexion.php';

// Iniciar sesión para mensajes
session_start();

// Verificar que se ha proporcionado un ID
if (!isset($_GET['id']) || !is_numeric($_GET['id'])) {
    header('Location: index.php');
    exit();
}

$id = (int)$_GET['id'];
$pdo = conectarBD();

// Obtener datos del usuario
$sql = "SELECT * FROM usuarios WHERE id = ?";
$stmt = $pdo->prepare($sql);
$stmt->execute([$id]);
$usuario = $stmt->fetch();

// Si no existe el usuario, redirigir
if (!$usuario) {
    header('Location: index.php');
    exit();
}

// Manejar mensajes de error
$error = '';
if (isset($_SESSION['error'])) {
    $error = $_SESSION['error'];
    unset($_SESSION['error']);
}
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Editar Usuario</title>
    
    <!-- Bootstrap 5 CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    
    <!-- Bootstrap Icons -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.0/font/bootstrap-icons.css">
    
    <!-- CSS Personalizado -->
    <link rel="stylesheet" href="assets/css/estilo.css">
</head>
<body>
    <div class="container mt-5">
        <div class="row justify-content-center">
            <div class="col-lg-8">
                <!-- Header -->
                <div class="d-flex align-items-center mb-4">
                    <a href="index.php" class="btn btn-outline-secondary me-3">
                        <i class="bi bi-arrow-left"></i>
                    </a>
                    <div>
                        <h1 class="h3 mb-0">
                            <i class="bi bi-pencil-square text-warning"></i>
                            Editar Usuario
                        </h1>
                        <p class="text-muted mb-0">Editando: <?php echo htmlspecialchars($usuario['nombre']); ?></p>
                    </div>
                    <span class="badge bg-primary ms-auto">
                        ID: #<?php echo $usuario['id']; ?>
                    </span>
                </div>

                <!-- Mensajes de error -->
                <?php if ($error): ?>
                    <div class="alert alert-danger alert-dismissible fade show" role="alert">
                        <div class="d-flex align-items-center">
                            <i class="bi bi-exclamation-triangle-fill me-2 fs-4"></i>
                            <div>
                                <?php echo $error; ?>
                            </div>
                        </div>
                        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                    </div>
                <?php endif; ?>

                <!-- Formulario -->
                <div class="card shadow-sm">
                    <div class="card-body">
                        <form action="guardar.php" method="POST" id="formUsuario" novalidate>
                            <!-- Campos ocultos -->
                            <input type="hidden" name="accion" value="editar">
                            <input type="hidden" name="id" value="<?php echo $usuario['id']; ?>">
                            
                            <div class="row">
                                <!-- Nombre -->
                                <div class="col-md-6 mb-3">
                                    <label for="nombre" class="form-label">
                                        <i class="bi bi-person"></i> Nombre Completo *
                                    </label>
                                    <input type="text" class="form-control" id="nombre" name="nombre" 
                                           required maxlength="100"
                                           value="<?php echo htmlspecialchars($usuario['nombre']); ?>">
                                    <div class="invalid-feedback">
                                        Por favor ingresa el nombre del usuario.
                                    </div>
                                </div>

                                <!-- Email -->
                                <div class="col-md-6 mb-3">
                                    <label for="email" class="form-label">
                                        <i class="bi bi-envelope"></i> Email *
                                    </label>
                                    <input type="email" class="form-control" id="email" name="email" 
                                           required maxlength="100"
                                           value="<?php echo htmlspecialchars($usuario['email']); ?>">
                                    <div class="invalid-feedback">
                                        Por favor ingresa un email válido.
                                    </div>
                                    <small class="form-text text-muted">
                                        El email debe ser único en el sistema
                                    </small>
                                </div>

                                <!-- Teléfono -->
                                <div class="col-md-6 mb-3">
                                    <label for="telefono" class="form-label">
                                        <i class="bi bi-telephone"></i> Teléfono
                                    </label>
                                    <input type="tel" class="form-control" id="telefono" name="telefono" 
                                           maxlength="20"
                                           value="<?php echo htmlspecialchars($usuario['telefono']); ?>">
                                </div>

                                <!-- Fecha de Nacimiento -->
                                <div class="col-md-6 mb-3">
                                    <label for="fecha_nacimiento" class="form-label">
                                        <i class="bi bi-calendar"></i> Fecha de Nacimiento
                                    </label>
                                    <?php
                                    $fechaNacimiento = $usuario['fecha_nacimiento'] ?: '';
                                    if ($fechaNacimiento) {
                                        $fecha = new DateTime($fechaNacimiento);
                                        $fechaNacimiento = $fecha->format('Y-m-d');
                                    }
                                    ?>
                                    <input type="date" class="form-control" id="fecha_nacimiento" 
                                           name="fecha_nacimiento"
                                           value="<?php echo $fechaNacimiento; ?>">
                                </div>

                                <!-- Dirección -->
                                <div class="col-12 mb-3">
                                    <label for="direccion" class="form-label">
                                        <i class="bi bi-geo-alt"></i> Dirección
                                    </label>
                                    <textarea class="form-control" id="direccion" name="direccion" 
                                              rows="3" maxlength="500"><?php echo htmlspecialchars($usuario['direccion']); ?></textarea>
                                </div>

                                <!-- Estado -->
                                <div class="col-md-6 mb-3">
                                    <label class="form-label">
                                        <i class="bi bi-toggle-on"></i> Estado del Usuario
                                    </label>
                                    <div class="form-check">
                                        <input class="form-check-input" type="radio" name="activo" 
                                               id="activo_si" value="1" 
                                               <?php echo $usuario['activo'] ? 'checked' : ''; ?>>
                                        <label class="form-check-label" for="activo_si">
                                            <span class="badge bg-success">Activo</span>
                                        </label>
                                    </div>
                                    <div class="form-check">
                                        <input class="form-check-input" type="radio" name="activo" 
                                               id="activo_no" value="0"
                                               <?php echo !$usuario['activo'] ? 'checked' : ''; ?>>
                                        <label class="form-check-label" for="activo_no">
                                            <span class="badge bg-danger">Inactivo</span>
                                        </label>
                                    </div>
                                </div>

                                <!-- Información del registro -->
                                <div class="col-md-6 mb-3">
                                    <div class="card bg-light">
                                        <div class="card-body py-2">
                                            <small class="text-muted">
                                                <i class="bi bi-clock"></i> 
                                                Registrado: <?php 
                                                $fechaRegistro = new DateTime($usuario['fecha_registro']);
                                                echo $fechaRegistro->format('d/m/Y H:i:s');
                                                ?>
                                            </small>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <!-- Botones -->
                            <div class="d-flex justify-content-between mt-4 pt-3 border-top">
                                <a href="index.php" class="btn btn-outline-secondary">
                                    <i class="bi bi-x-circle"></i> Cancelar
                                </a>
                                <div>
                                    <a href="eliminar.php?id=<?php echo $usuario['id']; ?>" 
                                       class="btn btn-outline-danger me-2"
                                       onclick="return confirm('¿Estás seguro de eliminar este usuario?')">
                                        <i class="bi bi-trash"></i> Eliminar
                                    </a>
                                    <button type="submit" class="btn btn-warning">
                                        <i class="bi bi-save"></i> Actualizar Usuario
                                    </button>
                                </div>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Bootstrap 5 JS Bundle -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    
    <!-- JS Personalizado -->
    <script src="assets/js/scripts.js"></script>
    
    <script>
        // Validación del formulario
        document.addEventListener('DOMContentLoaded', function() {
            const form = document.getElementById('formUsuario');
            const emailInput = document.getElementById('email');
            const originalEmail = emailInput.value;
            
            form.addEventListener('submit', function(event) {
                if (!form.checkValidity()) {
                    event.preventDefault();
                    event.stopPropagation();
                }
                
                form.classList.add('was-validated');
            }, false);
            
            // Validación en tiempo real del email
            emailInput.addEventListener('blur', function() {
                const email = this.value.trim();
                if (!email) return;
                
                // Si el email no ha cambiado, no validar
                if (email === originalEmail) {
                    this.classList.remove('is-invalid');
                    return;
                }
                
                // Validar formato básico
                const emailRegex = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
                if (!emailRegex.test(email)) {
                    this.classList.add('is-invalid');
                    return;
                }
                
                this.classList.remove('is-invalid');
            });
        });
    </script>
</body>
</html>