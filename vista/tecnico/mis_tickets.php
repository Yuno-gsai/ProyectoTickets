<?php
include 'header.php';

// Obtener tickets del técnico
include_once __DIR__ . '/../../controlador/controladorTicket.php';
$controladorTicket = new controladorTicket();

$id_tecnico = $usuario['id'];
$todos_tickets = $controladorTicket->modelo->obtenerTicketsTecnico($id_tecnico);

// Filtros
$filtro_estado = isset($_GET['estado']) ? $_GET['estado'] : 'todos';
$filtro_prioridad = isset($_GET['prioridad']) ? $_GET['prioridad'] : 'todos';

// Aplicar filtros
$tickets_filtrados = $todos_tickets;

if($filtro_estado !== 'todos') {
    $tickets_filtrados = array_filter($tickets_filtrados, function($ticket) use ($filtro_estado) {
        return $ticket['estado'] === $filtro_estado;
    });
}

if($filtro_prioridad !== 'todos') {
    $tickets_filtrados = array_filter($tickets_filtrados, function($ticket) use ($filtro_prioridad) {
        return $ticket['prioridad'] === $filtro_prioridad;
    });
}

// Estadísticas de filtros
$stats_filtro = [
    'total' => count($todos_tickets),
    'pendientes' => count(array_filter($todos_tickets, fn($t) => $t['estado'] === 'pendiente')),
    'en_progreso' => count(array_filter($todos_tickets, fn($t) => $t['estado'] === 'en_progreso')),
    'resueltos' => count(array_filter($todos_tickets, fn($t) => $t['estado'] === 'resuelto')),
];
?>

