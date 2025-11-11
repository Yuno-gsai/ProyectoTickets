<?php
// Vista de Lista de Tareas Pendientes para Administrador
include_once __DIR__ . '/../../controlador/controladorTicket.php';
include_once __DIR__ . '/../../controlador/controladorUsuario.php';
include_once __DIR__ . '/../../config/CN.php';

$controladorTicket = new controladorTicket();
$controladorUsuario = new controladorUsuario();

// Obtener tickets
$todos_tickets = $controladorTicket->listarTickets();

// Categorizar tickets por tareas pendientes
$tareas = [
    'sin_asignar' => [],
    'criticos' => [],
    'pendientes' => [],
    'requieren_atencion' => []
];

foreach($todos_tickets as $ticket) {
    // Tickets sin asignar
    if(empty($ticket['id_asignado']) && $ticket['estado'] !== 'resuelto') {
        $tareas['sin_asignar'][] = $ticket;
    }
    
    // Tickets críticos
    if(in_array($ticket['prioridad'], ['alta', 'critica']) && $ticket['estado'] !== 'resuelto') {
        $tareas['criticos'][] = $ticket;
    }
    
    // Tickets pendientes
    if($ticket['estado'] === 'pendiente') {
        $tareas['pendientes'][] = $ticket;
    }
    
    // Tickets que llevan más de 2 días sin actualizar (simulado)
    $dias_sin_actualizar = (time() - strtotime($ticket['fecha_actualizacion'])) / (60 * 60 * 24);
    if($dias_sin_actualizar > 2 && $ticket['estado'] !== 'resuelto') {
        $tareas['requieren_atencion'][] = $ticket;
    }
}

// Obtener técnicos disponibles
$tecnicos = $controladorUsuario->modelo->listarTecnicos(true);
$tecnico_sugerido = $controladorUsuario->modelo->obtenerTecnicoConMenosTickets();

