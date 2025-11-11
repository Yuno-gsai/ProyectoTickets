<?php
require_once __DIR__ . '/../../controlador/controladorResumen.php';

$controladorResumen = new controladorResumen();

// Procesar formulario
$tipo_resumen = isset($_POST['tipo_resumen']) ? $_POST['tipo_resumen'] : 'diario';
$fecha_seleccionada = isset($_POST['fecha']) ? $_POST['fecha'] : date('Y-m-d');

// Obtener datos según el tipo
if ($tipo_resumen === 'semanal') {
    $resumen = $controladorResumen->obtenerResumenSemanal($fecha_seleccionada);
} else {
    $resumen = $controladorResumen->obtenerResumenDiario($fecha_seleccionada);
}

// Obtener alertas críticas
$alertas = $controladorResumen->obtenerAlertasCriticas();

// Obtener tickets antiguos
$tickets_antiguos = $controladorResumen->obtenerTicketsAntiguos(7);

// Obtener total de tickets en el sistema (para referencia)
$query_total = "SELECT COUNT(*) as total FROM tickets";
$stmt_total = $controladorResumen->pdo->prepare($query_total);
$stmt_total->execute();
$total_sistema = $stmt_total->fetch(PDO::FETCH_ASSOC)['total'];

$page_title = "Resúmenes y Alertas - Sistema de Soporte";
?>

