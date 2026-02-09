<?php
/**
 * Lógica para eliminar usuarios
 * 
 * Recibe el ID del usuario por GET y lo elimina de la base de datos
 * después de confirmación.
 */

require_once 'database/conexion.php';

// Iniciar sesión para mensajes flash
session_start();

// Verificar que se ha proporcionado un ID
if (!isset($_GET['id']) || !is_numeric($_GET['id'])) {
    $_SESSION['mensaje'] = 'ID de usuario no válido';
    header('Location: index.php');
    exit();
}

$id = (int)$_GET['id'];
$pdo = conectarBD();

try {
    // Verificar si el usuario existe
    $sqlVerificar = "SELECT nombre FROM usuarios WHERE id = ?";
    $stmtVerificar = $pdo->prepare($sqlVerificar);
    $stmtVerificar->execute([$id]);
    
    $usuario = $stmtVerificar->fetch();
    
    if (!$usuario) {
        $_SESSION['mensaje'] = 'El usuario no existe';
        header('Location: index.php');
        exit();
    }
    
    // Eliminar usuario
    $sql = "DELETE FROM usuarios WHERE id = ?";
    $stmt = $pdo->prepare($sql);
    $stmt->execute([$id]);
    
    $_SESSION['mensaje'] = "Usuario '{$usuario['nombre']}' eliminado exitosamente";
    
} catch (PDOException $e) {
    error_log('Error al eliminar usuario: ' . $e->getMessage());
    $_SESSION['mensaje'] = 'Error al eliminar el usuario: ' . $e->getMessage();
}

// Redirigir al listado
header('Location: index.php');
exit();
?>