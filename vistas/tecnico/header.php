<?php
// Verificar sesión y rol
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

if(!isset($_SESSION['usuario']) || $_SESSION['usuario']['rol'] !== 'tecnico'){
    header("Location: /proyectophp/index.php");
    exit();
}

// Obtener información del usuario
if(!isset($usuario)){
    $usuario = $_SESSION['usuario'];
}

// Obtener notificaciones no leídas (solo si no está definido)
if(!isset($controladorNotif)){
    include_once __DIR__ . '/../../controlador/controladorNotificacion.php';
    $controladorNotif = new controladorNotificacion();
}
if(!isset($notificaciones_no_leidas)){
    $notificaciones_no_leidas = $controladorNotif->modelo->contarNoLeidas($usuario['id']);
}
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0, user-scalable=no">
    <title>Panel de Técnico - Sistema de Soporte</title>
    <!-- Bootstrap CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <!-- Font Awesome -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <!-- Custom Responsive CSS -->
    <link rel="stylesheet" href="../../assets/css/responsive.css">
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }
        
        body {
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            min-height: 100vh;
        }
        
        .header {
            background: white;
            box-shadow: 0 2px 10px rgba(0,0,0,0.1);
            position: sticky;
            top: 0;
            z-index: 1000;
        }
        
        .header-content {
            max-width: 1400px;
            margin: 0 auto;
            padding: 15px 30px;
            display: flex;
            justify-content: space-between;
            align-items: center;
        }
        
        .logo {
            display: flex;
            align-items: center;
            gap: 15px;
            font-size: 1.5rem;
            font-weight: bold;
            color: #f39c12;
        }
        
        .logo i {
            font-size: 2rem;
        }
        
        .nav-menu {
            display: flex;
            gap: 10px;
            align-items: center;
        }
        
        .nav-link {
            padding: 10px 20px;
            text-decoration: none;
            color: #2c3e50;
            border-radius: 8px;
            transition: all 0.3s ease;
            display: flex;
            align-items: center;
            gap: 8px;
            font-weight: 500;
        }
        
        .nav-link:hover {
            background: #f8f9fa;
            color: #f39c12;
        }
        
        .nav-link.active {
            background: linear-gradient(135deg, #f39c12, #e67e22);
            color: white;
        }
        
        .user-info {
            display: flex;
            align-items: center;
            gap: 20px;
        }
        
        .notifications {
            position: relative;
            cursor: pointer;
        }
        
        .notif-icon {
            font-size: 1.5rem;
            color: #7f8c8d;
            transition: color 0.3s ease;
        }
        
        .notif-icon:hover {
            color: #f39c12;
        }
        
        .notif-badge {
            position: absolute;
            top: -5px;
            right: -5px;
            background: #e74c3c;
            color: white;
            border-radius: 50%;
            width: 20px;
            height: 20px;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 0.75rem;
            font-weight: bold;
        }
        
        .user-profile {
            display: flex;
            align-items: center;
            gap: 10px;
            padding: 8px 15px;
            background: #f8f9fa;
            border-radius: 25px;
        }
        
        .user-avatar {
            width: 40px;
            height: 40px;
            border-radius: 50%;
            background: linear-gradient(135deg, #f39c12, #e67e22);
            display: flex;
            align-items: center;
            justify-content: center;
            color: white;
            font-weight: bold;
            font-size: 1.2rem;
        }
        
        .user-name {
            font-weight: 600;
            color: #2c3e50;
        }
        
        .logout-btn {
            padding: 10px 20px;
            background: #e74c3c;
            color: white;
            border: none;
            border-radius: 8px;
            cursor: pointer;
            font-weight: 600;
            transition: all 0.3s ease;
            display: flex;
            align-items: center;
            gap: 8px;
            text-decoration: none;
        }
        
        .logout-btn:hover {
            background: #c0392b;
            transform: translateY(-2px);
        }
        
        .container {
            max-width: 1400px;
            margin: 30px auto;
            padding: 0 30px;
        }
        
        @media (max-width: 768px) {
            .nav-menu {
                flex-direction: column;
                gap: 5px;
            }
            
            .user-name {
                display: none;
            }
        }
    </style>
</head>
<body>
    <div class="header">
        <div class="header-content">
            <div class="logo">
                <i class="fas fa-tools"></i>
                <span>Panel Técnico</span>
            </div>
            
            <nav class="nav-menu">
                <a href="index.php" class="nav-link <?php echo basename($_SERVER['PHP_SELF']) == 'index.php' ? 'active' : ''; ?>">
                    <i class="fas fa-home"></i>
                    Dashboard
                </a>
                <a href="mis_tickets.php" class="nav-link <?php echo basename($_SERVER['PHP_SELF']) == 'mis_tickets.php' ? 'active' : ''; ?>">
                    <i class="fas fa-ticket-alt"></i>
                    Mis Tickets
                </a>
                <a href="notificaciones.php" class="nav-link <?php echo basename($_SERVER['PHP_SELF']) == 'notificaciones.php' ? 'active' : ''; ?>">
                    <i class="fas fa-bell"></i>
                    Notificaciones
                </a>
            </nav>
            
            <div class="user-info">
                <div class="notifications" onclick="window.location.href='notificaciones.php'">
                    <i class="fas fa-bell notif-icon"></i>
                    <?php if($notificaciones_no_leidas > 0): ?>
                        <span class="notif-badge"><?php echo $notificaciones_no_leidas; ?></span>
                    <?php endif; ?>
                </div>
                
                <div class="user-profile">
                    <div class="user-avatar">
                        <?php echo strtoupper(substr($usuario['nombre'], 0, 1)); ?>
                    </div>
                    <span class="user-name"><?php echo htmlspecialchars($usuario['nombre_completo']); ?></span>
                </div>
                
                <a href="logout.php" class="logout-btn">
                    <i class="fas fa-sign-out-alt"></i>
                    Salir
                </a>
            </div>
        </div>
    </div>
