<?php
// Vista Mejorada de Gestión de Tickets con Asignación
include_once __DIR__ . '/../../controlador/controladorTicket.php';
include_once __DIR__ . '/../../controlador/controladorUsuario.php';

$controladorTicket = new controladorTicket();
$controladorUsuario = new controladorUsuario();

// Obtener tickets
$tickets = $controladorTicket->listarTickets();

// Obtener técnicos con su estado de disponibilidad
$tecnicos = $controladorUsuario->modelo->listarTecnicosConDisponibilidad();

// Procesar asignación si se envió el formulario
$mensaje = '';
$tipo_mensaje = '';

if($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['accion'])) {
    if($_POST['accion'] === 'asignar') {
        $id_ticket = intval($_POST['id_ticket']);
        $id_tecnico = intval($_POST['id_tecnico']);
        
        $resultado = $controladorTicket->asignarTicket($id_ticket, $id_tecnico);
        
        if($resultado['success']) {
            $mensaje = "Ticket asignado exitosamente";
            $tipo_mensaje = "success";
            // Recargar tickets
            $tickets = $controladorTicket->listarTickets();
        } else {
            $mensaje = "Error al asignar ticket: " . $resultado['mensaje'];
            $tipo_mensaje = "error";
        }
    }
}

// Filtros
$filtro_estado = isset($_GET['estado']) ? $_GET['estado'] : 'activos'; // Por defecto muestra solo activos
$filtro_prioridad = isset($_GET['prioridad']) ? $_GET['prioridad'] : 'todos';
$filtro_asignacion = isset($_GET['asignacion']) ? $_GET['asignacion'] : 'todos';

// Aplicar filtros
$tickets_filtrados = $tickets;

if($filtro_estado === 'activos') {
    // Filtro especial: solo tickets activos (pendiente o en_progreso)
    $tickets_filtrados = array_filter($tickets_filtrados, function($ticket) {
        return $ticket['estado'] === 'pendiente' || $ticket['estado'] === 'en_progreso';
    });
} elseif($filtro_estado !== 'todos') {
    $tickets_filtrados = array_filter($tickets_filtrados, function($ticket) use ($filtro_estado) {
        return $ticket['estado'] === $filtro_estado;
    });
}

if($filtro_prioridad !== 'todos') {
    $tickets_filtrados = array_filter($tickets_filtrados, function($ticket) use ($filtro_prioridad) {
        return $ticket['prioridad'] === $filtro_prioridad;
    });
}

if($filtro_asignacion === 'sin_asignar') {
    $tickets_filtrados = array_filter($tickets_filtrados, function($ticket) {
        return empty($ticket['id_asignado']);
    });
} elseif($filtro_asignacion === 'asignados') {
    $tickets_filtrados = array_filter($tickets_filtrados, function($ticket) {
        return !empty($ticket['id_asignado']);
    });
}

// Estadísticas
$stats = [
    'total' => count($tickets),
    'sin_asignar' => count(array_filter($tickets, fn($t) => empty($t['id_asignado']))),
    'pendientes' => count(array_filter($tickets, fn($t) => $t['estado'] === 'pendiente')),
    'en_progreso' => count(array_filter($tickets, fn($t) => $t['estado'] === 'en_progreso')),
];
?>

