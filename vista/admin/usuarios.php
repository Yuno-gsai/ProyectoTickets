<?php
// Vista de Gestión de Usuarios para Administradores
include_once __DIR__ . '/../../controlador/controladorUsuario.php';

$controladorUsuario = new controladorUsuario();

// Obtener ID del usuario actual
$id_usuario_actual = $_SESSION['usuario']['id'];

// Mensajes
$mensaje = '';
$tipo_mensaje = '';

// Procesar acciones
if($_SERVER['REQUEST_METHOD'] === 'POST'){
    if(isset($_POST['accion'])){
        $accion = $_POST['accion'];
        
        switch($accion){
            case 'crear':
                $datos = [
                    'nombre' => trim($_POST['nombre']),
                    'apellido' => trim($_POST['apellido']),
                    'correo' => trim($_POST['correo']),
                    'password' => trim($_POST['password']),
                    'rol' => $_POST['rol'],
                    'estado' => $_POST['estado']
                ];
                $resultado = $controladorUsuario->crearUsuario($datos);
                $mensaje = $resultado['mensaje'];
                $tipo_mensaje = $resultado['success'] ? 'success' : 'error';
                break;
                
            case 'editar':
                $id = intval($_POST['id_usuario']);
                $datos = [
                    'nombre' => trim($_POST['nombre']),
                    'apellido' => trim($_POST['apellido']),
                    'correo' => trim($_POST['correo']),
                    'rol' => $_POST['rol'],
                    'estado' => $_POST['estado']
                ];
                $resultado = $controladorUsuario->actualizarUsuario($id, $datos);
                $mensaje = $resultado['mensaje'];
                $tipo_mensaje = $resultado['success'] ? 'success' : 'error';
                break;
                
            case 'cambiar_password':
                $id = intval($_POST['id_usuario']);
                $password = trim($_POST['nueva_password']);
                $resultado = $controladorUsuario->actualizarPassword($id, $password);
                $mensaje = $resultado['mensaje'];
                $tipo_mensaje = $resultado['success'] ? 'success' : 'error';
                break;
                
            case 'cambiar_estado':
                $id = intval($_POST['id_usuario']);
                $estado = $_POST['estado'];
                $resultado = $controladorUsuario->cambiarEstado($id, $estado);
                $mensaje = $resultado['mensaje'];
                $tipo_mensaje = $resultado['success'] ? 'success' : 'error';
                break;
                
            case 'eliminar':
                $id = intval($_POST['id_usuario']);
                $resultado = $controladorUsuario->eliminarUsuario($id);
                $mensaje = $resultado['mensaje'];
                $tipo_mensaje = $resultado['success'] ? 'success' : 'error';
                break;
        }
    }
}

// Obtener todos los usuarios
$usuarios = $controladorUsuario->listarUsuarios();

// Filtros
$filtro_rol = isset($_GET['rol']) ? $_GET['rol'] : 'todos';
$filtro_estado = isset($_GET['estado']) ? $_GET['estado'] : 'todos';
$busqueda = isset($_GET['busqueda']) ? trim($_GET['busqueda']) : '';

// Aplicar filtros
$usuarios_filtrados = $usuarios;

if($filtro_rol !== 'todos') {
    $usuarios_filtrados = array_filter($usuarios_filtrados, function($usuario) use ($filtro_rol) {
        return $usuario['rol'] === $filtro_rol;
    });
}

if($filtro_estado !== 'todos') {
    $usuarios_filtrados = array_filter($usuarios_filtrados, function($usuario) use ($filtro_estado) {
        return $usuario['estado'] === $filtro_estado;
    });
}

if(!empty($busqueda)) {
    $usuarios_filtrados = array_filter($usuarios_filtrados, function($usuario) use ($busqueda) {
        $busqueda_lower = strtolower($busqueda);
        return strpos(strtolower($usuario['nombre']), $busqueda_lower) !== false ||
               strpos(strtolower($usuario['apellido']), $busqueda_lower) !== false ||
               strpos(strtolower($usuario['correo']), $busqueda_lower) !== false;
    });
}

