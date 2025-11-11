<?php
// Exportar reportes a Excel (CSV)
session_start();
date_default_timezone_set('America/Mexico_City');

if (!isset($_SESSION['usuario']) || $_SESSION['usuario']['rol'] !== 'administrador') {
    die('Acceso denegado');
}

include_once __DIR__ . '/../../controlador/controladorReporte.php';

$controladorReporte = new controladorReporte();

// Obtener filtros
$filtros = [
    'fecha_inicio' => $_GET['fecha_inicio'] ?? '',
    'fecha_fin' => $_GET['fecha_fin'] ?? '',
    'estado' => $_GET['estado'] ?? '',
    'prioridad' => $_GET['prioridad'] ?? '',
    'categoria' => $_GET['categoria'] ?? ''
];

$tipo_reporte = $_GET['tipo'] ?? 'general';

// Configurar headers para descarga
header('Content-Type: text/csv; charset=utf-8');
header('Content-Disposition: attachment; filename=reporte_tickets_' . date('Y-m-d_His') . '.csv');

// Crear archivo
$output = fopen('php://output', 'w');

// BOM para UTF-8
fprintf($output, chr(0xEF).chr(0xBB).chr(0xBF));

if($tipo_reporte === 'general'){
    // Reporte General
    $tickets = $controladorReporte->obtenerTicketsReporte($filtros);
    
    // Encabezados
    fputcsv($output, [
        'ID Ticket',
        'Título',
        'Docente',
        'Correo Docente',
        'Categoría',
        'Prioridad',
        'Estado',
        'Admin Asignado',
        'Fecha Creación',
        'Días Abierto'
    ]);
    
    // Datos
    foreach($tickets as $ticket){
        fputcsv($output, [
            $ticket['id_ticket'],
            $ticket['titulo'],
            $ticket['docente_nombre'] . ' ' . $ticket['docente_apellido'],
            $ticket['docente_correo'],
            $ticket['categoria_nombre'] ?? 'Sin categoría',
            ucfirst($ticket['prioridad']),
            ucfirst(str_replace('_', ' ', $ticket['estado'])),
            $ticket['admin_nombre'] ? $ticket['admin_nombre'] . ' ' . $ticket['admin_apellido'] : 'Sin asignar',
            date('d/m/Y H:i', strtotime($ticket['fecha_creacion'])),
            $ticket['dias_abierto']
        ]);
    }
    
} elseif($tipo_reporte === 'mantenimiento'){
    // Reporte de Mantenimiento Preventivo
    $reporte = $controladorReporte->obtenerReporteMantenimientoPreventivo();
    
    // Sección 1: Categorías Críticas
    fputcsv($output, ['CATEGORÍAS CON MÁS INCIDENCIAS']);
    fputcsv($output, ['Categoría', 'Total Incidencias', 'Resueltos', 'Críticos', 'Última Incidencia']);
    
    foreach($reporte['categorias_criticas'] as $cat){
        fputcsv($output, [
            $cat['categoria'] ?? 'Sin categoría',
            $cat['total_incidencias'],
            $cat['resueltos'],
            $cat['criticos'],
            date('d/m/Y', strtotime($cat['ultima_incidencia']))
        ]);
    }
    
    // Espacio
    fputcsv($output, []);
    fputcsv($output, []);
    
    // Sección 2: Problemas Recurrentes
    fputcsv($output, ['PROBLEMAS RECURRENTES']);
    fputcsv($output, ['Docente', 'Categoría', 'Veces Reportado', 'Última Vez']);
    
    foreach($reporte['problemas_recurrentes'] as $rec){
        fputcsv($output, [
            $rec['docente'],
            $rec['categoria'] ?? 'Sin categoría',
            $rec['veces_reportado'],
            date('d/m/Y', strtotime($rec['ultima_vez']))
        ]);
    }
    
    // Espacio
    fputcsv($output, []);
    fputcsv($output, []);
    
    // Sección 3: Tickets Antiguos Sin Resolver
    fputcsv($output, ['TICKETS SIN RESOLVER (MÁS DE 7 DÍAS)']);
    fputcsv($output, ['ID', 'Título', 'Docente', 'Categoría', 'Prioridad', 'Estado', 'Días Sin Resolver']);
    
    foreach($reporte['tickets_antiguos'] as $ticket){
        fputcsv($output, [
            $ticket['id_ticket'],
            $ticket['titulo'],
            $ticket['docente'],
            $ticket['categoria'] ?? 'Sin categoría',
            ucfirst($ticket['prioridad']),
            ucfirst(str_replace('_', ' ', $ticket['estado'])),
            $ticket['dias_sin_resolver']
        ]);
    }
    
} elseif($tipo_reporte === 'rendimiento'){
    // Reporte de Rendimiento de Técnicos
    $tecnicos = $controladorReporte->obtenerRendimientoTecnicos($filtros);
    
    fputcsv($output, [
        'Técnico',
        'Tickets Asignados',
        'Resueltos',
        'Pendientes',
        'Tiempo Promedio (días)'
    ]);
    
    foreach($tecnicos as $tec){
        fputcsv($output, [
            $tec['tecnico'],
            $tec['tickets_asignados'],
            $tec['resueltos'],
            $tec['pendientes'],
            round($tec['tiempo_promedio'], 1)
        ]);
    }
}

fclose($output);
exit();
?>
