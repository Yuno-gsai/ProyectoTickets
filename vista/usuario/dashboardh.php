<?php
// Dashboard del Usuario (Docente)
include_once __DIR__ . '/../../controlador/controladorTicket.php';
include_once __DIR__ . '/../../controlador/controladorMensaje.php';

$controladorTicket = new controladorTicket();
$controladorMensaje = new controladorMensaje();

// Obtener ID del usuario actual
$id_usuario = $_SESSION['usuario']['id'];
$nombre_usuario = $_SESSION['usuario']['nombre_completo'];

// Obtener estadísticas del docente
$estadisticas = $controladorTicket->obtenerEstadisticas($id_usuario, 'docente');

// Obtener tickets del docente
$mis_tickets = $controladorTicket->obtenerTicketsDocente($id_usuario);

// Últimos 5 tickets
$tickets_recientes = array_slice($mis_tickets, 0, 5);

// Mensajes recientes
$mensajes_recientes = $controladorMensaje->obtenerMensajesRecientesUsuario($id_usuario, 5);

// Funciones auxiliares
function getPrioridadClase($prioridad) {
    switch($prioridad) {
        case 'baja': return 'priority-low';
        case 'media': return 'priority-medium';
        case 'alta': return 'priority-high';
        case 'critica': return 'priority-critical';
        default: return 'priority-medium';
    }
}

function getEstadoClase($estado) {
    switch($estado) {
        case 'pendiente': return 'status-pending';
        case 'en_progreso': return 'status-progress';
        case 'resuelto': return 'status-resolved';
        case 'rechazado': return 'status-rejected';
        default: return 'status-pending';
    }
}

function formatearTexto($texto) {
    return ucfirst(str_replace('_', ' ', $texto));
}

function formatearFecha($fecha) {
    return date('d/m/Y H:i', strtotime($fecha));
}

function tiempoTranscurrido($fecha) {
    $ahora = new DateTime();
    $fecha_ticket = new DateTime($fecha);
    $diff = $ahora->diff($fecha_ticket);
    
    if ($diff->d > 0) {
        return $diff->d . ' día' . ($diff->d > 1 ? 's' : '');
    } elseif ($diff->h > 0) {
        return $diff->h . ' hora' . ($diff->h > 1 ? 's' : '');
    } else {
        return $diff->i . ' minuto' . ($diff->i > 1 ? 's' : '');
    }
}
?>

<link rel="stylesheet" href="../../assets/css/dashboard.css">

