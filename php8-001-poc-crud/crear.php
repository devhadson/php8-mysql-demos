<?php
/**
 * Formulario para crear nuevo usuario
 * 
 * Muestra un formulario con validación HTML5 y prepara los datos
 * para ser enviados a guardar.php
 */

session_start();

// Inicializar variables del formulario
$form_data = [
    'nombre' => '',
    'email' => '',
    'telefono' => '',
    'fecha_nacimiento' => '',
    'direccion' => '',
    'activo' => 1
];

// Recuperar datos del formulario si hay error
if (isset($_SESSION['form_data'])) {
    $form_data = array_merge($form_data, $_SESSION['form_data']);
    unset($_SESSION['form_data']);
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
    <title>Crear Nuevo Usuario</title>
    
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
                            <i class="bi bi-person-plus text-primary"></i>
                            Crear Nuevo Usuario
                        </h1>
                        <p class="text-muted mb-0">Completa el formulario para registrar un nuevo usuario</p>
                    </div>
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
                            <!-- Campo oculto para acción -->
                            <input type="hidden" name="accion" value="crear">
                            
                            <div class="row">
                                <!-- Nombre -->
                                <div class="col-md-6 mb-3">
                                    <label for="nombre" class="form-label">
                                        <i class="bi bi-person"></i> Nombre Completo *
                                    </label>
                                    <input type="text" class="form-control" id="nombre" name="nombre" 
                                           required maxlength="100"
                                           placeholder="Ej: Juan Pérez"
                                           value="<?php echo htmlspecialchars($form_data['nombre']); ?>">
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
                                           placeholder="Ej: usuario@example.com"
                                           value="<?php echo htmlspecialchars($form_data['email']); ?>">
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
                                           placeholder="Ej: 555-1234"
                                           value="<?php echo htmlspecialchars($form_data['telefono']); ?>">
                                </div>

                                <!-- Fecha de Nacimiento -->
                                <div class="col-md-6 mb-3">
                                    <label for="fecha_nacimiento" class="form-label">
                                        <i class="bi bi-calendar"></i> Fecha de Nacimiento
                                    </label>
                                    <input type="date" class="form-control" id="fecha_nacimiento" 
                                           name="fecha_nacimiento"
                                           value="<?php echo htmlspecialchars($form_data['fecha_nacimiento']); ?>">
                                </div>

                                <!-- Dirección -->
                                <div class="col-12 mb-3">
                                    <label for="direccion" class="form-label">
                                        <i class="bi bi-geo-alt"></i> Dirección
                                    </label>
                                    <textarea class="form-control" id="direccion" name="direccion" 
                                              rows="3" maxlength="500"
                                              placeholder="Dirección completa del usuario"><?php echo htmlspecialchars($form_data['direccion']); ?></textarea>
                                </div>

                                <!-- Estado -->
                                <div class="col-md-6 mb-3">
                                    <label class="form-label">
                                        <i class="bi bi-toggle-on"></i> Estado del Usuario
                                    </label>
                                    <div class="form-check">
                                        <input class="form-check-input" type="radio" name="activo" 
                                               id="activo_si" value="1" 
                                               <?php echo $form_data['activo'] == 1 ? 'checked' : ''; ?>>
                                        <label class="form-check-label" for="activo_si">
                                            <span class="badge bg-success">Activo</span>
                                        </label>
                                    </div>
                                    <div class="form-check">
                                        <input class="form-check-input" type="radio" name="activo" 
                                               id="activo_no" value="0"
                                               <?php echo $form_data['activo'] == 0 ? 'checked' : ''; ?>>
                                        <label class="form-check-label" for="activo_no">
                                            <span class="badge bg-danger">Inactivo</span>
                                        </label>
                                    </div>
                                </div>
                            </div>

                            <!-- Botones -->
                            <div class="d-flex justify-content-between mt-4 pt-3 border-top">
                                <a href="index.php" class="btn btn-outline-secondary">
                                    <i class="bi bi-x-circle"></i> Cancelar
                                </a>
                                <button type="submit" class="btn btn-primary">
                                    <i class="bi bi-save"></i> Guardar Usuario
                                </button>
                            </div>
                        </form>
                    </div>
                </div>

                <!-- Información -->
                <div class="alert alert-info mt-4">
                    <h5 class="alert-heading">
                        <i class="bi bi-info-circle"></i> Información importante
                    </h5>
                    <ul class="mb-0">
                        <li>Los campos marcados con <span class="text-danger">*</span> son obligatorios</li>
                        <li>El <strong>email debe ser único</strong> en el sistema</li>
                        <li>Los usuarios inactivos no podrán acceder al sistema</li>
                        <li>Puedes editar esta información posteriormente</li>
                        <li>Verifica que el email no esté registrado antes de enviar</li>
                    </ul>
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