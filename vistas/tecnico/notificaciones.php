<?php
// IMPORTANTE: Procesar acciones ANTES de incluir header para evitar "headers already sent"
session_start();
if(!isset($_SESSION['usuario']) || $_SESSION['usuario']['rol'] !== 'tecnico'){
    header("Location: /proyectophp/index.php");
    exit();
}

$usuario = $_SESSION['usuario'];
$id_tecnico = $usuario['id'];

// Inicializar controlador
include_once __DIR__ . '/../../controlador/controladorNotificacion.php';
$controladorNotif = new controladorNotificacion();

// PROCESAR ACCIONES PRIMERO (antes de cualquier salida HTML)
if(isset($_GET['accion']) && isset($_GET['id'])){
    $accion = $_GET['accion'];
    $id_notif = intval($_GET['id']);
    
    if($accion === 'marcar_leida'){
        $controladorNotif->modelo->marcarComoLeida($id_notif);
        header("Location: notificaciones.php");
        exit();
    } elseif($accion === 'eliminar'){
        $controladorNotif->modelo->eliminarNotificacion($id_notif);
        header("Location: notificaciones.php");
        exit();
    }
}

if(isset($_GET['accion']) && $_GET['accion'] === 'marcar_todas'){
    $controladorNotif->modelo->marcarTodasComoLeidas($id_tecnico);
    header("Location: notificaciones.php");
    exit();
}

// Obtener notificaciones DESPUÉS de procesar acciones
$todas_notificaciones = $controladorNotif->modelo->obtenerNotificacionesUsuario($id_tecnico, 50);

// Separar por leídas y no leídas
$notif_no_leidas = array_filter($todas_notificaciones, fn($n) => !$n['leido']);
$notif_leidas = array_filter($todas_notificaciones, fn($n) => $n['leido']);

// AHORA SÍ incluir el header (después de todas las redirecciones)
include 'header.php';
?>