<style>
        .page-header {
            background: linear-gradient(135deg, #9b59b6, #8e44ad);
            color: white;
            padding: 30px;
            border-radius: 15px;
            margin-bottom: 30px;
            box-shadow: 0 10px 30px rgba(155, 89, 182, 0.3);
        }
        
        .page-header h1 {
            margin: 0 0 10px 0;
            font-size: 2rem;
            display: flex;
            align-items: center;
            gap: 15px;
        }
        
        .alert {
            padding: 15px 20px;
            border-radius: 10px;
            margin-bottom: 20px;
            display: flex;
            align-items: center;
            gap: 10px;
            animation: slideDown 0.3s ease;
        }
        
        .alert-success {
            background: #d4edda;
            color: #155724;
            border: 1px solid #c3e6cb;
        }
        
        .alert-error {
            background: #f8d7da;
            color: #721c24;
            border: 1px solid #f5c6cb;
        }
        
        @keyframes slideDown {
            from { opacity: 0; transform: translateY(-20px); }
            to { opacity: 1; transform: translateY(0); }
        }
        
        .stats-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
            gap: 20px;
            margin-bottom: 30px;
        }
        
        .stat-card {
            background: white;
            padding: 20px;
            border-radius: 10px;
            box-shadow: 0 5px 15px rgba(0,0,0,0.1);
            text-align: center;
        }
        
        .stat-card .number {
            font-size: 2.5rem;
            font-weight: bold;
            color: #9b59b6;
            margin-bottom: 5px;
        }
        
        .stat-card .label {
            color: #7f8c8d;
            font-size: 0.9rem;
        }
        
        .filters-section {
            background: white;
            padding: 20px;
            border-radius: 10px;
            box-shadow: 0 5px 15px rgba(0,0,0,0.1);
            margin-bottom: 30px;
        }
        
        .filters-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
            gap: 15px;
        }
        
        .filter-group {
            display: flex;
            flex-direction: column;
            gap: 5px;
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
        }
        
        .tickets-table {
            background: white;
            border-radius: 10px;
            overflow: hidden;
            box-shadow: 0 5px 15px rgba(0,0,0,0.1);
        }
        
        .tickets-table table {
            width: 100%;
            border-collapse: collapse;
        }
        
        .tickets-table thead {
            background: #9b59b6;
            color: white;
        }
        
        .tickets-table th, .tickets-table td {
            padding: 15px;
            text-align: left;
        }
        
        .tickets-table tbody tr {
            border-bottom: 1px solid #ecf0f1;
            transition: background 0.3s ease;
        }
        
        .tickets-table tbody tr:hover {
            background: #f8f9fa;
        }
        
        .badge {
            padding: 5px 12px;
            border-radius: 15px;
            font-size: 0.85rem;
            font-weight: 600;
            display: inline-block;
        }
        
        .badge-pendiente {
            background: #fff3cd;
            color: #856404;
        }
        
        .badge-en_progreso {
            background: #d1ecf1;
            color: #0c5460;
        }
        
        .badge-resuelto {
            background: #d4edda;
            color: #155724;
        }
        
        .badge-rechazado {
            background: #f8d7da;
            color: #721c24;
        }
        
        .priority-baja {
            background: #d1ecf1;
            color: #0c5460;
        }
        
        .priority-media {
            background: #fff3cd;
            color: #856404;
        }
        
        .priority-alta {
            background: #f8d7da;
            color: #721c24;
        }
        
        .priority-critica {
            background: #d63031;
            color: white;
        }
        
        .btn {
            padding: 8px 16px;
            border: none;
            border-radius: 8px;
            font-weight: 600;
            cursor: pointer;
            transition: all 0.3s ease;
            display: inline-flex;
            align-items: center;
            gap: 8px;
            text-decoration: none;
            font-size: 0.9rem;
        }
        
        .btn-primary {
            background: linear-gradient(135deg, #3498db, #2980b9);
            color: white;
        }
        
        .btn-primary:hover {
            transform: translateY(-2px);
            box-shadow: 0 6px 20px rgba(52, 152, 219, 0.4);
        }
        
        .btn-success {
            background: #27ae60;
            color: white;
        }
        
        .btn-warning {
            background: #f39c12;
            color: white;
        }
        
        .btn-sm {
            padding: 5px 10px;
            font-size: 0.85rem;
        }
        
        .modal {
            display: none;
            position: fixed;
            z-index: 1000;
            left: 0;
            top: 0;
            width: 100%;
            height: 100%;
            overflow: auto;
            background-color: rgba(0,0,0,0.5);
        }
        
        .modal-content {
            background-color: white;
            margin: 10% auto;
            padding: 30px;
            border-radius: 15px;
            width: 90%;
            max-width: 500px;
            box-shadow: 0 10px 40px rgba(0,0,0,0.3);
            animation: modalSlide 0.3s ease;
        }
        
        @keyframes modalSlide {
            from { opacity: 0; transform: translateY(-50px); }
            to { opacity: 1; transform: translateY(0); }
        }
        
        .modal-header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 20px;
            padding-bottom: 15px;
            border-bottom: 2px solid #ecf0f1;
        }
        
        .modal-header h3 {
            margin: 0;
            color: #2c3e50;
        }
        
        .close {
            font-size: 2rem;
            font-weight: bold;
            color: #7f8c8d;
            cursor: pointer;
            transition: color 0.3s ease;
        }
        
        .close:hover {
            color: #e74c3c;
        }
        
        .form-group {
            margin-bottom: 20px;
        }
        
        .form-group label {
            display: block;
            margin-bottom: 8px;
            font-weight: 600;
            color: #2c3e50;
        }
        
        .form-control {
            width: 100%;
            padding: 12px;
            border: 2px solid #ecf0f1;
            border-radius: 8px;
            font-size: 1rem;
            transition: border 0.3s ease;
        }
        
        .form-control:focus {
            outline: none;
            border-color: #9b59b6;
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
        
        .sin-asignar {
            background: #fff3cd !important;
        }
    </style>
</head>
<body>
    <div class="container">
        <!-- Encabezado -->
        <div class="page-header">
            <h1>
                <i class="fas fa-ticket-alt"></i>
                Gestión de Tickets
            </h1>
            <p>Administra y asigna tickets a tu equipo técnico</p>
        </div>

        <!-- Alertas -->
        <?php if(!empty($mensaje)): ?>
            <div class="alert alert-<?php echo $tipo_mensaje; ?>">
                <i class="fas fa-<?php echo $tipo_mensaje === 'success' ? 'check-circle' : 'exclamation-circle'; ?>"></i>
                <?php echo htmlspecialchars($mensaje); ?>
            </div>
        <?php endif; ?>

        <!-- Mensaje Informativo -->
        <div style="background: linear-gradient(135deg, #3498db, #2980b9); color: white; padding: 20px; border-radius: 10px; margin-bottom: 25px;">
            <div style="display: flex; align-items: center; gap: 15px;">
                <div style="font-size: 2rem;">ℹ️</div>
                <div style="flex: 1;">
                    <h3 style="margin: 0 0 10px 0; font-size: 1.2rem;">📋 ¿Cuándo puedo asignar un ticket?</h3>
                    <p style="margin: 0; line-height: 1.6; opacity: 0.95;">
                        <strong>✅ Puedes asignar:</strong> Tickets "Pendientes" o "Sin Asignar"<br>
                        <strong>🔴 NO puedes asignar:</strong> Tickets "En Progreso" (ya están siendo atendidos) o "Finalizados" (resueltos/rechazados)<br>
                        <strong>💡 Tip:</strong> Por defecto se muestran solo tickets activos. Usa los filtros para ver todos.
                    </p>
                </div>
            </div>
        </div>

        <!-- Estadísticas -->
        <div class="stats-grid">
            <div class="stat-card">
                <div class="number"><?php echo $stats['total']; ?></div>
                <div class="label">Total Tickets</div>
            </div>
            <div class="stat-card">
                <div class="number"><?php echo $stats['sin_asignar']; ?></div>
                <div class="label">Sin Asignar</div>
            </div>
            <div class="stat-card">
                <div class="number"><?php echo $stats['pendientes']; ?></div>
                <div class="label">Pendientes</div>
            </div>
            <div class="stat-card">
                <div class="number"><?php echo $stats['en_progreso']; ?></div>
                <div class="label">En Progreso</div>
            </div>
        </div>

        <!-- Filtros -->
        <div class="filters-section">
            <form method="GET" action="">
                <input type="hidden" name="url" value="gestion_tickets">
                <div class="filters-grid">
                    <div class="filter-group">
                        <label><i class="fas fa-tasks"></i> Estado</label>
                        <select name="estado" class="filter-select">
                            <option value="activos" <?php echo $filtro_estado === 'activos' ? 'selected' : ''; ?>>⚡ Tickets Activos</option>
                            <option value="todos" <?php echo $filtro_estado === 'todos' ? 'selected' : ''; ?>>Todos</option>
                            <option value="pendiente" <?php echo $filtro_estado === 'pendiente' ? 'selected' : ''; ?>>Pendiente</option>
                            <option value="en_progreso" <?php echo $filtro_estado === 'en_progreso' ? 'selected' : ''; ?>>En Progreso</option>
                            <option value="resuelto" <?php echo $filtro_estado === 'resuelto' ? 'selected' : ''; ?>>Resuelto</option>
                            <option value="rechazado" <?php echo $filtro_estado === 'rechazado' ? 'selected' : ''; ?>>Rechazado</option>
                        </select>
                    </div>
                    <div class="filter-group">
                        <label><i class="fas fa-exclamation-triangle"></i> Prioridad</label>
                        <select name="prioridad" class="filter-select">
                            <option value="todos" <?php echo $filtro_prioridad === 'todos' ? 'selected' : ''; ?>>Todas</option>
                            <option value="baja" <?php echo $filtro_prioridad === 'baja' ? 'selected' : ''; ?>>Baja</option>
                            <option value="media" <?php echo $filtro_prioridad === 'media' ? 'selected' : ''; ?>>Media</option>
                            <option value="alta" <?php echo $filtro_prioridad === 'alta' ? 'selected' : ''; ?>>Alta</option>
                            <option value="critica" <?php echo $filtro_prioridad === 'critica' ? 'selected' : ''; ?>>Crítica</option>
                        </select>
                    </div>
                    <div class="filter-group">
                        <label><i class="fas fa-user-tag"></i> Asignación</label>
                        <select name="asignacion" class="filter-select">
                            <option value="todos" <?php echo $filtro_asignacion === 'todos' ? 'selected' : ''; ?>>Todos</option>
                            <option value="sin_asignar" <?php echo $filtro_asignacion === 'sin_asignar' ? 'selected' : ''; ?>>Sin Asignar</option>
                            <option value="asignados" <?php echo $filtro_asignacion === 'asignados' ? 'selected' : ''; ?>>Asignados</option>
                        </select>
                    </div>
                    <div class="filter-group" style="display: flex; align-items: flex-end; gap: 10px;">
                        <button type="submit" class="btn btn-primary" style="flex: 1;">Buscar</button>
                        <a href="?url=gestion_tickets" class="btn" style="background: #ecf0f1; color: #2c3e50;">
                            <i class="fas fa-redo"></i>
                        </a>
                    </div>
                </div>
            </form>
        </div>

        <!-- Tabla de Tickets -->
        <?php if(!empty($tickets_filtrados)): ?>
        <div class="tickets-table">
            <table>
                <thead>
                    <tr>
                        <th>ID</th>
                        <th>Título</th>
                        <th>Docente</th>
                        <th>Prioridad</th>
                        <th>Estado</th>
                        <th>Asignado a</th>
                        <th>Fecha</th>
                        <th>Acciones</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach($tickets_filtrados as $ticket): ?>
                    <tr class="<?php echo empty($ticket['id_asignado']) ? 'sin-asignar' : ''; ?>">
                        <td><strong>#<?php echo $ticket['id_ticket']; ?></strong></td>
                        <td><?php echo htmlspecialchars($ticket['titulo']); ?></td>
                        <td><?php echo htmlspecialchars($ticket['docente_nombre'] . ' ' . $ticket['docente_apellido']); ?></td>
                        <td>
                            <span class="badge priority-<?php echo $ticket['prioridad']; ?>">
                                <?php echo ucfirst($ticket['prioridad']); ?>
                            </span>
                        </td>
                        <td>
                            <span class="badge badge-<?php echo $ticket['estado']; ?>">
                                <?php echo ucfirst(str_replace('_', ' ', $ticket['estado'])); ?>
                            </span>
                        </td>
                        <td>
                            <?php if(!empty($ticket['admin_nombre'])): ?>
                                <span class="badge badge-en_progreso">
                                    <i class="fas fa-user"></i>
                                    <?php echo htmlspecialchars($ticket['admin_nombre'] . ' ' . $ticket['admin_apellido']); ?>
                                </span>
                            <?php else: ?>
                                <span class="badge" style="background: #ecf0f1; color: #7f8c8d;">
                                    <i class="fas fa-user-times"></i> Sin asignar
                                </span>
                            <?php endif; ?>
                        </td>
                        <td><?php echo date('d/m/Y H:i', strtotime($ticket['fecha_creacion'])); ?></td>
                        <td>
                            <?php if($ticket['estado'] === 'resuelto' || $ticket['estado'] === 'rechazado'): ?>
                                <!-- Ticket finalizado - No se puede asignar -->
                                <span class="badge" style="background: #ecf0f1; color: #7f8c8d; padding: 8px 12px;">
                                    <i class="fas fa-check-circle"></i> Finalizado
                                </span>
                            <?php elseif(empty($ticket['id_asignado']) || $ticket['estado'] === 'pendiente'): ?>
                                <!-- Ticket disponible para asignar -->
                                <button class="btn btn-success btn-sm" onclick="abrirModalAsignar(<?php echo $ticket['id_ticket']; ?>, '<?php echo htmlspecialchars($ticket['titulo']); ?>')">
                                    <i class="fas fa-user-plus"></i> Asignar
                                </button>
                            <?php else: ?>
                                <!-- Ticket en progreso - No se puede reasignar -->
                                <span class="badge" style="background: #d1ecf1; color: #0c5460; padding: 8px 12px;">
                                    <i class="fas fa-cog fa-spin"></i> En Proceso
                                </span>
                            <?php endif; ?>
                            <a href="chat_ticket.php?id=<?php echo $ticket['id_ticket']; ?>" class="btn btn-primary btn-sm">
                                <i class="fas fa-eye"></i> Ver
                            </a>
                        </td>
                    </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
        <?php else: ?>
        <div class="empty-state">
            <i class="fas fa-inbox"></i>
            <h3>No se encontraron tickets</h3>
            <p>Prueba ajustando los filtros de búsqueda</p>
        </div>
        <?php endif; ?>
    </div>

    <!-- Modal Asignar Técnico -->
    <div id="modalAsignar" class="modal">
        <div class="modal-content">
            <div class="modal-header">
                <h3><i class="fas fa-user-plus"></i> Asignar Técnico</h3>
                <span class="close" onclick="cerrarModal()">&times;</span>
            </div>
            <form method="POST">
                <input type="hidden" name="accion" value="asignar">
                <input type="hidden" name="id_ticket" id="ticket_id">
                
                <div class="form-group">
                    <label>Ticket:</label>
                    <input type="text" id="ticket_titulo" class="form-control" readonly>
                </div>
                
                <div class="form-group">
                    <label>Seleccionar Técnico: *</label>
                    <select name="id_tecnico" class="form-control" required>
                        <option value="">-- Seleccionar técnico --</option>
                        <?php foreach($tecnicos as $tecnico): ?>
                        <option value="<?php echo $tecnico['id_usuario']; ?>" 
                                <?php echo $tecnico['disponible'] == 0 ? 'disabled' : ''; ?>
                                style="<?php echo $tecnico['disponible'] == 0 ? 'color: #95a5a6; background: #ecf0f1;' : ''; ?>">
                            <?php 
                            echo htmlspecialchars($tecnico['nombre'] . ' ' . $tecnico['apellido']); 
                            if($tecnico['disponible'] == 0){
                                echo ' 🔴 OCUPADO (' . $tecnico['tickets_en_progreso'] . ' ticket activo)';
                            } else {
                                echo ' ✅ DISPONIBLE';
                            }
                            ?>
                        </option>
                        <?php endforeach; ?>
                    </select>
                    <small style="display: block; margin-top: 5px; color: #7f8c8d;">
                        <i class="fas fa-info-circle"></i> Solo se pueden asignar técnicos disponibles (sin tickets activos)
                    </small>
                </div>
                
                <div style="display: flex; gap: 10px;">
                    <button type="submit" class="btn btn-success" style="flex: 1;">
                        <i class="fas fa-check"></i> Asignar Ticket
                    </button>
                    <button type="button" class="btn" style="background: #ecf0f1; color: #2c3e50;" onclick="cerrarModal()">
                        Cancelar
                    </button>
                </div>
            </form>
        </div>
    </div>

    <script>
        function abrirModalAsignar(idTicket, tituloTicket) {
            document.getElementById('ticket_id').value = idTicket;
            document.getElementById('ticket_titulo').value = '#' + idTicket + ' - ' + tituloTicket;
            document.getElementById('modalAsignar').style.display = 'block';
        }
        
        function cerrarModal() {
            document.getElementById('modalAsignar').style.display = 'none';
        }
        
        // Cerrar modal al hacer clic fuera
        window.onclick = function(event) {
            if (event.target.id === 'modalAsignar') {
                cerrarModal();
            }
        }
        
        // Auto-ocultar alertas
        setTimeout(function() {
            const alerts = document.querySelectorAll('.alert');
            alerts.forEach(alert => {
                alert.style.opacity = '0';
                setTimeout(() => alert.remove(), 300);
            });
        }, 5000);
    </script>
