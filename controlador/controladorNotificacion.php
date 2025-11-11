<?php
// controlador/controladorNotificacion.php

include_once __DIR__ . "/../modelo/modeloNotificacion.php";

class controladorNotificacion{
    public $modelo;
    
    public function __construct(){
        $this->modelo = new modeloNotificacion();
    }
    
    /**
     * Obtener notificaciones del usuario
     */
    public function obtenerNotificacionesUsuario($id_usuario, $limite = 10){
        return $this->modelo->obtenerNotificacionesUsuario($id_usuario, $limite);
    }
    
    /**
     * Contar notificaciones no leídas
     */
    public function contarNoLeidas($id_usuario){
        return $this->modelo->contarNoLeidas($id_usuario);
    }
    
    /**
     * Marcar notificación como leída
     */
    public function marcarComoLeida($id_notificacion){
        if($this->modelo->marcarComoLeida($id_notificacion)){
            return ['success' => true, 'mensaje' => 'Notificación marcada como leída'];
        }
        return ['success' => false, 'mensaje' => 'Error al marcar como leída'];
    }
    
    /**
     * Marcar todas como leídas
     */
    public function marcarTodasComoLeidas($id_usuario){
        if($this->modelo->marcarTodasComoLeidas($id_usuario)){
            return ['success' => true, 'mensaje' => 'Todas las notificaciones marcadas como leídas'];
        }
        return ['success' => false, 'mensaje' => 'Error al marcar notificaciones'];
    }
    
    /**
     * Crear notificación
     */
    public function crearNotificacion($id_usuario, $id_ticket, $tipo, $mensaje){
        if($this->modelo->crearNotificacion($id_usuario, $id_ticket, $tipo, $mensaje)){
            return ['success' => true, 'mensaje' => 'Notificación creada'];
        }
        return ['success' => false, 'mensaje' => 'Error al crear notificación'];
    }
    
    /**
     * Eliminar notificación
     */
    public function eliminarNotificacion($id_notificacion){
        if($this->modelo->eliminarNotificacion($id_notificacion)){
            return ['success' => true, 'mensaje' => 'Notificación eliminada'];
        }
        return ['success' => false, 'mensaje' => 'Error al eliminar notificación'];
    }
    
    /**
     * Obtener notificación específica
     */
    public function obtenerNotificacion($id_notificacion){
        return $this->modelo->obtenerNotificacion($id_notificacion);
    }
    
    /**
     * Notificar cambio de estado
     */
    public function notificarCambioEstado($id_ticket, $nuevo_estado, $id_usuario_destino){
        if($this->modelo->notificarCambioEstado($id_ticket, $nuevo_estado, $id_usuario_destino)){
            return ['success' => true, 'mensaje' => 'Notificación enviada'];
        }
        return ['success' => false, 'mensaje' => 'Error al enviar notificación'];
    }
    
    /**
     * Notificar asignación
     */
    public function notificarAsignacion($id_ticket, $id_admin_asignado){
        if($this->modelo->notificarAsignacion($id_ticket, $id_admin_asignado)){
            return ['success' => true, 'mensaje' => 'Notificación enviada'];
        }
        return ['success' => false, 'mensaje' => 'Error al enviar notificación'];
    }
    
    /**
     * Notificar nuevo mensaje
     */
    public function notificarNuevoMensaje($id_ticket, $id_usuario_destino, $nombre_emisor){
        if($this->modelo->notificarNuevoMensaje($id_ticket, $id_usuario_destino, $nombre_emisor)){
            return ['success' => true, 'mensaje' => 'Notificación enviada'];
        }
        return ['success' => false, 'mensaje' => 'Error al enviar notificación'];
    }
    
    /**
     * Procesar acciones de notificaciones desde la URL
     */
    public function procesarAccion($accion, $id_usuario, $id_notificacion = null){
        switch($accion){
            case 'marcar_leida':
                if($id_notificacion){
                    return $this->marcarComoLeida($id_notificacion);
                }
                return ['success' => false, 'mensaje' => 'ID de notificación requerido'];
                
            case 'marcar_todas':
                return $this->marcarTodasComoLeidas($id_usuario);
                
            case 'eliminar':
                if($id_notificacion){
                    return $this->eliminarNotificacion($id_notificacion);
                }
                return ['success' => false, 'mensaje' => 'ID de notificación requerido'];
                
            default:
                return ['success' => false, 'mensaje' => 'Acción no válida'];
        }
    }
}
?>