// Calcular estadísticas generales
$total_tareas_pendientes = count($tareas['sin_asignar']) + count($tareas['criticos']) + count($tareas['pendientes']);
?>

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
        
        .summary-card {
            background: white;
            padding: 25px;
            border-radius: 15px;
            box-shadow: 0 5px 15px rgba(0,0,0,0.1);
            margin-bottom: 30px;
            border-left: 5px solid #e74c3c;
        }
        
        .summary-card h2 {
            margin: 0 0 15px 0;
            color: #2c3e50;
            display: flex;
            align-items: center;
            gap: 10px;
        }
        
        .summary-stats {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(150px, 1fr));
            gap: 15px;
        }
        
        .summary-stat {
            text-align: center;
            padding: 15px;
            background: #f8f9fa;
            border-radius: 10px;
        }
        
        .summary-stat .number {
            font-size: 2rem;
            font-weight: bold;
            color: #e74c3c;
        }
        
        .summary-stat .label {
            color: #7f8c8d;
            font-size: 0.9rem;
            margin-top: 5px;
        }
        
        .task-section {
            background: white;
            padding: 25px;
            border-radius: 15px;
            box-shadow: 0 5px 15px rgba(0,0,0,0.1);
            margin-bottom: 25px;
        }
        
        .task-section h3 {
            margin: 0 0 20px 0;
            color: #2c3e50;
            display: flex;
            align-items: center;
            justify-content: space-between;
            padding-bottom: 15px;
            border-bottom: 2px solid #ecf0f1;
        }
        
        .task-count {
            background: #e74c3c;
            color: white;
            padding: 5px 15px;
            border-radius: 20px;
            font-size: 0.9rem;
            font-weight: bold;
        }
        
        .task-list {
            display: flex;
            flex-direction: column;
            gap: 15px;
        }
        
        .task-item {
            background: #f8f9fa;
            padding: 20px;
            border-radius: 10px;
            border-left: 4px solid #3498db;
            transition: all 0.3s ease;
        }
        
        .task-item:hover {
            transform: translateX(5px);
            box-shadow: 0 5px 15px rgba(0,0,0,0.1);
        }
        
        .task-item.critico {
            border-left-color: #e74c3c;
            background: #fff5f5;
        }
        
        .task-item.sin-asignar {
            border-left-color: #f39c12;
            background: #fffbf5;
        }
        
        .task-item.atencion {
            border-left-color: #9b59b6;
            background: #f9f5ff;
        }
        
        .task-header {
            display: flex;
            justify-content: space-between;
            align-items: start;
            margin-bottom: 10px;
        }
        
        .task-title {
            font-size: 1.1rem;
            font-weight: bold;
            color: #2c3e50;
            margin: 0 0 5px 0;
        }
        
        .task-meta {
            display: flex;
            gap: 15px;
            flex-wrap: wrap;
            margin-bottom: 10px;
        }
        
        .task-meta-item {
            display: flex;
            align-items: center;
            gap: 5px;
            color: #7f8c8d;
            font-size: 0.9rem;
        }
        
        .badge {
            padding: 5px 12px;
            border-radius: 15px;
            font-size: 0.85rem;
            font-weight: 600;
            display: inline-block;
        }
        
        .badge-critica {
            background: #d63031;
            color: white;
        }
        
        .badge-alta {
            background: #f8d7da;
            color: #721c24;
        }
        
        .badge-media {
            background: #fff3cd;
            color: #856404;
        }
        
        .badge-baja {
            background: #d1ecf1;
            color: #0c5460;
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
        
        .btn-success {
            background: #27ae60;
            color: white;
        }
        
        .btn-warning {
            background: #f39c12;
            color: white;
        }
        
        .btn:hover {
            transform: translateY(-2px);
            box-shadow: 0 4px 15px rgba(0,0,0,0.2);
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
        
        .recommendation-box {
            background: linear-gradient(135deg, #f8f9fa, #e9ecef);
            padding: 20px;
            border-radius: 10px;
            margin-bottom: 25px;
            border-left: 4px solid #27ae60;
        }
        
        .recommendation-box h4 {
            margin: 0 0 10px 0;
            color: #27ae60;
            display: flex;
            align-items: center;
            gap: 10px;
        }
        
        .quick-actions {
            display: flex;
            gap: 10px;
            flex-wrap: wrap;
            margin-top: 15px;
        }
    </style>
</head>
<body>
    <div class="container">
        <!-- Encabezado -->
        <div class="page-header">
            <h1>
                <i class="fas fa-clipboard-list"></i>
                Lista de Tareas Pendientes
            </h1>
            <p>Gestiona las tareas más importantes y asigna recursos eficientemente</p>
        </div>

        <!-- Resumen General -->
        <div class="summary-card">
            <h2>
                <i class="fas fa-chart-pie"></i>
                Resumen de Tareas Pendientes
            </h2>
            <div class="summary-stats">
                <div class="summary-stat">
                    <div class="number"><?php echo $total_tareas_pendientes; ?></div>
                    <div class="label">Total Tareas</div>
                </div>
                <div class="summary-stat">
                    <div class="number"><?php echo count($tareas['sin_asignar']); ?></div>
                    <div class="label">Sin Asignar</div>
                </div>
                <div class="summary-stat">
                    <div class="number"><?php echo count($tareas['criticos']); ?></div>
                    <div class="label">Críticos/Alta</div>
                </div>
                <div class="summary-stat">
                    <div class="number"><?php echo count($tecnicos); ?></div>
                    <div class="label">Técnicos Disponibles</div>
                </div>
            </div>
        </div>

        <!-- Recomendación de Técnico -->
        <?php if(!empty($tareas['sin_asignar']) && $tecnico_sugerido): ?>
        <div class="recommendation-box">
            <h4>
                <i class="fas fa-lightbulb"></i>
                Recomendación Inteligente
            </h4>
            <p>
                <strong><?php echo htmlspecialchars($tecnico_sugerido['nombre'] . ' ' . $tecnico_sugerido['apellido']); ?></strong> 
                es el técnico con menor carga de trabajo actual 
                (<?php echo $tecnico_sugerido['tickets_asignados']; ?> tickets activos).
                Considera asignarle los próximos tickets.
            </p>
            <div class="quick-actions">
                <a href="?url=gestion_tickets&asignacion=sin_asignar" class="btn btn-warning">
                    <i class="fas fa-user-plus"></i> Ir a Asignación
                </a>
                <a href="?url=personal_tecnico" class="btn btn-primary">
                    <i class="fas fa-users"></i> Ver Personal
                </a>
            </div>
        </div>
        <?php endif; ?>

        <!-- Tickets Sin Asignar -->
        <div class="task-section">
            <h3>
                <span>
                    <i class="fas fa-user-times"></i> Tickets Sin Asignar
                </span>
                <span class="task-count"><?php echo count($tareas['sin_asignar']); ?></span>
            </h3>
            
            <?php if(!empty($tareas['sin_asignar'])): ?>
            <div class="task-list">
                <?php foreach($tareas['sin_asignar'] as $ticket): ?>
                <div class="task-item sin-asignar">
                    <div class="task-header">
                        <div>
                            <h4 class="task-title">
                                #<?php echo $ticket['id_ticket']; ?> - <?php echo htmlspecialchars($ticket['titulo']); ?>
                            </h4>
                            <div class="task-meta">
                                <span class="task-meta-item">
                                    <i class="fas fa-user"></i>
                                    <?php echo htmlspecialchars($ticket['docente_nombre'] . ' ' . $ticket['docente_apellido']); ?>
                                </span>
                                <span class="task-meta-item">
                                    <i class="fas fa-calendar"></i>
                                    <?php echo date('d/m/Y H:i', strtotime($ticket['fecha_creacion'])); ?>
                                </span>
                                <span class="task-meta-item">
                                    <i class="fas fa-tag"></i>
                                    <?php echo htmlspecialchars($ticket['categoria_nombre']); ?>
                                </span>
                            </div>
                        </div>
                        <span class="badge badge-<?php echo $ticket['prioridad']; ?>">
                            <?php echo ucfirst($ticket['prioridad']); ?>
                        </span>
                    </div>
                    <div class="quick-actions">
                        <a href="?url=gestion_tickets#ticket-<?php echo $ticket['id_ticket']; ?>" class="btn btn-success">
                            <i class="fas fa-user-plus"></i> Asignar Ahora
                        </a>
                        <a href="chat_ticket.php?id=<?php echo $ticket['id_ticket']; ?>" class="btn btn-primary">
                            <i class="fas fa-eye"></i> Ver Detalles
                        </a>
                    </div>
                </div>
                <?php endforeach; ?>
            </div>
            <?php else: ?>
            <div class="empty-state">
                <i class="fas fa-check-circle"></i>
                <p>¡Excelente! Todos los tickets están asignados</p>
            </div>
            <?php endif; ?>
        </div>

        <!-- Tickets Críticos y de Alta Prioridad -->
        <div class="task-section">
            <h3>
                <span>
                    <i class="fas fa-exclamation-triangle"></i> Tickets Críticos y Alta Prioridad
                </span>
                <span class="task-count" style="background: #d63031;"><?php echo count($tareas['criticos']); ?></span>
            </h3>
            
            <?php if(!empty($tareas['criticos'])): ?>
            <div class="task-list">
                <?php foreach(array_slice($tareas['criticos'], 0, 5) as $ticket): ?>
                <div class="task-item critico">
                    <div class="task-header">
                        <div>
                            <h4 class="task-title">
                                #<?php echo $ticket['id_ticket']; ?> - <?php echo htmlspecialchars($ticket['titulo']); ?>
                            </h4>
                            <div class="task-meta">
                                <span class="task-meta-item">
                                    <i class="fas fa-user"></i>
                                    <?php echo htmlspecialchars($ticket['docente_nombre'] . ' ' . $ticket['docente_apellido']); ?>
                                </span>
                                <?php if(!empty($ticket['admin_nombre'])): ?>
                                <span class="task-meta-item">
                                    <i class="fas fa-user-tag"></i>
                                    Asignado a: <?php echo htmlspecialchars($ticket['admin_nombre'] . ' ' . $ticket['admin_apellido']); ?>
                                </span>
                                <?php endif; ?>
                                <span class="task-meta-item">
                                    <i class="fas fa-clock"></i>
                                    <?php echo date('d/m/Y H:i', strtotime($ticket['fecha_creacion'])); ?>
                                </span>
                            </div>
                        </div>
                        <span class="badge badge-<?php echo $ticket['prioridad']; ?>">
                            <?php echo ucfirst($ticket['prioridad']); ?>
                        </span>
                    </div>
                    <div class="quick-actions">
                        <a href="chat_ticket.php?id=<?php echo $ticket['id_ticket']; ?>" class="btn btn-primary">
                            <i class="fas fa-eye"></i> Ver y Atender
                        </a>
                    </div>
                </div>
                <?php endforeach; ?>
            </div>
            <?php else: ?>
            <div class="empty-state">
                <i class="fas fa-check-circle"></i>
                <p>¡Perfecto! No hay tickets críticos pendientes</p>
            </div>
            <?php endif; ?>
        </div>

        <!-- Tickets que Requieren Atención -->
        <div class="task-section">
            <h3>
                <span>
                    <i class="fas fa-bell"></i> Requieren Atención (Sin Actualizar)
                </span>
                <span class="task-count" style="background: #9b59b6;"><?php echo count($tareas['requieren_atencion']); ?></span>
            </h3>
            
            <?php if(!empty($tareas['requieren_atencion'])): ?>
            <div class="task-list">
                <?php foreach(array_slice($tareas['requieren_atencion'], 0, 5) as $ticket): ?>
                <div class="task-item atencion">
                    <div class="task-header">
                        <div>
                            <h4 class="task-title">
                                #<?php echo $ticket['id_ticket']; ?> - <?php echo htmlspecialchars($ticket['titulo']); ?>
                            </h4>
                            <div class="task-meta">
                                <span class="task-meta-item">
                                    <i class="fas fa-calendar-times"></i>
                                    Última actualización: <?php echo date('d/m/Y', strtotime($ticket['fecha_actualizacion'])); ?>
                                </span>
                                <?php if(!empty($ticket['admin_nombre'])): ?>
                                <span class="task-meta-item">
                                    <i class="fas fa-user-tag"></i>
                                    Asignado a: <?php echo htmlspecialchars($ticket['admin_nombre'] . ' ' . $ticket['admin_apellido']); ?>
                                </span>
                                <?php endif; ?>
                            </div>
                        </div>
                    </div>
                    <div class="quick-actions">
                        <a href="chat_ticket.php?id=<?php echo $ticket['id_ticket']; ?>" class="btn btn-warning">
                            <i class="fas fa-comment"></i> Solicitar Actualización
                        </a>
                        <a href="chat_ticket.php?id=<?php echo $ticket['id_ticket']; ?>" class="btn btn-primary">
                            <i class="fas fa-eye"></i> Ver Detalles
                        </a>
                    </div>
                </div>
                <?php endforeach; ?>
            </div>
            <?php else: ?>
            <div class="empty-state">
                <i class="fas fa-check-circle"></i>
                <p>Todos los tickets están siendo atendidos apropiadamente</p>
            </div>
            <?php endif; ?>
        </div>

        <!-- Acciones Rápidas -->
        <div class="task-section">
            <h3>
                <i class="fas fa-bolt"></i>
                Acciones Rápidas
            </h3>
            <div class="quick-actions">
                <a href="?url=gestion_tickets" class="btn btn-primary">
                    <i class="fas fa-tasks"></i> Ver Todos los Tickets
                </a>
                <a href="?url=personal_tecnico" class="btn btn-success">
                    <i class="fas fa-users"></i> Gestionar Personal
                </a>
                <a href="?url=usuarios" class="btn btn-warning">
                    <i class="fas fa-user-plus"></i> Agregar Técnico
                </a>
                <a href="?url=reportes" class="btn" style="background: #9b59b6; color: white;">
                    <i class="fas fa-chart-bar"></i> Ver Reportes
                </a>
            </div>
        </div>
    </div>
