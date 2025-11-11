<?php
// Vista de Mis Tickets para Docentes
include_once __DIR__ . '/../../controlador/controladorTicket.php';

$controladorTicket = new controladorTicket();

// Obtener ID del usuario actual
$id_usuario = $_SESSION['usuario']['id'];
$nombre_usuario = $_SESSION['usuario']['nombre_completo'];

// Obtener todos los tickets del docente
$todos_mis_tickets = $controladorTicket->obtenerTicketsDocente($id_usuario);

// Filtros
$filtro_estado = isset($_GET['estado']) ? $_GET['estado'] : 'todos';
$filtro_prioridad = isset($_GET['prioridad']) ? $_GET['prioridad'] : 'todos';
$busqueda = isset($_GET['busqueda']) ? trim($_GET['busqueda']) : '';

// Aplicar filtros
$tickets_filtrados = $todos_mis_tickets;

// Filtrar por estado
if($filtro_estado !== 'todos') {
    $tickets_filtrados = array_filter($tickets_filtrados, function($ticket) use ($filtro_estado) {
        return $ticket['estado'] === $filtro_estado;
    });
}

// Filtrar por prioridad
if($filtro_prioridad !== 'todos') {
    $tickets_filtrados = array_filter($tickets_filtrados, function($ticket) use ($filtro_prioridad) {
        return $ticket['prioridad'] === $filtro_prioridad;
    });
}

// Filtrar por búsqueda
if(!empty($busqueda)) {
    $tickets_filtrados = array_filter($tickets_filtrados, function($ticket) use ($busqueda) {
        $busqueda_lower = strtolower($busqueda);
        return strpos(strtolower($ticket['titulo']), $busqueda_lower) !== false ||
               strpos(strtolower($ticket['descripcion']), $busqueda_lower) !== false ||
               strpos(strtolower($ticket['id_ticket']), $busqueda_lower) !== false;
    });
}

// Contar tickets por estado
$contadores = [
    'total' => count($todos_mis_tickets),
    'pendientes' => 0,
    'en_progreso' => 0,
    'resueltos' => 0,
    'rechazados' => 0
];

foreach($todos_mis_tickets as $ticket) {
    switch($ticket['estado']) {
        case 'pendiente':
            $contadores['pendientes']++;
            break;
        case 'en_progreso':
            $contadores['en_progreso']++;
            break;
        case 'resuelto':
            $contadores['resueltos']++;
            break;
        case 'rechazado':
            $contadores['rechazados']++;
            break;
    }
}

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

function getPrioridadIcono($prioridad) {
    switch($prioridad) {
        case 'baja': return 'fa-arrow-down';
        case 'media': return 'fa-minus';
        case 'alta': return 'fa-arrow-up';
        case 'critica': return 'fa-exclamation-triangle';
        default: return 'fa-minus';
    }
}
?>

<link rel="stylesheet" href="../../assets/css/dashboard.css">
<link rel="stylesheet" href="../../assets/css/ticket.css">

