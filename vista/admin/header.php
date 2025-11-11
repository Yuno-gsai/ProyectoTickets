<?php

include_once(__DIR__ . '/../../controlador/controladorNotificacion.php');


$controladorNotif = new controladorNotificacion();


$id_usuario_actual = isset($_SESSION['usuario']['id']) ? $_SESSION['usuario']['id'] : 0;


$notificaciones = [];
$cantidad_no_leidas = 0;

if($id_usuario_actual > 0){
    $notificaciones = $controladorNotif->obtenerNotificacionesUsuario($id_usuario_actual, 10);
    $cantidad_no_leidas = $controladorNotif->contarNoLeidas($id_usuario_actual);
}


if(isset($_GET['accion_notif'])){
    $id_notif = isset($_GET['id_notif']) ? intval($_GET['id_notif']) : null;
    $resultado = $controladorNotif->procesarAccion($_GET['accion_notif'], $id_usuario_actual, $id_notif);
    
    
    $url_destino = isset($_GET['url']) ? $_GET['url'] : 'dashboardh';
    $parametros_extra = '';
    
    if(isset($_GET['id']) && $_GET['accion_notif'] == 'marcar_leida'){
        $parametros_extra = '&id=' . intval($_GET['id']);
    }
    
    header("Location: ?url=" . $url_destino . $parametros_extra);
    exit();
}
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0, user-scalable=no">
    <title><?php echo isset($page_title) ? $page_title : 'Sistema de Soporte'; ?></title>
    <!-- Bootstrap CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <!-- Font Awesome -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <!-- Custom CSS -->
    <link rel="stylesheet" href="../../assets/css/header.css">
    <link rel="stylesheet" href="../../assets/css/responsive.css">
