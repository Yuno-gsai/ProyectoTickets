<?php
// controlador/controladorTicket.php

include_once __DIR__ . "/../modelo/modeloTicket.php";
include_once __DIR__ . "/../modelo/modeloNotificacion.php";
include_once __DIR__ . "/../config/CN.php";

class controladorTicket{
    public $modelo;
    public $modeloNotificacion;
    
    public function __construct(){
        $this->modelo = new modeloTicket();
        $this->modeloNotificacion = new modeloNotificacion();
    }
    
   
    public function obtenerTicket($id_ticket){
        return $this->modelo->obtenerTicket($id_ticket);
    }
    
   
    public function obtenerTicketsDocente($id_docente){
        return $this->modelo->obtenerTicketsDocente($id_docente);
    }
    
    
    public function listarTickets(){
        return $this->modelo->obtenerTodosTickets();
    }
    
    
     
    public function crearTicket($datos){
        // Validar datos requeridos
        if(empty($datos['titulo']) || empty($datos['descripcion']) || 
           empty($datos['id_docente']) || empty($datos['id_categoria'])){
            return ['success' => false, 'mensaje' => 'Datos incompletos'];
        }
        
        if($this->modelo->crearTicket($datos)){
            $id_ticket = $this->modelo->obtenerUltimoId();
            
            // Notificar a todos los administradores sobre el nuevo ticket
            $this->notificarNuevoTicket($id_ticket);
            
            return [
                'success' => true, 
                'mensaje' => 'Ticket creado exitosamente',
                'id_ticket' => $id_ticket
            ];
        }
        
        return ['success' => false, 'mensaje' => 'Error al crear el ticket'];
    }
    
    
    public function actualizarEstado($id_ticket, $nuevo_estado, $id_usuario){
        if($this->modelo->actualizarEstado($id_ticket, $nuevo_estado)){
            
            $this->modelo->registrarCambioEstado($id_ticket, $nuevo_estado, $id_usuario);
            
            // Obtener info del ticket para notificar al docente
            $ticket = $this->modelo->obtenerTicket($id_ticket);
            if($ticket){
                $this->modeloNotificacion->notificarCambioEstado(
                    $id_ticket, 
                    $nuevo_estado, 
                    $ticket['id_docente']
                );
            }
            
            return ['success' => true, 'mensaje' => 'Estado actualizado'];
        }
        return ['success' => false, 'mensaje' => 'Error al actualizar estado'];
    }
    
    
    public function asignarTicket($id_ticket, $id_admin){
        // Verificar si el técnico está disponible
        include_once __DIR__ . "/../modelo/modeloUsuario.php";
        $modeloUsuario = new modeloUsuario();
        
        // Verificar que el usuario es técnico
        $usuario = $modeloUsuario->getUsuario($id_admin);
        if($usuario && $usuario['rol'] === 'tecnico'){
            // Verificar disponibilidad
            if(!$modeloUsuario->verificarDisponibilidadTecnico($id_admin)){
                $ticketActivo = $modeloUsuario->obtenerTicketActivoTecnico($id_admin);
                return [
                    'success' => false, 
                    'mensaje' => 'El técnico ' . $usuario['nombre'] . ' ' . $usuario['apellido'] . ' ya está trabajando en el ticket #' . $ticketActivo['id_ticket'] . ': "' . $ticketActivo['titulo'] . '"',
                    'ocupado' => true,
                    'ticket_activo' => $ticketActivo
                ];
            }
        }
        
        if($this->modelo->asignarTicket($id_ticket, $id_admin)){
            // Notificar al admin asignado
            $this->modeloNotificacion->notificarAsignacion($id_ticket, $id_admin);
            
            return ['success' => true, 'mensaje' => 'Ticket asignado correctamente'];
        }
        return ['success' => false, 'mensaje' => 'Error al asignar ticket'];
    }
    
    
    public function obtenerEstadisticas($id_usuario = null, $rol = null){
        return $this->modelo->obtenerEstadisticas($id_usuario, $rol);
    }
    

    public function getEstadisticas() {
        return $this->modelo->getEstadisticas();
    }

  
    public function listarTicketsRecientes($limite = 10) {
        return $this->modelo->listarTicketsRecientes($limite);
    }
    
  
    private function notificarNuevoTicket($id_ticket){
        // Obtener información del ticket
        $ticket = $this->modelo->obtenerTicket($id_ticket);
        if(!$ticket){
            return false;
        }
        
        // Obtener todos los administradores activos
        $pdo = getConnection();
        $query = "SELECT id_usuario FROM usuarios WHERE rol = 'administrador'";
        $stmt = $pdo->query($query);
        $admins = $stmt->fetchAll(PDO::FETCH_ASSOC);
        
        // Crear mensaje descriptivo
        $mensaje = "Nuevo ticket: " . $ticket['titulo'];
        
        // Notificar a cada administrador
        foreach($admins as $admin){
            $this->modeloNotificacion->crearNotificacion(
                $admin['id_usuario'],
                $id_ticket,
                'web',
                $mensaje
            );
        }
        
        return true;
    }
}
?>