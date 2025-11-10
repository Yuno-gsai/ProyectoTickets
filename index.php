<?php
session_start();
date_default_timezone_set('America/Mexico_City');

include_once "controlador/controladorUsuario.php";
$controlador = new controladorUsuario();
$mensaje = '';

if(isset($_POST['login'])){
    $correo = $_POST['correo'] ?? '';
    $password = $_POST['password'] ?? '';
    
    if(!empty($correo) && !empty($password)) {
        $resultado = $controlador->login($correo, $password);
        if(!$resultado) {
            $mensaje = "<div class='alert alert-error'>
                <i class='fas fa-exclamation-circle'></i> 
                Error: Correo o contraseña incorrectos
            </div>";
        }
    } else {
        $mensaje = "<div class='alert alert-error'>
            <i class='fas fa-exclamation-circle'></i> 
            Por favor, complete todos los campos
        </div>";
    }
}
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0, user-scalable=no">
    <title>Iniciar Sesión - Sistema de Soporte de Tickets</title>
    <!-- Bootstrap CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <!-- Font Awesome -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <!-- Custom CSS -->
    <link rel="stylesheet" href="assets/css/login.css">
    <link rel="stylesheet" href="assets/css/responsive.css">
</head>
<body>
    <div class="login-container">
        <div class="login-card">
            <div class="login-header">
                <i class="fas fa-headset"></i>
                <h1>Soporte de Tickets</h1>
                <p>Sistema de gestión de incidencias</p>
            </div>
            
            <div class="login-body">
                <?php if(!empty($mensaje)) echo $mensaje; ?>
                
                <form action="<?php echo htmlspecialchars($_SERVER['PHP_SELF']); ?>" method="post">
                    <div class="form-group">
                        <label for="correo">Correo Electrónico</label>
                        <div class="input-with-icon">
                            <i class="fas fa-envelope"></i>
                            <input type="email" name="correo" id="correo" placeholder="usuario@escuela.edu" value="<?php echo isset($_POST['correo']) ? htmlspecialchars($_POST['correo']) : ''; ?>" required>
                        </div>
                    </div>
                    
                    <div class="form-group">
                        <label for="password">Contraseña</label>
                        <div class="input-with-icon">
                            <i class="fas fa-lock"></i>
                            <input type="password" name="password" id="password" placeholder="Ingresa tu contraseña" required>
                        </div>
                    </div>
                    
                    <button type="submit" name="login" class="btn">
                        <i class="fas fa-sign-in-alt"></i> Iniciar Sesión
                    </button>
                </form>
                
                <div class="login-footer">
                    <p>¿Problemas para acceder? <a href="#">Contacta al administrador</a></p>
                </div>
            </div>
        </div>
        
        <div class="system-info">
            <p><i class="fas fa-info-circle"></i> Sistema de Soporte de Tickets v1.0</p>
        </div>
    </div>

    <!-- Bootstrap JS Bundle -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const alerts = document.querySelectorAll('.alert');
            alerts.forEach(alert => {
                setTimeout(() => {
                    alert.style.opacity = '0';
                    setTimeout(() => {
                        alert.remove();
                    }, 500);
                }, 5000);
            });
        });
    </script>
</body>
</html>