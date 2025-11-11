<?php


include_once __DIR__ . "/../config/CN.php";

class modeloMensaje{
    private $pdo;
    
    public function __construct(){
        $this->pdo = getConnection();
    }
    
    public function obtenerMensajesTicket($id_ticket){
        $query = "SELECT m.*, 
                         u.nombre, 
                         u.apellido, 
                         u.rol
                  FROM mensajes_chat m
                  INNER JOIN usuarios u ON m.id_emisor = u.id_usuario
                  WHERE m.id_ticket = :id_ticket
                  ORDER BY m.fecha_envio ASC";
        
        $stmt = $this->pdo->prepare($query);
        $stmt->execute(['id_ticket' => $id_ticket]);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
    
    public function enviarMensaje($id_ticket, $id_emisor, $mensaje){
        $query = "INSERT INTO mensajes_chat (id_ticket, id_emisor, mensaje) 
                  VALUES (:id_ticket, :id_emisor, :mensaje)";
        
        $stmt = $this->pdo->prepare($query);
        return $stmt->execute([
            'id_ticket' => $id_ticket,
            'id_emisor' => $id_emisor,
            'mensaje' => $mensaje
        ]);
    }
    
    public function obtenerUltimoMensaje($id_ticket){
        $query = "SELECT m.*, 
                         u.nombre, 
                         u.apellido,
                         u.rol
                  FROM mensajes_chat m
                  INNER JOIN usuarios u ON m.id_emisor = u.id_usuario
                  WHERE m.id_ticket = :id_ticket
                  ORDER BY m.fecha_envio DESC
                  LIMIT 1";
        
        $stmt = $this->pdo->prepare($query);
        $stmt->execute(['id_ticket' => $id_ticket]);
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }
    
    public function contarMensajes($id_ticket){
        $query = "SELECT COUNT(*) as total 
                  FROM mensajes_chat 
                  WHERE id_ticket = :id_ticket";
        
        $stmt = $this->pdo->prepare($query);
        $stmt->execute(['id_ticket' => $id_ticket]);
        $resultado = $stmt->fetch(PDO::FETCH_ASSOC);
        return $resultado['total'];
    }
    
    public function obtenerMensajesRecientesUsuario($id_usuario, $limite = 5){
        $query = "SELECT m.*, 
                         u.nombre, 
                         u.apellido,
                         u.rol,
                         t.titulo as ticket_titulo,
                         t.id_ticket
                  FROM mensajes_chat m
                  INNER JOIN usuarios u ON m.id_emisor = u.id_usuario
                  INNER JOIN tickets t ON m.id_ticket = t.id_ticket
                  WHERE t.id_docente = :id_usuario OR t.id_asignado = :id_usuario
                  ORDER BY m.fecha_envio DESC
                  LIMIT :limite";
        
        $stmt = $this->pdo->prepare($query);
        $stmt->bindValue(':id_usuario', $id_usuario, PDO::PARAM_INT);
        $stmt->bindValue(':limite', $limite, PDO::PARAM_INT);
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
    
    public function eliminarMensaje($id_mensaje){
        $query = "DELETE FROM mensajes_chat WHERE id_mensaje = :id_mensaje";
        $stmt = $this->pdo->prepare($query);
        return $stmt->execute(['id_mensaje' => $id_mensaje]);
    }
    
    public function obtenerInfoTicketPorIdTicket($id_ticket){
        $query = "SELECT id_docente, id_asignado
                  FROM tickets
                  WHERE id_ticket = :id_ticket";
        
        $stmt = $this->pdo->prepare($query);
        $stmt->execute(['id_ticket' => $id_ticket]);
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }
}
?>