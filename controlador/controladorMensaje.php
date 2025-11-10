<?php
// controlador/controladorMensaje.php

include_once __DIR__ . "/../modelo/modeloMensaje.php";
include_once __DIR__ . "/../modelo/modeloNotificacion.php";
include_once __DIR__ . "/../config/CN.php";

class controladorMensaje{
    public $modelo;
    public $modeloNotificacion;
    
    public function __construct(){
        $this->modelo = new modeloMensaje();
        $this->modeloNotificacion = new modeloNotificacion();
    }
    
    /**
     * Obtener mensajes del ticket
     */
    public function obtenerMensajesTicket($id_ticket){
        return $this->modelo->obtenerMensajesTicket($id_ticket);
    }
    
    /**
     * Enviar mensaje y notificar al destinatario
     */
    public function enviarMensaje($id_ticket, $id_emisor, $mensaje, $nombre_emisor){
        if(empty(trim($mensaje))){
            return ['success' => false, 'mensaje' => 'El mensaje no puede estar vacío'];
        }
        
        // Enviar el mensaje
        if($this->modelo->enviarMensaje($id_ticket, $id_emisor, $mensaje)){
            
            // Obtener info del ticket para enviar notificaciones
            $infoTicket = $this->modelo->obtenerInfoTicketPorIdTicket($id_ticket);
            
            if($infoTicket){
                // Si el emisor es el docente, notificar al admin asignado
                if($id_emisor == $infoTicket['id_docente']){
                    if($infoTicket['id_asignado']){
                        // Notificar solo al admin asignado
                        $this->modeloNotificacion->notificarNuevoMensaje(
                            $id_ticket, 
                            $infoTicket['id_asignado'], 
                            $nombre_emisor
                        );
                    } else {
                        // Si NO hay admin asignado, notificar a TODOS los admins
                        $this->notificarATodosLosAdmins($id_ticket, $nombre_emisor);
                    }
                } 
                // Si el emisor es el admin, notificar al docente
                else {
                    $this->modeloNotificacion->notificarNuevoMensaje(
                        $id_ticket, 
                        $infoTicket['id_docente'], 
                        $nombre_emisor
                    );
                }
            }
            
            return ['success' => true, 'mensaje' => 'Mensaje enviado exitosamente'];
        }
        
        return ['success' => false, 'mensaje' => 'Error al enviar el mensaje'];
    }
    
    /**
     * Obtener último mensaje del ticket
     */
    public function obtenerUltimoMensaje($id_ticket){
        return $this->modelo->obtenerUltimoMensaje($id_ticket);
    }
    
    /**
     * Contar mensajes del ticket
     */
    public function contarMensajes($id_ticket){
        return $this->modelo->contarMensajes($id_ticket);
    }
    
    /**
     * Obtener mensajes recientes del usuario
     */
    public function obtenerMensajesRecientesUsuario($id_usuario, $limite = 5){
        return $this->modelo->obtenerMensajesRecientesUsuario($id_usuario, $limite);
    }
    
    /**
     * Eliminar mensaje
     */
    public function eliminarMensaje($id_mensaje){
        if($this->modelo->eliminarMensaje($id_mensaje)){
            return ['success' => true, 'mensaje' => 'Mensaje eliminado'];
        }
        return ['success' => false, 'mensaje' => 'Error al eliminar mensaje'];
    }
    
    /**
     * Notificar a todos los administradores activos
     * Se usa cuando un ticket no tiene admin asignado
     */
    private function notificarATodosLosAdmins($id_ticket, $nombre_emisor){
        try {
            $pdo = getConnection();
            $query = "SELECT id_usuario FROM usuarios WHERE rol = 'administrador' AND estado = 'activo'";
            $stmt = $pdo->query($query);
            $admins = $stmt->fetchAll(PDO::FETCH_ASSOC);
            
            $mensaje = $nombre_emisor . " ha enviado un nuevo mensaje en el ticket #" . $id_ticket;
            
            foreach($admins as $admin){
                $this->modeloNotificacion->crearNotificacion(
                    $admin['id_usuario'],
                    $id_ticket,
                    'web',
                    $mensaje
                );
            }
            
            return true;
        } catch(Exception $e) {
            error_log("Error al notificar a admins: " . $e->getMessage());
            return false;
        }
    }
}
?>