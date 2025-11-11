<?php
// vista/tickets/chat_ticket.php
session_start();
date_default_timezone_set('America/Mexico_City');

// Verificar sesión
if(!isset($_SESSION['usuario'])){
    header('Location: /proyectophp/index.php');
    exit;
}

include_once __DIR__ . '/../../controlador/controladorMensaje.php';
include_once __DIR__ . '/../../controlador/controladorTicket.php';

$controladorMensaje = new controladorMensaje();
$controladorTicket = new controladorTicket();

$id_ticket = isset($_GET['id']) ? intval($_GET['id']) : 0;
$id_usuario = $_SESSION['usuario']['id'];
$nombre_usuario = $_SESSION['usuario']['nombre'] . ' ' . $_SESSION['usuario']['apellido'];

// Obtener información del ticket
$ticket = $controladorTicket->obtenerTicket($id_ticket);

if(!$ticket){
    die("Ticket no encontrado");
}

// Procesar envío de mensaje
$mensaje_error = '';
$mensaje_exito = '';

if($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['enviar_mensaje'])){
    $mensaje = trim($_POST['mensaje']);
    
    if(!empty($mensaje)){
        $resultado = $controladorMensaje->enviarMensaje(
            $id_ticket, 
            $id_usuario, 
            $mensaje, 
            $nombre_usuario
        );
        
        if($resultado['success']){
            $mensaje_exito = $resultado['mensaje'];
            header("Location: chat_ticket.php?id=$id_ticket&enviado=1");
            exit;
        } else {
            $mensaje_error = $resultado['mensaje'];
        }
    } else {
        $mensaje_error = "El mensaje no puede estar vacío";
    }
}

