<?php
// Vista de Reportes y Exportación
include_once __DIR__ . '/../../controlador/controladorReporte.php';

$controladorReporte = new controladorReporte();

// Obtener categorías para filtros
$categorias = $controladorReporte->obtenerCategorias();

// Procesar vista previa
$mostrar_preview = false;
$tickets_preview = [];
$stats_preview = [];
$reporte_mantenimiento = [];
$reporte_rendimiento = [];

// Obtener lista de técnicos para filtro
include_once __DIR__ . '/../../controlador/controladorUsuario.php';
$controladorUsuario = new controladorUsuario();
$tecnicos = $controladorUsuario->modelo->listarTecnicos(false);

if(isset($_POST['generar_preview'])){
    $filtros = [
        'fecha_inicio' => $_POST['fecha_inicio'] ?? '',
        'fecha_fin' => $_POST['fecha_fin'] ?? '',
        'estado' => $_POST['estado'] ?? '',
        'prioridad' => $_POST['prioridad'] ?? '',
        'categoria' => $_POST['categoria'] ?? '',
        'tecnico' => $_POST['tecnico'] ?? ''
    ];
    
    $tipo_reporte = $_POST['tipo_reporte'];
    
    if($tipo_reporte === 'general'){
        $tickets_preview = $controladorReporte->obtenerTicketsReporte($filtros);
        $stats_preview = $controladorReporte->obtenerEstadisticasGenerales($filtros);
    } elseif($tipo_reporte === 'mantenimiento'){
        $reporte_mantenimiento = $controladorReporte->obtenerReporteMantenimientoPreventivo();
    } elseif($tipo_reporte === 'rendimiento'){
        $reporte_rendimiento = $controladorReporte->obtenerReporteRendimiento($filtros);
    }
    
    $mostrar_preview = true;
}
?>

