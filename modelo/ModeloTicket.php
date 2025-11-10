<?php


include_once __DIR__ . "/../config/CN.php";

class modeloTicket{
    private $pdo;
    
    public function __construct(){
        $this->pdo = getConnection();
    }
    
    /**
     * Obtener un ticket específico con toda su información
     */
    public function obtenerTicket($id_ticket){
        $query = "SELECT t.*, 
                         c.nombre as categoria_nombre,
                         d.nombre as docente_nombre,
                         d.apellido as docente_apellido,
                         d.correo as docente_correo,
                         a.nombre as admin_nombre,
                         a.apellido as admin_apellido
                  FROM tickets t
                  LEFT JOIN categorias c ON t.id_categoria = c.id_categoria
                  LEFT JOIN usuarios d ON t.id_docente = d.id_usuario
                  LEFT JOIN usuarios a ON t.id_asignado = a.id_usuario
                  WHERE t.id_ticket = :id_ticket";
        
        $stmt = $this->pdo->prepare($query);
        $stmt->execute(['id_ticket' => $id_ticket]);
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }
    
    /**
     * Obtener todos los tickets de un docente
     */
    public function obtenerTicketsDocente($id_docente){
        $query = "SELECT t.*, 
                         c.nombre as categoria_nombre,
                         a.nombre as admin_nombre,
                         a.apellido as admin_apellido
                  FROM tickets t
                  LEFT JOIN categorias c ON t.id_categoria = c.id_categoria
                  LEFT JOIN usuarios a ON t.id_asignado = a.id_usuario
                  WHERE t.id_docente = :id_docente
                  ORDER BY t.fecha_creacion DESC";
        
        $stmt = $this->pdo->prepare($query);
        $stmt->execute(['id_docente' => $id_docente]);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
    
    /**
     * Obtener todos los tickets (para administradores)
     */
    public function obtenerTodosTickets(){
        $query = "SELECT t.*, 
                         c.nombre as categoria_nombre,
                         d.nombre as docente_nombre,
                         d.apellido as docente_apellido,
                         a.nombre as admin_nombre,
                         a.apellido as admin_apellido
                  FROM tickets t
                  LEFT JOIN categorias c ON t.id_categoria = c.id_categoria
                  LEFT JOIN usuarios d ON t.id_docente = d.id_usuario
                  LEFT JOIN usuarios a ON t.id_asignado = a.id_usuario
                  ORDER BY t.fecha_creacion DESC";
        
        $stmt = $this->pdo->prepare($query);
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
    
    /**
     * Crear un nuevo ticket
     */
    public function crearTicket($datos){
        $query = "INSERT INTO tickets (id_docente, id_categoria, titulo, descripcion, prioridad) 
                  VALUES (:id_docente, :id_categoria, :titulo, :descripcion, :prioridad)";
        
        $stmt = $this->pdo->prepare($query);
        return $stmt->execute([
            'id_docente' => $datos['id_docente'],
            'id_categoria' => $datos['id_categoria'],
            'titulo' => $datos['titulo'],
            'descripcion' => $datos['descripcion'],
            'prioridad' => $datos['prioridad'] ?? 'media',
        ]); 
    }
    
    /**
     * Actualizar estado del ticket
     */
    public function actualizarEstado($id_ticket, $nuevo_estado){
        $query = "UPDATE tickets 
                  SET estado = :estado 
                  WHERE id_ticket = :id_ticket";
        
        $stmt = $this->pdo->prepare($query);
        return $stmt->execute([
            'estado' => $nuevo_estado,
            'id_ticket' => $id_ticket
        ]);
    }
    
    /**
     * Asignar ticket a un administrador
     */
    public function asignarTicket($id_ticket, $id_admin){
        $query = "UPDATE tickets 
                  SET id_asignado = :id_admin,
                      estado = 'en_progreso'
                  WHERE id_ticket = :id_ticket";
        
        $stmt = $this->pdo->prepare($query);
        return $stmt->execute([
            'id_admin' => $id_admin,
            'id_ticket' => $id_ticket
        ]);
    }
    
    /**
     * Registrar cambio de estado en el historial
     */
    public function registrarCambioEstado($id_ticket, $nuevo_estado, $id_usuario){
        // Obtener estado anterior
        $ticket = $this->obtenerTicket($id_ticket);
        $estado_anterior = $ticket['estado'];
        
        $query = "INSERT INTO historial_estados (id_ticket, estado_anterior, nuevo_estado, cambiado_por)
                  VALUES (:id_ticket, :estado_anterior, :nuevo_estado, :cambiado_por)";
        
        $stmt = $this->pdo->prepare($query);
        return $stmt->execute([
            'id_ticket' => $id_ticket,
            'estado_anterior' => $estado_anterior,
            'nuevo_estado' => $nuevo_estado,
            'cambiado_por' => $id_usuario
        ]);
    }
    
    /**
     * Obtener estadísticas de tickets
     */
    public function obtenerEstadisticas($id_usuario = null, $rol = null){
        $where = "";
        $params = [];
        
        if($id_usuario && $rol === 'docente'){
            $where = "WHERE id_docente = :id_usuario";
            $params['id_usuario'] = $id_usuario;
        } elseif($id_usuario && $rol === 'administrador'){
            $where = "WHERE id_asignado = :id_usuario";
            $params['id_usuario'] = $id_usuario;
        }
        
        $query = "SELECT 
                    COUNT(*) as total,
                    SUM(CASE WHEN estado = 'pendiente' THEN 1 ELSE 0 END) as pendientes,
                    SUM(CASE WHEN estado = 'en_progreso' THEN 1 ELSE 0 END) as en_progreso,
                    SUM(CASE WHEN estado = 'resuelto' THEN 1 ELSE 0 END) as resueltos,
                    SUM(CASE WHEN estado = 'rechazado' THEN 1 ELSE 0 END) as rechazados
                  FROM tickets $where";
        
        $stmt = $this->pdo->prepare($query);
        $stmt->execute($params);
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }
    
    /**
     * Obtener último ID insertado
     */
    public function obtenerUltimoId(){
        return $this->pdo->lastInsertId();
    }

    /**
     * Obtener estadísticas para dashboard
     */
    public function getEstadisticas() {
        $query = "SELECT 
                    COUNT(*) AS total,
                    SUM(CASE WHEN estado = 'pendiente' THEN 1 ELSE 0 END) AS pendientes,
                    SUM(CASE WHEN estado = 'en_progreso' THEN 1 ELSE 0 END) AS en_proceso,
                    SUM(CASE WHEN estado = 'resuelto' THEN 1 ELSE 0 END) AS resueltos,
                    SUM(CASE WHEN prioridad = 'critica' THEN 1 ELSE 0 END) AS criticos
                  FROM tickets";
        
        $stmt = $this->pdo->prepare($query);
        $stmt->execute();
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    
    public function listarTicketsRecientes($limite = 10) {
        $query = "SELECT t.*, 
                         c.nombre as categoria_nombre,
                         CONCAT(d.nombre, ' ', d.apellido) as nombre_usuario,
                         d.correo as correo_usuario
                  FROM tickets t
                  LEFT JOIN categorias c ON t.id_categoria = c.id_categoria
                  LEFT JOIN usuarios d ON t.id_docente = d.id_usuario
                  ORDER BY t.fecha_creacion DESC
                  LIMIT :limite";
        
        $stmt = $this->pdo->prepare($query);
        $stmt->bindValue(':limite', $limite, PDO::PARAM_INT);
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
    
    /**
     * Obtener todos los tickets asignados a un técnico
     */
    public function obtenerTicketsTecnico($id_tecnico){
        $query = "SELECT t.*, 
                         c.nombre as categoria_nombre,
                         d.nombre as docente_nombre,
                         d.apellido as docente_apellido,
                         d.correo as docente_correo
                  FROM tickets t
                  LEFT JOIN categorias c ON t.id_categoria = c.id_categoria
                  LEFT JOIN usuarios d ON t.id_docente = d.id_usuario
                  WHERE t.id_asignado = :id_tecnico
                  ORDER BY 
                    CASE t.prioridad
                        WHEN 'critica' THEN 1
                        WHEN 'alta' THEN 2
                        WHEN 'media' THEN 3
                        WHEN 'baja' THEN 4
                    END,
                    t.fecha_creacion DESC";
        
        $stmt = $this->pdo->prepare($query);
        $stmt->execute(['id_tecnico' => $id_tecnico]);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
    
    /**
     * Obtener estadísticas de tickets del técnico
     */
    public function obtenerEstadisticasTecnico($id_tecnico){
        $query = "SELECT 
                    COUNT(*) as total,
                    SUM(CASE WHEN estado = 'pendiente' THEN 1 ELSE 0 END) as pendientes,
                    SUM(CASE WHEN estado = 'en_progreso' THEN 1 ELSE 0 END) as en_progreso,
                    SUM(CASE WHEN estado = 'resuelto' THEN 1 ELSE 0 END) as resueltos,
                    SUM(CASE WHEN prioridad = 'critica' THEN 1 ELSE 0 END) as criticos
                  FROM tickets 
                  WHERE id_asignado = :id_tecnico";
        
        $stmt = $this->pdo->prepare($query);
        $stmt->execute(['id_tecnico' => $id_tecnico]);
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }
}
?>