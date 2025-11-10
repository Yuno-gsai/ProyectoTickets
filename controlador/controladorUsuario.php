<?php
include_once __DIR__ . "/../modelo/modeloUsuario.php";
class controladorUsuario{
    public $modelo;
    
    public function __construct(){
        $this->modelo = new modeloUsuario();
    }
    
     public function login($correo, $password){
        $resultado = $this->modelo->login($correo, $password);
        if($resultado){
            // Guardar información completa del usuario
            $_SESSION['usuario'] = [
                'id' => $resultado['id_usuario'],
                'correo' => $resultado['correo'],
                'nombre' => $resultado['nombre'],
                'apellido' => $resultado['apellido'],
                'nombre_completo' => $resultado['nombre'] . ' ' . $resultado['apellido'],
                'rol' => $resultado['rol']
            ];
            
            // Redirigir según el rol
            if($resultado['rol'] == 'administrador'){
                header("Location: /proyectophp/vista/admin/");
            } elseif($resultado['rol'] == 'tecnico'){
                header("Location: /proyectophp/vista/tecnico/");
            } else {
                header("Location: /proyectophp/vista/usuario/");
            }
            exit();
        }
        return false;
    }
    
    public function listarUsuarios(){
        return $this->modelo->listarUsuarios();
    }

    public function getUsuario($id){
        return $this->modelo->getUsuario($id);
    }
    
    public function crearUsuario($datos){
        // Validar datos
        if(empty($datos['nombre']) || empty($datos['apellido']) || empty($datos['correo']) || empty($datos['password'])){
            return ['success' => false, 'mensaje' => 'Todos los campos son obligatorios'];
        }
        
        // Validar email
        if(!filter_var($datos['correo'], FILTER_VALIDATE_EMAIL)){
            return ['success' => false, 'mensaje' => 'El correo no es válido'];
        }
        
        // Verificar si el correo ya existe
        if($this->modelo->correoExiste($datos['correo'])){
            return ['success' => false, 'mensaje' => 'El correo ya está registrado'];
        }
        
        if($this->modelo->crearUsuario($datos)){
            return ['success' => true, 'mensaje' => 'Usuario creado exitosamente'];
        }
        
        return ['success' => false, 'mensaje' => 'Error al crear el usuario'];
    }
    
    public function actualizarUsuario($id, $datos){
        // Validar datos
        if(empty($datos['nombre']) || empty($datos['apellido']) || empty($datos['correo'])){
            return ['success' => false, 'mensaje' => 'Todos los campos son obligatorios'];
        }
        
        // Validar email
        if(!filter_var($datos['correo'], FILTER_VALIDATE_EMAIL)){
            return ['success' => false, 'mensaje' => 'El correo no es válido'];
        }
        
        // Verificar si el correo ya existe (excluyendo el usuario actual)
        if($this->modelo->correoExiste($datos['correo'], $id)){
            return ['success' => false, 'mensaje' => 'El correo ya está registrado'];
        }
        
        if($this->modelo->actualizarUsuario($id, $datos)){
            return ['success' => true, 'mensaje' => 'Usuario actualizado exitosamente'];
        }
        
        return ['success' => false, 'mensaje' => 'Error al actualizar el usuario'];
    }
    
    public function cambiarEstado($id, $estado){
        if($this->modelo->cambiarEstado($id, $estado)){
            return ['success' => true, 'mensaje' => 'Estado actualizado'];
        }
        return ['success' => false, 'mensaje' => 'Error al cambiar el estado'];
    }
    
    public function eliminarUsuario($id){
        // Validar que no sea el usuario actual
        if(isset($_SESSION['usuario']['id']) && $_SESSION['usuario']['id'] == $id){
            return ['success' => false, 'mensaje' => 'No puedes eliminar tu propio usuario'];
        }
        
        if($this->modelo->eliminarUsuario($id)){
            return ['success' => true, 'mensaje' => 'Usuario eliminado exitosamente'];
        }
        
        return ['success' => false, 'mensaje' => 'Error al eliminar el usuario'];
    }
    
    public function actualizarPassword($id, $password){
        if(empty($password)){
            return ['success' => false, 'mensaje' => 'La contraseña no puede estar vacía'];
        }
        
        if($this->modelo->actualizarPassword($id, $password)){
            return ['success' => true, 'mensaje' => 'Contraseña actualizada'];
        }
        
        return ['success' => false, 'mensaje' => 'Error al actualizar la contraseña'];
    }
}
?>
