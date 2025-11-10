-- ==========================================
--  SISTEMA DE SOPORTE DE TICKETS
--  ESTRUCTURA DE BASE DE DATOS COMPLETA
--  Incluye rol de técnicos para asignación
-- ==========================================

-- Crear base de datos
CREATE DATABASE IF NOT EXISTS soporte_tickets;
USE soporte_tickets;

-- ==========================================
--  TABLA: usuarios
-- ==========================================
CREATE TABLE usuarios (
    id_usuario INT AUTO_INCREMENT PRIMARY KEY,
    nombre VARCHAR(100) NOT NULL,
    apellido VARCHAR(100) NOT NULL,
    correo VARCHAR(150) UNIQUE NOT NULL,
    PASSWORD VARCHAR(255) NOT NULL,
    rol ENUM('docente','administrador','tecnico') NOT NULL,
    estado ENUM('activo','inactivo') DEFAULT 'activo',
    fecha_registro TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

-- ==========================================
--  TABLA: categorias
-- ==========================================
CREATE TABLE categorias (
    id_categoria INT AUTO_INCREMENT PRIMARY KEY,
    nombre VARCHAR(100) NOT NULL,
    descripcion TEXT,
    prioridad_predeterminada ENUM('baja','media','alta','critica') DEFAULT 'media',
    fecha_creacion TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

-- ==========================================
--  TABLA: tickets
-- ==========================================
CREATE TABLE tickets (
    id_ticket INT AUTO_INCREMENT PRIMARY KEY,
    id_docente INT NOT NULL,
    id_categoria INT,
    titulo VARCHAR(200) NOT NULL,
    descripcion TEXT NOT NULL,
    prioridad ENUM('baja','media','alta','critica') DEFAULT 'media',
    estado ENUM('pendiente','en_progreso','resuelto','rechazado') DEFAULT 'pendiente',
    id_asignado INT NULL,
    fecha_creacion TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    fecha_actualizacion TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    FOREIGN KEY (id_docente) REFERENCES usuarios(id_usuario),
    FOREIGN KEY (id_categoria) REFERENCES categorias(id_categoria),
    FOREIGN KEY (id_asignado) REFERENCES usuarios(id_usuario)
);

-- ==========================================
--  TABLA: mensajes_chat
-- ==========================================
CREATE TABLE mensajes_chat (
    id_mensaje INT AUTO_INCREMENT PRIMARY KEY,
    id_ticket INT NOT NULL,
    id_emisor INT NOT NULL,
    mensaje TEXT NOT NULL,
    fecha_envio TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (id_ticket) REFERENCES tickets(id_ticket),
    FOREIGN KEY (id_emisor) REFERENCES usuarios(id_usuario)
);

-- ==========================================
--  TABLA: notificaciones
-- ==========================================
CREATE TABLE notificaciones (
    id_notificacion INT AUTO_INCREMENT PRIMARY KEY,
    id_usuario INT NOT NULL,
    id_ticket INT,
    tipo ENUM('web','correo') DEFAULT 'web',
    mensaje VARCHAR(255) NOT NULL,
    leido BOOLEAN DEFAULT FALSE,
    fecha_envio TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (id_usuario) REFERENCES usuarios(id_usuario),
    FOREIGN KEY (id_ticket) REFERENCES tickets(id_ticket)
);

-- ==========================================
--  TABLA: historial_estados
-- ==========================================
CREATE TABLE historial_estados (
    id_historial INT AUTO_INCREMENT PRIMARY KEY,
    id_ticket INT NOT NULL,
    estado_anterior ENUM('pendiente','en_progreso','resuelto','rechazado'),
    nuevo_estado ENUM('pendiente','en_progreso','resuelto','rechazado'),
    cambiado_por INT NOT NULL,
    fecha_cambio TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (id_ticket) REFERENCES tickets(id_ticket),
    FOREIGN KEY (cambiado_por) REFERENCES usuarios(id_usuario)
);

-- ==========================================
--  TABLA: reportes
-- ==========================================
CREATE TABLE reportes (
    id_reporte INT AUTO_INCREMENT PRIMARY KEY,
    id_admin INT NOT NULL,
    titulo VARCHAR(150) NOT NULL,
    descripcion TEXT,
    fecha_generacion TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (id_admin) REFERENCES usuarios(id_usuario)
);

-- ==========================================
--  DATOS INICIALES
-- ==========================================

-- Usuarios de ejemplo
INSERT INTO usuarios (nombre, apellido, correo, password, rol)
VALUES 
('Juan', 'Pérez', 'juan@escuela.edu', '1234', 'docente'),
('María', 'López', 'maria@soporte.edu', '1234', 'administrador'),
('Carlos', 'Ramírez', 'carlos.tecnico@soporte.edu', '1234', 'tecnico'),
('Ana', 'Martínez', 'ana.tecnico@soporte.edu', '1234', 'tecnico'),
('Luis', 'González', 'luis.tecnico@soporte.edu', '1234', 'tecnico');

-- Categorías iniciales
INSERT INTO categorias (nombre, descripcion, prioridad_predeterminada)
VALUES
('Problemas de red', 'Fallas en la conexión o acceso a internet', 'alta'),
('Software', 'Errores en aplicaciones institucionales', 'media'),
('Hardware', 'Daños o mantenimiento de equipos', 'critica'),
('Soporte General', 'Consultas y asistencia general', 'baja');

-- Ticket de ejemplo
INSERT INTO tickets (id_docente, id_categoria, titulo, descripcion, prioridad)
VALUES
(1, 1, 'Sin conexión a Internet', 'El aula 3 no tiene acceso a internet desde ayer.', 'alta'),
(1, 2, 'Error en sistema de calificaciones', 'No puedo acceder al sistema para registrar notas', 'media');

-- Mensaje de ejemplo
INSERT INTO mensajes_chat (id_ticket, id_emisor, mensaje)
VALUES
(1, 1, 'Hola, necesito ayuda con la conexión de red.');

-- Notificación de ejemplo
INSERT INTO notificaciones (id_usuario, id_ticket, tipo, mensaje)
VALUES
(2, 1, 'web', 'Nuevo ticket creado: Sin conexión a Internet');

-- ==========================================
--  CONSULTAS ÚTILES
-- ==========================================

-- Ver todos los usuarios por rol
-- SELECT rol, COUNT(*) as total FROM usuarios GROUP BY rol;

-- Ver tickets sin asignar
-- SELECT * FROM tickets WHERE id_asignado IS NULL AND estado = 'pendiente';

-- Ver técnicos disponibles
-- SELECT id_usuario, CONCAT(nombre, ' ', apellido) as nombre_completo, correo 
-- FROM usuarios WHERE rol = 'tecnico' AND estado = 'activo';


