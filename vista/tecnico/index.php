<?php
include 'header.php';

// Obtener datos del técnico
include_once __DIR__ . '/../../controlador/controladorTicket.php';
$controladorTicket = new controladorTicket();

$id_tecnico = $usuario['id'];

// Obtener estadísticas
$stats = $controladorTicket->modelo->obtenerEstadisticasTecnico($id_tecnico);

// Obtener tickets recientes
$tickets_recientes = array_slice($controladorTicket->modelo->obtenerTicketsTecnico($id_tecnico), 0, 5);

// Obtener notificaciones recientes
$notificaciones = $controladorNotif->modelo->obtenerNotificacionesUsuario($id_tecnico, 5);
?>

<style>
    .dashboard-grid {
        display: grid;
        grid-template-columns: repeat(auto-fit, minmax(250px, 1fr));
        gap: 25px;
        margin-bottom: 30px;
    }
    
    .stat-card {
        background: white;
        padding: 25px;
        border-radius: 15px;
        box-shadow: 0 5px 15px rgba(0,0,0,0.1);
        display: flex;
        align-items: center;
        gap: 20px;
        transition: transform 0.3s ease;
    }
    
    .stat-card:hover {
        transform: translateY(-5px);
        box-shadow: 0 8px 25px rgba(0,0,0,0.15);
    }
    
    .stat-icon {
        width: 70px;
        height: 70px;
        border-radius: 15px;
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 2rem;
        color: white;
    }
    
    .stat-icon.total { background: linear-gradient(135deg, #667eea, #764ba2); }
    .stat-icon.pendientes { background: linear-gradient(135deg, #f39c12, #e67e22); }
    .stat-icon.progreso { background: linear-gradient(135deg, #3498db, #2980b9); }
    .stat-icon.resueltos { background: linear-gradient(135deg, #27ae60, #229954); }
    .stat-icon.criticos { background: linear-gradient(135deg, #e74c3c, #c0392b); }
    
    .stat-info h3 {
        font-size: 2.5rem;
        color: #2c3e50;
        margin: 0;
        font-weight: bold;
    }
    
    .stat-info p {
        color: #7f8c8d;
        margin: 5px 0 0 0;
        font-size: 0.9rem;
    }
    
    .section-header {
        background: white;
        padding: 20px 25px;
        border-radius: 15px;
        margin-bottom: 20px;
        box-shadow: 0 2px 10px rgba(0,0,0,0.05);
    }
    
    .section-header h2 {
        color: #2c3e50;
        margin: 0;
        display: flex;
        align-items: center;
        gap: 10px;
    }
    
    .content-grid {
        display: grid;
        grid-template-columns: 2fr 1fr;
        gap: 25px;
        margin-top: 30px;
    }
    
    .tickets-section, .notif-section {
        background: white;
        padding: 25px;
        border-radius: 15px;
        box-shadow: 0 5px 15px rgba(0,0,0,0.1);
    }
    
    .section-title {
        font-size: 1.3rem;
        color: #2c3e50;
        margin-bottom: 20px;
        display: flex;
        align-items: center;
        justify-content: space-between;
        padding-bottom: 15px;
        border-bottom: 2px solid #ecf0f1;
    }
    
    .ticket-item {
        padding: 15px;
        background: #f8f9fa;
        border-radius: 10px;
        margin-bottom: 15px;
        border-left: 4px solid #3498db;
        transition: all 0.3s ease;
    }
    
    .ticket-item:hover {
        transform: translateX(5px);
        box-shadow: 0 3px 10px rgba(0,0,0,0.1);
    }
    
    .ticket-item.critico { border-left-color: #e74c3c; background: #fff5f5; }
    .ticket-item.alta { border-left-color: #f39c12; background: #fffbf5; }
    
    .ticket-header {
        display: flex;
        justify-content: space-between;
        align-items: start;
        margin-bottom: 10px;
    }
    
    .ticket-title {
        font-weight: 600;
        color: #2c3e50;
        font-size: 1rem;
    }
    
    .ticket-meta {
        display: flex;
        gap: 15px;
        font-size: 0.85rem;
        color: #7f8c8d;
        margin-top: 8px;
    }
    
    .badge {
        padding: 4px 10px;
        border-radius: 12px;
        font-size: 0.75rem;
        font-weight: 600;
    }
    
    .badge-critica { background: #e74c3c; color: white; }
    .badge-alta { background: #f39c12; color: white; }
    .badge-media { background: #3498db; color: white; }
    .badge-baja { background: #95a5a6; color: white; }
    
    .badge-pendiente { background: #f39c12; color: white; }
    .badge-en_progreso { background: #3498db; color: white; }
    .badge-resuelto { background: #27ae60; color: white; }
    
    .notif-item {
        padding: 15px;
        background: #f8f9fa;
        border-radius: 10px;
        margin-bottom: 12px;
        border-left: 3px solid #3498db;
        transition: all 0.3s ease;
    }
    
    .notif-item:hover {
        background: #e8f4f8;
    }
    
    .notif-item.no-leida {
        background: #fff3cd;
        border-left-color: #f39c12;
    }
    
    .notif-text {
        color: #2c3e50;
        font-size: 0.9rem;
        margin-bottom: 8px;
    }
    
    .notif-time {
        font-size: 0.75rem;
        color: #7f8c8d;
    }
    
    .empty-state {
        text-align: center;
        padding: 40px 20px;
        color: #95a5a6;
    }
    
    .empty-state i {
        font-size: 3rem;
        margin-bottom: 15px;
        opacity: 0.5;
    }
    
    .btn-view {
        padding: 8px 15px;
        background: #3498db;
        color: white;
        text-decoration: none;
        border-radius: 6px;
        font-size: 0.85rem;
        display: inline-flex;
        align-items: center;
        gap: 5px;
        transition: all 0.3s ease;
    }
    
    .btn-view:hover {
        background: #2980b9;
        transform: translateY(-2px);
    }
    
    @media (max-width: 768px) {
        .content-grid {
            grid-template-columns: 1fr;
        }
    }
</style>

<div class="container">
    <!-- Bienvenida -->
    <div class="section-header">
        <h2>
            <i class="fas fa-hand-wave"></i>
            Bienvenido, <?php echo htmlspecialchars($usuario['nombre']); ?>
        </h2>
    </div>
    
    <!-- Estadísticas -->
    <div class="dashboard-grid">
        <div class="stat-card">
            <div class="stat-icon total">
                <i class="fas fa-clipboard-list"></i>
            </div>
            <div class="stat-info">
                <h3><?php echo $stats['total'] ?? 0; ?></h3>
                <p>Total Tickets Asignados</p>
            </div>
        </div>
        
        <div class="stat-card">
            <div class="stat-icon pendientes">
                <i class="fas fa-clock"></i>
            </div>
            <div class="stat-info">
                <h3><?php echo $stats['pendientes'] ?? 0; ?></h3>
                <p>Pendientes</p>
            </div>
        </div>
        
        <div class="stat-card">
            <div class="stat-icon progreso">
                <i class="fas fa-spinner"></i>
            </div>
            <div class="stat-info">
                <h3><?php echo $stats['en_progreso'] ?? 0; ?></h3>
                <p>En Progreso</p>
            </div>
        </div>
        
        <div class="stat-card">
            <div class="stat-icon resueltos">
                <i class="fas fa-check-circle"></i>
            </div>
            <div class="stat-info">
                <h3><?php echo $stats['resueltos'] ?? 0; ?></h3>
                <p>Resueltos</p>
            </div>
        </div>
        
        <div class="stat-card">
            <div class="stat-icon criticos">
                <i class="fas fa-exclamation-triangle"></i>
            </div>
            <div class="stat-info">
                <h3><?php echo $stats['criticos'] ?? 0; ?></h3>
                <p>Críticos</p>
            </div>
        </div>
    </div>
    
    <!-- Contenido Principal -->
    <div class="content-grid">
        <!-- Tickets Recientes -->
        <div class="tickets-section">
            <div class="section-title">
                <span>
                    <i class="fas fa-ticket-alt"></i> Tickets Recientes
                </span>
                <a href="mis_tickets.php" class="btn-view">
                    Ver Todos <i class="fas fa-arrow-right"></i>
                </a>
            </div>
            
            <?php if(!empty($tickets_recientes)): ?>
                <?php foreach($tickets_recientes as $ticket): ?>
                <div class="ticket-item <?php echo $ticket['prioridad']; ?>">
                    <div class="ticket-header">
                        <div>
                            <div class="ticket-title">
                                #<?php echo $ticket['id_ticket']; ?> - <?php echo htmlspecialchars($ticket['titulo']); ?>
                            </div>
                            <div class="ticket-meta">
                                <span><i class="fas fa-user"></i> <?php echo htmlspecialchars($ticket['docente_nombre'] . ' ' . $ticket['docente_apellido']); ?></span>
                                <span><i class="fas fa-tag"></i> <?php echo htmlspecialchars($ticket['categoria_nombre']); ?></span>
                                <span><i class="fas fa-calendar"></i> <?php echo date('d/m/Y', strtotime($ticket['fecha_creacion'])); ?></span>
                            </div>
                        </div>
                        <div style="display: flex; flex-direction: column; align-items: flex-end; gap: 5px;">
                            <span class="badge badge-<?php echo $ticket['prioridad']; ?>">
                                <?php echo ucfirst($ticket['prioridad']); ?>
                            </span>
                            <span class="badge badge-<?php echo $ticket['estado']; ?>">
                                <?php echo ucfirst(str_replace('_', ' ', $ticket['estado'])); ?>
                            </span>
                        </div>
                    </div>
                    <a href="../admin/chat_ticket.php?id=<?php echo $ticket['id_ticket']; ?>" class="btn-view" style="margin-top: 10px;">
                        <i class="fas fa-eye"></i> Ver Detalles
                    </a>
                </div>
                <?php endforeach; ?>
            <?php else: ?>
                <div class="empty-state">
                    <i class="fas fa-inbox"></i>
                    <p>No tienes tickets asignados actualmente</p>
                </div>
            <?php endif; ?>
        </div>
        
        <!-- Notificaciones -->
        <div class="notif-section">
            <div class="section-title">
                <span>
                    <i class="fas fa-bell"></i> Notificaciones
                </span>
                <a href="notificaciones.php" class="btn-view">
                    Ver Todas <i class="fas fa-arrow-right"></i>
                </a>
            </div>
            
            <?php if(!empty($notificaciones)): ?>
                <?php foreach($notificaciones as $notif): ?>
                <div class="notif-item <?php echo !$notif['leido'] ? 'no-leida' : ''; ?>">
                    <div class="notif-text">
                        <?php echo htmlspecialchars($notif['mensaje']); ?>
                    </div>
                    <div class="notif-time">
                        <i class="fas fa-clock"></i>
                        <?php 
                        $fecha = strtotime($notif['fecha_envio']);
                        $diferencia = time() - $fecha;
                        if($diferencia < 3600) {
                            echo floor($diferencia / 60) . ' minutos';
                        } elseif($diferencia < 86400) {
                            echo floor($diferencia / 3600) . ' horas';
                        } else {
                            echo date('d/m/Y H:i', $fecha);
                        }
                        ?>
                    </div>
                </div>
                <?php endforeach; ?>
            <?php else: ?>
                <div class="empty-state">
                    <i class="fas fa-bell-slash"></i>
                    <p>No tienes notificaciones</p>
                </div>
            <?php endif; ?>
        </div>
    </div>
</div>

<!-- Bootstrap JS Bundle -->
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>

</body>
</html>
