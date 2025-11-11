<?php
// Vista de Gestión de Personal Técnico
include_once __DIR__ . '/../../controlador/controladorUsuario.php';
include_once __DIR__ . '/../../config/CN.php';

$controladorUsuario = new controladorUsuario();

// Obtener técnicos
$tecnicos = $controladorUsuario->modelo->listarTecnicos(false);

// Obtener estadísticas de cada técnico y ticket activo
$pdo = getConnection();
include_once __DIR__ . '/../../modelo/modeloUsuario.php';
$modeloUsuario = new modeloUsuario();

foreach($tecnicos as &$tecnico) {
    $query = "SELECT 
                COUNT(*) as total_tickets,
                SUM(CASE WHEN estado = 'pendiente' THEN 1 ELSE 0 END) as pendientes,
                SUM(CASE WHEN estado = 'en_progreso' THEN 1 ELSE 0 END) as en_progreso,
                SUM(CASE WHEN estado = 'resuelto' THEN 1 ELSE 0 END) as resueltos
              FROM tickets 
              WHERE id_asignado = :id_tecnico";
    
    $stmt = $pdo->prepare($query);
    $stmt->execute(['id_tecnico' => $tecnico['id_usuario']]);
    $stats = $stmt->fetch(PDO::FETCH_ASSOC);
    
    // Obtener ticket activo si existe
    $tecnico['ticket_activo'] = $modeloUsuario->obtenerTicketActivoTecnico($tecnico['id_usuario']);
    $tecnico['disponible'] = $modeloUsuario->verificarDisponibilidadTecnico($tecnico['id_usuario']);
    
    $tecnico['stats'] = $stats;
}
unset($tecnico);

// Contar técnicos por estado
$activos = count(array_filter($tecnicos, fn($t) => $t['estado'] === 'activo'));
$inactivos = count($tecnicos) - $activos;

// Calcular totales generales
$total_tickets_global = 0;
$total_resueltos_global = 0;
$total_en_progreso_global = 0;
$total_pendientes_global = 0;

foreach($tecnicos as $tec) {
    $total_tickets_global += ($tec['stats']['total_tickets'] ?? 0);
    $total_resueltos_global += ($tec['stats']['resueltos'] ?? 0);
    $total_en_progreso_global += ($tec['stats']['en_progreso'] ?? 0);
    $total_pendientes_global += ($tec['stats']['pendientes'] ?? 0);
}

// Calcular tasa de resolución global
$tasa_resolucion_global = 0;
if($total_tickets_global > 0) {
    $tasa_resolucion_global = round(($total_resueltos_global / $total_tickets_global) * 100, 1);
}
?>