<style>
    .page-header {
        background: linear-gradient(135deg, #e74c3c, #c0392b);
        color: white;
        padding: 30px;
        border-radius: 15px;
        margin-bottom: 30px;
        box-shadow: 0 10px 30px rgba(231, 76, 60, 0.3);
    }
    
    .page-header h1 {
        margin: 0 0 10px 0;
        font-size: 2rem;
        display: flex;
        align-items: center;
        gap: 15px;
    }
    
    .page-header p {
        margin: 0;
        opacity: 0.9;
    }
    
    .filters-section {
        background: white;
        padding: 25px;
        border-radius: 15px;
        margin-bottom: 30px;
        box-shadow: 0 5px 20px rgba(0,0,0,0.1);
    }
    
    .filters-grid {
        display: grid;
        grid-template-columns: repeat(auto-fit, minmax(250px, 1fr));
        gap: 20px;
        margin-bottom: 20px;
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
    
    .filter-select, .filter-input {
        padding: 12px;
        border: 2px solid #ecf0f1;
        border-radius: 8px;
        font-size: 1rem;
        transition: all 0.3s ease;
    }
    
    .filter-select:focus, .filter-input:focus {
        outline: none;
        border-color: #e74c3c;
    }
    
    .filter-buttons {
        display: flex;
        gap: 10px;
        justify-content: flex-end;
    }
    
    .btn-filter {
        padding: 12px 25px;
        border: none;
        border-radius: 8px;
        font-weight: 600;
        cursor: pointer;
        transition: all 0.3s ease;
        display: flex;
        align-items: center;
        gap: 8px;
    }
    
    .btn-primary {
        background: linear-gradient(135deg, #e74c3c, #c0392b);
        color: white;
    }
    
    .btn-primary:hover {
        transform: translateY(-2px);
        box-shadow: 0 6px 20px rgba(231, 76, 60, 0.4);
    }
    
    .btn-secondary {
        background: #ecf0f1;
        color: #2c3e50;
    }
    
    .btn-secondary:hover {
        background: #bdc3c7;
    }
    
    .quick-filters {
        display: flex;
        gap: 10px;
        margin-bottom: 20px;
        flex-wrap: wrap;
    }
    
    .quick-filter-btn {
        padding: 10px 20px;
        border: 2px solid #ecf0f1;
        background: white;
        border-radius: 25px;
        cursor: pointer;
        transition: all 0.3s ease;
        font-weight: 500;
        text-decoration: none;
        color: #2c3e50;
        display: flex;
        align-items: center;
        gap: 8px;
    }
    
    .quick-filter-btn:hover {
        border-color: #e74c3c;
        color: #e74c3c;
        transform: translateY(-2px);
    }
    
    .quick-filter-btn.active {
        background: #e74c3c;
        border-color: #e74c3c;
        color: white;
    }
    
    .tickets-grid {
        display: grid;
        gap: 20px;
    }
    
    .ticket-card {
        background: white;
        border-radius: 15px;
        padding: 25px;
        box-shadow: 0 5px 20px rgba(0,0,0,0.1);
        transition: all 0.3s ease;
        border-left: 5px solid #ecf0f1;
    }
    
    .ticket-card:hover {
        transform: translateY(-5px);
        box-shadow: 0 10px 30px rgba(0,0,0,0.15);
    }
    
    .ticket-card.pendiente {
        border-left-color: #f39c12;
    }
    
    .ticket-card.en_progreso {
        border-left-color: #3498db;
    }
    
    .ticket-card.resuelto {
        border-left-color: #27ae60;
    }
    
    .ticket-card.rechazado {
        border-left-color: #e74c3c;
    }
    
    .ticket-header {
        display: flex;
        justify-content: space-between;
        align-items: start;
        margin-bottom: 15px;
    }
    
    .ticket-title {
        font-size: 1.3rem;
        font-weight: 700;
        color: #2c3e50;
        margin: 0 0 8px 0;
        flex: 1;
    }
    
    .ticket-id {
        background: #ecf0f1;
        color: #2c3e50;
        padding: 5px 15px;
        border-radius: 20px;
        font-weight: 600;
        font-size: 0.9rem;
    }
    
    .ticket-description {
        color: #7f8c8d;
        margin-bottom: 15px;
        line-height: 1.6;
    }
    
    .ticket-meta {
        display: flex;
        flex-wrap: wrap;
        gap: 15px;
        margin-bottom: 15px;
    }
    
    .meta-item {
        display: flex;
        align-items: center;
        gap: 8px;
        color: #7f8c8d;
        font-size: 0.9rem;
    }
    
    .meta-item i {
        color: #e74c3c;
    }
    
    .ticket-footer {
        display: flex;
        justify-content: space-between;
        align-items: center;
        padding-top: 15px;
        border-top: 1px solid #ecf0f1;
    }
    
    .ticket-badges {
        display: flex;
        gap: 10px;
    }
    
    .ticket-actions {
        display: flex;
        gap: 10px;
    }
    
    .action-btn {
        padding: 8px 16px;
        border-radius: 8px;
        text-decoration: none;
        display: flex;
        align-items: center;
        gap: 6px;
        font-weight: 600;
        transition: all 0.3s ease;
        font-size: 0.9rem;
    }
    
    .action-btn-primary {
        background: #e74c3c;
        color: white;
    }
    
    .action-btn-primary:hover {
        background: #c0392b;
        transform: translateY(-2px);
    }
    
    .action-btn-secondary {
        background: #ecf0f1;
        color: #2c3e50;
    }
    
    .action-btn-secondary:hover {
        background: #bdc3c7;
    }
    
    .empty-state {
        text-align: center;
        padding: 80px 20px;
        color: #95a5a6;
    }
    
    .empty-state i {
        font-size: 5rem;
        margin-bottom: 20px;
        opacity: 0.5;
    }
    
    .empty-state h3 {
        font-size: 1.5rem;
        margin-bottom: 10px;
    }
    
    .empty-state p {
        font-size: 1.1rem;
        margin-bottom: 30px;
    }
    
    .results-info {
        display: flex;
        justify-content: space-between;
        align-items: center;
        margin-bottom: 20px;
        padding: 15px 20px;
        background: #f8f9fa;
        border-radius: 10px;
    }
    
    @media (max-width: 768px) {
        .filters-grid {
            grid-template-columns: 1fr;
        }
        
        .ticket-header {
            flex-direction: column;
            gap: 10px;
        }
        
        .ticket-footer {
            flex-direction: column;
            gap: 15px;
            align-items: flex-start;
        }
    }
</style>

<!-- Encabezado de Página -->
<div class="page-header">
    <h1>
        <i class="fas fa-ticket-alt"></i>
        Mis Tickets
    </h1>
    <p>Gestiona y da seguimiento a todos tus tickets de soporte</p>
</div>

<!-- Filtros Rápidos -->
<div class="quick-filters">
    <a href="?url=mis_tickets" class="quick-filter-btn <?php echo ($filtro_estado === 'todos') ? 'active' : ''; ?>">
        <i class="fas fa-list"></i>
        Todos (<?php echo $contadores['total']; ?>)
    </a>
    <a href="?url=mis_tickets&estado=pendiente" class="quick-filter-btn <?php echo ($filtro_estado === 'pendiente') ? 'active' : ''; ?>">
        <i class="fas fa-clock"></i>
        Pendientes (<?php echo $contadores['pendientes']; ?>)
    </a>
    <a href="?url=mis_tickets&estado=en_progreso" class="quick-filter-btn <?php echo ($filtro_estado === 'en_progreso') ? 'active' : ''; ?>">
        <i class="fas fa-spinner"></i>
        En Progreso (<?php echo $contadores['en_progreso']; ?>)
    </a>
    <a href="?url=mis_tickets&estado=resuelto" class="quick-filter-btn <?php echo ($filtro_estado === 'resuelto') ? 'active' : ''; ?>">
        <i class="fas fa-check-circle"></i>
        Resueltos (<?php echo $contadores['resueltos']; ?>)
    </a>
    <?php if($contadores['rechazados'] > 0): ?>
    <a href="?url=mis_tickets&estado=rechazado" class="quick-filter-btn <?php echo ($filtro_estado === 'rechazado') ? 'active' : ''; ?>">
        <i class="fas fa-times-circle"></i>
        Rechazados (<?php echo $contadores['rechazados']; ?>)
    </a>
    <?php endif; ?>
</div>

<!-- Sección de Filtros Avanzados -->
<div class="filters-section">
    <h3 style="margin: 0 0 20px 0; color: #2c3e50;">
        <i class="fas fa-filter"></i> Filtros Avanzados
    </h3>
    
    <form method="GET" action="">
        <input type="hidden" name="url" value="mis_tickets">
        
        <div class="filters-grid">
            <div class="filter-group">
                <label for="estado">
                    <i class="fas fa-flag"></i> Estado
                </label>
                <select name="estado" id="estado" class="filter-select">
                    <option value="todos" <?php echo ($filtro_estado === 'todos') ? 'selected' : ''; ?>>Todos los estados</option>
                    <option value="pendiente" <?php echo ($filtro_estado === 'pendiente') ? 'selected' : ''; ?>>Pendiente</option>
                    <option value="en_progreso" <?php echo ($filtro_estado === 'en_progreso') ? 'selected' : ''; ?>>En Progreso</option>
                    <option value="resuelto" <?php echo ($filtro_estado === 'resuelto') ? 'selected' : ''; ?>>Resuelto</option>
                    <option value="rechazado" <?php echo ($filtro_estado === 'rechazado') ? 'selected' : ''; ?>>Rechazado</option>
                </select>
            </div>
            
            <div class="filter-group">
                <label for="prioridad">
                    <i class="fas fa-exclamation-circle"></i> Prioridad
                </label>
                <select name="prioridad" id="prioridad" class="filter-select">
                    <option value="todos" <?php echo ($filtro_prioridad === 'todos') ? 'selected' : ''; ?>>Todas las prioridades</option>
                    <option value="baja" <?php echo ($filtro_prioridad === 'baja') ? 'selected' : ''; ?>>Baja</option>
                    <option value="media" <?php echo ($filtro_prioridad === 'media') ? 'selected' : ''; ?>>Media</option>
                    <option value="alta" <?php echo ($filtro_prioridad === 'alta') ? 'selected' : ''; ?>>Alta</option>
                    <option value="critica" <?php echo ($filtro_prioridad === 'critica') ? 'selected' : ''; ?>>Crítica</option>
                </select>
            </div>
            
            <div class="filter-group">
                <label for="busqueda">
                    <i class="fas fa-search"></i> Buscar
                </label>
                <input 
                    type="text" 
                    name="busqueda" 
                    id="busqueda" 
                    class="filter-input" 
                    placeholder="Buscar por título o descripción..."
                    value="<?php echo htmlspecialchars($busqueda); ?>"
                >
            </div>
        </div>
        
        <div class="filter-buttons">
            <a href="?url=mis_tickets" class="btn-filter btn-secondary">
                <i class="fas fa-redo"></i>
                Limpiar Filtros
            </a>
            <button type="submit" class="btn-filter btn-primary">
                <i class="fas fa-search"></i>
                Buscar
            </button>
        </div>
    </form>
</div>

<!-- Información de Resultados -->
<?php if(count($tickets_filtrados) > 0): ?>
<div class="results-info">
    <div>
        <strong><?php echo count($tickets_filtrados); ?></strong> 
        <?php echo count($tickets_filtrados) === 1 ? 'ticket encontrado' : 'tickets encontrados'; ?>
        <?php if($filtro_estado !== 'todos' || $filtro_prioridad !== 'todos' || !empty($busqueda)): ?>
            <span style="color: #7f8c8d;">(filtrado de <?php echo $contadores['total']; ?> total)</span>
        <?php endif; ?>
    </div>
    <a href="?url=nuevo_ticket" class="action-btn action-btn-primary">
        <i class="fas fa-plus-circle"></i>
        Nuevo Ticket
    </a>
</div>
<?php endif; ?>

<!-- Grid de Tickets -->
<div class="tickets-grid">
    <?php if (!empty($tickets_filtrados)): ?>
        <?php foreach ($tickets_filtrados as $ticket): ?>
            <div class="ticket-card <?php echo $ticket['estado']; ?>">
                <div class="ticket-header">
                    <div style="flex: 1;">
                        <h3 class="ticket-title"><?php echo htmlspecialchars($ticket['titulo']); ?></h3>
                        <span class="ticket-id">#<?php echo $ticket['id_ticket']; ?></span>
                    </div>
                </div>
                
                <p class="ticket-description">
                    <?php 
                    $desc = htmlspecialchars($ticket['descripcion']);
                    echo strlen($desc) > 150 ? substr($desc, 0, 150) . '...' : $desc;
                    ?>
                </p>
                
                <div class="ticket-meta">
                    <div class="meta-item">
                        <i class="fas fa-calendar"></i>
                        <?php echo formatearFecha($ticket['fecha_creacion']); ?>
                    </div>
                    <div class="meta-item">
                        <i class="fas fa-clock"></i>
                        Hace <?php echo tiempoTranscurrido($ticket['fecha_creacion']); ?>
                    </div>
                    <?php if($ticket['categoria_nombre']): ?>
                    <div class="meta-item">
                        <i class="fas fa-tag"></i>
                        <?php echo htmlspecialchars($ticket['categoria_nombre']); ?>
                    </div>
                    <?php endif; ?>
                    <?php if($ticket['admin_nombre']): ?>
                    <div class="meta-item">
                        <i class="fas fa-user-shield"></i>
                        <?php echo htmlspecialchars($ticket['admin_nombre'] . ' ' . $ticket['admin_apellido']); ?>
                    </div>
                    <?php endif; ?>
                </div>
                
                <div class="ticket-footer">
                    <div class="ticket-badges">
                        <span class="priority-badge <?php echo getPrioridadClase($ticket['prioridad']); ?>">
                            <i class="fas <?php echo getPrioridadIcono($ticket['prioridad']); ?>"></i>
                            <?php echo formatearTexto($ticket['prioridad']); ?>
                        </span>
                        <span class="status-badge <?php echo getEstadoClase($ticket['estado']); ?>">
                            <?php echo formatearTexto($ticket['estado']); ?>
                        </span>
                    </div>
                    
                    <div class="ticket-actions">
                        <a href="?url=chat_ticket&id=<?php echo $ticket['id_ticket']; ?>" class="action-btn action-btn-primary">
                            <i class="fas fa-comments"></i>
                            Ver Chat
                        </a>
                    </div>
                </div>
            </div>
        <?php endforeach; ?>
    <?php else: ?>
        <div class="empty-state">
            <i class="fas fa-inbox"></i>
            <h3>No se encontraron tickets</h3>
            <p>
                <?php if($filtro_estado !== 'todos' || $filtro_prioridad !== 'todos' || !empty($busqueda)): ?>
                    Prueba ajustando los filtros o realiza una búsqueda diferente
                <?php else: ?>
                    Aún no has creado ningún ticket de soporte
                <?php endif; ?>
            </p>
            <?php if($filtro_estado === 'todos' && $filtro_prioridad === 'todos' && empty($busqueda)): ?>
                <a href="?url=nuevo_ticket" class="btn-filter btn-primary">
                    <i class="fas fa-plus-circle"></i>
                    Crear Mi Primer Ticket
                </a>
            <?php else: ?>
                <a href="?url=mis_tickets" class="btn-filter btn-secondary">
                    <i class="fas fa-redo"></i>
                    Ver Todos los Tickets
                </a>
            <?php endif; ?>
        </div>
    <?php endif; ?>
</div>
