<?php
/**
 * Lógica para guardar usuarios (crear y editar)
 * 
 * Procesa los datos del formulario y realiza las operaciones
 * correspondientes en la base de datos.
 */

require_once 'database/conexion.php';

// Iniciar sesión para mensajes flash
session_start();

// Verificar método POST
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header('Location: index.php');
    exit();
}

// Validar acción
$accion = $_POST['accion'] ?? '';
if (!in_array($accion, ['crear', 'editar'])) {
    $_SESSION['mensaje'] = 'Acción no válida';
    header('Location: index.php');
    exit();
}

// Validar datos requeridos
$camposRequeridos = ['nombre', 'email'];
foreach ($camposRequeridos as $campo) {
    if (empty($_POST[$campo])) {
        $_SESSION['error'] = "El campo <strong>$campo</strong> es requerido";
        header('Location: ' . ($accion === 'crear' ? 'crear.php' : 'editar.php?id=' . ($_POST['id'] ?? '')));
        exit();
    }
}

// Sanitizar y validar datos
$nombre = trim($_POST['nombre']);
$email = trim($_POST['email']);
$telefono = trim($_POST['telefono'] ?? '');
$fecha_nacimiento = $_POST['fecha_nacimiento'] ?: null;
$direccion = trim($_POST['direccion'] ?? '');
$activo = isset($_POST['activo']) ? (int)$_POST['activo'] : 1;

// Validar email
if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
    $_SESSION['error'] = 'El <strong>email</strong> no tiene un formato válido';
    header('Location: ' . ($accion === 'crear' ? 'crear.php' : 'editar.php?id=' . ($_POST['id'] ?? '')));
    exit();
}

// Conectar a la base de datos
$pdo = conectarBD();

try {
    if ($accion === 'crear') {
        // Verificar si el email ya existe
        $sqlVerificar = "SELECT id, nombre FROM usuarios WHERE email = ?";
        $stmtVerificar = $pdo->prepare($sqlVerificar);
        $stmtVerificar->execute([$email]);
        
        if ($usuarioExistente = $stmtVerificar->fetch()) {
            $_SESSION['error'] = "El email <strong>$email</strong> ya está registrado por el usuario <strong>{$usuarioExistente['nombre']}</strong>";
            
            // Guardar datos del formulario para no perderlos
            $_SESSION['form_data'] = [
                'nombre' => $nombre,
                'email' => $email,
                'telefono' => $telefono,
                'fecha_nacimiento' => $fecha_nacimiento,
                'direccion' => $direccion,
                'activo' => $activo
            ];
            
            header('Location: crear.php');
            exit();
        }
        
        // Insertar nuevo usuario
        $sql = "INSERT INTO usuarios (nombre, email, telefono, fecha_nacimiento, direccion, activo) 
                VALUES (?, ?, ?, ?, ?, ?)";
        
        $stmt = $pdo->prepare($sql);
        $stmt->execute([$nombre, $email, $telefono ?: null, $fecha_nacimiento, $direccion ?: null, $activo]);
        
        $idUsuario = $pdo->lastInsertId();
        $_SESSION['mensaje'] = " Usuario <strong>$nombre</strong> creado exitosamente (ID: $idUsuario)";
        
    } else { // editar
        // Verificar ID
        $id = (int)($_POST['id'] ?? 0);
        if ($id <= 0) {
            $_SESSION['error'] = 'ID de usuario no válido';
            header('Location: index.php');
            exit();
        }
        
        // Verificar si el email ya existe (excluyendo el usuario actual)
        $sqlVerificar = "SELECT id, nombre FROM usuarios WHERE email = ? AND id != ?";
        $stmtVerificar = $pdo->prepare($sqlVerificar);
        $stmtVerificar->execute([$email, $id]);
        
        if ($usuarioExistente = $stmtVerificar->fetch()) {
            $_SESSION['error'] = "El email <strong>$email</strong> ya está registrado por el usuario <strong>{$usuarioExistente['nombre']}</strong>";
            header("Location: editar.php?id=$id");
            exit();
        }
        
        // Actualizar usuario
        $sql = "UPDATE usuarios SET 
                nombre = ?,
                email = ?,
                telefono = ?,
                fecha_nacimiento = ?,
                direccion = ?,
                activo = ?
                WHERE id = ?";
        
        $stmt = $pdo->prepare($sql);
        $stmt->execute([$nombre, $email, $telefono ?: null, $fecha_nacimiento, $direccion ?: null, $activo, $id]);
        
        $_SESSION['mensaje'] = " Usuario <strong>$nombre</strong> actualizado exitosamente";
    }
    
} catch (PDOException $e) {
    error_log('Error al guardar usuario: ' . $e->getMessage());
    $_SESSION['error'] = ' Error al guardar el usuario: ' . $e->getMessage();
    header('Location: ' . ($accion === 'crear' ? 'crear.php' : 'editar.php?id=' . ($_POST['id'] ?? '')));
    exit();
}

// Redirigir al listado
header('Location: index.php');
exit();
?>