<style>
    .page-header {
        background: white;
        padding: 25px;
        border-radius: 15px;
        margin-bottom: 30px;
        box-shadow: 0 2px 10px rgba(0,0,0,0.05);
    }
    
    .page-header h1 {
        color: #2c3e50;
        margin: 0 0 10px 0;
        display: flex;
        align-items: center;
        gap: 15px;
    }
    
    .stats-mini {
        display: grid;
        grid-template-columns: repeat(auto-fit, minmax(150px, 1fr));
        gap: 15px;
        margin-bottom: 30px;
    }
    
    .stat-mini {
        background: white;
        padding: 20px;
        border-radius: 10px;
        text-align: center;
        box-shadow: 0 2px 10px rgba(0,0,0,0.05);
    }
    
    .stat-mini .number {
        font-size: 2rem;
        font-weight: bold;
        color: #f39c12;
    }
    
    .stat-mini .label {
        color: #7f8c8d;
        font-size: 0.9rem;
        margin-top: 5px;
    }
    
    .filters-section {
        background: white;
        padding: 20px 25px;
        border-radius: 15px;
        margin-bottom: 30px;
        box-shadow: 0 2px 10px rgba(0,0,0,0.05);
    }
    
    .filters-grid {
        display: grid;
        grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
        gap: 15px;
    }
    
    .filter-group {
        display: flex;
        flex-direction: column;
        gap: 8px;
    }
    
    .filter-group label {
        font-weight: 600;
        color: #2c3e50;
        font-size: 0.9rem;
    }
    
    .filter-select {
        padding: 10px;
        border: 2px solid #ecf0f1;
        border-radius: 8px;
        font-size: 1rem;
        cursor: pointer;
    }
    
    .tickets-grid {
        display: grid;
        gap: 20px;
    }
    
    .ticket-card {
        background: white;
        padding: 25px;
        border-radius: 15px;
        box-shadow: 0 2px 10px rgba(0,0,0,0.05);
        border-left: 5px solid #3498db;
        transition: all 0.3s ease;
    }
    
    .ticket-card:hover {
        transform: translateY(-3px);
        box-shadow: 0 5px 20px rgba(0,0,0,0.1);
    }
    
    .ticket-card.critica { border-left-color: #e74c3c; }
    .ticket-card.alta { border-left-color: #f39c12; }
    .ticket-card.media { border-left-color: #3498db; }
    .ticket-card.baja { border-left-color: #95a5a6; }
    
    .ticket-header {
        display: flex;
        justify-content: space-between;
        align-items: start;
        margin-bottom: 15px;
        padding-bottom: 15px;
        border-bottom: 2px solid #ecf0f1;
    }
    
    .ticket-title-section h3 {
        color: #2c3e50;
        margin: 0 0 10px 0;
        font-size: 1.2rem;
    }
    
    .ticket-id {
        color: #7f8c8d;
        font-size: 0.9rem;
    }
    
    .ticket-badges {
        display: flex;
        flex-direction: column;
        gap: 8px;
        align-items: flex-end;
    }
    
    .badge {
        padding: 6px 14px;
        border-radius: 15px;
        font-size: 0.85rem;
        font-weight: 600;
    }
    
    .badge-critica { background: #e74c3c; color: white; }
    .badge-alta { background: #f39c12; color: white; }
    .badge-media { background: #3498db; color: white; }
    .badge-baja { background: #95a5a6; color: white; }
    
    .badge-pendiente { background: #fff3cd; color: #856404; }
    .badge-en_progreso { background: #d1ecf1; color: #0c5460; }
    .badge-resuelto { background: #d4edda; color: #155724; }
    
    .ticket-info {
        display: grid;
        grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
        gap: 15px;
        margin-bottom: 15px;
    }
    
    .info-item {
        display: flex;
        align-items: center;
        gap: 10px;
        color: #7f8c8d;
        font-size: 0.9rem;
    }
    
    .info-item i {
        color: #f39c12;
        width: 20px;
    }
    
    .ticket-description {
        color: #2c3e50;
        line-height: 1.6;
        margin-bottom: 15px;
        padding: 15px;
        background: #f8f9fa;
        border-radius: 8px;
    }
    
    .ticket-actions {
        display: flex;
        gap: 10px;
        margin-top: 15px;
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
    
    .btn-success {
        background: #27ae60;
        color: white;
    }
    
    .btn-success:hover {
        background: #229954;
    }
    
    .empty-state {
        background: white;
        padding: 60px 20px;
        border-radius: 15px;
        text-align: center;
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
            <i class="fas fa-clipboard-list"></i>
            Mis Tickets Asignados
        </h1>
        <p>Gestiona y resuelve los tickets que te han sido asignados</p>
    </div>
    
    <!-- Estadísticas Rápidas -->
    <div class="stats-mini">
        <div class="stat-mini">
            <div class="number"><?php echo $stats_filtro['total']; ?></div>
            <div class="label">Total Asignados</div>
        </div>
        <div class="stat-mini">
            <div class="number"><?php echo $stats_filtro['pendientes']; ?></div>
            <div class="label">Pendientes</div>
        </div>
        <div class="stat-mini">
            <div class="number"><?php echo $stats_filtro['en_progreso']; ?></div>
            <div class="label">En Progreso</div>
        </div>
        <div class="stat-mini">
            <div class="number"><?php echo $stats_filtro['resueltos']; ?></div>
            <div class="label">Resueltos</div>
        </div>
    </div>
    
    <!-- Filtros -->
    <div class="filters-section">
        <form method="GET" action="">
            <div class="filters-grid">
                <div class="filter-group">
                    <label><i class="fas fa-tasks"></i> Estado</label>
                    <select name="estado" class="filter-select" onchange="this.form.submit()">
                        <option value="todos" <?php echo $filtro_estado === 'todos' ? 'selected' : ''; ?>>Todos</option>
                        <option value="pendiente" <?php echo $filtro_estado === 'pendiente' ? 'selected' : ''; ?>>Pendiente</option>
                        <option value="en_progreso" <?php echo $filtro_estado === 'en_progreso' ? 'selected' : ''; ?>>En Progreso</option>
                        <option value="resuelto" <?php echo $filtro_estado === 'resuelto' ? 'selected' : ''; ?>>Resuelto</option>
                    </select>
                </div>
                <div class="filter-group">
                    <label><i class="fas fa-exclamation-triangle"></i> Prioridad</label>
                    <select name="prioridad" class="filter-select" onchange="this.form.submit()">
                        <option value="todos" <?php echo $filtro_prioridad === 'todos' ? 'selected' : ''; ?>>Todas</option>
                        <option value="critica" <?php echo $filtro_prioridad === 'critica' ? 'selected' : ''; ?>>Crítica</option>
                        <option value="alta" <?php echo $filtro_prioridad === 'alta' ? 'selected' : ''; ?>>Alta</option>
                        <option value="media" <?php echo $filtro_prioridad === 'media' ? 'selected' : ''; ?>>Media</option>
                        <option value="baja" <?php echo $filtro_prioridad === 'baja' ? 'selected' : ''; ?>>Baja</option>
                    </select>
                </div>
                <div class="filter-group" style="display: flex; align-items: flex-end;">
                    <a href="mis_tickets.php" class="btn btn-primary" style="width: 100%; justify-content: center;">
                        <i class="fas fa-redo"></i> Limpiar Filtros
                    </a>
                </div>
            </div>
        </form>
    </div>
    
    <!-- Lista de Tickets -->
    <?php if(!empty($tickets_filtrados)): ?>
        <div class="tickets-grid">
            <?php foreach($tickets_filtrados as $ticket): ?>
            <div class="ticket-card <?php echo $ticket['prioridad']; ?>">
                <div class="ticket-header">
                    <div class="ticket-title-section">
                        <h3><?php echo htmlspecialchars($ticket['titulo']); ?></h3>
                        <span class="ticket-id">Ticket #<?php echo $ticket['id_ticket']; ?></span>
                    </div>
                    <div class="ticket-badges">
                        <span class="badge badge-<?php echo $ticket['prioridad']; ?>">
                            <i class="fas fa-flag"></i>
                            <?php echo ucfirst($ticket['prioridad']); ?>
                        </span>
                        <span class="badge badge-<?php echo $ticket['estado']; ?>">
                            <?php echo ucfirst(str_replace('_', ' ', $ticket['estado'])); ?>
                        </span>
                    </div>
                </div>
                
                <div class="ticket-info">
                    <div class="info-item">
                        <i class="fas fa-user"></i>
                        <span><?php echo htmlspecialchars($ticket['docente_nombre'] . ' ' . $ticket['docente_apellido']); ?></span>
                    </div>
                    <div class="info-item">
                        <i class="fas fa-envelope"></i>
                        <span><?php echo htmlspecialchars($ticket['docente_correo']); ?></span>
                    </div>
                    <div class="info-item">
                        <i class="fas fa-tag"></i>
                        <span><?php echo htmlspecialchars($ticket['categoria_nombre']); ?></span>
                    </div>
                    <div class="info-item">
                        <i class="fas fa-calendar"></i>
                        <span><?php echo date('d/m/Y H:i', strtotime($ticket['fecha_creacion'])); ?></span>
                    </div>
                </div>
                
                <div class="ticket-description">
                    <strong>Descripción:</strong><br>
                    <?php echo nl2br(htmlspecialchars($ticket['descripcion'])); ?>
                </div>
                
                <div class="ticket-actions">
                    <a href="../admin/chat_ticket.php?id=<?php echo $ticket['id_ticket']; ?>" class="btn btn-primary">
                        <i class="fas fa-comments"></i> Abrir Chat
                    </a>
                    <?php if($ticket['estado'] !== 'resuelto'): ?>
                        <button class="btn btn-success" onclick="marcarResuelto(<?php echo $ticket['id_ticket']; ?>)">
                            <i class="fas fa-check-circle"></i> Marcar como Resuelto
                        </button>
                    <?php endif; ?>
                </div>
            </div>
            <?php endforeach; ?>
        </div>
    <?php else: ?>
        <div class="empty-state">
            <i class="fas fa-inbox"></i>
            <h3>No hay tickets que mostrar</h3>
            <p>No tienes tickets con los filtros seleccionados</p>
        </div>
    <?php endif; ?>
</div>

<script>
function marcarResuelto(idTicket) {
    if(confirm('¿Estás seguro de marcar este ticket como resuelto?')) {
        // Aquí podrías hacer una llamada AJAX
        // Por ahora redirigimos a una página de procesamiento
        window.location.href = 'procesar_ticket.php?id=' + idTicket + '&accion=resolver';
    }
}
</script>

<!-- Bootstrap JS Bundle -->
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>

</body>
</html>
