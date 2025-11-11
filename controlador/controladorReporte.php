<?php
// Controlador de Reportes
include_once __DIR__ . "/../modelo/modeloTicket.php";
include_once __DIR__ . "/../modelo/modeloUsuario.php";
include_once __DIR__ . "/../config/CN.php";

class controladorReporte{
    public $modeloTicket;
    public $modeloUsuario;
    public $pdo;
    
    public function __construct(){
        $this->modeloTicket = new modeloTicket();
        $this->modeloUsuario = new modeloUsuario();
        $this->pdo = getConnection();
    }
    
    /**
     * Obtener tickets para reporte con filtros
     */
    public function obtenerTicketsReporte($filtros = []){
        $query = "SELECT t.*, 
                         c.nombre as categoria_nombre,
                         d.nombre as docente_nombre,
                         d.apellido as docente_apellido,
                         d.correo as docente_correo,
                         a.nombre as admin_nombre,
                         a.apellido as admin_apellido,
                         DATEDIFF(COALESCE(t.fecha_actualizacion, NOW()), t.fecha_creacion) as dias_abierto
                  FROM tickets t
                  LEFT JOIN categorias c ON t.id_categoria = c.id_categoria
                  LEFT JOIN usuarios d ON t.id_docente = d.id_usuario
                  LEFT JOIN usuarios a ON t.id_asignado = a.id_usuario
                  WHERE 1=1";
        
        $params = [];
        
        // Aplicar filtros
        if(!empty($filtros['fecha_inicio'])){
            $query .= " AND DATE(t.fecha_creacion) >= :fecha_inicio";
            $params['fecha_inicio'] = $filtros['fecha_inicio'];
        }
        
        if(!empty($filtros['fecha_fin'])){
            $query .= " AND DATE(t.fecha_creacion) <= :fecha_fin";
            $params['fecha_fin'] = $filtros['fecha_fin'];
        }
        
        if(!empty($filtros['estado'])){
            $query .= " AND t.estado = :estado";
            $params['estado'] = $filtros['estado'];
        }
        
        if(!empty($filtros['prioridad'])){
            $query .= " AND t.prioridad = :prioridad";
            $params['prioridad'] = $filtros['prioridad'];
        }
        
        if(!empty($filtros['categoria'])){
            $query .= " AND t.id_categoria = :categoria";
            $params['categoria'] = $filtros['categoria'];
        }
        
        if(!empty($filtros['docente'])){
            $query .= " AND t.id_docente = :docente";
            $params['docente'] = $filtros['docente'];
        }
        
        $query .= " ORDER BY t.fecha_creacion DESC";
        
        $stmt = $this->pdo->prepare($query);
        $stmt->execute($params);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
    
    /**
     * Estadísticas generales para reportes
     */
    public function obtenerEstadisticasGenerales($filtros = []){
        $tickets = $this->obtenerTicketsReporte($filtros);
        
        $stats = [
            'total' => count($tickets),
            'pendientes' => 0,
            'en_progreso' => 0,
            'resueltos' => 0,
            'rechazados' => 0,
            'criticos' => 0,
            'tiempo_promedio' => 0,
            'por_categoria' => [],
            'por_docente' => []
        ];
        
        $tiempos = [];
        
        foreach($tickets as $ticket){
            // Contar por estado
            switch($ticket['estado']){
                case 'pendiente': $stats['pendientes']++; break;
                case 'en_progreso': $stats['en_progreso']++; break;
                case 'resuelto': $stats['resueltos']++; break;
                case 'rechazado': $stats['rechazados']++; break;
            }
            
            // Contar críticos
            if($ticket['prioridad'] === 'critica'){
                $stats['criticos']++;
            }
            
            // Tiempos
            if($ticket['estado'] === 'resuelto'){
                $tiempos[] = $ticket['dias_abierto'];
            }
            
            // Por categoría
            $cat = $ticket['categoria_nombre'] ?? 'Sin categoría';
            if(!isset($stats['por_categoria'][$cat])){
                $stats['por_categoria'][$cat] = 0;
            }
            $stats['por_categoria'][$cat]++;
            
            // Por docente
            $doc = $ticket['docente_nombre'] . ' ' . $ticket['docente_apellido'];
            if(!isset($stats['por_docente'][$doc])){
                $stats['por_docente'][$doc] = 0;
            }
            $stats['por_docente'][$doc]++;
        }
        
        // Calcular tiempo promedio
        if(!empty($tiempos)){
            $stats['tiempo_promedio'] = round(array_sum($tiempos) / count($tiempos), 1);
        }
        
        return $stats;
    }
    
    /**
     * Reporte de mantenimiento preventivo
     */
    public function obtenerReporteMantenimientoPreventivo(){
        // Equipos/categorías con más incidencias
        $query = "SELECT c.nombre as categoria,
                         COUNT(*) as total_incidencias,
                         SUM(CASE WHEN t.estado = 'resuelto' THEN 1 ELSE 0 END) as resueltos,
                         SUM(CASE WHEN t.prioridad = 'critica' THEN 1 ELSE 0 END) as criticos,
                         MAX(t.fecha_creacion) as ultima_incidencia
                  FROM tickets t
                  LEFT JOIN categorias c ON t.id_categoria = c.id_categoria
                  GROUP BY t.id_categoria
                  ORDER BY total_incidencias DESC";
        
        $stmt = $this->pdo->prepare($query);
        $stmt->execute();
        $categorias = $stmt->fetchAll(PDO::FETCH_ASSOC);
        
        // Tickets recurrentes (mismas categorías del mismo docente)
        $query2 = "SELECT CONCAT(d.nombre, ' ', d.apellido) as docente,
                          c.nombre as categoria,
                          COUNT(*) as veces_reportado,
                          MAX(t.fecha_creacion) as ultima_vez
                   FROM tickets t
                   JOIN usuarios d ON t.id_docente = d.id_usuario
                   LEFT JOIN categorias c ON t.id_categoria = c.id_categoria
                   GROUP BY t.id_docente, t.id_categoria
                   HAVING veces_reportado > 2
                   ORDER BY veces_reportado DESC";
        
        $stmt2 = $this->pdo->prepare($query2);
        $stmt2->execute();
        $recurrentes = $stmt2->fetchAll(PDO::FETCH_ASSOC);
        
        // Tickets sin resolver por mucho tiempo
        $query3 = "SELECT t.id_ticket,
                          t.titulo,
                          CONCAT(d.nombre, ' ', d.apellido) as docente,
                          c.nombre as categoria,
                          t.prioridad,
                          t.estado,
                          t.fecha_creacion,
                          DATEDIFF(NOW(), t.fecha_creacion) as dias_sin_resolver
                   FROM tickets t
                   JOIN usuarios d ON t.id_docente = d.id_usuario
                   LEFT JOIN categorias c ON t.id_categoria = c.id_categoria
                   WHERE t.estado IN ('pendiente', 'en_progreso')
                   AND DATEDIFF(NOW(), t.fecha_creacion) > 7
                   ORDER BY dias_sin_resolver DESC";
        
        $stmt3 = $this->pdo->prepare($query3);
        $stmt3->execute();
        $sin_resolver = $stmt3->fetchAll(PDO::FETCH_ASSOC);
        
        return [
            'categorias_criticas' => $categorias,
            'problemas_recurrentes' => $recurrentes,
            'tickets_antiguos' => $sin_resolver
        ];
    }
    
    /**
     * Obtener categorías para filtros
     */
    public function obtenerCategorias(){
        $query = "SELECT * FROM categorias ORDER BY nombre";
        $stmt = $this->pdo->prepare($query);
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
    
    /**
     * Reporte de rendimiento de técnicos
     */
    public function obtenerRendimientoTecnicos($filtros = []){
        $where = "WHERE a.rol = 'administrador'";
        $params = [];
        
        if(!empty($filtros['fecha_inicio'])){
            $where .= " AND DATE(t.fecha_creacion) >= :fecha_inicio";
            $params['fecha_inicio'] = $filtros['fecha_inicio'];
        }
        
        if(!empty($filtros['fecha_fin'])){
            $where .= " AND DATE(t.fecha_creacion) <= :fecha_fin";
            $params['fecha_fin'] = $filtros['fecha_fin'];
        }
        
        $query = "SELECT CONCAT(a.nombre, ' ', a.apellido) as tecnico,
                         COUNT(t.id_ticket) as tickets_asignados,
                         SUM(CASE WHEN t.estado = 'resuelto' THEN 1 ELSE 0 END) as resueltos,
                         SUM(CASE WHEN t.estado = 'pendiente' THEN 1 ELSE 0 END) as pendientes,
                         AVG(DATEDIFF(COALESCE(t.fecha_actualizacion, NOW()), t.fecha_creacion)) as tiempo_promedio
                  FROM usuarios a
                  LEFT JOIN tickets t ON a.id_usuario = t.id_asignado
                  $where
                  GROUP BY a.id_usuario
                  ORDER BY tickets_asignados DESC";
        
        $stmt = $this->pdo->prepare($query);
        $stmt->execute($params);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
    
    /**
     * Reporte de Rendimiento de Técnicos
     */
    public function obtenerReporteRendimiento($filtros = []) {
        $where_conditions = ["u.rol = 'tecnico'"];
        $params = [];
        
        // Construir condiciones de fecha para los tickets
        $fecha_where = "";
        if(!empty($filtros['fecha_inicio']) && !empty($filtros['fecha_fin'])) {
            $fecha_where = " AND t.fecha_creacion BETWEEN :fecha_inicio AND :fecha_fin";
            $params['fecha_inicio'] = $filtros['fecha_inicio'];
            $params['fecha_fin'] = $filtros['fecha_fin'];
        }
        
        // Filtrar por técnico específico
        if(!empty($filtros['tecnico'])) {
            $where_conditions[] = "u.id_usuario = :id_tecnico";
            $params['id_tecnico'] = $filtros['tecnico'];
        }
        
        // Filtrar por estado
        $estado_where = "";
        if(!empty($filtros['estado'])) {
            $estado_where = " AND t.estado = :estado";
            $params['estado'] = $filtros['estado'];
        }
        
        // Filtrar por prioridad
        $prioridad_where = "";
        if(!empty($filtros['prioridad'])) {
            $prioridad_where = " AND t.prioridad = :prioridad";
            $params['prioridad'] = $filtros['prioridad'];
        }
        
        // Filtrar por categoría
        $categoria_where = "";
        if(!empty($filtros['categoria'])) {
            $categoria_where = " AND t.id_categoria = :categoria";
            $params['categoria'] = $filtros['categoria'];
        }
        
        $where = implode(' AND ', $where_conditions);
        
        $query = "SELECT 
                    u.id_usuario,
                    CONCAT(u.nombre, ' ', u.apellido) as tecnico,
                    u.correo,
                    COUNT(t.id_ticket) as total_tickets,
                    SUM(CASE WHEN t.estado = 'resuelto' THEN 1 ELSE 0 END) as resueltos,
                    SUM(CASE WHEN t.estado = 'en_progreso' THEN 1 ELSE 0 END) as en_progreso,
                    SUM(CASE WHEN t.estado = 'pendiente' THEN 1 ELSE 0 END) as pendientes,
                    ROUND(
                        AVG(
                            CASE 
                                WHEN t.estado = 'resuelto' 
                                THEN TIMESTAMPDIFF(HOUR, t.fecha_creacion, t.fecha_actualizacion)
                                ELSE NULL 
                            END
                        ), 1
                    ) as tiempo_promedio,
                    ROUND(
                        (SUM(CASE WHEN t.estado = 'resuelto' THEN 1 ELSE 0 END) / 
                        NULLIF(COUNT(t.id_ticket), 0)) * 100, 1
                    ) as tasa_resolucion
                  FROM usuarios u
                  LEFT JOIN tickets t ON u.id_usuario = t.id_asignado 
                    $fecha_where 
                    $estado_where 
                    $prioridad_where 
                    $categoria_where
                  WHERE $where
                  GROUP BY u.id_usuario, u.nombre, u.apellido, u.correo
                  HAVING total_tickets > 0
                  ORDER BY tasa_resolucion DESC, resueltos DESC";
        
        $stmt = $this->pdo->prepare($query);
        $stmt->execute($params);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
}
?>
