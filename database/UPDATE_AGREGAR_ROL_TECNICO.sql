

USE soporte_tickets;

-- Modificar la tabla usuarios para agregar el rol 'tecnico'
ALTER TABLE usuarios 
MODIFY COLUMN rol ENUM('docente','administrador','tecnico') NOT NULL;

-- Insertar usuarios técnicos de ejemplo
INSERT INTO usuarios (nombre, apellido, correo, password, rol, estado)
VALUES 
('Carlos', 'Ramírez', 'carlos.tecnico@soporte.edu', '1234', 'tecnico', 'activo'),
('Ana', 'Martínez', 'ana.tecnico@soporte.edu', '1234', 'tecnico', 'activo'),
('Luis', 'González', 'luis.tecnico@soporte.edu', '1234', 'tecnico', 'activo');

-- Verificar la actualización
SELECT id_usuario, nombre, apellido, correo, rol, estado FROM usuarios WHERE rol = 'tecnico';