<style>
    .page-header {
        background: white;
        padding: 25px;
        border-radius: 15px;
        margin-bottom: 30px;
        box-shadow: 0 2px 10px rgba(0,0,0,0.05);
        display: flex;
        justify-content: space-between;
        align-items: center;
    }
    
    .page-header h1 {
        color: #2c3e50;
        margin: 0;
        display: flex;
        align-items: center;
        gap: 15px;
    }
    
    .btn {
        padding: 10px 20px;
        border: none;
        border-radius: 8px;
        font-weight: 600;
        cursor: pointer;
        transition: all 0.3s ease;
        text-decoration: none;
        display: inline-flex;
        align-items: center;
        gap: 8px;
    }
    
    .btn-primary {
        background: linear-gradient(135deg, #3498db, #2980b9);
        color: white;
    }
    
    .btn-primary:hover {
        transform: translateY(-2px);
        box-shadow: 0 4px 15px rgba(52, 152, 219, 0.4);
    }
    
    .notif-section {
        background: white;
        padding: 25px;
        border-radius: 15px;
        box-shadow: 0 2px 10px rgba(0,0,0,0.05);
        margin-bottom: 30px;
    }
    
    .notif-section h2 {
        color: #2c3e50;
        margin: 0 0 20px 0;
        padding-bottom: 15px;
        border-bottom: 2px solid #ecf0f1;
        display: flex;
        align-items: center;
        gap: 10px;
    }
    
    .notif-count {
        background: #e74c3c;
        color: white;
        padding: 3px 10px;
        border-radius: 15px;
        font-size: 0.9rem;
    }
    
    .notif-list {
        display: flex;
        flex-direction: column;
        gap: 15px;
    }
    
    .notif-item {
        background: #f8f9fa;
        padding: 20px;
        border-radius: 12px;
        border-left: 4px solid #3498db;
        display: flex;
        justify-content: space-between;
        align-items: start;
        gap: 15px;
        transition: all 0.3s ease;
    }
    
    .notif-item:hover {
        transform: translateX(5px);
        box-shadow: 0 3px 10px rgba(0,0,0,0.1);
    }
    
    .notif-item.no-leida {
        background: linear-gradient(90deg, #fff3cd 0%, #fffbf5 100%);
        border-left-color: #f39c12;
    }
    
    .notif-content {
        flex: 1;
    }
    
    .notif-icon {
        width: 50px;
        height: 50px;
        border-radius: 50%;
        background: linear-gradient(135deg, #f39c12, #e67e22);
        display: flex;
        align-items: center;
        justify-content: center;
        color: white;
        font-size: 1.3rem;
        flex-shrink: 0;
    }
    
    .notif-message {
        color: #2c3e50;
        font-size: 1rem;
        line-height: 1.6;
        margin-bottom: 10px;
    }
    
    .notif-meta {
        display: flex;
        gap: 20px;
        font-size: 0.85rem;
        color: #7f8c8d;
    }
    
    .notif-meta span {
        display: flex;
        align-items: center;
        gap: 5px;
    }
    
    .notif-actions {
        display: flex;
        flex-direction: column;
        gap: 8px;
    }
    
    .btn-icon {
        padding: 8px 12px;
        background: #ecf0f1;
        color: #2c3e50;
        border: none;
        border-radius: 6px;
        cursor: pointer;
        transition: all 0.3s ease;
        display: flex;
        align-items: center;
        gap: 5px;
        font-size: 0.85rem;
        text-decoration: none;
    }
    
    .btn-icon:hover {
        background: #3498db;
        color: white;
    }
    
    .btn-icon.delete:hover {
        background: #e74c3c;
        color: white;
    }
    
    .ticket-link {
        background: #3498db;
        color: white;
        padding: 8px 15px;
        border-radius: 6px;
        text-decoration: none;
        display: inline-flex;
        align-items: center;
        gap: 5px;
        font-size: 0.85rem;
        margin-top: 10px;
        transition: all 0.3s ease;
    }
    
    .ticket-link:hover {
        background: #2980b9;
        transform: translateY(-2px);
    }
    
    .empty-state {
        text-align: center;
        padding: 60px 20px;
        color: #95a5a6;
    }
    
    .empty-state i {
        font-size: 4rem;
        margin-bottom: 20px;
        opacity: 0.5;
    }
</style>

<div class="container">
    <!-- Encabezado -->
    <div class="page-header">
        <h1>
            <i class="fas fa-bell"></i>
            Mis Notificaciones
        </h1>
        <?php if(count($notif_no_leidas) > 0): ?>
            <a href="?accion=marcar_todas" class="btn btn-primary">
                <i class="fas fa-check-double"></i>
                Marcar todas como leídas
            </a>
        <?php endif; ?>
    </div>
    
    <!-- Notificaciones No Leídas -->
    <?php if(!empty($notif_no_leidas)): ?>
    <div class="notif-section">
        <h2>
            <i class="fas fa-envelope"></i>
            No Leídas
            <span class="notif-count"><?php echo count($notif_no_leidas); ?></span>
        </h2>
        <div class="notif-list">
            <?php foreach($notif_no_leidas as $notif): ?>
            <div class="notif-item no-leida">
                <div class="notif-icon">
                    <i class="fas fa-bell"></i>
                </div>
                <div class="notif-content">
                    <div class="notif-message">
                        <?php echo htmlspecialchars($notif['mensaje']); ?>
                    </div>
                    <div class="notif-meta">
                        <span>
                            <i class="fas fa-clock"></i>
                            <?php 
                            $fecha = strtotime($notif['fecha_envio']);
                            $diferencia = time() - $fecha;
                            if($diferencia < 3600) {
                                echo 'Hace ' . floor($diferencia / 60) . ' minutos';
                            } elseif($diferencia < 86400) {
                                echo 'Hace ' . floor($diferencia / 3600) . ' horas';
                            } else {
                                echo date('d/m/Y H:i', $fecha);
                            }
                            ?>
                        </span>
                        <?php if($notif['id_ticket']): ?>
                        <span>
                            <i class="fas fa-ticket-alt"></i>
                            Ticket #<?php echo $notif['id_ticket']; ?>
                        </span>
                        <?php endif; ?>
                    </div>
                    <?php if($notif['id_ticket']): ?>
                    <a href="../admin/chat_ticket.php?id=<?php echo $notif['id_ticket']; ?>" class="ticket-link">
                        <i class="fas fa-eye"></i> Ver Ticket
                    </a>
                    <?php endif; ?>
                </div>
                <div class="notif-actions">
                    <a href="?accion=marcar_leida&id=<?php echo $notif['id_notificacion']; ?>" class="btn-icon">
                        <i class="fas fa-check"></i> Marcar leída
                    </a>
                    <a href="?accion=eliminar&id=<?php echo $notif['id_notificacion']; ?>" class="btn-icon delete" onclick="return confirm('¿Eliminar esta notificación?')">
                        <i class="fas fa-trash"></i> Eliminar
                    </a>
                </div>
            </div>
            <?php endforeach; ?>
        </div>
    </div>
    <?php endif; ?>
    
    <!-- Notificaciones Leídas -->
    <?php if(!empty($notif_leidas)): ?>
    <div class="notif-section">
        <h2>
            <i class="fas fa-envelope-open"></i>
            Leídas
        </h2>
        <div class="notif-list">
            <?php foreach($notif_leidas as $notif): ?>
            <div class="notif-item">
                <div class="notif-icon" style="opacity: 0.6;">
                    <i class="fas fa-check"></i>
                </div>
                <div class="notif-content">
                    <div class="notif-message">
                        <?php echo htmlspecialchars($notif['mensaje']); ?>
                    </div>
                    <div class="notif-meta">
                        <span>
                            <i class="fas fa-clock"></i>
                            <?php echo date('d/m/Y H:i', strtotime($notif['fecha_envio'])); ?>
                        </span>
                        <?php if($notif['id_ticket']): ?>
                        <span>
                            <i class="fas fa-ticket-alt"></i>
                            Ticket #<?php echo $notif['id_ticket']; ?>
                        </span>
                        <?php endif; ?>
                    </div>
                    <?php if($notif['id_ticket']): ?>
                    <a href="../admin/chat_ticket.php?id=<?php echo $notif['id_ticket']; ?>" class="ticket-link">
                        <i class="fas fa-eye"></i> Ver Ticket
                    </a>
                    <?php endif; ?>
                </div>
                <div class="notif-actions">
                    <a href="?accion=eliminar&id=<?php echo $notif['id_notificacion']; ?>" class="btn-icon delete" onclick="return confirm('¿Eliminar esta notificación?')">
                        <i class="fas fa-trash"></i> Eliminar
                    </a>
                </div>
            </div>
            <?php endforeach; ?>
        </div>
    </div>
    <?php endif; ?>
    
    <!-- Estado vacío -->
    <?php if(empty($todas_notificaciones)): ?>
    <div class="empty-state">
        <i class="fas fa-bell-slash"></i>
        <h3>No tienes notificaciones</h3>
        <p>Cuando te asignen tickets o haya actualizaciones, aparecerán aquí</p>
    </div>
    <?php endif; ?>
</div>

<!-- Bootstrap JS Bundle -->
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>

</body>
</html>