<style>
    .welcome-section {
        background: linear-gradient(135deg, #e74c3c, #c0392b);
        color: white;
        padding: 30px;
        border-radius: 15px;
        margin-bottom: 30px;
        box-shadow: 0 10px 30px rgba(231, 76, 60, 0.3);
    }
    
    .welcome-section h1 {
        margin: 0 0 10px 0;
        font-size: 2rem;
    }
    
    .welcome-section p {
        margin: 0;
        opacity: 0.9;
        font-size: 1.1rem;
    }
    
    .quick-action-btn {
        background: white;
        color: #e74c3c;
        padding: 12px 25px;
        border-radius: 8px;
        text-decoration: none;
        display: inline-flex;
        align-items: center;
        gap: 10px;
        font-weight: 600;
        margin-top: 15px;
        transition: all 0.3s ease;
        box-shadow: 0 4px 15px rgba(0,0,0,0.2);
    }
    
    .quick-action-btn:hover {
        transform: translateY(-2px);
        box-shadow: 0 6px 20px rgba(0,0,0,0.3);
    }
    
    .stats-summary {
        display: flex;
        justify-content: space-between;
        align-items: center;
        margin-top: 20px;
        padding-top: 20px;
        border-top: 1px solid rgba(255,255,255,0.3);
    }
    
    .stat-item {
        text-align: center;
    }
    
    .stat-item .number {
        font-size: 2rem;
        font-weight: bold;
        display: block;
    }
    
    .stat-item .label {
        font-size: 0.9rem;
        opacity: 0.9;
    }
    
    .activity-feed {
        background: white;
        border-radius: 15px;
        padding: 25px;
        box-shadow: 0 5px 20px rgba(0,0,0,0.1);
        margin-top: 30px;
    }
    
    .activity-item {
        display: flex;
        gap: 15px;
        padding: 15px;
        border-left: 3px solid #e74c3c;
        background: #f8f9fa;
        border-radius: 8px;
        margin-bottom: 15px;
        transition: all 0.3s ease;
    }
    
    .activity-item:hover {
        transform: translateX(5px);
        box-shadow: 0 3px 10px rgba(0,0,0,0.1);
    }
    
    .activity-icon {
        width: 40px;
        height: 40px;
        background: #e74c3c;
        color: white;
        border-radius: 50%;
        display: flex;
        align-items: center;
        justify-content: center;
        flex-shrink: 0;
    }
    
    .activity-content {
        flex: 1;
    }
    
    .activity-title {
        font-weight: 600;
        color: #2c3e50;
        margin-bottom: 5px;
    }
    
    .activity-meta {
        font-size: 0.85rem;
        color: #7f8c8d;
    }
    
    .chart-container {
        background: white;
        border-radius: 15px;
        padding: 25px;
        box-shadow: 0 5px 20px rgba(0,0,0,0.1);
        margin-bottom: 30px;
    }
    
    .progress-bar {
        background: #ecf0f1;
        border-radius: 10px;
        height: 30px;
        overflow: hidden;
        margin: 10px 0;
    }
    
    .progress-fill {
        height: 100%;
        display: flex;
        align-items: center;
        justify-content: center;
        color: white;
        font-weight: 600;
        font-size: 0.85rem;
        transition: width 0.5s ease;
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
    
    .empty-state p {
        font-size: 1.1rem;
        margin-bottom: 20px;
    }
    
    @media (max-width: 768px) {
        .stats-summary {
            flex-direction: column;
            gap: 15px;
        }
        
        .dashboard-cards {
            grid-template-columns: 1fr;
        }
    }
</style>

<!-- Sección de Bienvenida -->
<div class="welcome-section">
    <h1>¡Bienvenido, <?php echo htmlspecialchars($nombre_usuario); ?>! 👋</h1>
    <p>Panel de control de soporte técnico</p>
    
    <a href="?url=nuevo_ticket" class="quick-action-btn">
        <i class="fas fa-plus-circle"></i>
        Crear Nuevo Ticket
    </a>
    
    <div class="stats-summary">
        <div class="stat-item">
            <span class="number"><?php echo $estadisticas['total']; ?></span>
            <span class="label">Total de Tickets</span>
        </div>
        <div class="stat-item">
            <span class="number"><?php echo $estadisticas['pendientes']; ?></span>
            <span class="label">Pendientes</span>
        </div>
        <div class="stat-item">
            <span class="number"><?php echo $estadisticas['en_progreso']; ?></span>
            <span class="label">En Progreso</span>
        </div>
        <div class="stat-item">
            <span class="number"><?php echo $estadisticas['resueltos']; ?></span>
            <span class="label">Resueltos</span>
        </div>
    </div>
</div>

<!-- Tarjetas de Estadísticas -->
<div class="dashboard-cards">
    <div class="stat-card card-tickets">
        <div class="stat-icon">
            <i class="fas fa-ticket-alt"></i>
        </div>
        <div class="stat-info">
            <h3><?php echo $estadisticas['total']; ?></h3>
            <p>Mis Tickets</p>
        </div>
    </div>
    
    <div class="stat-card card-pending">
        <div class="stat-icon">
            <i class="fas fa-clock"></i>
        </div>
        <div class="stat-info">
            <h3><?php echo $estadisticas['pendientes']; ?></h3>
            <p>Pendientes</p>
        </div>
    </div>
    
    <div class="stat-card card-progress">
        <div class="stat-icon">
            <i class="fas fa-spinner"></i>
        </div>
        <div class="stat-info">
            <h3><?php echo $estadisticas['en_progreso']; ?></h3>
            <p>En Progreso</p>
        </div>
    </div>
    
    <div class="stat-card card-resolved">
        <div class="stat-icon">
            <i class="fas fa-check-circle"></i>
        </div>
        <div class="stat-info">
            <h3><?php echo $estadisticas['resueltos']; ?></h3>
            <p>Resueltos</p>
        </div>
    </div>
</div>

<!-- Gráfico de Progreso -->
<?php if($estadisticas['total'] > 0): ?>
<div class="chart-container">
    <h2 class="section-title">
        <i class="fas fa-chart-pie"></i>
        Estado de Mis Tickets
    </h2>
    
    <?php 
    $porcentajes = [
        'pendientes' => ($estadisticas['pendientes'] / $estadisticas['total']) * 100,
        'en_progreso' => ($estadisticas['en_progreso'] / $estadisticas['total']) * 100,
        'resueltos' => ($estadisticas['resueltos'] / $estadisticas['total']) * 100,
        'rechazados' => ($estadisticas['rechazados'] / $estadisticas['total']) * 100
    ];
    ?>
    
    <div style="margin: 20px 0;">
        <div style="margin-bottom: 20px;">
            <div style="display: flex; justify-content: space-between; margin-bottom: 5px;">
                <span><strong>Pendientes:</strong> <?php echo $estadisticas['pendientes']; ?></span>
                <span><?php echo round($porcentajes['pendientes'], 1); ?>%</span>
            </div>
            <div class="progress-bar">
                <div class="progress-fill" style="width: <?php echo $porcentajes['pendientes']; ?>%; background: #f39c12;">
                    <?php if($porcentajes['pendientes'] > 10): ?>
                        <?php echo round($porcentajes['pendientes']); ?>%
                    <?php endif; ?>
                </div>
            </div>
        </div>
        
        <div style="margin-bottom: 20px;">
            <div style="display: flex; justify-content: space-between; margin-bottom: 5px;">
                <span><strong>En Progreso:</strong> <?php echo $estadisticas['en_progreso']; ?></span>
                <span><?php echo round($porcentajes['en_progreso'], 1); ?>%</span>
            </div>
            <div class="progress-bar">
                <div class="progress-fill" style="width: <?php echo $porcentajes['en_progreso']; ?>%; background: #3498db;">
                    <?php if($porcentajes['en_progreso'] > 10): ?>
                        <?php echo round($porcentajes['en_progreso']); ?>%
                    <?php endif; ?>
                </div>
            </div>
        </div>
        
        <div style="margin-bottom: 20px;">
            <div style="display: flex; justify-content: space-between; margin-bottom: 5px;">
                <span><strong>Resueltos:</strong> <?php echo $estadisticas['resueltos']; ?></span>
                <span><?php echo round($porcentajes['resueltos'], 1); ?>%</span>
            </div>
            <div class="progress-bar">
                <div class="progress-fill" style="width: <?php echo $porcentajes['resueltos']; ?>%; background: #27ae60;">
                    <?php if($porcentajes['resueltos'] > 10): ?>
                        <?php echo round($porcentajes['resueltos']); ?>%
                    <?php endif; ?>
                </div>
            </div>
        </div>
        
        <?php if($estadisticas['rechazados'] > 0): ?>
        <div style="margin-bottom: 20px;">
            <div style="display: flex; justify-content: space-between; margin-bottom: 5px;">
                <span><strong>Rechazados:</strong> <?php echo $estadisticas['rechazados']; ?></span>
                <span><?php echo round($porcentajes['rechazados'], 1); ?>%</span>
            </div>
            <div class="progress-bar">
                <div class="progress-fill" style="width: <?php echo $porcentajes['rechazados']; ?>%; background: #e74c3c;">
                    <?php if($porcentajes['rechazados'] > 10): ?>
                        <?php echo round($porcentajes['rechazados']); ?>%
                    <?php endif; ?>
                </div>
            </div>
        </div>
        <?php endif; ?>
    </div>
</div>
<?php endif; ?>

<!-- Tickets Recientes -->
<h2 class="section-title">
    <i class="fas fa-list"></i>
    Mis Últimos Tickets
</h2>

<div class="recent-tickets">
    <?php if (!empty($tickets_recientes)): ?>
        <div class="table-responsive">
            <table class="tickets-table">
                <thead>
                    <tr>
                        <th>ID</th>
                        <th>Título</th>
                        <th>Categoría</th>
                        <th>Prioridad</th>
                        <th>Estado</th>
                        <th>Creado</th>
                        <th>Acciones</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($tickets_recientes as $ticket): ?>
                        <tr>
                            <td><strong>#<?php echo $ticket['id_ticket']; ?></strong></td>
                            <td><?php echo htmlspecialchars($ticket['titulo']); ?></td>
                            <td>
                                <span class="category-badge">
                                    <?php echo htmlspecialchars($ticket['categoria_nombre'] ?? 'Sin categoría'); ?>
                                </span>
                            </td>
                            <td>
                                <span class="priority-badge <?php echo getPrioridadClase($ticket['prioridad']); ?>">
                                    <?php echo formatearTexto($ticket['prioridad']); ?>
                                </span>
                            </td>
                            <td>
                                <span class="status-badge <?php echo getEstadoClase($ticket['estado']); ?>">
                                    <?php echo formatearTexto($ticket['estado']); ?>
                                </span>
                            </td>
                            <td>
                                <small><?php echo formatearFecha($ticket['fecha_creacion']); ?></small>
                                <br>
                                <small style="color: #7f8c8d;">Hace <?php echo tiempoTranscurrido($ticket['fecha_creacion']); ?></small>
                            </td>
                            <td>
                                <a href="?url=chat_ticket&id=<?php echo $ticket['id_ticket']; ?>" class="action-btn" title="Ver chat">
                                    <i class="fas fa-comments"></i>
                                </a>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
        
        <?php if(count($mis_tickets) > 5): ?>
            <div style="text-align: center; margin-top: 20px;">
                <a href="?url=mis_tickets" class="quick-action-btn" style="color: #e74c3c;">
                    <i class="fas fa-list"></i>
                    Ver Todos Mis Tickets (<?php echo count($mis_tickets); ?>)
                </a>
            </div>
        <?php endif; ?>
    <?php else: ?>
        <div class="empty-state">
            <i class="fas fa-inbox"></i>
            <p>No tienes tickets registrados</p>
            <a href="?url=nuevo_ticket" class="quick-action-btn" style="color: #e74c3c;">
                <i class="fas fa-plus-circle"></i>
                Crear Mi Primer Ticket
            </a>
        </div>
    <?php endif; ?>
</div>

<!-- Actividad Reciente -->
<?php if (!empty($mensajes_recientes)): ?>
<div class="activity-feed">
    <h2 class="section-title">
        <i class="fas fa-history"></i>
        Actividad Reciente
    </h2>
    
    <?php foreach($mensajes_recientes as $msg): ?>
        <div class="activity-item">
            <div class="activity-icon">
                <i class="fas fa-<?php echo $msg['rol'] == 'docente' ? 'user' : 'user-shield'; ?>"></i>
            </div>
            <div class="activity-content">
                <div class="activity-title">
                    <?php echo htmlspecialchars($msg['nombre'] . ' ' . $msg['apellido']); ?>
                    <span style="color: #7f8c8d; font-weight: normal;">
                        en 
                        <a href="?url=chat_ticket&id=<?php echo $msg['id_ticket']; ?>" style="color: #e74c3c;">
                            <?php echo htmlspecialchars($msg['ticket_titulo']); ?>
                        </a>
                    </span>
                </div>
                <div class="activity-meta">
                    <?php echo date('d/m/Y H:i', strtotime($msg['fecha_envio'])); ?> • 
                    Hace <?php echo tiempoTranscurrido($msg['fecha_envio']); ?>
                </div>
                <div style="margin-top: 8px; color: #555;">
                    <?php 
                    $mensaje_preview = htmlspecialchars($msg['mensaje']);
                    echo strlen($mensaje_preview) > 100 ? substr($mensaje_preview, 0, 100) . '...' : $mensaje_preview;
                    ?>
                </div>
            </div>
        </div>
    <?php endforeach; ?>
</div>
<?php endif; ?>

<script>
// Animación de las barras de progreso al cargar
document.addEventListener('DOMContentLoaded', function() {
    const progressBars = document.querySelectorAll('.progress-fill');
    progressBars.forEach((bar, index) => {
        const width = bar.style.width;
        bar.style.width = '0%';
        setTimeout(() => {
            bar.style.width = width;
        }, 100 * index);
    });
});
</script>
