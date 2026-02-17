-- Crear base de datos
CREATE DATABASE IF NOT EXISTS agenda CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
USE agenda_contactos;

-- Tabla usuarios para login
CREATE TABLE IF NOT EXISTS usuarios (
    id INT AUTO_INCREMENT PRIMARY KEY,
    nombre VARCHAR(100) NOT NULL,
    email VARCHAR(100) NOT NULL UNIQUE,
    password VARCHAR(255) NOT NULL, -- para almacenar hash de contraseña
    fecha_registro TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

-- Tabla contactos
CREATE TABLE IF NOT EXISTS contactos (
    id INT AUTO_INCREMENT PRIMARY KEY,
    usuario_id INT NOT NULL, -- para relacionar los contactos con un usuario
    nombre VARCHAR(100) NOT NULL,
    apellido VARCHAR(100),
    telefono VARCHAR(20),
    email VARCHAR(100),
    direccion VARCHAR(255),
    fecha_creacion TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (usuario_id) REFERENCES usuarios(id) ON DELETE CASCADE
);

-- Usuario de prueba para login (email: prueba@mail.com / contraseña: 1234)
INSERT INTO usuarios (nombre, email, password)
VALUES (
    'Usuario de Prueba',
    'prueba@mail.com',
    '$2y$10$JXzqZcrZQAjXHtFcvfDAUe7uIS7.Q3eA6kVIXGgfkmZ5glXHTvJ9C'
);

