<?php
include_once __DIR__ . "/../config/CN.php";
class modeloUsuario{
    private $pdo;
    
    public function __construct(){
        $this->pdo = getConnection();
    }


   public function login($correo, $password){
        $query = "SELECT id_usuario, correo, nombre, apellido, rol, password, estado 
                  FROM usuarios 
                  WHERE correo = :correo AND password = :password AND estado = 'activo'";
        $stmt = $this->pdo->prepare($query);
        $stmt->execute(['correo' => $correo, 'password' => $password]);
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }
    
    public function listarUsuarios(){
        $query = "SELECT id_usuario, nombre, apellido, correo, rol, estado, fecha_registro 
                  FROM usuarios 
                  ORDER BY fecha_registro DESC";
        $stmt = $this->pdo->prepare($query);
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
    
    public function getUsuario($id){
        $query = "SELECT id_usuario, nombre, apellido, correo, rol, estado, fecha_registro 
                  FROM usuarios 
                  WHERE id_usuario = :id";
        $stmt = $this->pdo->prepare($query);
        $stmt->execute(['id' => $id]);
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }
    
    public function crearUsuario($datos){
        $query = "INSERT INTO usuarios (nombre, apellido, correo, password, rol, estado) 
                  VALUES (:nombre, :apellido, :correo, :password, :rol, :estado)";
        
        $stmt = $this->pdo->prepare($query);
        return $stmt->execute([
            'nombre' => $datos['nombre'],
            'apellido' => $datos['apellido'],
            'correo' => $datos['correo'],
            'password' => $datos['password'],
            'rol' => $datos['rol'],
            'estado' => $datos['estado'] ?? 'activo'
        ]);
    }
    
    public function actualizarUsuario($id, $datos){
        $query = "UPDATE usuarios 
                  SET nombre = :nombre, 
                      apellido = :apellido, 
                      correo = :correo, 
                      rol = :rol, 
                      estado = :estado
                  WHERE id_usuario = :id";
        
        $stmt = $this->pdo->prepare($query);
        return $stmt->execute([
            'id' => $id,
            'nombre' => $datos['nombre'],
            'apellido' => $datos['apellido'],
            'correo' => $datos['correo'],
            'rol' => $datos['rol'],
            'estado' => $datos['estado']
        ]);
    }
    
    public function actualizarPassword($id, $password){
        $query = "UPDATE usuarios SET password = :password WHERE id_usuario = :id";
        $stmt = $this->pdo->prepare($query);
        return $stmt->execute(['id' => $id, 'password' => $password]);
    }
    
    public function cambiarEstado($id, $estado){
        $query = "UPDATE usuarios SET estado = :estado WHERE id_usuario = :id";
        $stmt = $this->pdo->prepare($query);
        return $stmt->execute(['id' => $id, 'estado' => $estado]);
    }
    
    public function eliminarUsuario($id){
        $query = "DELETE FROM usuarios WHERE id_usuario = :id";
        $stmt = $this->pdo->prepare($query);
        return $stmt->execute(['id' => $id]);
    }
    
    public function correoExiste($correo, $excluir_id = null){
        if($excluir_id){
            $query = "SELECT COUNT(*) as total FROM usuarios WHERE correo = :correo AND id_usuario != :id";
            $stmt = $this->pdo->prepare($query);
            $stmt->execute(['correo' => $correo, 'id' => $excluir_id]);
        } else {
            $query = "SELECT COUNT(*) as total FROM usuarios WHERE correo = :correo";
            $stmt = $this->pdo->prepare($query);
            $stmt->execute(['correo' => $correo]);
        }
        $resultado = $stmt->fetch(PDO::FETCH_ASSOC);
        return $resultado['total'] > 0;
    }
    
    public function contarUsuariosPorRol(){
        $query = "SELECT rol, COUNT(*) as total FROM usuarios GROUP BY rol";
        $stmt = $this->pdo->prepare($query);
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
    
    public function listarTecnicos($solo_activos = true){
        $query = "SELECT id_usuario, nombre, apellido, correo, estado, fecha_registro 
                  FROM usuarios 
                  WHERE rol = 'tecnico'";
        
        if($solo_activos) {
            $query .= " AND estado = 'activo'";
        }
        
        $query .= " ORDER BY nombre ASC";
        
        $stmt = $this->pdo->prepare($query);
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
    
    public function contarTecnicosDisponibles(){
        $query = "SELECT COUNT(*) as total FROM usuarios WHERE rol = 'tecnico' AND estado = 'activo'";
        $stmt = $this->pdo->prepare($query);
        $stmt->execute();
        $resultado = $stmt->fetch(PDO::FETCH_ASSOC);
        return $resultado['total'];
    }
    
    public function obtenerTecnicoConMenosTickets(){
        $query = "SELECT u.id_usuario, u.nombre, u.apellido, COUNT(t.id_ticket) as tickets_asignados
                  FROM usuarios u
                  LEFT JOIN tickets t ON u.id_usuario = t.id_asignado 
                      AND t.estado IN ('pendiente', 'en_progreso')
                  WHERE u.rol = 'tecnico' AND u.estado = 'activo'
                  GROUP BY u.id_usuario
                  ORDER BY tickets_asignados ASC
                  LIMIT 1";
        
        $stmt = $this->pdo->prepare($query);
        $stmt->execute();
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }
    
    /**
     * Verificar si un técnico está disponible (no tiene tickets en progreso)
     */
    public function verificarDisponibilidadTecnico($id_tecnico){
        $query = "SELECT COUNT(*) as tickets_activos
                  FROM tickets
                  WHERE id_asignado = :id_tecnico
                  AND estado = 'en_progreso'";
        
        $stmt = $this->pdo->prepare($query);
        $stmt->execute(['id_tecnico' => $id_tecnico]);
        $result = $stmt->fetch(PDO::FETCH_ASSOC);
        
        // Retorna true si está disponible (0 tickets activos)
        return $result['tickets_activos'] == 0;
    }
    
    /**
     * Obtener ticket activo de un técnico
     */
    public function obtenerTicketActivoTecnico($id_tecnico){
        $query = "SELECT t.id_ticket, 
                         t.titulo, 
                         t.prioridad,
                         t.fecha_creacion,
                         DATEDIFF(NOW(), t.fecha_creacion) as dias_trabajando
                  FROM tickets t
                  WHERE t.id_asignado = :id_tecnico
                  AND t.estado = 'en_progreso'
                  LIMIT 1";
        
        $stmt = $this->pdo->prepare($query);
        $stmt->execute(['id_tecnico' => $id_tecnico]);
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }
    
    /**
     * Listar técnicos con su estado de disponibilidad
     */
    public function listarTecnicosConDisponibilidad(){
        $query = "SELECT u.id_usuario,
                         u.nombre,
                         u.apellido,
                         u.correo,
                         u.estado,
                         COUNT(CASE WHEN t.estado = 'en_progreso' THEN 1 END) as tickets_en_progreso,
                         COUNT(CASE WHEN t.estado = 'pendiente' THEN 1 END) as tickets_pendientes,
                         COUNT(CASE WHEN t.estado = 'resuelto' THEN 1 END) as tickets_resueltos,
                         (COUNT(CASE WHEN t.estado = 'en_progreso' THEN 1 END) = 0) as disponible
                  FROM usuarios u
                  LEFT JOIN tickets t ON u.id_usuario = t.id_asignado
                  WHERE u.rol = 'tecnico' AND u.estado = 'activo'
                  GROUP BY u.id_usuario
                  ORDER BY disponible DESC, u.nombre ASC";
        
        $stmt = $this->pdo->prepare($query);
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
}
?>