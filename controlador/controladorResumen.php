<?php
require_once __DIR__ . '/../config/CN.php';
require_once __DIR__ . '/../modelo/modeloTicket.php';

class controladorResumen {
    public $modeloTicket;
    public $pdo;
    
    public function __construct() {
        $this->modeloTicket = new modeloTicket();
        $this->pdo = getConnection();
    }
    
    /**
     * Obtener tickets antiguos sin resolver (alertas)
     */
    public function obtenerTicketsAntiguos($dias_limite = 7) {
        $query = "SELECT t.id_ticket,
                         t.titulo,
                         t.descripcion,
                         t.prioridad,
                         t.estado,
                         t.fecha_creacion,
                         DATEDIFF(NOW(), t.fecha_creacion) as dias_abierto,
                         CONCAT(d.nombre, ' ', d.apellido) as docente_nombre,
                         d.correo as docente_correo,
                         c.nombre as categoria_nombre,
                         CONCAT(u.nombre, ' ', u.apellido) as asignado_nombre
                  FROM tickets t
                  JOIN usuarios d ON t.id_docente = d.id_usuario
                  LEFT JOIN categorias c ON t.id_categoria = c.id_categoria
                  LEFT JOIN usuarios u ON t.id_asignado = u.id_usuario
                  WHERE t.estado IN ('pendiente', 'en_progreso')
                  AND DATEDIFF(NOW(), t.fecha_creacion) >= :dias_limite
                  ORDER BY dias_abierto DESC, t.prioridad DESC";
        
        $stmt = $this->pdo->prepare($query);
        $stmt->execute(['dias_limite' => $dias_limite]);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
    
    /**
     * Obtener resumen diario
     */
    public function obtenerResumenDiario($fecha = null) {
        if (!$fecha) {
            $fecha = date('Y-m-d');
        }
        
        // Tickets creados hoy
        $query_creados = "SELECT COUNT(*) as total,
                                 SUM(CASE WHEN prioridad = 'critica' THEN 1 ELSE 0 END) as criticos,
                                 SUM(CASE WHEN prioridad = 'alta' THEN 1 ELSE 0 END) as altos
                          FROM tickets
                          WHERE DATE(fecha_creacion) = :fecha";
        
        $stmt = $this->pdo->prepare($query_creados);
        $stmt->execute(['fecha' => $fecha]);
        $creados = $stmt->fetch(PDO::FETCH_ASSOC);
        
        // Tickets resueltos hoy
        $query_resueltos = "SELECT COUNT(*) as total,
                                   AVG(DATEDIFF(fecha_actualizacion, fecha_creacion)) as tiempo_promedio
                            FROM tickets
                            WHERE DATE(fecha_actualizacion) = :fecha
                            AND estado = 'resuelto'";
        
        $stmt = $this->pdo->prepare($query_resueltos);
        $stmt->execute(['fecha' => $fecha]);
        $resueltos = $stmt->fetch(PDO::FETCH_ASSOC);
        
        // Tickets pendientes al final del día
        $query_pendientes = "SELECT COUNT(*) as total,
                                    SUM(CASE WHEN DATEDIFF(NOW(), fecha_creacion) > 7 THEN 1 ELSE 0 END) as antiguos
                             FROM tickets
                             WHERE estado IN ('pendiente', 'en_progreso')";
        
        $stmt = $this->pdo->prepare($query_pendientes);
        $stmt->execute();
        $pendientes = $stmt->fetch(PDO::FETCH_ASSOC);
        
        // Actividad por técnico hoy
        $query_tecnicos = "SELECT CONCAT(u.nombre, ' ', u.apellido) as tecnico,
                                  COUNT(*) as tickets_atendidos,
                                  SUM(CASE WHEN t.estado = 'resuelto' THEN 1 ELSE 0 END) as resueltos
                           FROM tickets t
                           JOIN usuarios u ON t.id_asignado = u.id_usuario
                           WHERE DATE(t.fecha_actualizacion) = :fecha
                           AND t.id_asignado IS NOT NULL
                           GROUP BY t.id_asignado";
        
        $stmt = $this->pdo->prepare($query_tecnicos);
        $stmt->execute(['fecha' => $fecha]);
        $actividad_tecnicos = $stmt->fetchAll(PDO::FETCH_ASSOC);
        
        return [
            'fecha' => $fecha,
            'tickets_creados' => $creados,
            'tickets_resueltos' => $resueltos,
            'tickets_pendientes' => $pendientes,
            'actividad_tecnicos' => $actividad_tecnicos
        ];
    }
    
    /**
     * Obtener resumen semanal
     */
    public function obtenerResumenSemanal($fecha_inicio = null) {
        if (!$fecha_inicio) {
            // Lunes de esta semana
            $fecha_inicio = date('Y-m-d', strtotime('monday this week'));
        }
        
        $fecha_fin = date('Y-m-d', strtotime($fecha_inicio . ' +6 days'));
        
        // Tickets creados en la semana
        $query_creados = "SELECT COUNT(*) as total,
                                 SUM(CASE WHEN prioridad = 'critica' THEN 1 ELSE 0 END) as criticos,
                                 SUM(CASE WHEN prioridad = 'alta' THEN 1 ELSE 0 END) as altos,
                                 SUM(CASE WHEN prioridad = 'media' THEN 1 ELSE 0 END) as medios,
                                 SUM(CASE WHEN prioridad = 'baja' THEN 1 ELSE 0 END) as bajos
                          FROM tickets
                          WHERE DATE(fecha_creacion) BETWEEN :fecha_inicio AND :fecha_fin";
        
        $stmt = $this->pdo->prepare($query_creados);
        $stmt->execute(['fecha_inicio' => $fecha_inicio, 'fecha_fin' => $fecha_fin]);
        $creados = $stmt->fetch(PDO::FETCH_ASSOC);
        
        // Tickets resueltos en la semana
        $query_resueltos = "SELECT COUNT(*) as total,
                                   AVG(DATEDIFF(fecha_actualizacion, fecha_creacion)) as tiempo_promedio,
                                   MIN(DATEDIFF(fecha_actualizacion, fecha_creacion)) as tiempo_minimo,
                                   MAX(DATEDIFF(fecha_actualizacion, fecha_creacion)) as tiempo_maximo
                            FROM tickets
                            WHERE DATE(fecha_actualizacion) BETWEEN :fecha_inicio AND :fecha_fin
                            AND estado = 'resuelto'";
        
        $stmt = $this->pdo->prepare($query_resueltos);
        $stmt->execute(['fecha_inicio' => $fecha_inicio, 'fecha_fin' => $fecha_fin]);
        $resueltos = $stmt->fetch(PDO::FETCH_ASSOC);
        
        // Categorías más reportadas
        $query_categorias = "SELECT c.nombre as categoria,
                                    COUNT(*) as total
                             FROM tickets t
                             LEFT JOIN categorias c ON t.id_categoria = c.id_categoria
                             WHERE DATE(t.fecha_creacion) BETWEEN :fecha_inicio AND :fecha_fin
                             GROUP BY t.id_categoria
                             ORDER BY total DESC
                             LIMIT 5";
        
        $stmt = $this->pdo->prepare($query_categorias);
        $stmt->execute(['fecha_inicio' => $fecha_inicio, 'fecha_fin' => $fecha_fin]);
        $categorias = $stmt->fetchAll(PDO::FETCH_ASSOC);
        
        // Rendimiento de técnicos en la semana
        $query_tecnicos = "SELECT CONCAT(u.nombre, ' ', u.apellido) as tecnico,
                                  COUNT(t.id_ticket) as total_asignados,
                                  SUM(CASE WHEN t.estado = 'resuelto' THEN 1 ELSE 0 END) as resueltos,
                                  ROUND(SUM(CASE WHEN t.estado = 'resuelto' THEN 1 ELSE 0 END) * 100.0 / COUNT(t.id_ticket), 1) as tasa_resolucion,
                                  AVG(CASE WHEN t.estado = 'resuelto' 
                                      THEN DATEDIFF(t.fecha_actualizacion, t.fecha_creacion) 
                                      ELSE NULL END) as tiempo_promedio
                           FROM tickets t
                           JOIN usuarios u ON t.id_asignado = u.id_usuario
                           WHERE DATE(t.fecha_creacion) BETWEEN :fecha_inicio AND :fecha_fin
                           AND t.id_asignado IS NOT NULL
                           GROUP BY t.id_asignado
                           ORDER BY resueltos DESC";
        
        $stmt = $this->pdo->prepare($query_tecnicos);
        $stmt->execute(['fecha_inicio' => $fecha_inicio, 'fecha_fin' => $fecha_fin]);
        $tecnicos = $stmt->fetchAll(PDO::FETCH_ASSOC);
        
        // Tickets por día de la semana (gráfica)
        $query_por_dia = "SELECT DATE(fecha_creacion) as fecha,
                                 DAYNAME(fecha_creacion) as dia,
                                 COUNT(*) as total
                          FROM tickets
                          WHERE DATE(fecha_creacion) BETWEEN :fecha_inicio AND :fecha_fin
                          GROUP BY DATE(fecha_creacion)
                          ORDER BY fecha";
        
        $stmt = $this->pdo->prepare($query_por_dia);
        $stmt->execute(['fecha_inicio' => $fecha_inicio, 'fecha_fin' => $fecha_fin]);
        $por_dia = $stmt->fetchAll(PDO::FETCH_ASSOC);
        
        return [
            'fecha_inicio' => $fecha_inicio,
            'fecha_fin' => $fecha_fin,
            'tickets_creados' => $creados,
            'tickets_resueltos' => $resueltos,
            'categorias_top' => $categorias,
            'rendimiento_tecnicos' => $tecnicos,
            'tickets_por_dia' => $por_dia
        ];
    }
    
    /**
     * Obtener alertas críticas
     */
    public function obtenerAlertasCriticas() {
        $alertas = [];
        
        // Tickets críticos sin asignar
        $query1 = "SELECT COUNT(*) as total
                   FROM tickets
                   WHERE prioridad = 'critica'
                   AND estado = 'pendiente'
                   AND id_asignado IS NULL";
        
        $stmt = $this->pdo->prepare($query1);
        $stmt->execute();
        $criticos_sin_asignar = $stmt->fetch(PDO::FETCH_ASSOC)['total'];
        
        if ($criticos_sin_asignar > 0) {
            $alertas[] = [
                'tipo' => 'critico',
                'titulo' => 'Tickets Críticos Sin Asignar',
                'mensaje' => "$criticos_sin_asignar ticket(s) crítico(s) esperan asignación",
                'cantidad' => $criticos_sin_asignar,
                'icono' => 'fa-exclamation-circle',
                'color' => '#e74c3c'
            ];
        }
        
        // Tickets muy antiguos (>14 días)
        $query2 = "SELECT COUNT(*) as total
                   FROM tickets
                   WHERE estado IN ('pendiente', 'en_progreso')
                   AND DATEDIFF(NOW(), fecha_creacion) > 14";
        
        $stmt = $this->pdo->prepare($query2);
        $stmt->execute();
        $muy_antiguos = $stmt->fetch(PDO::FETCH_ASSOC)['total'];
        
        if ($muy_antiguos > 0) {
            $alertas[] = [
                'tipo' => 'advertencia',
                'titulo' => 'Tickets Muy Antiguos',
                'mensaje' => "$muy_antiguos ticket(s) con más de 14 días sin resolver",
                'cantidad' => $muy_antiguos,
                'icono' => 'fa-clock',
                'color' => '#f39c12'
            ];
        }
        
        // Técnicos sobrecargados (>10 tickets asignados)
        $query3 = "SELECT CONCAT(u.nombre, ' ', u.apellido) as tecnico,
                          COUNT(*) as total
                   FROM tickets t
                   JOIN usuarios u ON t.id_asignado = u.id_usuario
                   WHERE t.estado IN ('pendiente', 'en_progreso')
                   GROUP BY t.id_asignado
                   HAVING total > 10";
        
        $stmt = $this->pdo->prepare($query3);
        $stmt->execute();
        $sobrecargados = $stmt->fetchAll(PDO::FETCH_ASSOC);
        
        if (count($sobrecargados) > 0) {
            $nombres = implode(', ', array_column($sobrecargados, 'tecnico'));
            $alertas[] = [
                'tipo' => 'info',
                'titulo' => 'Técnicos Sobrecargados',
                'mensaje' => count($sobrecargados) . " técnico(s) con más de 10 tickets activos: $nombres",
                'cantidad' => count($sobrecargados),
                'icono' => 'fa-users',
                'color' => '#3498db'
            ];
        }
        
        return $alertas;
    }
    
    /**
     * Generar notificaciones automáticas para tareas antiguas
     */
    public function generarNotificacionesAutomaticas() {
        require_once __DIR__ . '/../modelo/modeloNotificacion.php';
        $modeloNotif = new modeloNotificacion();
        
        // Obtener tickets antiguos que no han sido notificados hoy
        $tickets_antiguos = $this->obtenerTicketsAntiguos(7);
        
        $notificaciones_creadas = 0;
        
        foreach ($tickets_antiguos as $ticket) {
            // Verificar si ya se notificó hoy sobre este ticket
            $query = "SELECT COUNT(*) as total
                      FROM notificaciones
                      WHERE tipo = 'ticket_antiguo'
                      AND id_ticket = :id_ticket
                      AND DATE(fecha) = CURDATE()";
            
            $stmt = $this->pdo->prepare($query);
            $stmt->execute(['id_ticket' => $ticket['id_ticket']]);
            $ya_notificado = $stmt->fetch(PDO::FETCH_ASSOC)['total'] > 0;
            
            if (!$ya_notificado) {
                // Notificar al administrador
                $mensaje = "El ticket #{$ticket['id_ticket']} '{$ticket['titulo']}' lleva {$ticket['dias_abierto']} días sin resolver";
                
                // Obtener admins
                $query_admin = "SELECT id_usuario FROM usuarios WHERE rol = 'administrador'";
                $stmt_admin = $this->pdo->prepare($query_admin);
                $stmt_admin->execute();
                $admins = $stmt_admin->fetchAll(PDO::FETCH_ASSOC);
                
                foreach ($admins as $admin) {
                    $modeloNotif->crear([
                        'id_usuario' => $admin['id_usuario'],
                        'tipo' => 'ticket_antiguo',
                        'mensaje' => $mensaje,
                        'id_ticket' => $ticket['id_ticket']
                    ]);
                    $notificaciones_creadas++;
                }
            }
        }
        
        return $notificaciones_creadas;
    }
}
?>
