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
    
    $url_destino = isset($_GET['url']) ? $_GET['url'] : 'dashboard_docente';
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
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo isset($page_title) ? $page_title : 'Sistema de Soporte'; ?></title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <link rel="stylesheet" href="../../assets/css/header.css">
    <style>
        /* ESTILOS ESPECÍFICOS PARA DOCENTES */
        .user-header {
            background: linear-gradient(135deg, #e74c3c, #c0392b);
        }
        
        .user-header .nav-link.active {
            border-bottom-color: #e74c3c;
        }
        
        .user-header .logo-text p {
            color: rgba(255, 255, 255, 0.8);
        }
        
        .btn-new-ticket {
            background: linear-gradient(135deg, #2ecc71, #27ae60);
            color: white;
            border: none;
            padding: 10px 20px;
            border-radius: 8px;
            cursor: pointer;
            display: flex;
            align-items: center;
            gap: 8px;
            font-weight: 600;
            transition: all 0.3s ease;
            text-decoration: none;
            font-size: 0.9rem;
        }
        
        .btn-new-ticket:hover {
            transform: translateY(-2px);
            box-shadow: 0 6px 12px rgba(46, 204, 113, 0.3);
        }
        
        .ticket-count {
            background-color: #e74c3c;
            color: white;
            border-radius: 10px;
            padding: 2px 8px;
            font-size: 0.7rem;
            margin-left: 5px;
        }
        
        .user-avatar {
            background: linear-gradient(135deg, #e74c3c, #c0392b);
        }
        
        /* Responsive para docentes */
        @media (max-width: 768px) {
            .btn-new-ticket span {
                display: none;
            }
            
            .btn-new-ticket {
                padding: 10px;
                width: 45px;
                height: 45px;
                border-radius: 50%;
            }
        }
        
        @media (max-width: 576px) {
            .nav-link span {
                display: none;
            }
            
            .nav-link i {
                font-size: 1.2rem;
            }
        }
    </style>
</head>
<body>
    <!-- HEADER PARA USUARIO NORMAL (DOCENTE) -->
    <header class="admin-header user-header">
        <div class="header-top">
            <div class="logo-container">
                <div class="logo">
                    <i class="fas fa-headset"></i>
                </div>
                <div class="logo-text">
                    <h1>Sistema de Soporte</h1>
                    <p>Área de Docentes</p>
                </div>
            </div>

            <div class="user-menu">
                <!-- BOTÓN NUEVO TICKET -->
                <a href="?url=nuevo_ticket" class="btn-new-ticket">
                    <i class="fas fa-plus"></i>
                    <span>Nuevo Ticket</span>
                </a>
                
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
                                <h3>Mis Notificaciones</h3>
                                <?php if($cantidad_no_leidas > 0): ?>
                                    <a href="?url=<?php echo isset($_GET['url']) ? $_GET['url'] : 'dashboard_docente'; ?>&accion_notif=marcar_todas" class="mark-all-read">
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
                                                            (strpos($notif['mensaje'], 'mensaje') !== false ? 'comment' : 
                                                            (strpos($notif['mensaje'], 'resuelto') !== false ? 'check-circle' : 'ticket-alt')); 
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
                                                            <a href="/proyectophp/vista/usuario/chat_ticket.php?id=<?php echo $notif['id_ticket']; ?>" class="notif-action-btn" onclick="<?php if(!$notif['leido']): ?>fetch('?url=<?php echo isset($_GET['url']) ? $_GET['url'] : 'dashboard_docente'; ?>&accion_notif=marcar_leida&id_notif=<?php echo $notif['id_notificacion']; ?>');<?php endif; ?>">
                                                                Ver ticket
                                                            </a>
                                                        <?php endif; ?>
                                                        <?php if(!$notif['leido']): ?>
                                                            <a href="?url=<?php echo isset($_GET['url']) ? $_GET['url'] : 'dashboard_docente'; ?>&accion_notif=marcar_leida&id_notif=<?php echo $notif['id_notificacion']; ?>" class="notif-action-btn">
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
                </div>
                
                <!-- MENÚ DE USUARIO -->
                <div class="user-info" id="userDropdown">
                    <div class="user-avatar">
                        <?php 
                        $nombre = isset($_SESSION['usuario']['nombre']) ? $_SESSION['usuario']['nombre'] : 'D';
                        echo strtoupper(substr($nombre, 0, 1));
                        ?>
                    </div>
                    <div class="user-details">
                        <div class="user-name">
                            <?php 
                            echo isset($_SESSION['usuario']['nombre_completo']) 
                                ? htmlspecialchars($_SESSION['usuario']['nombre_completo']) 
                                : 'Docente';
                            ?>
                        </div>
                        <div class="user-role">
                            <?php 
                            echo isset($_SESSION['usuario']['rol']) 
                                ? ucfirst(htmlspecialchars($_SESSION['usuario']['rol'])) 
                                : 'Docente';
                            ?>
                        </div>
                    </div>
                    <i class="fas fa-chevron-down"></i>
                    
                    <div class="dropdown-menu" id="dropdownMenu">
                        <a href="?url=perfil" class="dropdown-item">
                            <i class="fas fa-user-circle"></i> Mi Perfil
                        </a>
                        <a href="?url=mis_tickets" class="dropdown-item">
                            <i class="fas fa-ticket-alt"></i> Mis Tickets
                            <?php 
                            // Contar tickets activos del docente
                            $tickets_activos = 0; // Esto vendría de la base de datos
                            if($tickets_activos > 0): ?>
                                <span class="ticket-count"><?php echo $tickets_activos; ?></span>
                            <?php endif; ?>
                        </a>
                        <a href="?url=ayuda" class="dropdown-item">
                            <i class="fas fa-question-circle"></i> Ayuda
                        </a>
                        <div class="dropdown-divider"></div>
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
                    <a href="?url=dashboard_docente" class="nav-link <?php echo (isset($_GET['url']) && $_GET['url'] == 'dashboard_docente') || !isset($_GET['url']) ? 'active' : ''; ?>">
                        <i class="fas fa-home"></i>
                        <span>Inicio</span>
                    </a>
                </li>
                <li class="nav-item">
                    <a href="?url=mis_tickets" class="nav-link <?php echo (isset($_GET['url']) && $_GET['url'] == 'mis_tickets') ? 'active' : ''; ?>">
                        <i class="fas fa-ticket-alt"></i>
                        <span>Mis Tickets</span>
                        <?php if($tickets_activos > 0): ?>
                            <span class="ticket-count"><?php echo $tickets_activos; ?></span>
                        <?php endif; ?>
                    </a>
                </li>
                <li class="nav-item">
                    <a href="?url=nuevo_ticket" class="nav-link <?php echo (isset($_GET['url']) && $_GET['url'] == 'nuevo_ticket') ? 'active' : ''; ?>">
                        <i class="fas fa-plus-circle"></i>
                        <span>Nuevo Ticket</span>
                    </a>
                </li>
                <li class="nav-item">
                    <a href="?url=historial" class="nav-link <?php echo (isset($_GET['url']) && $_GET['url'] == 'historial') ? 'active' : ''; ?>">
                        <i class="fas fa-history"></i>
                        <span>Historial</span>
                    </a>
                </li>
                <li class="nav-item">
                    <a href="?url=ayuda" class="nav-link <?php echo (isset($_GET['url']) && $_GET['url'] == 'ayuda') ? 'active' : ''; ?>">
                        <i class="fas fa-question-circle"></i>
                        <span>Ayuda</span>
                    </a>
                </li>
            </ul>
        </nav>
    </header>

    <main class="admin-main">
    
    <script>
        // Toggle panel de notificaciones
        document.getElementById('notificationBtn').addEventListener('click', function(e) {
            e.stopPropagation();
            const panel = document.getElementById('notificationsPanel');
            const dropdown = document.getElementById('dropdownMenu');
            
            panel.classList.toggle('show');
            dropdown.classList.remove('show');
        });

        // Toggle dropdown de usuario
        document.getElementById('userDropdown').addEventListener('click', function(e) {
            e.stopPropagation();
            const dropdown = document.getElementById('dropdownMenu');
            const panel = document.getElementById('notificationsPanel');
            
            dropdown.classList.toggle('show');
            panel.classList.remove('show');
        });

        // Cerrar paneles al hacer clic fuera
        document.addEventListener('click', function() {
            document.getElementById('dropdownMenu').classList.remove('show');
            document.getElementById('notificationsPanel').classList.remove('show');
        });
        
        // Prevenir que los clics dentro de los paneles los cierren
        document.getElementById('notificationsPanel').addEventListener('click', function(e) {
            e.stopPropagation();
        });
        
        document.getElementById('dropdownMenu').addEventListener('click', function(e) {
            e.stopPropagation();
        });

        // Actualizar contadores de notificaciones periódicamente
        function actualizarContadores() {
            // En un sistema real, aquí harías una petición AJAX para obtener los contadores actualizados
            console.log('Actualizando contadores...');
            // Ejemplo de actualización:
            // fetch('ruta/para/obtener_contadores.php')
            //   .then(response => response.json())
            //   .then(data => {
            //       // Actualizar badge de notificaciones
            //       document.querySelector('.notification-badge').textContent = data.nuevas_notificaciones;
            //   });
        }

        // Actualizar cada 30 segundos
        setInterval(actualizarContadores, 30000);
    </script>