// Obtener mensajes del ticket
$mensajes = $controladorMensaje->obtenerMensajesTicket($id_ticket);
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Chat - Ticket #<?php echo $id_ticket; ?></title>
    <style>
        * { margin: 0; padding: 0; box-sizing: border-box; }
        body { font-family: Arial, sans-serif; background: #f5f5f5; }
        
        .container { max-width: 900px; margin: 20px auto; padding: 20px; }
        
        .header {
            background: white;
            padding: 20px;
            border-radius: 8px;
            margin-bottom: 20px;
            box-shadow: 0 2px 4px rgba(0,0,0,0.1);
        }
        
        .header h1 { font-size: 20px; color: #333; margin-bottom: 10px; }
        .header p { color: #666; font-size: 14px; }
        
        .badge {
            display: inline-block;
            padding: 4px 8px;
            border-radius: 4px;
            font-size: 12px;
            margin-left: 10px;
        }
        
        .badge-pendiente { background: #fef3c7; color: #d97706; }
        .badge-en_progreso { background: #dbeafe; color: #2563eb; }
        .badge-resuelto { background: #d1fae5; color: #059669; }
        
        .chat-container {
            background: white;
            border-radius: 8px;
            box-shadow: 0 2px 4px rgba(0,0,0,0.1);
            height: 500px;
            display: flex;
            flex-direction: column;
        }
        
        .mensajes {
            flex: 1;
            padding: 20px;
            overflow-y: auto;
        }
        
        .mensaje {
            margin-bottom: 15px;
            display: flex;
            flex-direction: column;
        }
        
        .mensaje-propio {
            align-items: flex-end;
        }
        
        .mensaje-otro {
            align-items: flex-start;
        }
        
        .mensaje-contenido {
            max-width: 70%;
            padding: 10px 15px;
            border-radius: 8px;
            word-wrap: break-word;
        }
        
        .mensaje-propio .mensaje-contenido {
            background: #3498db;
            color: white;
        }
        
        .mensaje-otro .mensaje-contenido {
            background: #e5e7eb;
            color: #333;
        }
        
        .mensaje-info {
            font-size: 11px;
            color: #666;
            margin-top: 5px;
        }
        
        .form-enviar {
            padding: 20px;
            border-top: 1px solid #e5e7eb;
        }
        
        .input-group {
            display: flex;
            gap: 10px;
        }
        
        .input-mensaje {
            flex: 1;
            padding: 12px;
            border: 1px solid #ddd;
            border-radius: 6px;
            font-size: 14px;
            font-family: Arial, sans-serif;
            resize: vertical;
            min-height: 60px;
        }
        
        .input-mensaje:focus {
            outline: none;
            border-color: #3498db;
        }
        
        .btn-enviar {
            padding: 12px 24px;
            background: #3498db;
            color: white;
            border: none;
            border-radius: 6px;
            cursor: pointer;
            font-size: 14px;
            align-self: flex-end;
        }
        
        .btn-enviar:hover {
            background: #2980b9;
        }
        
        .alerta {
            padding: 12px;
            border-radius: 6px;
            margin-bottom: 15px;
        }
        
        .alerta-error {
            background: #fee2e2;
            color: #dc2626;
            border: 1px solid #fecaca;
        }
        
        .alerta-exito {
            background: #d1fae5;
            color: #059669;
            border: 1px solid #a7f3d0;
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
        
        .sin-mensajes {
            text-align: center;
            padding: 40px;
            color: #666;
        }
    </style>
</head>
<body>
    <div class="container">
        <a href="/proyectophp/vista/usuario/?url=mis_tickets" class="volver">← Volver a mis tickets</a>
        
        <div class="header">
            <h1>
                <?php echo htmlspecialchars($ticket['titulo']); ?>
                <span class="badge badge-<?php echo $ticket['estado']; ?>">
                    <?php echo ucfirst(str_replace('_', ' ', $ticket['estado'])); ?>
                </span>
            </h1>
            <p><?php echo htmlspecialchars($ticket['descripcion']); ?></p>
        </div>
        
        <?php if($mensaje_error): ?>
            <div class="alerta alerta-error"><?php echo $mensaje_error; ?></div>
        <?php endif; ?>
        
        <?php if(isset($_GET['enviado'])): ?>
            <div class="alerta alerta-exito">Mensaje enviado correctamente</div>
        <?php endif; ?>
        
        <div class="chat-container">
            <div class="mensajes" id="mensajes">
                <?php if(empty($mensajes)): ?>
                    <div class="sin-mensajes">
                        <p>No hay mensajes aún. ¡Sé el primero en escribir!</p>
                    </div>
                <?php else: ?>
                    <?php foreach($mensajes as $msg): 
                        // Determinar rol en español
                        $rol_texto = '';
                        $rol_color = '';
                        switch($msg['rol']) {
                            case 'administrador':
                                $rol_texto = '👨‍💼 Administrador';
                                $rol_color = '#9b59b6';
                                break;
                            case 'tecnico':
                                $rol_texto = '🔧 Técnico';
                                $rol_color = '#3498db';
                                break;
                            case 'docente':
                                $rol_texto = '👨‍🏫 Docente';
                                $rol_color = '#27ae60';
                                break;
                            default:
                                $rol_texto = '👤 Usuario';
                                $rol_color = '#7f8c8d';
                        }
                    ?>
                        <div class="mensaje <?php echo ($msg['id_emisor'] == $id_usuario) ? 'mensaje-propio' : 'mensaje-otro'; ?>">
                            <div class="mensaje-contenido">
                                <?php echo nl2br(htmlspecialchars($msg['mensaje'])); ?>
                            </div>
                            <div class="mensaje-info">
                                <span class="mensaje-rol" style="background: <?php echo $rol_color; ?>; color: white; padding: 3px 8px; border-radius: 10px; font-size: 0.75rem; font-weight: 600;">
                                    <?php echo $rol_texto; ?>
                                </span>
                                <?php 
                                echo htmlspecialchars($msg['nombre'] . ' ' . $msg['apellido']);
                                echo ' • ';
                                echo date('d/m/Y H:i', strtotime($msg['fecha_envio']));
                                ?>
                            </div>
                        </div>
                    <?php endforeach; ?>
                <?php endif; ?>
            </div>
            
            <form method="POST" class="form-enviar">
                <div class="input-group">
                    <textarea 
                        name="mensaje" 
                        class="input-mensaje" 
                        placeholder="Escribe tu mensaje..."
                        required
                        rows="3"
                    ></textarea>
                    <button type="submit" name="enviar_mensaje" class="btn-enviar">
                        Enviar
                    </button>
                </div>
            </form>
        </div>
    </div>
    
    <script>
        // Auto-scroll al último mensaje
        const mensajesDiv = document.getElementById('mensajes');
        mensajesDiv.scrollTop = mensajesDiv.scrollHeight;
        
        // Actualizar mensajes cada 5 segundos sin recargar la página
        let ultimoScroll = mensajesDiv.scrollHeight;
        
        setInterval(async () => {
            // Solo actualizar si el usuario NO está escribiendo
            const textarea = document.querySelector('.input-mensaje');
            if(document.activeElement !== textarea || textarea.value.trim() === '') {
                try {
                    const response = await fetch('chat_ticket.php?id=<?php echo $id_ticket; ?>');
                    const html = await response.text();
                    const parser = new DOMParser();
                    const doc = parser.parseFromString(html, 'text/html');
                    const nuevosMensajes = doc.getElementById('mensajes');
                    
                    if(nuevosMensajes) {
                        const scrollAbajo = mensajesDiv.scrollTop + mensajesDiv.clientHeight >= mensajesDiv.scrollHeight - 50;
                        mensajesDiv.innerHTML = nuevosMensajes.innerHTML;
                        
                        // Solo hacer scroll si estaba al final
                        if(scrollAbajo) {
                            mensajesDiv.scrollTop = mensajesDiv.scrollHeight;
                        }
                    }
                } catch(error) {
                    console.error('Error al actualizar mensajes:', error);
                }
            }
        }, 5000); // Cada 5 segundos
    </script>
</body>
</html>