// Contar usuarios
$contadores = [
    'total' => count($usuarios),
    'docentes' => 0,
    'administradores' => 0,
    'tecnicos' => 0,
    'activos' => 0,
    'inactivos' => 0
];

foreach($usuarios as $user) {
    if($user['rol'] === 'docente') $contadores['docentes']++;
    if($user['rol'] === 'administrador') $contadores['administradores']++;
    if($user['rol'] === 'tecnico') $contadores['tecnicos']++;
    if($user['estado'] === 'activo') $contadores['activos']++;
    if($user['estado'] === 'inactivo') $contadores['inactivos']++;
}
?>

<link rel="stylesheet" href="../../assets/css/dashboard.css">

<style>
    .page-header {
        background: linear-gradient(135deg, #3498db, #2980b9);
        color: white;
        padding: 30px;
        border-radius: 15px;
        margin-bottom: 30px;
        box-shadow: 0 10px 30px rgba(52, 152, 219, 0.3);
    }
    
    .page-header h1 {
        margin: 0 0 10px 0;
        font-size: 2rem;
        display: flex;
        align-items: center;
        gap: 15px;
    }
    
    .alert {
        padding: 15px 20px;
        border-radius: 10px;
        margin-bottom: 20px;
        display: flex;
        align-items: center;
        gap: 10px;
        animation: slideDown 0.3s ease;
    }
    
    .alert-success {
        background: #d4edda;
        color: #155724;
        border: 1px solid #c3e6cb;
    }
    
    .alert-error {
        background: #f8d7da;
        color: #721c24;
        border: 1px solid #f5c6cb;
    }
    
    @keyframes slideDown {
        from { opacity: 0; transform: translateY(-20px); }
        to { opacity: 1; transform: translateY(0); }
    }
    
    .stats-grid {
        display: grid;
        grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
        gap: 20px;
        margin-bottom: 30px;
    }
    
    .stat-card-mini {
        background: white;
        padding: 20px;
        border-radius: 10px;
        box-shadow: 0 5px 15px rgba(0,0,0,0.1);
        text-align: center;
    }
    
    .stat-card-mini .number {
        font-size: 2.5rem;
        font-weight: bold;
        color: #3498db;
        margin-bottom: 5px;
    }
    
    .stat-card-mini .label {
        color: #7f8c8d;
        font-size: 0.9rem;
    }
    
    .actions-bar {
        background: white;
        padding: 20px;
        border-radius: 10px;
        box-shadow: 0 5px 15px rgba(0,0,0,0.1);
        margin-bottom: 30px;
        display: flex;
        justify-content: space-between;
        align-items: center;
        flex-wrap: wrap;
        gap: 15px;
    }
    
    .btn {
        padding: 12px 25px;
        border: none;
        border-radius: 8px;
        font-weight: 600;
        cursor: pointer;
        transition: all 0.3s ease;
        display: inline-flex;
        align-items: center;
        gap: 8px;
        text-decoration: none;
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
    
    .btn-danger {
        background: #e74c3c;
        color: white;
    }
    
    .btn-warning {
        background: #f39c12;
        color: white;
    }
    
    .btn-sm {
        padding: 6px 12px;
        font-size: 0.85rem;
    }
    
    .filters-section {
        background: white;
        padding: 20px;
        border-radius: 10px;
        box-shadow: 0 5px 15px rgba(0,0,0,0.1);
        margin-bottom: 30px;
    }
    
    .filters-grid {
        display: grid;
        grid-template-columns: repeat(auto-fit, minmax(250px, 1fr));
        gap: 15px;
    }
    
    .filter-group {
        display: flex;
        flex-direction: column;
        gap: 5px;
    }
    
    .filter-group label {
        font-weight: 600;
        color: #2c3e50;
        font-size: 0.9rem;
    }
    
    .filter-select, .filter-input {
        padding: 10px;
        border: 2px solid #ecf0f1;
        border-radius: 8px;
        font-size: 1rem;
    }
    
    .users-table {
        width: 100%;
        background: white;
        border-radius: 10px;
        overflow: hidden;
        box-shadow: 0 5px 15px rgba(0,0,0,0.1);
    }
    
    .users-table table {
        width: 100%;
        border-collapse: collapse;
    }
    
    .users-table thead {
        background: #3498db;
        color: white;
    }
    
    .users-table th, .users-table td {
        padding: 15px;
        text-align: left;
    }
    
    .users-table tbody tr {
        border-bottom: 1px solid #ecf0f1;
        transition: background 0.3s ease;
    }
    
    .users-table tbody tr:hover {
        background: #f8f9fa;
    }
    
    .badge {
        padding: 5px 12px;
        border-radius: 15px;
        font-size: 0.85rem;
        font-weight: 600;
        display: inline-block;
    }
    
    .badge-docente {
        background: #e8f5e9;
        color: #27ae60;
    }
    
    .badge-administrador {
        background: #e3f2fd;
        color: #2980b9;
    }
    
    .badge-tecnico {
        background: #fff3e0;
        color: #f57c00;
    }
    
    .badge-activo {
        background: #d4edda;
        color: #155724;
    }
    
    .badge-inactivo {
        background: #f8d7da;
        color: #721c24;
    }
    
    .modal {
        display: none;
        position: fixed;
        z-index: 1000;
        left: 0;
        top: 0;
        width: 100%;
        height: 100%;
        overflow: auto;
        background-color: rgba(0,0,0,0.5);
    }
    
    .modal-content {
        background-color: white;
        margin: 5% auto;
        padding: 30px;
        border-radius: 15px;
        width: 90%;
        max-width: 600px;
        box-shadow: 0 10px 40px rgba(0,0,0,0.3);
        animation: modalSlide 0.3s ease;
    }
    
    @keyframes modalSlide {
        from { opacity: 0; transform: translateY(-50px); }
        to { opacity: 1; transform: translateY(0); }
    }
    
    .modal-header {
        display: flex;
        justify-content: space-between;
        align-items: center;
        margin-bottom: 20px;
        padding-bottom: 15px;
        border-bottom: 2px solid #ecf0f1;
    }
    
    .modal-header h2 {
        margin: 0;
        color: #2c3e50;
    }
    
    .close {
        font-size: 2rem;
        font-weight: bold;
        color: #7f8c8d;
        cursor: pointer;
        transition: color 0.3s ease;
    }
    
    .close:hover {
        color: #e74c3c;
    }
    
    .form-group {
        margin-bottom: 20px;
    }
    
    .form-group label {
        display: block;
        margin-bottom: 8px;
        font-weight: 600;
        color: #2c3e50;
    }
    
    .form-control {
        width: 100%;
        padding: 12px;
        border: 2px solid #ecf0f1;
        border-radius: 8px;
        font-size: 1rem;
        transition: border 0.3s ease;
    }
    
    .form-control:focus {
        outline: none;
        border-color: #3498db;
    }
    
    .empty-state {
        text-align: center;
        padding: 60px 20px;
        color: #95a5a6;
    }
    
    .empty-state i {
        font-size: 4rem;
        margin-bottom: 20px;
        opacity: 0.5;
    }
</style>

<!-- Encabezado -->
<div class="page-header">
    <h1>
        <i class="fas fa-users"></i>
        Gestión de Usuarios
    </h1>
    <p>Administra los usuarios del sistema de soporte</p>
</div>

<!-- Alertas -->
<?php if(!empty($mensaje)): ?>
    <div class="alert alert-<?php echo $tipo_mensaje; ?>">
        <i class="fas fa-<?php echo $tipo_mensaje === 'success' ? 'check-circle' : 'exclamation-circle'; ?>"></i>
        <?php echo htmlspecialchars($mensaje); ?>
    </div>
<?php endif; ?>

<!-- Estadísticas -->
<div class="stats-grid">
    <div class="stat-card-mini">
        <div class="number"><?php echo $contadores['total']; ?></div>
        <div class="label">Total Usuarios</div>
    </div>
    <div class="stat-card-mini">
        <div class="number"><?php echo $contadores['docentes']; ?></div>
        <div class="label">Docentes</div>
    </div>
    <div class="stat-card-mini">
        <div class="number"><?php echo $contadores['administradores']; ?></div>
        <div class="label">Administradores</div>
    </div>
    <div class="stat-card-mini">
        <div class="number"><?php echo $contadores['tecnicos']; ?></div>
        <div class="label">Técnicos</div>
    </div>
    <div class="stat-card-mini">
        <div class="number"><?php echo $contadores['activos']; ?></div>
        <div class="label">Activos</div>
    </div>
</div>

<!-- Barra de Acciones -->
<div class="actions-bar">
    <h3 style="margin: 0;">Lista de Usuarios</h3>
    <button class="btn btn-primary" onclick="abrirModalCrear()">
        <i class="fas fa-user-plus"></i>
        Nuevo Usuario
    </button>
</div>

<!-- Filtros -->
<div class="filters-section">
    <form method="GET" action="">
        <input type="hidden" name="url" value="usuarios">
        <div class="filters-grid">
            <div class="filter-group">
                <label><i class="fas fa-user-tag"></i> Rol</label>
                <select name="rol" class="filter-select">
                    <option value="todos" <?php echo $filtro_rol === 'todos' ? 'selected' : ''; ?>>Todos</option>
                    <option value="docente" <?php echo $filtro_rol === 'docente' ? 'selected' : ''; ?>>Docentes</option>
                    <option value="administrador" <?php echo $filtro_rol === 'administrador' ? 'selected' : ''; ?>>Administradores</option>
                    <option value="tecnico" <?php echo $filtro_rol === 'tecnico' ? 'selected' : ''; ?>>Técnicos</option>
                </select>
            </div>
            <div class="filter-group">
                <label><i class="fas fa-toggle-on"></i> Estado</label>
                <select name="estado" class="filter-select">
                    <option value="todos" <?php echo $filtro_estado === 'todos' ? 'selected' : ''; ?>>Todos</option>
                    <option value="activo" <?php echo $filtro_estado === 'activo' ? 'selected' : ''; ?>>Activos</option>
                    <option value="inactivo" <?php echo $filtro_estado === 'inactivo' ? 'selected' : ''; ?>>Inactivos</option>
                </select>
            </div>
            <div class="filter-group">
                <label><i class="fas fa-search"></i> Buscar</label>
                <input type="text" name="busqueda" class="filter-input" placeholder="Nombre, apellido o correo..." value="<?php echo htmlspecialchars($busqueda); ?>">
            </div>
            <div class="filter-group" style="display: flex; align-items: flex-end; gap: 10px;">
                <button type="submit" class="btn btn-primary" style="flex: 1;">Buscar</button>
                <a href="?url=usuarios" class="btn" style="background: #ecf0f1; color: #2c3e50;">
                    <i class="fas fa-redo"></i>
                </a>
            </div>
        </div>
    </form>
</div>

<!-- Tabla de Usuarios -->
<?php if(!empty($usuarios_filtrados)): ?>
<div class="users-table">
    <table>
        <thead>
            <tr>
                <th>ID</th>
                <th>Nombre Completo</th>
                <th>Correo</th>
                <th>Rol</th>
                <th>Estado</th>
                <th>Registro</th>
                <th>Acciones</th>
            </tr>
        </thead>
        <tbody>
            <?php foreach($usuarios_filtrados as $user): ?>
            <tr>
                <td><strong>#<?php echo $user['id_usuario']; ?></strong></td>
                <td><?php echo htmlspecialchars($user['nombre'] . ' ' . $user['apellido']); ?></td>
                <td><?php echo htmlspecialchars($user['correo']); ?></td>
                <td>
                    <span class="badge badge-<?php echo $user['rol']; ?>">
                        <i class="fas fa-<?php echo $user['rol'] === 'administrador' ? 'user-shield' : ($user['rol'] === 'tecnico' ? 'tools' : 'user'); ?>"></i>
                        <?php echo ucfirst($user['rol']); ?>
                    </span>
                </td>
                <td>
                    <span class="badge badge-<?php echo $user['estado']; ?>">
                        <?php echo ucfirst($user['estado']); ?>
                    </span>
                </td>
                <td><?php echo date('d/m/Y', strtotime($user['fecha_registro'])); ?></td>
                <td>
                    <button class="btn btn-sm btn-primary" onclick='abrirModalEditar(<?php echo json_encode($user); ?>)' title="Editar">
                        <i class="fas fa-edit"></i>
                    </button>
                    <?php if($user['id_usuario'] != $id_usuario_actual): ?>
                        <button class="btn btn-sm <?php echo $user['estado'] === 'activo' ? 'btn-warning' : 'btn-success'; ?>" 
                                onclick="cambiarEstado(<?php echo $user['id_usuario']; ?>, '<?php echo $user['estado'] === 'activo' ? 'inactivo' : 'activo'; ?>')"
                                title="<?php echo $user['estado'] === 'activo' ? 'Desactivar' : 'Activar'; ?>">
                            <i class="fas fa-<?php echo $user['estado'] === 'activo' ? 'toggle-off' : 'toggle-on'; ?>"></i>
                        </button>
                        <button class="btn btn-sm btn-danger" onclick="confirmarEliminar(<?php echo $user['id_usuario']; ?>, '<?php echo htmlspecialchars($user['nombre'] . ' ' . $user['apellido']); ?>')" title="Eliminar">
                            <i class="fas fa-trash"></i>
                        </button>
                    <?php endif; ?>
                </td>
            </tr>
            <?php endforeach; ?>
        </tbody>
    </table>
</div>
<?php else: ?>
<div class="empty-state">
    <i class="fas fa-users-slash"></i>
    <h3>No se encontraron usuarios</h3>
    <p>Prueba ajustando los filtros de búsqueda</p>
</div>
<?php endif; ?>

<!-- Modal Crear Usuario -->
<div id="modalCrear" class="modal">
    <div class="modal-content">
        <div class="modal-header">
            <h2><i class="fas fa-user-plus"></i> Nuevo Usuario</h2>
            <span class="close" onclick="cerrarModal('modalCrear')">&times;</span>
        </div>
        <form method="POST">
            <input type="hidden" name="accion" value="crear">
            <div class="form-group">
                <label>Nombre *</label>
                <input type="text" name="nombre" class="form-control" required>
            </div>
            <div class="form-group">
                <label>Apellido *</label>
                <input type="text" name="apellido" class="form-control" required>
            </div>
            <div class="form-group">
                <label>Correo Electrónico *</label>
                <input type="email" name="correo" class="form-control" required>
            </div>
            <div class="form-group">
                <label>Contraseña *</label>
                <input type="password" name="password" class="form-control" required>
            </div>
            <div class="form-group">
                <label>Rol *</label>
                <select name="rol" class="form-control" required>
                    <option value="docente">Docente</option>
                    <option value="administrador">Administrador</option>
                    <option value="tecnico">Técnico</option>
                </select>
            </div>
            <div class="form-group">
                <label>Estado *</label>
                <select name="estado" class="form-control" required>
                    <option value="activo">Activo</option>
                    <option value="inactivo">Inactivo</option>
                </select>
            </div>
            <div style="display: flex; gap: 10px;">
                <button type="submit" class="btn btn-primary" style="flex: 1;">
                    <i class="fas fa-save"></i> Crear Usuario
                </button>
                <button type="button" class="btn" style="background: #ecf0f1; color: #2c3e50;" onclick="cerrarModal('modalCrear')">
                    Cancelar
                </button>
            </div>
        </form>
    </div>
</div>

<!-- Modal Editar Usuario -->
<div id="modalEditar" class="modal">
    <div class="modal-content">
        <div class="modal-header">
            <h2><i class="fas fa-user-edit"></i> Editar Usuario</h2>
            <span class="close" onclick="cerrarModal('modalEditar')">&times;</span>
        </div>
        <form method="POST">
            <input type="hidden" name="accion" value="editar">
            <input type="hidden" name="id_usuario" id="edit_id">
            <div class="form-group">
                <label>Nombre *</label>
                <input type="text" name="nombre" id="edit_nombre" class="form-control" required>
            </div>
            <div class="form-group">
                <label>Apellido *</label>
                <input type="text" name="apellido" id="edit_apellido" class="form-control" required>
            </div>
            <div class="form-group">
                <label>Correo Electrónico *</label>
                <input type="email" name="correo" id="edit_correo" class="form-control" required>
            </div>
            <div class="form-group">
                <label>Rol *</label>
                <select name="rol" id="edit_rol" class="form-control" required>
                    <option value="docente">Docente</option>
                    <option value="administrador">Administrador</option>
                    <option value="tecnico">Técnico</option>
                </select>
            </div>
            <div class="form-group">
                <label>Estado *</label>
                <select name="estado" id="edit_estado" class="form-control" required>
                    <option value="activo">Activo</option>
                    <option value="inactivo">Inactivo</option>
                </select>
            </div>
            <div style="display: flex; gap: 10px;">
                <button type="submit" class="btn btn-success" style="flex: 1;">
                    <i class="fas fa-save"></i> Guardar Cambios
                </button>
                <button type="button" class="btn" style="background: #ecf0f1; color: #2c3e50;" onclick="cerrarModal('modalEditar')">
                    Cancelar
                </button>
            </div>
        </form>
    </div>
</div>

<script>
// Funciones para modales
function abrirModalCrear() {
    document.getElementById('modalCrear').style.display = 'block';
}

function abrirModalEditar(user) {
    document.getElementById('edit_id').value = user.id_usuario;
    document.getElementById('edit_nombre').value = user.nombre;
    document.getElementById('edit_apellido').value = user.apellido;
    document.getElementById('edit_correo').value = user.correo;
    document.getElementById('edit_rol').value = user.rol;
    document.getElementById('edit_estado').value = user.estado;
    document.getElementById('modalEditar').style.display = 'block';
}

function cerrarModal(modalId) {
    document.getElementById(modalId).style.display = 'none';
}

// Cerrar modal al hacer clic fuera
window.onclick = function(event) {
    if (event.target.classList.contains('modal')) {
        event.target.style.display = 'none';
    }
}

// Cambiar estado
function cambiarEstado(id, nuevoEstado) {
    const mensaje = nuevoEstado === 'activo' ? '¿Activar este usuario?' : '¿Desactivar este usuario?';
    if(confirm(mensaje)) {
        const form = document.createElement('form');
        form.method = 'POST';
        form.innerHTML = `
            <input type="hidden" name="accion" value="cambiar_estado">
            <input type="hidden" name="id_usuario" value="${id}">
            <input type="hidden" name="estado" value="${nuevoEstado}">
        `;
        document.body.appendChild(form);
        form.submit();
    }
}

// Confirmar eliminación
function confirmarEliminar(id, nombre) {
    if(confirm(`¿Estás seguro de eliminar al usuario "${nombre}"?\n\nEsta acción no se puede deshacer.`)) {
        const form = document.createElement('form');
        form.method = 'POST';
        form.innerHTML = `
            <input type="hidden" name="accion" value="eliminar">
            <input type="hidden" name="id_usuario" value="${id}">
        `;
        document.body.appendChild(form);
        form.submit();
    }
}

// Auto-ocultar alertas
setTimeout(function() {
    const alerts = document.querySelectorAll('.alert');
    alerts.forEach(alert => {
        alert.style.opacity = '0';
        setTimeout(() => alert.remove(), 300);
    });
}, 5000);
</script>
