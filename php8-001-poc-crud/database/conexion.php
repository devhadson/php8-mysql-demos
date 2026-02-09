<?php
/**
 * Conexión PDO a MySQL
 * 
 * Este archivo maneja la conexión a la base de datos utilizando PDO.
 * Incluye manejo de errores y configuración de charset UTF-8.
 */

// Configuración de la base de datos
define('DB_HOST', 'localhost');      // Servidor de base de datos
define('DB_NAME', 'demo_nativo'); // Nombre de la base de datos
define('DB_USER', 'root');           // Usuario de MySQL
define('DB_PASS', '');               // Contraseña de MySQL (vacía por defecto en XAMPP)

// Variable global para la conexión
$pdo = null;

/**
 * Establece conexión PDO con la base de datos MySQL
 * 
 * @return PDO|null Retorna el objeto PDO de conexión o null en caso de error
 */
function conectarBD() {
    global $pdo;
    
    // Si ya hay una conexión activa, retornarla
    if ($pdo !== null) {
        return $pdo;
    }
    
    try {
        // Crear cadena de conexión DSN
        $dsn = 'mysql:host=' . DB_HOST . ';dbname=' . DB_NAME . ';charset=utf8mb4';
        
        // Opciones de configuración PDO
        $opciones = [
            PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,    // Lanzar excepciones en errores
            PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC, // Retornar arrays asociativos
            PDO::ATTR_EMULATE_PREPARES => false,            // Usar prepared statements nativos
            PDO::MYSQL_ATTR_INIT_COMMAND => "SET NAMES utf8mb4 COLLATE utf8mb4_unicode_ci"
        ];
        
        // Crear instancia PDO
        $pdo = new PDO($dsn, DB_USER, DB_PASS, $opciones);
        
        return $pdo;
        
    } catch (PDOException $e) {
        // Manejo de errores de conexión
        error_log('Error de conexión PDO: ' . $e->getMessage());
        
        // Mostrar mensaje amigable en desarrollo
        if (defined('DEBUG') && DEBUG) {
            die("Error de conexión a la base de datos: " . $e->getMessage());
        } else {
            die("Error de conexión a la base de datos. Por favor, intente más tarde.");
        }
    }
}

// Función para verificar si la conexión es exitosa
function verificarConexion() {
    try {
        $pdo = conectarBD();
        return $pdo !== null;
    } catch (Exception $e) {
        return false;
    }
}

// Función para obtener información de la base de datos
function obtenerInfoBD() {
    try {
        $pdo = conectarBD();
        return [
            'version' => $pdo->getAttribute(PDO::ATTR_SERVER_VERSION),
            'conexion' => 'Establecida',
            'base_datos' => DB_NAME
        ];
    } catch (Exception $e) {
        return [
            'version' => 'Desconocida',
            'conexion' => 'Fallida',
            'error' => $e->getMessage()
        ];
    }
}
?>