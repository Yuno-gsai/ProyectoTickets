<?php
include_once __DIR__ . '/../../controlador/controladorTicket.php';
include_once __DIR__ . '/../../controlador/controladorResumen.php';

// Crear instancia de los controladores
$controlador = new controladorTicket();
$controladorResumen = new controladorResumen();

// Obtener estadísticas
$estadisticas = $controlador->getEstadisticas();

// Obtener tickets recientes
$tickets_recientes = $controlador->listarTicketsRecientes(10);

// Obtener alertas críticas y tickets antiguos
$alertas = $controladorResumen->obtenerAlertasCriticas();
$tickets_antiguos_count = count($controladorResumen->obtenerTicketsAntiguos(7));

// Funciones auxiliares para las clases CSS
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
    return date('d/m/Y', strtotime($fecha));
}
?>

<link rel="stylesheet" href="../../assets/css/dashboard.css">

<style>
    .alert-banner {
        background: linear-gradient(135deg, #ff6b6b 0%, #ee5a6f 100%);
        color: white;
        padding: 15px 20px;
        border-radius: 10px;
        margin-bottom: 20px;
        display: flex;
        align-items: center;
        gap: 15px;
        box-shadow: 0 4px 6px rgba(0,0,0,0.1);
        animation: pulse 2s infinite;
    }
    
    @keyframes pulse {
        0%, 100% { transform: scale(1); }
        50% { transform: scale(1.02); }
    }
    
    .alert-banner-icon {
        font-size: 2rem;
    }
    
    .alert-banner-content {
        flex: 1;
    }
    
    .alert-banner-title {
        font-size: 1.1rem;
        font-weight: 600;
        margin-bottom: 5px;
    }
    
    .alert-banner-text {
        font-size: 0.9rem;
        opacity: 0.9;
    }
    
    .alert-banner-btn {
        background: white;
        color: #ff6b6b;
        padding: 10px 20px;
        border-radius: 5px;
        text-decoration: none;
        font-weight: 600;
        transition: transform 0.3s;
        white-space: nowrap;
    }
    
    .alert-banner-btn:hover {
        transform: scale(1.05);
    }
    
    .mini-alerts {
        display: grid;
        grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
        gap: 15px;
        margin-bottom: 20px;
    }
    
    .mini-alert {
        background: white;
        padding: 15px;
        border-radius: 8px;
        border-left: 4px solid;
        display: flex;
        align-items: center;
        gap: 10px;
        box-shadow: 0 2px 4px rgba(0,0,0,0.1);
    }
    
    .mini-alert-icon {
        font-size: 1.5rem;
    }
    
    .mini-alert-text {
        flex: 1;
        font-size: 0.85rem;
        line-height: 1.3;
    }
    
    .mini-alert-number {
        font-size: 1.5rem;
        font-weight: 700;
    }
</style>

<!-- ALERTAS IMPORTANTES -->
<?php if($tickets_antiguos_count > 0): ?>
<div class="alert-banner">
    <div class="alert-banner-icon">
        <i class="fas fa-exclamation-triangle"></i>
    </div>
    <div class="alert-banner-content">
        <div class="alert-banner-title">⚠️ Atención Requerida</div>
        <div class="alert-banner-text">
            Hay <strong><?php echo $tickets_antiguos_count; ?> ticket(s)</strong> con más de 7 días sin resolver
        </div>
    </div>
    <a href="?url=resumenes" class="alert-banner-btn">
        Ver Detalles <i class="fas fa-arrow-right"></i>
    </a>
</div>
<?php endif; ?>

<!-- MINI ALERTAS -->
<?php if(!empty($alertas)): ?>
<div class="mini-alerts">
    <?php foreach(array_slice($alertas, 0, 3) as $alerta): ?>
    <div class="mini-alert" style="border-left-color: <?php echo $alerta['color']; ?>;">
        <div class="mini-alert-icon" style="color: <?php echo $alerta['color']; ?>;">
            <i class="fas <?php echo $alerta['icono']; ?>"></i>
        </div>
        <div class="mini-alert-text">
            <strong><?php echo $alerta['titulo']; ?></strong><br>
            <?php echo $alerta['mensaje']; ?>
        </div>
        <div class="mini-alert-number" style="color: <?php echo $alerta['color']; ?>;">
            <?php echo $alerta['cantidad']; ?>
        </div>
    </div>
    <?php endforeach; ?>
</div>
<?php endif; ?>

<div class="dashboard-cards">
    <div class="stat-card card-tickets">
        <div class="stat-icon">
            <i class="fas fa-ticket-alt"></i>
        </div>
        <div class="stat-info">
            <h3><?php echo $estadisticas['total']; ?></h3>
            <p>Tickets Totales</p>
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
    <div class="stat-card card-resolved">
        <div class="stat-icon">
            <i class="fas fa-check-circle"></i>
        </div>
        <div class="stat-info">
            <h3><?php echo $estadisticas['resueltos']; ?></h3>
            <p>Resueltos</p>
        </div>
    </div>
    <div class="stat-card card-critical">
        <div class="stat-icon">
            <i class="fas fa-exclamation-triangle"></i>
        </div>
        <div class="stat-info">
            <h3><?php echo $estadisticas['criticos']; ?></h3>
            <p>Críticos</p>
        </div>
    </div>
</div>

<h2 class="section-title">
    <i class="fas fa-list"></i>
    Tickets Recientes
</h2>

<div class="recent-tickets">
    <div class="table-responsive">
        <table class="tickets-table">
            <thead>
                <tr>
                    <th>ID</th>
                    <th>Título</th>
                    <th>Usuario</th>
                    <th>Prioridad</th>
                    <th>Estado</th>
                    <th>Fecha</th>
                    <th>Acciones</th>
                </tr>
            </thead>
            <tbody>
                <?php if (!empty($tickets_recientes)): ?>
                    <?php foreach ($tickets_recientes as $ticket): ?>
                        <tr>
                            <td>#<?php echo $ticket['id_ticket']; ?></td>
                            <td><?php echo htmlspecialchars($ticket['titulo']); ?></td>
                            <td><?php echo htmlspecialchars($ticket['nombre_usuario']); ?></td>
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
                            <td><?php echo formatearFecha($ticket['fecha_creacion']); ?></td>
                            <td>
                                <button class="action-btn" title="Ver detalles" onclick="verTicket(<?php echo $ticket['id_ticket']; ?>)">
                                    <i class="fas fa-eye"></i>
                                </button>
                                <button class="action-btn" title="Asignar" onclick="asignarTicket(<?php echo $ticket['id_ticket']; ?>)">
                                    <i class="fas fa-user-plus"></i>
                                </button>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                <?php else: ?>
                    <tr>
                        <td colspan="7" style="text-align: center; padding: 30px; color: #999;">
                            <i class="fas fa-inbox" style="font-size: 3rem; display: block; margin-bottom: 10px;"></i>
                            No hay tickets registrados
                        </td>
                    </tr>
                <?php endif; ?>
            </tbody>
        </table>
    </div>
</div>

<script>
function verTicket(id) {
    // Redirigir a la página de detalles del ticket
    window.location.href = 'detalles_ticket.php?id=' + id;
}

function asignarTicket(id) {
    // Redirigir a la página de asignación o abrir modal
    window.location.href = 'asignar_ticket.php?id=' + id;
}
</script>
