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

/**
 * Establece conexión PDO con la base de datos MySQL
 * 
 * @return PDO|null Retorna el objeto PDO de conexión o null en caso de error
 */
function conectarBD() {
    try {
        // Crear cadena de conexión DSN
        $dsn = 'mysql:host=' . DB_HOST . ';dbname=' . DB_NAME . ';charset=utf8mb4';
        
        // Opciones de configuración PDO
        $opciones = [
            PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,    // Lanzar excepciones en errores
            PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC, // Retornar arrays asociativos
            PDO::ATTR_EMULATE_PREPARES => false,            // Usar prepared statements nativos
            PDO::MYSQL_ATTR_INIT_COMMAND => "SET NAMES utf8mb4" // Asegurar charset UTF-8
        ];
        
        // Crear instancia PDO
        $conexion = new PDO($dsn, DB_USER, DB_PASS, $opciones);
        
        return $conexion;
        
    } catch (PDOException $e) {
        // Manejo de errores de conexión
        error_log('Error de conexión: ' . $e->getMessage());
        return null;
    }
}

// Prueba de conexión (opcional, descomentar para probar)
// $pdo = conectarBD();
// if ($pdo) {
//     echo "Conexión exitosa a la base de datos";
// } else {
//     echo "Error al conectar a la base de datos";
// }
?>