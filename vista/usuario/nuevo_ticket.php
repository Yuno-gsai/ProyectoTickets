<?php

date_default_timezone_set('America/Mexico_City');

// Verificar sesión
if(!isset($_SESSION['usuario'])){
    header('Location: /proyectophp/index.php');
    exit;
}

include_once __DIR__ . '/../../controlador/controladorTicket.php';
include_once __DIR__ . '/../../config/CN.php';

$controlador = new controladorTicket();

// Obtener categorías
$pdo = getConnection();
$query = "SELECT * FROM categorias ORDER BY nombre ASC";
$stmt = $pdo->query($query);
$categorias = $stmt->fetchAll(PDO::FETCH_ASSOC);

// Procesar formulario
$mensaje_error = '';

if($_SERVER['REQUEST_METHOD'] === 'POST'){
    $datos = [
       'id_docente' => $_SESSION['usuario']['id'],
       'id_categoria' => intval($_POST['id_categoria']),
       'titulo' => trim($_POST['titulo']),
       'descripcion' => trim($_POST['descripcion']),
       'prioridad' => $_POST['prioridad']
    ];
    
    $resultado = $controlador->crearTicket($datos);
    
    if($resultado['success']){
        // Redirigir al chat del ticket recién creado
        $id_ticket = $resultado['id_ticket'];
        header("Location: /proyectophp/vista/usuario/chat_ticket.php?id=$id_ticket");
        exit;
    } else {
        $mensaje_error = $resultado['mensaje'];
    }
}
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Crear Ticket - Sistema de Soporte</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <style>
        * { margin: 0; padding: 0; box-sizing: border-box; }
        body { font-family: Arial, sans-serif; background: #f5f5f5; }
        
        .container { max-width: 800px; margin: 20px auto; padding: 20px; }
        
        .header {
            background: white;
            padding: 20px;
            border-radius: 8px;
            margin-bottom: 20px;
            box-shadow: 0 2px 4px rgba(0,0,0,0.1);
        }
        
        .header h1 { font-size: 24px; color: #333; }
        
        .formulario {
            background: white;
            padding: 30px;
            border-radius: 8px;
            box-shadow: 0 2px 4px rgba(0,0,0,0.1);
        }
        
        .form-group {
            margin-bottom: 20px;
        }
        
        .form-group label {
            display: block;
            margin-bottom: 8px;
            color: #333;
            font-weight: 500;
        }
        
        .form-group input,
        .form-group textarea,
        .form-group select {
            width: 100%;
            padding: 12px;
            border: 1px solid #ddd;
            border-radius: 6px;
            font-size: 14px;
            font-family: Arial, sans-serif;
        }
        
        .form-group textarea {
            min-height: 120px;
            resize: vertical;
        }
        
        .form-group input:focus,
        .form-group textarea:focus,
        .form-group select:focus {
            outline: none;
            border-color: #3498db;
        }
        
        .btn-group {
            display: flex;
            gap: 10px;
            margin-top: 25px;
        }
        
        .btn {
            padding: 12px 24px;
            border: none;
            border-radius: 6px;
            cursor: pointer;
            font-size: 14px;
            text-decoration: none;
            display: inline-block;
            text-align: center;
        }
        
        .btn-primary {
            background: #3498db;
            color: white;
        }
        
        .btn-primary:hover {
            background: #2980b9;
        }
        
        .btn-secondary {
            background: #6b7280;
            color: white;
        }
        
        .btn-secondary:hover {
            background: #4b5563;
        }
        
        .alerta {
            padding: 12px;
            border-radius: 6px;
            margin-bottom: 20px;
        }
        
        .alerta-error {
            background: #fee2e2;
            color: #dc2626;
            border: 1px solid #fecaca;
        }
        
        .requerido {
            color: #ef4444;
        }
        
        .volver {
            display: inline-block;
            margin-bottom: 15px;
            color: #3498db;
            text-decoration: none;
        }
        
        .volver:hover {
            text-decoration: underline;
        }
    </style>
</head>
<body>
    <div class="container">
        
        
        <div class="header">
            <h1><i class="fas fa-plus-circle"></i> Crear Nuevo Ticket</h1>
        </div>
        
        <?php if($mensaje_error): ?>
            <div class="alerta alerta-error">
                <i class="fas fa-exclamation-circle"></i> <?php echo $mensaje_error; ?>
            </div>
        <?php endif; ?>
        
        <form method="POST" class="formulario">
            <div class="form-group">
                <label>Título <span class="requerido">*</span></label>
                <input 
                    type="text" 
                    name="titulo" 
                    required 
                    maxlength="200"
                    placeholder="Describe brevemente el problema"
                >
            </div>
            
            <div class="form-group">
                <label>Categoría <span class="requerido">*</span></label>
                <select name="id_categoria" required>
                    <option value="">Selecciona una categoría</option>
                    <?php foreach($categorias as $cat): ?>
                        <option value="<?php echo $cat['id_categoria']; ?>">
                            <?php echo htmlspecialchars($cat['nombre']); ?>
                        </option>
                    <?php endforeach; ?>
                </select>
            </div>
            
            <div class="form-group">
                <label>Prioridad <span class="requerido">*</span></label>
                <select name="prioridad" required>
                    <option value="baja">Baja</option>
                    <option value="media" selected>Media</option>
                    <option value="alta">Alta</option>
                    <option value="critica">Crítica</option>
                </select>
            </div>
            
            <div class="form-group">
                <label>Descripción <span class="requerido">*</span></label>
                <textarea 
                    name="descripcion" 
                    required
                    placeholder="Describe detalladamente el problema que estás experimentando"
                ></textarea>
            </div>
            
            <div class="btn-group">
                <button type="submit" class="btn btn-primary">
                    <i class="fas fa-paper-plane"></i> Crear Ticket
                </button>
                <a href="/proyectophp/vista/admin/index.php" class="btn btn-secondary">
                    <i class="fas fa-times"></i> Cancelar
                </a>
            </div>
        </form>
    </div>
</body>
</html>