<link rel="stylesheet" href="../../assets/css/dashboard.css">

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
    
    .report-types {
        display: grid;
        grid-template-columns: repeat(auto-fit, minmax(280px, 1fr));
        gap: 20px;
        margin-bottom: 30px;
    }
    
    .report-card {
        background: white;
        border-radius: 15px;
        padding: 25px;
        box-shadow: 0 5px 20px rgba(0,0,0,0.1);
        cursor: pointer;
        transition: all 0.3s ease;
        border: 3px solid transparent;
    }
    
    .report-card:hover {
        transform: translateY(-5px);
        box-shadow: 0 10px 30px rgba(0,0,0,0.15);
    }
    
    .report-card.active {
        border-color: #9b59b6;
        background: #f8f5fb;
    }
    
    .report-card input[type="radio"] {
        display: none;
    }
    
    .report-card-header {
        display: flex;
        align-items: center;
        gap: 15px;
        margin-bottom: 15px;
    }
    
    .report-icon {
        width: 60px;
        height: 60px;
        background: linear-gradient(135deg, #9b59b6, #8e44ad);
        border-radius: 15px;
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 1.8rem;
        color: white;
    }
    
    .report-title {
        font-size: 1.3rem;
        font-weight: 700;
        color: #2c3e50;
        margin: 0;
    }
    
    .report-description {
        color: #7f8c8d;
        line-height: 1.6;
        font-size: 0.95rem;
    }
    
    .filters-section {
        background: white;
        padding: 30px;
        border-radius: 15px;
        box-shadow: 0 5px 20px rgba(0,0,0,0.1);
        margin-bottom: 30px;
    }
    
    .filters-grid {
        display: grid;
        grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
        gap: 20px;
        margin-bottom: 25px;
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
    
    .form-control {
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
    
    .action-buttons {
        display: flex;
        gap: 15px;
        justify-content: center;
        flex-wrap: wrap;
    }
    
    .btn {
        padding: 14px 30px;
        border: none;
        border-radius: 10px;
        font-weight: 600;
        cursor: pointer;
        transition: all 0.3s ease;
        display: inline-flex;
        align-items: center;
        gap: 10px;
        font-size: 1rem;
        text-decoration: none;
    }
    
    .btn-primary {
        background: linear-gradient(135deg, #9b59b6, #8e44ad);
        color: white;
    }
    
    .btn-primary:hover {
        transform: translateY(-2px);
        box-shadow: 0 6px 20px rgba(155, 89, 182, 0.4);
    }
    
    .btn-success {
        background: linear-gradient(135deg, #27ae60, #229954);
        color: white;
    }
    
    .btn-success:hover {
        transform: translateY(-2px);
        box-shadow: 0 6px 20px rgba(39, 174, 96, 0.4);
    }
    
    .btn-excel {
        background: linear-gradient(135deg, #2ecc71, #27ae60);
        color: white;
    }
    
    .btn-pdf {
        background: linear-gradient(135deg, #e74c3c, #c0392b);
        color: white;
    }
    
    .preview-section {
        background: white;
        padding: 30px;
        border-radius: 15px;
        box-shadow: 0 5px 20px rgba(0,0,0,0.1);
        margin-top: 30px;
    }
    
    .stats-mini-grid {
        display: grid;
        grid-template-columns: repeat(auto-fit, minmax(150px, 1fr));
        gap: 15px;
        margin: 25px 0;
    }
    
    .stat-mini {
        background: #f8f9fa;
        padding: 15px;
        border-radius: 10px;
        text-align: center;
        border-left: 4px solid #9b59b6;
    }
    
    .stat-mini .number {
        font-size: 2rem;
        font-weight: bold;
        color: #9b59b6;
    }
    
    .stat-mini .label {
        font-size: 0.85rem;
        color: #7f8c8d;
        margin-top: 5px;
    }
    
    .preview-table {
        width: 100%;
        border-collapse: collapse;
        margin-top: 20px;
    }
    
    .preview-table th {
        background: #9b59b6;
        color: white;
        padding: 12px;
        text-align: left;
    }
    
    .preview-table td {
        padding: 12px;
        border-bottom: 1px solid #ecf0f1;
    }
    
    .preview-table tbody tr:hover {
        background: #f8f9fa;
    }
    
    .badge {
        padding: 5px 12px;
        border-radius: 15px;
        font-size: 0.85rem;
        font-weight: 600;
        display: inline-block;
    }
    
    .badge-pendiente { background: #f39c12; color: white; }
    .badge-en_progreso { background: #3498db; color: white; }
    .badge-resuelto { background: #27ae60; color: white; }
    .badge-rechazado { background: #e74c3c; color: white; }
    .badge-critica { background: #e74c3c; color: white; }
    .badge-alta { background: #e67e22; color: white; }
    .badge-media { background: #f39c12; color: white; }
    .badge-baja { background: #95a5a6; color: white; }
    
    .alert-info {
        background: #d1ecf1;
        border-left: 4px solid #0dcaf0;
        padding: 15px 20px;
        border-radius: 8px;
        margin: 20px 0;
        color: #0c5460;
    }
    
    .alert-warning {
        background: #fff3cd;
        border-left: 4px solid #f39c12;
        padding: 15px 20px;
        border-radius: 8px;
        margin: 20px 0;
        color: #856404;
    }
    
    .maintenance-section {
        margin-bottom: 40px;
    }
    
    .maintenance-section h3 {
        color: #2c3e50;
        margin-bottom: 15px;
        padding-bottom: 10px;
        border-bottom: 2px solid #9b59b6;
    }
</style>

<!-- Encabezado -->
<div class="page-header">
    <h1>
        <i class="fas fa-file-alt"></i>
        Reportes y Exportación
    </h1>
    <p>Genera reportes detallados y exporta en Excel o PDF</p>
</div>

<!-- Selector de Tipo de Reporte -->
<form method="POST" id="reportForm">
    <h2 style="color: #2c3e50; margin-bottom: 20px;">
        <i class="fas fa-clipboard-list"></i> Selecciona el Tipo de Reporte
    </h2>
    
    <div class="report-types">
        <!-- Reporte General -->
        <label class="report-card" onclick="selectReport(this)">
            <input type="radio" name="tipo_reporte" value="general" checked>
            <div class="report-card-header">
                <div class="report-icon">
                    <i class="fas fa-chart-bar"></i>
                </div>
                <h3 class="report-title">Reporte General</h3>
            </div>
            <p class="report-description">
                Lista completa de tickets con estadísticas generales, filtros por fecha, estado, prioridad y categoría.
            </p>
        </label>
        
        <!-- Reporte de Mantenimiento Preventivo -->
        <label class="report-card" onclick="selectReport(this)">
            <input type="radio" name="tipo_reporte" value="mantenimiento">
            <div class="report-card-header">
                <div class="report-icon">
                    <i class="fas fa-tools"></i>
                </div>
                <h3 class="report-title">Mantenimiento Preventivo</h3>
            </div>
            <p class="report-description">
                Identifica áreas críticas, problemas recurrentes y tickets sin resolver para planificar mantenimientos preventivos.
            </p>
        </label>
        
        <!-- Reporte de Rendimiento -->
        <label class="report-card" onclick="selectReport(this)">
            <input type="radio" name="tipo_reporte" value="rendimiento">
            <div class="report-card-header">
                <div class="report-icon">
                    <i class="fas fa-user-clock"></i>
                </div>
                <h3 class="report-title">Rendimiento Técnicos</h3>
            </div>
            <p class="report-description">
                Analiza el desempeño de cada técnico: tickets asignados, resueltos y tiempo promedio de resolución.
            </p>
        </label>
    </div>
    
    <!-- Filtros -->
    <div class="filters-section" id="filtersSection">
        <h3 style="color: #2c3e50; margin-bottom: 20px;">
            <i class="fas fa-filter"></i> Filtros (Opcional)
        </h3>
        
        <div class="filters-grid">
            <div class="filter-group">
                <label><i class="fas fa-calendar"></i> Fecha Inicio</label>
                <input type="date" name="fecha_inicio" class="form-control">
            </div>
            
            <div class="filter-group">
                <label><i class="fas fa-calendar"></i> Fecha Fin</label>
                <input type="date" name="fecha_fin" class="form-control">
            </div>
            
            <div class="filter-group" id="filtroEstado">
                <label><i class="fas fa-flag"></i> Estado</label>
                <select name="estado" class="form-control">
                    <option value="">Todos</option>
                    <option value="pendiente">Pendiente</option>
                    <option value="en_progreso">En Progreso</option>
                    <option value="resuelto">Resuelto</option>
                    <option value="rechazado">Rechazado</option>
                </select>
            </div>
            
            <div class="filter-group" id="filtroPrioridad">
                <label><i class="fas fa-exclamation-circle"></i> Prioridad</label>
                <select name="prioridad" class="form-control">
                    <option value="">Todas</option>
                    <option value="baja">Baja</option>
                    <option value="media">Media</option>
                    <option value="alta">Alta</option>
                    <option value="critica">Crítica</option>
                </select>
            </div>
            
            <div class="filter-group" id="filtroCategoria">
                <label><i class="fas fa-tag"></i> Categoría</label>
                <select name="categoria" class="form-control">
                    <option value="">Todas</option>
                    <?php foreach($categorias as $cat): ?>
                        <option value="<?php echo $cat['id_categoria']; ?>">
                            <?php echo htmlspecialchars($cat['nombre']); ?>
                        </option>
                    <?php endforeach; ?>
                </select>
            </div>
            
            <div class="filter-group" id="filtroTecnico" style="display: none;">
                <label><i class="fas fa-user-tie"></i> Técnico</label>
                <select name="tecnico" class="form-control">
                    <option value="">Todos los Técnicos</option>
                    <?php foreach($tecnicos as $tec): ?>
                        <option value="<?php echo $tec['id_usuario']; ?>">
                            <?php echo htmlspecialchars($tec['nombre'] . ' ' . $tec['apellido']); ?>
                        </option>
                    <?php endforeach; ?>
                </select>
            </div>
        </div>
        
        <div class="alert-info">
            <i class="fas fa-info-circle"></i>
            <strong>Tip:</strong> Los filtros varían según el tipo de reporte. El Reporte de Mantenimiento Preventivo analiza todos los tickets históricos sin filtros.
        </div>
        
        <div class="action-buttons">
            <button type="submit" name="generar_preview" class="btn btn-primary">
                <i class="fas fa-eye"></i>
                Vista Previa
            </button>
        </div>
    </div>
</form>

<!-- Vista Previa -->
<?php if($mostrar_preview): ?>
<div class="preview-section">
    <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 25px;">
        <h2 style="color: #2c3e50; margin: 0;">
            <i class="fas fa-eye"></i> Vista Previa del Reporte
        </h2>
        
        <div style="display: flex; gap: 10px;">
            <?php
            // Construir parámetros limpios para exportación
            $params_exportar = [
                'tipo' => $_POST['tipo_reporte'],
                'fecha_inicio' => $_POST['fecha_inicio'] ?? '',
                'fecha_fin' => $_POST['fecha_fin'] ?? '',
                'estado' => $_POST['estado'] ?? '',
                'prioridad' => $_POST['prioridad'] ?? '',
                'categoria' => $_POST['categoria'] ?? ''
            ];
            $query_string = http_build_query($params_exportar);
            ?>
            <a href="exportar_excel.php?<?php echo $query_string; ?>" 
               class="btn btn-excel" target="_blank">
                <i class="fas fa-file-excel"></i>
                Exportar a Excel
            </a>
            <a href="exportar_pdf.php?<?php echo $query_string; ?>" 
               class="btn btn-pdf" target="_blank">
                <i class="fas fa-file-pdf"></i>
                Exportar a PDF
            </a>
        </div>
    </div>
    
    <?php if($_POST['tipo_reporte'] === 'general'): ?>
        <!-- Preview Reporte General -->
        <div class="stats-mini-grid">
            <div class="stat-mini">
                <div class="number"><?php echo $stats_preview['total']; ?></div>
                <div class="label">Total Tickets</div>
            </div>
            <div class="stat-mini">
                <div class="number"><?php echo $stats_preview['resueltos']; ?></div>
                <div class="label">Resueltos</div>
            </div>
            <div class="stat-mini">
                <div class="number"><?php echo $stats_preview['pendientes']; ?></div>
                <div class="label">Pendientes</div>
            </div>
            <div class="stat-mini">
                <div class="number"><?php echo $stats_preview['tiempo_promedio']; ?></div>
                <div class="label">Días Promedio</div>
            </div>
        </div>
        
        <h3 style="color: #2c3e50; margin-top: 30px;">Primeros 20 Tickets</h3>
        <table class="preview-table">
            <thead>
                <tr>
                    <th>ID</th>
                    <th>Título</th>
                    <th>Docente</th>
                    <th>Prioridad</th>
                    <th>Estado</th>
                    <th>Fecha</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach(array_slice($tickets_preview, 0, 20) as $ticket): ?>
                <tr>
                    <td><strong>#<?php echo $ticket['id_ticket']; ?></strong></td>
                    <td><?php echo htmlspecialchars(substr($ticket['titulo'], 0, 40)); ?></td>
                    <td><?php echo htmlspecialchars($ticket['docente_nombre'] . ' ' . $ticket['docente_apellido']); ?></td>
                    <td>
                        <span class="badge badge-<?php echo $ticket['prioridad']; ?>">
                            <?php echo ucfirst($ticket['prioridad']); ?>
                        </span>
                    </td>
                    <td>
                        <span class="badge badge-<?php echo $ticket['estado']; ?>">
                            <?php echo ucfirst(str_replace('_', ' ', $ticket['estado'])); ?>
                        </span>
                    </td>
                    <td><?php echo date('d/m/Y', strtotime($ticket['fecha_creacion'])); ?></td>
                </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
        
        <?php if(count($tickets_preview) > 20): ?>
        <div class="alert-info">
            <i class="fas fa-info-circle"></i>
            Mostrando 20 de <?php echo count($tickets_preview); ?> tickets. Exporta el reporte completo para ver todos los datos.
        </div>
        <?php endif; ?>
        
    <?php elseif($_POST['tipo_reporte'] === 'mantenimiento'): ?>
        <!-- Preview Mantenimiento Preventivo -->
        
        <?php if(empty($reporte_mantenimiento['categorias_criticas']) && 
                 empty($reporte_mantenimiento['problemas_recurrentes']) && 
                 empty($reporte_mantenimiento['tickets_antiguos'])): ?>
            <div class="alert-warning">
                <i class="fas fa-info-circle"></i>
                <h3 style="margin: 0 0 10px 0;">No hay datos suficientes para el Reporte de Mantenimiento Preventivo</h3>
                <p style="margin: 0;">
                    Este reporte requiere tickets existentes en el sistema. Asegúrate de que:
                </p>
                <ul style="margin: 10px 0 0 20px; padding: 0;">
                    <li>Hay tickets creados en el sistema</li>
                    <li>Los tickets tienen categorías asignadas</li>
                    <li>Existen tickets con más de 7 días sin resolver</li>
                    <li>Hay problemas reportados múltiples veces</li>
                </ul>
                <p style="margin: 10px 0 0 0;">
                    <strong>Sugerencia:</strong> Crea algunos tickets de prueba y vuelve a generar el reporte.
                </p>
            </div>
        <?php else: ?>
        
        <div class="maintenance-section">
            <h3><i class="fas fa-exclamation-triangle"></i> Categorías con Más Incidencias</h3>
            <?php if(!empty($reporte_mantenimiento['categorias_criticas'])): ?>
            <table class="preview-table">
                <thead>
                    <tr>
                        <th>Categoría</th>
                        <th>Total Incidencias</th>
                        <th>Resueltos</th>
                        <th>Críticos</th>
                        <th>Última Incidencia</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach(array_slice($reporte_mantenimiento['categorias_criticas'], 0, 10) as $cat): ?>
                    <tr>
                        <td><strong><?php echo htmlspecialchars($cat['categoria'] ?? 'Sin categoría'); ?></strong></td>
                        <td><?php echo $cat['total_incidencias']; ?></td>
                        <td><?php echo $cat['resueltos']; ?></td>
                        <td><?php echo $cat['criticos']; ?></td>
                        <td><?php echo date('d/m/Y', strtotime($cat['ultima_incidencia'])); ?></td>
                    </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
            <?php else: ?>
            <div class="alert-info">
                <i class="fas fa-info-circle"></i>
                No hay categorías con incidencias registradas aún.
            </div>
            <?php endif; ?>
        </div>
        
        <?php if(!empty($reporte_mantenimiento['problemas_recurrentes'])): ?>
        <div class="maintenance-section">
            <h3><i class="fas fa-redo"></i> Problemas Recurrentes</h3>
            <div class="alert-warning">
                <i class="fas fa-exclamation-triangle"></i>
                <strong>Atención:</strong> Estos problemas se han reportado múltiples veces. Requieren atención prioritaria.
            </div>
            <table class="preview-table">
                <thead>
                    <tr>
                        <th>Docente</th>
                        <th>Categoría</th>
                        <th>Veces Reportado</th>
                        <th>Última Vez</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach(array_slice($reporte_mantenimiento['problemas_recurrentes'], 0, 10) as $rec): ?>
                    <tr>
                        <td><?php echo htmlspecialchars($rec['docente']); ?></td>
                        <td><?php echo htmlspecialchars($rec['categoria'] ?? 'Sin categoría'); ?></td>
                        <td><strong><?php echo $rec['veces_reportado']; ?> veces</strong></td>
                        <td><?php echo date('d/m/Y', strtotime($rec['ultima_vez'])); ?></td>
                    </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
        <?php endif; ?>
        
        <?php if(!empty($reporte_mantenimiento['tickets_antiguos'])): ?>
        <div class="maintenance-section">
            <h3><i class="fas fa-clock"></i> Tickets Sin Resolver (Más de 7 días)</h3>
            <table class="preview-table">
                <thead>
                    <tr>
                        <th>ID</th>
                        <th>Título</th>
                        <th>Docente</th>
                        <th>Prioridad</th>
                        <th>Días Sin Resolver</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach(array_slice($reporte_mantenimiento['tickets_antiguos'], 0, 15) as $ticket): ?>
                    <tr>
                        <td><strong>#<?php echo $ticket['id_ticket']; ?></strong></td>
                        <td><?php echo htmlspecialchars(substr($ticket['titulo'], 0, 40)); ?></td>
                        <td><?php echo htmlspecialchars($ticket['docente']); ?></td>
                        <td>
                            <span class="badge badge-<?php echo $ticket['prioridad']; ?>">
                                <?php echo ucfirst($ticket['prioridad']); ?>
                            </span>
                        </td>
                        <td><strong style="color: #e74c3c;"><?php echo $ticket['dias_sin_resolver']; ?> días</strong></td>
                    </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
        <?php endif; ?>
        
        <?php endif; // Fin del else de datos de mantenimiento ?>
        
    <?php elseif($_POST['tipo_reporte'] === 'rendimiento'): ?>
        <!-- Preview Rendimiento de Técnicos -->
        
        <?php if(!empty($reporte_rendimiento)): ?>
            <div class="stats-mini-grid">
                <div class="stat-mini">
                    <div class="number"><?php echo count($reporte_rendimiento); ?></div>
                    <div class="label">Técnicos Evaluados</div>
                </div>
                <div class="stat-mini">
                    <div class="number">
                        <?php echo array_sum(array_column($reporte_rendimiento, 'total_tickets')); ?>
                    </div>
                    <div class="label">Total Tickets</div>
                </div>
                <div class="stat-mini">
                    <div class="number">
                        <?php echo array_sum(array_column($reporte_rendimiento, 'resueltos')); ?>
                    </div>
                    <div class="label">Tickets Resueltos</div>
                </div>
                <div class="stat-mini">
                    <div class="number">
                        <?php 
                        $promedios = array_filter(array_column($reporte_rendimiento, 'tiempo_promedio'));
                        echo !empty($promedios) ? round(array_sum($promedios) / count($promedios), 1) : 0;
                        ?>h
                    </div>
                    <div class="label">Tiempo Promedio Global</div>
                </div>
            </div>
            
            <h3 style="color: #2c3e50; margin-top: 30px;">Desempeño por Técnico</h3>
            <table class="preview-table">
                <thead>
                    <tr>
                        <th>Técnico</th>
                        <th>Total Asignados</th>
                        <th>Resueltos</th>
                        <th>En Progreso</th>
                        <th>Pendientes</th>
                        <th>Tasa Resolución</th>
                        <th>Tiempo Promedio</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach($reporte_rendimiento as $tec_rendimiento): ?>
                    <tr>
                        <td>
                            <strong><?php echo htmlspecialchars($tec_rendimiento['tecnico']); ?></strong>
                            <br>
                            <small style="color: #7f8c8d;"><?php echo htmlspecialchars($tec_rendimiento['correo']); ?></small>
                        </td>
                        <td><strong><?php echo $tec_rendimiento['total_tickets']; ?></strong></td>
                        <td>
                            <span style="color: #27ae60; font-weight: bold;">
                                <?php echo $tec_rendimiento['resueltos']; ?>
                            </span>
                        </td>
                        <td>
                            <span style="color: #3498db;">
                                <?php echo $tec_rendimiento['en_progreso']; ?>
                            </span>
                        </td>
                        <td>
                            <span style="color: #f39c12;">
                                <?php echo $tec_rendimiento['pendientes']; ?>
                            </span>
                        </td>
                        <td>
                            <strong style="color: <?php 
                                $tasa = $tec_rendimiento['tasa_resolucion'];
                                echo $tasa >= 80 ? '#27ae60' : ($tasa >= 60 ? '#f39c12' : '#e74c3c');
                            ?>;">
                                <?php echo $tec_rendimiento['tasa_resolucion']; ?>%
                            </strong>
                        </td>
                        <td>
                            <?php echo $tec_rendimiento['tiempo_promedio']; ?> horas
                        </td>
                    </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
            
            <div class="alert-info" style="margin-top: 20px;">
                <i class="fas fa-info-circle"></i>
                <strong>Interpretación:</strong>
                <ul style="margin: 10px 0; padding-left: 20px;">
                    <li><strong>Tasa de Resolución:</strong> Verde (≥80%) = Excelente, Amarillo (60-79%) = Bueno, Rojo (<60%) = Necesita mejorar</li>
                    <li><strong>Tiempo Promedio:</strong> Tiempo en horas desde la asignación hasta la resolución del ticket</li>
                </ul>
            </div>
        <?php else: ?>
            <div class="alert-warning">
                <i class="fas fa-exclamation-triangle"></i>
                No hay datos de rendimiento disponibles con los filtros aplicados. 
                Intenta seleccionar un rango de fechas diferente o asegúrate de que hay tickets asignados a técnicos.
            </div>
        <?php endif; ?>
        
    <?php endif; ?>
</div>
<?php endif; ?>

<script>
function selectReport(card) {
    // Remover active de todas las tarjetas
    document.querySelectorAll('.report-card').forEach(c => c.classList.remove('active'));
    // Agregar active a la seleccionada
    card.classList.add('active');
    // Marcar el radio
    card.querySelector('input[type="radio"]').checked = true;
    
    // Mostrar/ocultar filtros según el tipo
    const tipoReporte = card.querySelector('input[type="radio"]').value;
    const filtroEstado = document.getElementById('filtroEstado');
    const filtroPrioridad = document.getElementById('filtroPrioridad');
    const filtroCategoria = document.getElementById('filtroCategoria');
    const filtroTecnico = document.getElementById('filtroTecnico');
    
    if(tipoReporte === 'mantenimiento') {
        // Mantenimiento: sin filtros
        filtroEstado.style.display = 'none';
        filtroPrioridad.style.display = 'none';
        filtroCategoria.style.display = 'none';
        filtroTecnico.style.display = 'none';
    } else if(tipoReporte === 'rendimiento') {
        // Rendimiento: filtros de fecha y técnico
        filtroEstado.style.display = 'flex';
        filtroPrioridad.style.display = 'flex';
        filtroCategoria.style.display = 'flex';
        filtroTecnico.style.display = 'flex';
    } else {
        // General: todos los filtros excepto técnico
        filtroEstado.style.display = 'flex';
        filtroPrioridad.style.display = 'flex';
        filtroCategoria.style.display = 'flex';
        filtroTecnico.style.display = 'none';
    }
}

// Activar la primera tarjeta por defecto
document.addEventListener('DOMContentLoaded', function() {
    document.querySelector('.report-card').classList.add('active');
});
</script>