</head>
<body>
    <header class="admin-header">
        <div class="header-top">
            <div class="logo-container">
                <div class="logo">
                    <i class="fas fa-headset"></i>
                </div>
                <div class="logo-text">
                    <h1>Sistema de Soporte</h1>
                    <p>Panel de Administración</p>
                </div>
            </div>

            <div class="user-menu">
                <div class="header-actions">
                    <!-- BOTÓN DE NOTIFICACIONES -->
                    <div style="position: relative;">
                        <button class="header-btn" id="notificationBtn">
                            <i class="fas fa-bell"></i>
                            <?php if($cantidad_no_leidas > 0): ?>
                                <span class="notification-badge"><?php echo $cantidad_no_leidas; ?></span>
                            <?php endif; ?>
                        </button>
                        
                        <!-- PANEL DE NOTIFICACIONES -->
                        <div class="notifications-panel" id="notificationsPanel">
                            <div class="notifications-header">
                                <h3>Notificaciones</h3>
                                <?php if($cantidad_no_leidas > 0): ?>
                                    <a href="?url=<?php echo isset($_GET['url']) ? $_GET['url'] : 'dashboardh'; ?>&accion_notif=marcar_todas" class="mark-all-read">
                                        Marcar todas leídas
                                    </a>
                                <?php endif; ?>
                            </div>
                            
                            <div class="notifications-body">
                                <?php if(empty($notificaciones)): ?>
                                    <div class="no-notifications">
                                        <i class="fas fa-bell-slash"></i>
                                        <p>No tienes notificaciones</p>
                                    </div>
                                <?php else: ?>
                                    <?php foreach($notificaciones as $notif): ?>
                                        <div class="notification-item <?php echo $notif['leido'] ? '' : 'unread'; ?>">
                                            <div class="notification-content">
                                                <div class="notification-icon">
                                                    <i class="fas fa-<?php 
                                                        echo strpos($notif['mensaje'], 'asignado') !== false ? 'user-check' : 
                                                            (strpos($notif['mensaje'], 'mensaje') !== false ? 'comment' : 'ticket-alt'); 
                                                    ?>"></i>
                                                </div>
                                                <div class="notification-text">
                                                    <div class="notification-message">
                                                        <?php echo htmlspecialchars($notif['mensaje']); ?>
                                                        <?php if($notif['ticket_titulo']): ?>
                                                            <br><strong><?php echo htmlspecialchars($notif['ticket_titulo']); ?></strong>
                                                        <?php endif; ?>
                                                    </div>
                                                    <div class="notification-time">
                                                        <?php 
                                                            $fecha = strtotime($notif['fecha_envio']);
                                                            $diff = time() - $fecha;
                                                            if($diff < 60) echo "Hace unos segundos";
                                                            elseif($diff < 3600) echo "Hace " . floor($diff/60) . " minutos";
                                                            elseif($diff < 86400) echo "Hace " . floor($diff/3600) . " horas";
                                                            else echo date('d/m/Y H:i', $fecha);
                                                        ?>
                                                    </div>
                                                    <div class="notification-actions">
                                                        <?php if($notif['id_ticket']): ?>
                                                            <a href="/proyectophp/vista/admin/chat_ticket.php?id=<?php echo $notif['id_ticket']; ?>" class="notif-action-btn" onclick="<?php if(!$notif['leido']): ?>fetch('?url=<?php echo isset($_GET['url']) ? $_GET['url'] : 'dashboardh'; ?>&accion_notif=marcar_leida&id_notif=<?php echo $notif['id_notificacion']; ?>');<?php endif; ?>">
                                                                Ver ticket
                                                            </a>
                                                        <?php endif; ?>
                                                        <?php if(!$notif['leido']): ?>
                                                            <a href="?url=<?php echo isset($_GET['url']) ? $_GET['url'] : 'dashboardh'; ?>&accion_notif=marcar_leida&id_notif=<?php echo $notif['id_notificacion']; ?>" class="notif-action-btn">
                                                                Marcar leída
                                                            </a>
                                                        <?php endif; ?>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    <?php endforeach; ?>
                                <?php endif; ?>
                            </div>
                        </div>
                    </div>
                    
                    <button class="header-btn">
                        <i class="fas fa-cog"></i>
                    </button>
                </div>
                
                <!-- MENÚ DE USUARIO -->
                <div class="user-info" id="userDropdown">
                    <div class="user-avatar">
                        <?php 
                        $nombre = isset($_SESSION['usuario']['nombre']) ? $_SESSION['usuario']['nombre'] : 'Usuario';
                        echo strtoupper(substr($nombre, 0, 1));
                        ?>
                    </div>
                    <div class="user-details">
                        <div class="user-name">
                            <?php echo isset($_SESSION['usuario']['nombre_completo']) ? htmlspecialchars($_SESSION['usuario']['nombre_completo']) : 'Usuario'; ?>
                        </div>
                        <div class="user-role">
                            <?php echo isset($_SESSION['usuario']['rol']) ? ucfirst(htmlspecialchars($_SESSION['usuario']['rol'])) : 'Usuario'; ?>
                        </div>
                    </div>
                    <i class="fas fa-chevron-down"></i>
                    
                    <div class="dropdown-menu" id="dropdownMenu">
                        <a href="?url=perfil" class="dropdown-item">
                            <i class="fas fa-user-circle"></i> Mi Perfil
                        </a>
                        <a href="?url=configuracion" class="dropdown-item">
                            <i class="fas fa-cog"></i> Configuración
                        </a>
                        <a href="?url=ayuda" class="dropdown-item">
                            <i class="fas fa-question-circle"></i> Ayuda
                        </a>
                        <a href="/proyectophp/logout.php" class="dropdown-item">
                            <i class="fas fa-sign-out-alt"></i> Cerrar Sesión
                        </a>
                    </div>
                </div>
            </div>
        </div>

        <nav class="admin-nav">
            <ul class="nav-menu">
                <li class="nav-item">
                    <a href="?url=dashboardh" class="nav-link <?php echo (isset($_GET['url']) && $_GET['url'] == 'dashboardh') || !isset($_GET['url']) ? 'active' : ''; ?>">
                        <i class="fas fa-tachometer-alt"></i>
                        <span>Dashboard</span>
                    </a>
                </li>
                <li class="nav-item">
                    <a href="?url=tareas_pendientes" class="nav-link <?php echo (isset($_GET['url']) && $_GET['url'] == 'tareas_pendientes') ? 'active' : ''; ?>">
                        <i class="fas fa-clipboard-list"></i>
                        <span>Tareas Pendientes</span>
                    </a>
                </li>
                <li class="nav-item">
                    <a href="?url=gestion_tickets" class="nav-link <?php echo (isset($_GET['url']) && $_GET['url'] == 'gestion_tickets') ? 'active' : ''; ?>">
                        <i class="fas fa-tasks"></i>
                        <span>Gestión Tickets</span>
                    </a>
                </li>
                <li class="nav-item">
                    <a href="?url=personal_tecnico" class="nav-link <?php echo (isset($_GET['url']) && $_GET['url'] == 'personal_tecnico') ? 'active' : ''; ?>">
                        <i class="fas fa-tools"></i>
                        <span>Personal Técnico</span>
                    </a>
                </li>
                <li class="nav-item">
                    <a href="?url=usuarios" class="nav-link <?php echo (isset($_GET['url']) && $_GET['url'] == 'usuarios') ? 'active' : ''; ?>">
                        <i class="fas fa-users"></i>
                        <span>Usuarios</span>
                    </a>
                </li>
                <li class="nav-item">
                    <a href="?url=reportes" class="nav-link <?php echo (isset($_GET['url']) && $_GET['url'] == 'reportes') ? 'active' : ''; ?>">
                        <i class="fas fa-chart-bar"></i>
                        <span>Reportes</span>
                    </a>
                </li>
                <li class="nav-item">
                    <a href="?url=resumenes" class="nav-link <?php echo (isset($_GET['url']) && $_GET['url'] == 'resumenes') ? 'active' : ''; ?>">
                        <i class="fas fa-chart-line"></i>
                        <span>Resúmenes</span>
                    </a>
                </li>
            </ul>
        </nav>
    </header>

    <main class="admin-main">
    
    <script>
        // Esperar a que el DOM esté cargado
        document.addEventListener('DOMContentLoaded', function() {
            // Toggle panel de notificaciones
            const notificationBtn = document.getElementById('notificationBtn');
            const notificationsPanel = document.getElementById('notificationsPanel');
            const userDropdown = document.getElementById('userDropdown');
            const dropdownMenu = document.getElementById('dropdownMenu');
            
            if (notificationBtn) {
                notificationBtn.addEventListener('click', function(e) {
                    e.stopPropagation();
                    if (notificationsPanel) {
                        notificationsPanel.classList.toggle('show');
                    }
                    if (dropdownMenu) {
                        dropdownMenu.classList.remove('show');
                    }
                });
            }

            // Toggle dropdown de usuario
            if (userDropdown) {
                userDropdown.addEventListener('click', function(e) {
                    e.stopPropagation();
                    if (dropdownMenu) {
                        dropdownMenu.classList.toggle('show');
                    }
                    if (notificationsPanel) {
                        notificationsPanel.classList.remove('show');
                    }
                });
            }

            // Cerrar paneles al hacer clic fuera
            document.addEventListener('click', function() {
                if (dropdownMenu) {
                    dropdownMenu.classList.remove('show');
                }
                if (notificationsPanel) {
                    notificationsPanel.classList.remove('show');
                }
            });
            
            // Prevenir que los clics dentro de los paneles los cierren
            if (notificationsPanel) {
                notificationsPanel.addEventListener('click', function(e) {
                    e.stopPropagation();
                });
            }
            
            if (dropdownMenu) {
                dropdownMenu.addEventListener('click', function(e) {
                    e.stopPropagation();
                });
            }
        });
    </script>