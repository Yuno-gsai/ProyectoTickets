<?php
// Exportar reportes a PDF
session_start();
date_default_timezone_set('America/Mexico_City');

// Verificar acceso
if (!isset($_SESSION['usuario']) || $_SESSION['usuario']['rol'] !== 'administrador') {
    http_response_code(403);
    die('<h1>Acceso Denegado</h1><p>Solo administradores pueden generar reportes PDF.</p>');
}

// Incluir controlador
try {
    include_once __DIR__ . '/../../controlador/controladorReporte.php';
    $controladorReporte = new controladorReporte();
} catch (Exception $e) {
    die('<h1>Error</h1><p>No se pudo cargar el controlador: ' . htmlspecialchars($e->getMessage()) . '</p>');
}

// Obtener y limpiar filtros
$filtros = [
    'fecha_inicio' => trim($_GET['fecha_inicio'] ?? ''),
    'fecha_fin' => trim($_GET['fecha_fin'] ?? ''),
    'estado' => trim($_GET['estado'] ?? ''),
    'prioridad' => trim($_GET['prioridad'] ?? ''),
    'categoria' => trim($_GET['categoria'] ?? '')
];

$tipo_reporte = $_GET['tipo'] ?? 'general';
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Reporte de Tickets - <?php echo date('d/m/Y'); ?></title>
    <style>
        @page {
            margin: 1.5cm;
            size: A4;
        }
        * {
            box-sizing: border-box;
        }
        body {
            font-family: 'Arial', 'Helvetica', sans-serif;
            font-size: 11px;
            color: #333;
            margin: 0;
            padding: 20px;
            line-height: 1.4;
        }
        .header {
            text-align: center;
            margin-bottom: 30px;
            border-bottom: 3px solid #3498db;
            padding-bottom: 15px;
        }
        .header h1 {
            margin: 0;
            color: #2c3e50;
            font-size: 24px;
        }
        .header .subtitle {
            color: #7f8c8d;
            margin-top: 5px;
        }
        .info-box {
            background: #ecf0f1;
            padding: 15px;
            border-radius: 5px;
            margin-bottom: 20px;
        }
        table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 25px;
            page-break-inside: auto;
        }
        tr {
            page-break-inside: avoid;
            page-break-after: auto;
        }
        thead {
            display: table-header-group;
        }
        tfoot {
            display: table-footer-group;
        }
        th {
            background: #3498db;
            color: white;
            padding: 12px 8px;
            text-align: left;
            font-weight: bold;
        }
        td {
            padding: 10px 8px;
            border-bottom: 1px solid #ddd;
        }
        tr:nth-child(even) {
            background: #f8f9fa;
        }
        .badge {
            padding: 4px 10px;
            border-radius: 12px;
            font-size: 11px;
            font-weight: bold;
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
        .section-title {
            font-size: 18px;
            color: #2c3e50;
            margin-top: 30px;
            margin-bottom: 15px;
            border-bottom: 2px solid #3498db;
            padding-bottom: 5px;
        }
        .stats-grid {
            display: grid;
            grid-template-columns: repeat(4, 1fr);
            gap: 15px;
            margin-bottom: 30px;
        }
        .stat-card {
            background: #f8f9fa;
            padding: 15px;
            border-radius: 8px;
            text-align: center;
            border-left: 4px solid #3498db;
        }
        .stat-number {
            font-size: 32px;
            font-weight: bold;
            color: #2c3e50;
        }
        .stat-label {
            color: #7f8c8d;
            margin-top: 5px;
            font-size: 11px;
        }
        .footer {
            margin-top: 50px;
            text-align: center;
            color: #7f8c8d;
            font-size: 10px;
            border-top: 1px solid #ddd;
            padding-top: 10px;
        }
        .alert {
            background: #fff3cd;
            border-left: 4px solid #f39c12;
            padding: 15px;
            margin: 20px 0;
        }
        @media print {
            .no-print {
                display: none;
            }
            body {
                print-color-adjust: exact;
                -webkit-print-color-adjust: exact;
            }
        }
    </style>
</head>
<body>
    <!-- Botón de imprimir -->
    <div class="no-print" style="text-align: right; margin-bottom: 20px;">
        <button onclick="window.print()" style="padding: 10px 20px; background: #3498db; color: white; border: none; border-radius: 5px; cursor: pointer; font-size: 14px;">
            🖨️ Imprimir / Guardar como PDF
        </button>
        <button onclick="window.close()" style="padding: 10px 20px; background: #95a5a6; color: white; border: none; border-radius: 5px; cursor: pointer; font-size: 14px; margin-left: 10px;">
            ✖️ Cerrar
        </button>
    </div>

    <!-- Encabezado -->
    <div class="header">
        <h1>📊 Sistema de Gestión de Tickets</h1>
        <div class="subtitle">Reporte Generado el <?php echo date('d/m/Y H:i'); ?></div>
    </div>

    <?php if($tipo_reporte === 'general'): ?>
        <!-- REPORTE GENERAL -->
        <?php 
        try {
            $tickets = $controladorReporte->obtenerTicketsReporte($filtros);
            $stats = $controladorReporte->obtenerEstadisticasGenerales($filtros);
            
            if(!$tickets || !$stats) {
                echo '<div class="alert" style="background: #f8d7da; border-color: #e74c3c;">
                      <strong>⚠️ Error:</strong> No se pudieron cargar los datos del reporte.
                      </div>';
                exit;
            }
        } catch (Exception $e) {
            echo '<div class="alert" style="background: #f8d7da; border-color: #e74c3c;">
                  <strong>⚠️ Error:</strong> ' . htmlspecialchars($e->getMessage()) . '
                  </div>';
            exit;
        }
        ?>
        
        <div class="info-box">
            <strong>Filtros aplicados:</strong><br>
            <?php if(!empty($filtros['fecha_inicio'])): ?>
                Fecha inicio: <?php echo date('d/m/Y', strtotime($filtros['fecha_inicio'])); ?><br>
            <?php endif; ?>
            <?php if(!empty($filtros['fecha_fin'])): ?>
                Fecha fin: <?php echo date('d/m/Y', strtotime($filtros['fecha_fin'])); ?><br>
            <?php endif; ?>
            <?php if(empty($filtros['fecha_inicio']) && empty($filtros['fecha_fin'])): ?>
                Sin filtro de fechas (todos los tickets)
            <?php endif; ?>
        </div>

        <h2 class="section-title">Estadísticas Generales</h2>
        <div class="stats-grid">
            <div class="stat-card">
                <div class="stat-number"><?php echo $stats['total']; ?></div>
                <div class="stat-label">Total de Tickets</div>
            </div>
            <div class="stat-card">
                <div class="stat-number"><?php echo $stats['resueltos']; ?></div>
                <div class="stat-label">Resueltos</div>
            </div>
            <div class="stat-card">
                <div class="stat-number"><?php echo $stats['pendientes']; ?></div>
                <div class="stat-label">Pendientes</div>
            </div>
            <div class="stat-card">
                <div class="stat-number"><?php echo $stats['tiempo_promedio']; ?></div>
                <div class="stat-label">Días Promedio</div>
            </div>
        </div>

        <h2 class="section-title">Lista de Tickets</h2>
        <table>
            <thead>
                <tr>
                    <th style="width: 50px;">ID</th>
                    <th>Título</th>
                    <th>Docente</th>
                    <th>Categoría</th>
                    <th style="width: 80px;">Prioridad</th>
                    <th style="width: 80px;">Estado</th>
                    <th style="width: 100px;">Fecha</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach($tickets as $ticket): ?>
                <tr>
                    <td><strong>#<?php echo $ticket['id_ticket']; ?></strong></td>
                    <td><?php echo htmlspecialchars(substr($ticket['titulo'], 0, 50)); ?></td>
                    <td><?php echo htmlspecialchars($ticket['docente_nombre'] . ' ' . $ticket['docente_apellido']); ?></td>
                    <td><?php echo htmlspecialchars($ticket['categoria_nombre'] ?? 'Sin categoría'); ?></td>
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

    <?php elseif($tipo_reporte === 'mantenimiento'): ?>
        <!-- REPORTE DE MANTENIMIENTO PREVENTIVO -->
        <?php $reporte = $controladorReporte->obtenerReporteMantenimientoPreventivo(); ?>
        
        <h2 class="section-title">🔧 Reporte de Mantenimiento Preventivo</h2>
        
        <div class="alert">
            <strong>⚠️ Propósito del Reporte:</strong> Identificar áreas críticas que requieren atención preventiva para evitar futuras incidencias.
        </div>

        <!-- Categorías Críticas -->
        <h3 style="color: #e74c3c; margin-top: 30px;">📊 Categorías con Más Incidencias</h3>
        <table>
            <thead>
                <tr>
                    <th>Categoría</th>
                    <th style="width: 120px;">Total Incidencias</th>
                    <th style="width: 100px;">Resueltos</th>
                    <th style="width: 100px;">Críticos</th>
                    <th style="width: 120px;">Última Incidencia</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach($reporte['categorias_criticas'] as $cat): ?>
                <tr>
                    <td><strong><?php echo htmlspecialchars($cat['categoria'] ?? 'Sin categoría'); ?></strong></td>
                    <td style="text-align: center; font-weight: bold; color: #e74c3c;"><?php echo $cat['total_incidencias']; ?></td>
                    <td style="text-align: center;"><?php echo $cat['resueltos']; ?></td>
                    <td style="text-align: center; color: #e67e22;"><?php echo $cat['criticos']; ?></td>
                    <td><?php echo date('d/m/Y', strtotime($cat['ultima_incidencia'])); ?></td>
                </tr>
                <?php endforeach; ?>
            </tbody>
        </table>

        <!-- Problemas Recurrentes -->
        <?php if(!empty($reporte['problemas_recurrentes'])): ?>
        <h3 style="color: #f39c12; margin-top: 30px;">🔄 Problemas Recurrentes (Requieren Atención)</h3>
        <table>
            <thead>
                <tr>
                    <th>Docente</th>
                    <th>Categoría</th>
                    <th style="width: 150px;">Veces Reportado</th>
                    <th style="width: 120px;">Última Vez</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach($reporte['problemas_recurrentes'] as $rec): ?>
                <tr>
                    <td><?php echo htmlspecialchars($rec['docente']); ?></td>
                    <td><?php echo htmlspecialchars($rec['categoria'] ?? 'Sin categoría'); ?></td>
                    <td style="text-align: center; font-weight: bold; color: #f39c12;"><?php echo $rec['veces_reportado']; ?> veces</td>
                    <td><?php echo date('d/m/Y', strtotime($rec['ultima_vez'])); ?></td>
                </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
        <?php endif; ?>

        <!-- Tickets Antiguos -->
        <?php if(!empty($reporte['tickets_antiguos'])): ?>
        <h3 style="color: #e67e22; margin-top: 30px;">⏰ Tickets Sin Resolver (Más de 7 días)</h3>
        <table>
            <thead>
                <tr>
                    <th style="width: 50px;">ID</th>
                    <th>Título</th>
                    <th>Docente</th>
                    <th>Categoría</th>
                    <th style="width: 100px;">Prioridad</th>
                    <th style="width: 100px;">Días</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach($reporte['tickets_antiguos'] as $ticket): ?>
                <tr style="background: <?php echo $ticket['dias_sin_resolver'] > 14 ? '#ffebee' : ''; ?>">
                    <td><strong>#<?php echo $ticket['id_ticket']; ?></strong></td>
                    <td><?php echo htmlspecialchars(substr($ticket['titulo'], 0, 40)); ?></td>
                    <td><?php echo htmlspecialchars($ticket['docente']); ?></td>
                    <td><?php echo htmlspecialchars($ticket['categoria'] ?? 'Sin categoría'); ?></td>
                    <td>
                        <span class="badge badge-<?php echo $ticket['prioridad']; ?>">
                            <?php echo ucfirst($ticket['prioridad']); ?>
                        </span>
                    </td>
                    <td style="text-align: center; font-weight: bold; color: #e74c3c;">
                        <?php echo $ticket['dias_sin_resolver']; ?> días
                    </td>
                </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
        <?php endif; ?>

        <div class="alert" style="background: #d4edda; border-color: #27ae60; margin-top: 30px;">
            <strong>💡 Recomendaciones:</strong>
            <ul style="margin: 10px 0 0 20px;">
                <li>Realizar mantenimiento preventivo en las categorías con más incidencias</li>
                <li>Investigar y resolver los problemas recurrentes de forma permanente</li>
                <li>Priorizar la atención de tickets con más de 14 días sin resolver</li>
                <li>Capacitar a docentes en las áreas con más reportes</li>
            </ul>
        </div>

    <?php elseif($tipo_reporte === 'rendimiento'): ?>
        <!-- REPORTE DE RENDIMIENTO DE TÉCNICOS -->
        <?php $tecnicos = $controladorReporte->obtenerRendimientoTecnicos($filtros); ?>
        
        <h2 class="section-title">👨‍💻 Rendimiento de Técnicos</h2>
        
        <table>
            <thead>
                <tr>
                    <th>Técnico</th>
                    <th style="width: 120px;">Tickets Asignados</th>
                    <th style="width: 100px;">Resueltos</th>
                    <th style="width: 100px;">Pendientes</th>
                    <th style="width: 120px;">Tiempo Promedio</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach($tecnicos as $tec): ?>
                <tr>
                    <td><strong><?php echo htmlspecialchars($tec['tecnico']); ?></strong></td>
                    <td style="text-align: center;"><?php echo $tec['tickets_asignados']; ?></td>
                    <td style="text-align: center; color: #27ae60; font-weight: bold;"><?php echo $tec['resueltos']; ?></td>
                    <td style="text-align: center; color: #f39c12;"><?php echo $tec['pendientes']; ?></td>
                    <td style="text-align: center;"><?php echo round($tec['tiempo_promedio'], 1); ?> días</td>
                </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    <?php endif; ?>

    <!-- Footer -->
    <div class="footer">
        <p>Sistema de Gestión de Tickets - Generado el <?php echo date('d/m/Y H:i:s'); ?></p>
        <p>Este es un documento confidencial generado automáticamente por el sistema</p>
    </div>

    <script>
        // Auto-abrir diálogo de impresión si se solicita
        <?php if(isset($_GET['auto_print'])): ?>
        window.onload = function() {
            window.print();
        }
        <?php endif; ?>
    </script>
</body>
</html>