<style>
    .alert-card {
        background: white;
        border-radius: 10px;
        padding: 20px;
        margin-bottom: 20px;
        border-left: 4px solid;
        box-shadow: 0 2px 4px rgba(0,0,0,0.1);
        display: flex;
        align-items: center;
        gap: 15px;
        transition: transform 0.3s;
    }
    
    .alert-card:hover {
        transform: translateX(5px);
    }
    
    .alert-icon {
        font-size: 2.5rem;
        width: 60px;
        height: 60px;
        display: flex;
        align-items: center;
        justify-content: center;
        border-radius: 50%;
        background: rgba(255,255,255,0.3);
    }
    
    .alert-content {
        flex: 1;
    }
    
    .alert-title {
        font-size: 1.1rem;
        font-weight: 600;
        margin-bottom: 5px;
    }
    
    .alert-message {
        color: #666;
        margin: 0;
    }
    
    .alert-badge {
        background: rgba(0,0,0,0.1);
        padding: 5px 15px;
        border-radius: 20px;
        font-weight: 600;
        font-size: 1.2rem;
    }
    
    .resume-selector {
        background: white;
        padding: 25px;
        border-radius: 10px;
        box-shadow: 0 2px 4px rgba(0,0,0,0.1);
        margin-bottom: 30px;
    }
    
    .resume-grid {
        display: grid;
        grid-template-columns: repeat(auto-fit, minmax(250px, 1fr));
        gap: 20px;
        margin-top: 20px;
    }
    
    .stat-card-summary {
        background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
        color: white;
        padding: 25px;
        border-radius: 10px;
        box-shadow: 0 4px 6px rgba(0,0,0,0.1);
    }
    
    .stat-card-summary .number {
        font-size: 2.5rem;
        font-weight: 700;
        margin-bottom: 10px;
    }
    
    .stat-card-summary .label {
        font-size: 0.9rem;
        opacity: 0.9;
    }
    
    .tickets-antiguos-section {
        background: white;
        padding: 25px;
        border-radius: 10px;
        box-shadow: 0 2px 4px rgba(0,0,0,0.1);
        margin-top: 20px;
    }
    
    .ticket-antiguo-item {
        border-left: 4px solid #e74c3c;
        background: #fff5f5;
        padding: 15px;
        margin-bottom: 15px;
        border-radius: 5px;
        display: flex;
        justify-content: space-between;
        align-items: center;
        flex-wrap: wrap;
        gap: 15px;
    }
    
    .ticket-antiguo-info {
        flex: 1;
        min-width: 200px;
    }
    
    .ticket-antiguo-info h4 {
        margin: 0 0 5px 0;
        color: #2c3e50;
    }
    
    .ticket-antiguo-info p {
        margin: 5px 0;
        color: #666;
        font-size: 0.9rem;
    }
    
    .ticket-antiguo-dias {
        font-size: 2rem;
        font-weight: 700;
        color: #e74c3c;
        text-align: center;
        min-width: 100px;
    }
    
    .tecnicos-table {
        width: 100%;
        background: white;
        border-radius: 10px;
        overflow: hidden;
        box-shadow: 0 2px 4px rgba(0,0,0,0.1);
        margin-top: 20px;
    }
    
    .tecnicos-table table {
        width: 100%;
        border-collapse: collapse;
    }
    
    .tecnicos-table th {
        background: #667eea;
        color: white;
        padding: 15px;
        text-align: left;
    }
    
    .tecnicos-table td {
        padding: 12px 15px;
        border-bottom: 1px solid #eee;
    }
    
    .tecnicos-table tr:hover {
        background: #f8f9fa;
    }
    
    .progress-bar {
        width: 100%;
        height: 8px;
        background: #e0e0e0;
        border-radius: 4px;
        overflow: hidden;
    }
    
    .progress-fill {
        height: 100%;
        background: linear-gradient(90deg, #667eea 0%, #764ba2 100%);
        transition: width 0.3s;
    }
</style>

<div class="page-header">
    <div>
        <h1><i class="fas fa-chart-line"></i> Resúmenes y Alertas</h1>
        <p>Monitoreo de tareas antiguas y resúmenes diarios/semanales del sistema</p>
    </div>
</div>

<!-- ALERTAS CRÍTICAS -->
<?php if(!empty($alertas)): ?>
<div class="section-header">
    <h2><i class="fas fa-bell"></i> Alertas Importantes</h2>
</div>

<div class="alerts-grid">
    <?php foreach($alertas as $alerta): ?>
    <div class="alert-card" style="border-left-color: <?php echo $alerta['color']; ?>;">
        <div class="alert-icon" style="color: <?php echo $alerta['color']; ?>;">
            <i class="fas <?php echo $alerta['icono']; ?>"></i>
        </div>
        <div class="alert-content">
            <div class="alert-title"><?php echo $alerta['titulo']; ?></div>
            <p class="alert-message"><?php echo $alerta['mensaje']; ?></p>
        </div>
        <div class="alert-badge" style="color: <?php echo $alerta['color']; ?>;">
            <?php echo $alerta['cantidad']; ?>
        </div>
    </div>
    <?php endforeach; ?>
</div>
<?php endif; ?>

<!-- TICKETS ANTIGUOS SIN RESOLVER -->
<?php if(!empty($tickets_antiguos)): ?>
<div class="section-header" style="margin-top: 30px;">
    <h2><i class="fas fa-clock"></i> Tickets Antiguos Sin Resolver (>7 días)</h2>
</div>

<div class="tickets-antiguos-section">
    <?php foreach(array_slice($tickets_antiguos, 0, 10) as $ticket): ?>
    <div class="ticket-antiguo-item">
        <div class="ticket-antiguo-info">
            <h4>
                #<?php echo $ticket['id_ticket']; ?> - <?php echo htmlspecialchars($ticket['titulo']); ?>
                <span class="badge badge-<?php echo $ticket['prioridad']; ?>">
                    <?php echo ucfirst($ticket['prioridad']); ?>
                </span>
            </h4>
            <p><i class="fas fa-user"></i> Docente: <?php echo htmlspecialchars($ticket['docente_nombre']); ?></p>
            <p><i class="fas fa-folder"></i> Categoría: <?php echo htmlspecialchars($ticket['categoria_nombre'] ?? 'Sin categoría'); ?></p>
            <?php if($ticket['asignado_nombre']): ?>
            <p><i class="fas fa-user-cog"></i> Asignado a: <?php echo htmlspecialchars($ticket['asignado_nombre']); ?></p>
            <?php else: ?>
            <p style="color: #e74c3c;"><i class="fas fa-exclamation-triangle"></i> Sin asignar</p>
            <?php endif; ?>
            <p><i class="fas fa-calendar"></i> Creado: <?php echo date('d/m/Y H:i', strtotime($ticket['fecha_creacion'])); ?></p>
        </div>
        <div class="ticket-antiguo-dias">
            <?php echo $ticket['dias_abierto']; ?>
            <div style="font-size: 0.8rem; font-weight: normal;">días</div>
        </div>
        <div>
            <a href="?url=gestion_tickets&id=<?php echo $ticket['id_ticket']; ?>" class="btn btn-primary">
                <i class="fas fa-eye"></i> Ver Ticket
            </a>
        </div>
    </div>
    <?php endforeach; ?>
    
    <?php if(count($tickets_antiguos) > 10): ?>
    <div class="alert-info" style="margin-top: 15px;">
        <i class="fas fa-info-circle"></i>
        Mostrando 10 de <?php echo count($tickets_antiguos); ?> tickets antiguos. Consulta la gestión de tickets para ver todos.
    </div>
    <?php endif; ?>
</div>
<?php else: ?>
<div class="alert-info" style="margin-top: 20px;">
    <i class="fas fa-check-circle"></i>
    <strong>¡Excelente!</strong> No hay tickets con más de 7 días sin resolver.
</div>
<?php endif; ?>

<!-- SELECTOR DE RESUMEN -->
<div class="section-header" style="margin-top: 40px;">
    <h2><i class="fas fa-calendar-alt"></i> Resúmenes del Sistema</h2>
</div>

<div class="resume-selector">
    <form method="POST" action="">
        <div class="filters-grid">
            <div class="filter-group">
                <label><i class="fas fa-chart-bar"></i> Tipo de Resumen</label>
                <select name="tipo_resumen" class="filter-select" onchange="this.form.submit()">
                    <option value="diario" <?php echo $tipo_resumen === 'diario' ? 'selected' : ''; ?>>Resumen Diario</option>
                    <option value="semanal" <?php echo $tipo_resumen === 'semanal' ? 'selected' : ''; ?>>Resumen Semanal</option>
                </select>
            </div>
            <div class="filter-group">
                <label><i class="fas fa-calendar"></i> 
                    <?php echo $tipo_resumen === 'semanal' ? 'Semana del' : 'Fecha'; ?>
                </label>
                <input type="date" name="fecha" class="filter-select" value="<?php echo $fecha_seleccionada; ?>" onchange="this.form.submit()">
            </div>
        </div>
    </form>
</div>

<!-- RESUMEN DIARIO -->
<?php if($tipo_resumen === 'diario'): ?>

<?php if($resumen['tickets_creados']['total'] == 0): ?>
<div class="alert-info" style="margin-bottom: 20px;">
    <i class="fas fa-info-circle"></i>
    <strong>Nota:</strong> No hay tickets creados el día <strong><?php echo date('d/m/Y', strtotime($fecha_seleccionada)); ?></strong>.
    <br>
    <small>
        El resumen diario muestra solo los tickets de la fecha seleccionada. 
        <strong>Total en el sistema: <?php echo $total_sistema; ?> tickets</strong>. 
        Cambia la fecha o usa el resumen semanal para ver más datos.
    </small>
</div>
<?php else: ?>
<div class="alert-info" style="margin-bottom: 20px;">
    <i class="fas fa-database"></i>
    <strong>Sistema:</strong> <?php echo $total_sistema; ?> tickets totales registrados | 
    Mostrando datos del <strong><?php echo date('d/m/Y', strtotime($fecha_seleccionada)); ?></strong>
</div>
<?php endif; ?>

<div class="resume-grid">
    <div class="stat-card-summary" style="background: linear-gradient(135deg, #3498db 0%, #2980b9 100%);">
        <div class="number"><?php echo $resumen['tickets_creados']['total'] ?? 0; ?></div>
        <div class="label">Tickets Creados (<?php echo date('d/m/Y', strtotime($fecha_seleccionada)); ?>)</div>
        <small style="display: block; margin-top: 10px; opacity: 0.9;">
            <?php echo ($resumen['tickets_creados']['criticos'] ?? 0); ?> críticos | 
            <?php echo ($resumen['tickets_creados']['altos'] ?? 0); ?> altos
        </small>
    </div>
    
    <div class="stat-card-summary" style="background: linear-gradient(135deg, #27ae60 0%, #229954 100%);">
        <div class="number"><?php echo $resumen['tickets_resueltos']['total'] ?? 0; ?></div>
        <div class="label">Tickets Resueltos</div>
        <small style="display: block; margin-top: 10px; opacity: 0.9;">
            Tiempo promedio: <?php echo round($resumen['tickets_resueltos']['tiempo_promedio'] ?? 0, 1); ?> días
        </small>
    </div>
    
    <div class="stat-card-summary" style="background: linear-gradient(135deg, #f39c12 0%, #e67e22 100%);">
        <div class="number"><?php echo $resumen['tickets_pendientes']['total'] ?? 0; ?></div>
        <div class="label">Tickets Pendientes</div>
        <small style="display: block; margin-top: 10px; opacity: 0.9;">
            <?php echo ($resumen['tickets_pendientes']['antiguos'] ?? 0); ?> con más de 7 días
        </small>
    </div>
    
    <div class="stat-card-summary" style="background: linear-gradient(135deg, #9b59b6 0%, #8e44ad 100%);">
        <div class="number"><?php echo count($resumen['actividad_tecnicos']); ?></div>
        <div class="label">Técnicos Activos</div>
        <small style="display: block; margin-top: 10px; opacity: 0.9;">
            Con actividad hoy
        </small>
    </div>
</div>

<?php if(!empty($resumen['actividad_tecnicos'])): ?>
<div class="tecnicos-table">
    <table>
        <thead>
            <tr>
                <th>Técnico</th>
                <th>Tickets Atendidos</th>
                <th>Resueltos</th>
                <th>Tasa de Resolución</th>
            </tr>
        </thead>
        <tbody>
            <?php foreach($resumen['actividad_tecnicos'] as $tec): ?>
            <tr>
                <td><strong><?php echo htmlspecialchars($tec['tecnico']); ?></strong></td>
                <td><?php echo $tec['tickets_atendidos']; ?></td>
                <td><?php echo $tec['resueltos']; ?></td>
                <td>
                    <?php 
                    $tasa = $tec['tickets_atendidos'] > 0 
                        ? round(($tec['resueltos'] / $tec['tickets_atendidos']) * 100, 1) 
                        : 0;
                    ?>
                    <div class="progress-bar">
                        <div class="progress-fill" style="width: <?php echo $tasa; ?>%;"></div>
                    </div>
                    <small><?php echo $tasa; ?>%</small>
                </td>
            </tr>
            <?php endforeach; ?>
        </tbody>
    </table>
</div>
<?php endif; ?>

<?php else: ?>
<!-- RESUMEN SEMANAL -->
<div class="resume-grid">
    <div class="stat-card-summary" style="background: linear-gradient(135deg, #3498db 0%, #2980b9 100%);">
        <div class="number"><?php echo $resumen['tickets_creados']['total'] ?? 0; ?></div>
        <div class="label">Tickets Creados en la Semana</div>
        <small style="display: block; margin-top: 10px; opacity: 0.9;">
            Críticos: <?php echo ($resumen['tickets_creados']['criticos'] ?? 0); ?> | 
            Altos: <?php echo ($resumen['tickets_creados']['altos'] ?? 0); ?>
        </small>
    </div>
    
    <div class="stat-card-summary" style="background: linear-gradient(135deg, #27ae60 0%, #229954 100%);">
        <div class="number"><?php echo $resumen['tickets_resueltos']['total'] ?? 0; ?></div>
        <div class="label">Tickets Resueltos</div>
        <small style="display: block; margin-top: 10px; opacity: 0.9;">
            Promedio: <?php echo round($resumen['tickets_resueltos']['tiempo_promedio'] ?? 0, 1); ?> días
        </small>
    </div>
    
    <div class="stat-card-summary" style="background: linear-gradient(135deg, #e74c3c 0%, #c0392b 100%);">
        <div class="number"><?php echo round($resumen['tickets_resueltos']['tiempo_maximo'] ?? 0, 1); ?></div>
        <div class="label">Tiempo Máximo de Resolución</div>
        <small style="display: block; margin-top: 10px; opacity: 0.9;">
            Mínimo: <?php echo round($resumen['tickets_resueltos']['tiempo_minimo'] ?? 0, 1); ?> días
        </small>
    </div>
    
    <div class="stat-card-summary" style="background: linear-gradient(135deg, #9b59b6 0%, #8e44ad 100%);">
        <div class="number"><?php echo count($resumen['rendimiento_tecnicos']); ?></div>
        <div class="label">Técnicos Activos</div>
        <small style="display: block; margin-top: 10px; opacity: 0.9;">
            En la semana
        </small>
    </div>
</div>

<!-- Categorías Más Reportadas -->
<?php if(!empty($resumen['categorias_top'])): ?>
<div class="section-header" style="margin-top: 30px;">
    <h3><i class="fas fa-chart-pie"></i> Categorías Más Reportadas</h3>
</div>
<div class="tecnicos-table">
    <table>
        <thead>
            <tr>
                <th>Categoría</th>
                <th>Total de Incidencias</th>
                <th>Porcentaje</th>
            </tr>
        </thead>
        <tbody>
            <?php 
            $total_tickets = $resumen['tickets_creados']['total'];
            foreach($resumen['categorias_top'] as $cat): 
                $porcentaje = $total_tickets > 0 ? round(($cat['total'] / $total_tickets) * 100, 1) : 0;
            ?>
            <tr>
                <td><strong><?php echo htmlspecialchars($cat['categoria'] ?? 'Sin categoría'); ?></strong></td>
                <td><?php echo $cat['total']; ?></td>
                <td>
                    <div class="progress-bar">
                        <div class="progress-fill" style="width: <?php echo $porcentaje; ?>%;"></div>
                    </div>
                    <small><?php echo $porcentaje; ?>%</small>
                </td>
            </tr>
            <?php endforeach; ?>
        </tbody>
    </table>
</div>
<?php endif; ?>

<!-- Rendimiento de Técnicos -->
<?php if(!empty($resumen['rendimiento_tecnicos'])): ?>
<div class="section-header" style="margin-top: 30px;">
    <h3><i class="fas fa-users"></i> Rendimiento de Técnicos (Semana)</h3>
</div>
<div class="tecnicos-table">
    <table>
        <thead>
            <tr>
                <th>Técnico</th>
                <th>Asignados</th>
                <th>Resueltos</th>
                <th>Tasa de Resolución</th>
                <th>Tiempo Promedio</th>
            </tr>
        </thead>
        <tbody>
            <?php foreach($resumen['rendimiento_tecnicos'] as $tec): ?>
            <tr>
                <td><strong><?php echo htmlspecialchars($tec['tecnico']); ?></strong></td>
                <td><?php echo $tec['total_asignados']; ?></td>
                <td><?php echo $tec['resueltos']; ?></td>
                <td>
                    <div class="progress-bar">
                        <div class="progress-fill" style="width: <?php echo $tec['tasa_resolucion']; ?>%;"></div>
                    </div>
                    <small><?php echo $tec['tasa_resolucion']; ?>%</small>
                </td>
                <td><?php echo $tec['tiempo_promedio'] ? round($tec['tiempo_promedio'], 1) . ' días' : 'N/A'; ?></td>
            </tr>
            <?php endforeach; ?>
        </tbody>
    </table>
</div>
<?php endif; ?>

<?php endif; ?>