<style>
        .page-header {
            background: linear-gradient(135deg, #f39c12, #e67e22);
            color: white;
            padding: 30px;
            border-radius: 15px;
            margin-bottom: 30px;
            box-shadow: 0 10px 30px rgba(243, 156, 18, 0.3);
        }
        
        .page-header h1 {
            margin: 0 0 10px 0;
            font-size: 2rem;
            display: flex;
            align-items: center;
            gap: 15px;
        }
        
        .stats-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
            gap: 20px;
            margin-bottom: 30px;
        }
        
        .stat-card {
            background: white;
            padding: 25px;
            border-radius: 15px;
            box-shadow: 0 5px 15px rgba(0,0,0,0.1);
            text-align: center;
            transition: transform 0.3s ease;
        }
        
        .stat-card:hover {
            transform: translateY(-5px);
        }
        
        .stat-card .icon {
            font-size: 3rem;
            margin-bottom: 15px;
            color: #f39c12;
        }
        
        .stat-card .number {
            font-size: 2.5rem;
            font-weight: bold;
            color: #2c3e50;
            margin-bottom: 5px;
        }
        
        .stat-card .label {
            color: #7f8c8d;
            font-size: 0.9rem;
        }
        
        .tecnicos-grid {
            display: grid;
            grid-template-columns: repeat(auto-fill, minmax(350px, 1fr));
            gap: 25px;
            margin-top: 30px;
        }
        
        .tecnico-card {
            background: white;
            border-radius: 15px;
            padding: 25px;
            box-shadow: 0 5px 15px rgba(0,0,0,0.1);
            transition: all 0.3s ease;
            border-left: 5px solid #f39c12;
        }
        
        .tecnico-card:hover {
            transform: translateY(-5px);
            box-shadow: 0 10px 25px rgba(0,0,0,0.15);
        }
        
        .tecnico-card.inactivo {
            opacity: 0.6;
            border-left-color: #95a5a6;
        }
        
        .tecnico-header {
            display: flex;
            justify-content: space-between;
            align-items: start;
            margin-bottom: 20px;
            padding-bottom: 15px;
            border-bottom: 2px solid #ecf0f1;
        }
        
        .tecnico-info h3 {
            margin: 0 0 5px 0;
            color: #2c3e50;
            display: flex;
            align-items: center;
            gap: 10px;
        }
        
        .tecnico-info p {
            margin: 0;
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
        
        .badge-activo {
            background: #d4edda;
            color: #155724;
        }
        
        .badge-inactivo {
            background: #f8d7da;
            color: #721c24;
        }
        
        .tickets-stats {
            display: grid;
            grid-template-columns: repeat(2, 1fr);
            gap: 15px;
            margin-bottom: 15px;
        }
        
        .stat-item {
            background: #f8f9fa;
            padding: 12px;
            border-radius: 10px;
            text-align: center;
        }
        
        .stat-item .number {
            font-size: 1.8rem;
            font-weight: bold;
            color: #3498db;
        }
        
        .stat-item .label {
            color: #7f8c8d;
            font-size: 0.85rem;
            margin-top: 5px;
        }
        
        .stat-item.pendientes .number { color: #e74c3c; }
        .stat-item.en-progreso .number { color: #f39c12; }
        .stat-item.resueltos .number { color: #27ae60; }
        
        .actions {
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
            display: inline-flex;
            align-items: center;
            gap: 8px;
            text-decoration: none;
            justify-content: center;
            flex: 1;
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
        
        .empty-state {
            text-align: center;
            padding: 60px 20px;
            color: #95a5a6;
            background: white;
            border-radius: 15px;
            margin-top: 30px;
        }
        
        .empty-state i {
            font-size: 4rem;
            margin-bottom: 20px;
            opacity: 0.5;
        }
        
        .workload-bar {
            height: 8px;
            background: #ecf0f1;
            border-radius: 10px;
            overflow: hidden;
            margin: 10px 0;
        }
        
        .workload-fill {
            height: 100%;
            background: linear-gradient(90deg, #27ae60, #f39c12, #e74c3c);
            border-radius: 10px;
            transition: width 0.3s ease;
        }
        
        .workload-label {
            font-size: 0.85rem;
            color: #7f8c8d;
            margin-bottom: 5px;
        }
    </style>
</head>
<body>
    <div class="container">
        <!-- Encabezado -->
        <div class="page-header">
            <h1>
                <i class="fas fa-tools"></i>
                Gestión de Personal Técnico
            </h1>
            <p>Monitorea el rendimiento y carga de trabajo de tu equipo técnico</p>
        </div>

        <!-- Estadísticas Generales del Equipo -->
        <div class="stats-grid">
            <div class="stat-card">
                <div class="icon"><i class="fas fa-users"></i></div>
                <div class="number"><?php echo count($tecnicos); ?></div>
                <div class="label">Total Técnicos</div>
            </div>
            <div class="stat-card">
                <div class="icon"><i class="fas fa-user-check"></i></div>
                <div class="number"><?php echo $activos; ?></div>
                <div class="label">Activos</div>
            </div>
            <div class="stat-card">
                <div class="icon"><i class="fas fa-clipboard-list"></i></div>
                <div class="number"><?php echo $total_tickets_global; ?></div>
                <div class="label">Total Tickets Gestionados</div>
            </div>
            <div class="stat-card">
                <div class="icon"><i class="fas fa-check-circle"></i></div>
                <div class="number"><?php echo $total_resueltos_global; ?></div>
                <div class="label">Tickets Resueltos</div>
            </div>
            <div class="stat-card">
                <div class="icon"><i class="fas fa-spinner"></i></div>
                <div class="number"><?php echo $total_en_progreso_global; ?></div>
                <div class="label">En Progreso</div>
            </div>
            <div class="stat-card">
                <div class="icon"><i class="fas fa-percentage"></i></div>
                <div class="number"><?php echo $tasa_resolucion_global; ?>%</div>
                <div class="label">Tasa de Resolución</div>
            </div>
        </div>

        <!-- Lista de Técnicos -->
        <?php if(!empty($tecnicos)): ?>
        <div class="tecnicos-grid">
            <?php foreach($tecnicos as $tecnico): ?>
            <div class="tecnico-card <?php echo $tecnico['estado']; ?>">
                <div class="tecnico-header">
                    <div class="tecnico-info">
                        <h3>
                            <i class="fas fa-user-tie"></i>
                            <?php echo htmlspecialchars($tecnico['nombre'] . ' ' . $tecnico['apellido']); ?>
                        </h3>
                        <p><i class="fas fa-envelope"></i> <?php echo htmlspecialchars($tecnico['correo']); ?></p>
                    </div>
                    <span class="badge badge-<?php echo $tecnico['estado']; ?>">
                        <?php echo ucfirst($tecnico['estado']); ?>
                    </span>
                </div>
                
                <!-- Estado de Disponibilidad -->
                <?php if($tecnico['ticket_activo']): ?>
                <div class="alert-warning" style="margin: 15px 0; padding: 12px; border-radius: 8px; background: #fff3cd; border-left: 4px solid #ffc107;">
                    <div style="display: flex; align-items: center; gap: 10px;">
                        <div style="font-size: 1.5rem;">🔴</div>
                        <div style="flex: 1;">
                            <strong style="color: #856404;">OCUPADO - Trabajando en:</strong>
                            <div style="margin-top: 5px; font-size: 0.9rem;">
                                Ticket #<?php echo $tecnico['ticket_activo']['id_ticket']; ?>: 
                                <strong><?php echo htmlspecialchars($tecnico['ticket_activo']['titulo']); ?></strong>
                                <br>
                                <small style="color: #856404;">
                                    <i class="fas fa-clock"></i> 
                                    Trabajando hace <?php echo $tecnico['ticket_activo']['dias_trabajando']; ?> día(s) | 
                                    <i class="fas fa-exclamation-triangle"></i>
                                    <?php echo ucfirst($tecnico['ticket_activo']['prioridad']); ?>
                                </small>
                            </div>
                        </div>
                    </div>
                </div>
                <?php else: ?>
                <div class="alert-info" style="margin: 15px 0; padding: 12px; border-radius: 8px; background: #d1ecf1; border-left: 4px solid #17a2b8;">
                    <div style="display: flex; align-items: center; gap: 10px;">
                        <div style="font-size: 1.5rem;">✅</div>
                        <div>
                            <strong style="color: #0c5460;">DISPONIBLE</strong>
                            <br>
                            <small style="color: #0c5460;">Puede recibir nuevos tickets</small>
                        </div>
                    </div>
                </div>
                <?php endif; ?>

                <div class="tickets-stats">
                    <div class="stat-item">
                        <div class="number"><?php echo $tecnico['stats']['total_tickets'] ?? 0; ?></div>
                        <div class="label">Total Tickets</div>
                    </div>
                    <div class="stat-item pendientes">
                        <div class="number"><?php echo $tecnico['stats']['pendientes'] ?? 0; ?></div>
                        <div class="label">Pendientes</div>
                    </div>
                    <div class="stat-item en-progreso">
                        <div class="number"><?php echo $tecnico['stats']['en_progreso'] ?? 0; ?></div>
                        <div class="label">En Progreso</div>
                    </div>
                    <div class="stat-item resueltos">
                        <div class="number"><?php echo $tecnico['stats']['resueltos'] ?? 0; ?></div>
                        <div class="label">Resueltos</div>
                    </div>
                </div>
                
                <!-- Tasa de resolución individual -->
                <?php 
                $total_tec = $tecnico['stats']['total_tickets'] ?? 0;
                $resueltos_tec = $tecnico['stats']['resueltos'] ?? 0;
                $tasa_tec = 0;
                if($total_tec > 0) {
                    $tasa_tec = round(($resueltos_tec / $total_tec) * 100, 1);
                }
                ?>
                <div style="margin: 15px 0; padding: 10px; background: #f8f9fa; border-radius: 8px; text-align: center;">
                    <strong style="color: #3498db; font-size: 1.3rem;"><?php echo $tasa_tec; ?>%</strong>
                    <span style="color: #7f8c8d; font-size: 0.9rem; display: block; margin-top: 5px;">
                        Tasa de Resolución
                    </span>
                </div>

                <!-- Barra de carga de trabajo -->
                <div class="workload-label">Carga de trabajo actual:</div>
                <div class="workload-bar">
                    <?php 
                    $carga = ($tecnico['stats']['pendientes'] ?? 0) + ($tecnico['stats']['en_progreso'] ?? 0);
                    $porcentaje = min($carga * 10, 100); // 10% por cada ticket pendiente/en progreso
                    ?>
                    <div class="workload-fill" style="width: <?php echo $porcentaje; ?>%"></div>
                </div>

                <div class="actions">
                    <a href="?url=usuarios" class="btn btn-primary" title="Editar técnico">
                        <i class="fas fa-edit"></i> Editar
                    </a>
                    <a href="?url=reportes&tecnico=<?php echo $tecnico['id_usuario']; ?>" class="btn btn-success" title="Ver reportes">
                        <i class="fas fa-chart-bar"></i> Reportes
                    </a>
                </div>
            </div>
            <?php endforeach; ?>
        </div>
        <?php else: ?>
        <div class="empty-state">
            <i class="fas fa-user-slash"></i>
            <h3>No hay técnicos registrados</h3>
            <p>Crea técnicos desde la sección de Usuarios</p>
            <a href="?url=usuarios" class="btn btn-primary" style="display: inline-flex; margin-top: 20px;">
                <i class="fas fa-user-plus"></i> Ir a Usuarios
            </a>
        </div>
        <?php endif; ?>
    </div>
