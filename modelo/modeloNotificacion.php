<?php

include_once __DIR__ . "/../config/CN.php";

class modeloNotificacion{
    private $pdo;
    
    public function __construct(){
        $this->pdo = getConnection();
    }
    
    public function obtenerNotificacionesUsuario($id_usuario, $limite = 10){
        $query = "SELECT n.*, 
                         t.titulo as ticket_titulo, 
                         t.id_ticket,
                         u.nombre, 
                         u.apellido
                  FROM notificaciones n
                  LEFT JOIN tickets t ON n.id_ticket = t.id_ticket
                  LEFT JOIN usuarios u ON t.id_docente = u.id_usuario
                  WHERE n.id_usuario = :id_usuario
                  ORDER BY n.fecha_envio DESC
                  LIMIT :limite";
        
        $stmt = $this->pdo->prepare($query);
        $stmt->bindValue(':id_usuario', $id_usuario, PDO::PARAM_INT);
        $stmt->bindValue(':limite', $limite, PDO::PARAM_INT);
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
    
    public function contarNoLeidas($id_usuario){
        $query = "SELECT COUNT(*) as total 
                  FROM notificaciones 
                  WHERE id_usuario = :id_usuario AND leido = 0";
        
        $stmt = $this->pdo->prepare($query);
        $stmt->execute(['id_usuario' => $id_usuario]);
        $resultado = $stmt->fetch(PDO::FETCH_ASSOC);
        return $resultado['total'];
    }
    
    public function marcarComoLeida($id_notificacion){
        $query = "UPDATE notificaciones 
                  SET leido = 1 
                  WHERE id_notificacion = :id_notificacion";
        
        $stmt = $this->pdo->prepare($query);
        return $stmt->execute(['id_notificacion' => $id_notificacion]);
    }
    
    public function marcarTodasComoLeidas($id_usuario){
        $query = "UPDATE notificaciones 
                  SET leido = 1 
                  WHERE id_usuario = :id_usuario AND leido = 0";
        
        $stmt = $this->pdo->prepare($query);
        return $stmt->execute(['id_usuario' => $id_usuario]);
    }
    
    public function crearNotificacion($id_usuario, $id_ticket, $tipo, $mensaje){
        $query = "INSERT INTO notificaciones (id_usuario, id_ticket, tipo, mensaje) 
                  VALUES (:id_usuario, :id_ticket, :tipo, :mensaje)";
        
        $stmt = $this->pdo->prepare($query);
        return $stmt->execute([
            'id_usuario' => $id_usuario,
            'id_ticket' => $id_ticket,
            'tipo' => $tipo,
            'mensaje' => $mensaje
        ]);
    }
    
    public function eliminarNotificacion($id_notificacion){
        $query = "DELETE FROM notificaciones WHERE id_notificacion = :id_notificacion";
        $stmt = $this->pdo->prepare($query);
        return $stmt->execute(['id_notificacion' => $id_notificacion]);
    }
    
    public function obtenerNotificacion($id_notificacion){
        $query = "SELECT n.*, 
                         t.titulo as ticket_titulo, 
                         t.id_ticket
                  FROM notificaciones n
                  LEFT JOIN tickets t ON n.id_ticket = t.id_ticket
                  WHERE n.id_notificacion = :id_notificacion";
        
        $stmt = $this->pdo->prepare($query);
        $stmt->execute(['id_notificacion' => $id_notificacion]);
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }
    
    public function notificarCambioEstado($id_ticket, $nuevo_estado, $id_usuario_destino){
        $mensaje = "El ticket ha cambiado a estado: " . ucfirst(str_replace('_', ' ', $nuevo_estado));
        return $this->crearNotificacion($id_usuario_destino, $id_ticket, 'web', $mensaje);
    }
    

    public function notificarAsignacion($id_ticket, $id_admin_asignado){
        // Obtener información del ticket para mensaje personalizado
        $query = "SELECT t.titulo, t.prioridad, c.nombre as categoria, 
                         CONCAT(u.nombre, ' ', u.apellido) as docente
                  FROM tickets t
                  LEFT JOIN categorias c ON t.id_categoria = c.id_categoria
                  LEFT JOIN usuarios u ON t.id_docente = u.id_usuario
                  WHERE t.id_ticket = :id_ticket";
        
        $stmt = $this->pdo->prepare($query);
        $stmt->execute(['id_ticket' => $id_ticket]);
        $ticket = $stmt->fetch(PDO::FETCH_ASSOC);
        
        if($ticket){
            $prioridad_text = strtoupper($ticket['prioridad']);
            $mensaje = " NUEVO TICKET ASIGNADO [{$prioridad_text}]: {$ticket['titulo']} - {$ticket['categoria']} ({$ticket['docente']})";
        } else {
            $mensaje = "Se te ha asignado un nuevo ticket para revisión";
        }
        
        return $this->crearNotificacion($id_admin_asignado, $id_ticket, 'web', $mensaje);
    }
    
    public function notificarNuevoMensaje($id_ticket, $id_usuario_destino, $nombre_emisor){
        $mensaje = $nombre_emisor . " ha enviado un nuevo mensaje en tu ticket";
        return $this->crearNotificacion($id_usuario_destino, $id_ticket, 'web', $mensaje);
    }
}
?>