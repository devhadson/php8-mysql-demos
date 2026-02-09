-- Crear la base de datos si no existe
CREATE DATABASE IF NOT EXISTS sistema_usuarios;
USE sistema_usuarios;

-- Crear tabla usuarios
CREATE TABLE IF NOT EXISTS usuarios (
    id INT AUTO_INCREMENT PRIMARY KEY,
    nombre VARCHAR(100) NOT NULL,
    email VARCHAR(100) NOT NULL UNIQUE,
    telefono VARCHAR(20),
    fecha_nacimiento DATE,
    direccion TEXT,
    fecha_registro TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    activo TINYINT(1) DEFAULT 1
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- Insertar datos de prueba
INSERT INTO usuarios (nombre, email, telefono, fecha_nacimiento, direccion) VALUES
('Juan Pérez', 'juan.perez@example.com', '555-0101', '1990-05-15', 'Calle Principal 123, Ciudad'),
('María García', 'maria.garcia@example.com', '555-0102', '1985-08-22', 'Avenida Central 456, Ciudad'),
('Carlos López', 'carlos.lopez@example.com', '555-0103', '1992-11-30', 'Boulevard Norte 789, Ciudad'),
('Ana Rodríguez', 'ana.rodriguez@example.com', '555-0104', '1988-03-10', 'Calle Sur 101, Ciudad'),
('Pedro Martínez', 'pedro.martinez@example.com', '555-0105', '1995-07-18', 'Avenida Este 202, Ciudad');