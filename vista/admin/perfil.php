<?php
// Vista de Perfil de Usuario
$usuario_actual = $_SESSION['usuario'];
?>

<style>
    .profile-header {
        background: linear-gradient(135deg, #667eea, #764ba2);
        color: white;
        padding: 40px;
        border-radius: 15px;
        margin-bottom: 30px;
        box-shadow: 0 10px 30px rgba(102, 126, 234, 0.3);
        display: flex;
        align-items: center;
        gap: 30px;
    }
    
    .profile-avatar {
        width: 120px;
        height: 120px;
        border-radius: 50%;
        background: white;
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 3rem;
        color: #667eea;
        font-weight: bold;
        box-shadow: 0 5px 15px rgba(0,0,0,0.2);
    }
    
    .profile-info h1 {
        margin: 0 0 10px 0;
        font-size: 2.5rem;
    }
    
    .profile-info p {
        margin: 0;
        opacity: 0.9;
        font-size: 1.1rem;
    }
    
    .profile-content {
        background: white;
        padding: 30px;
        border-radius: 15px;
        box-shadow: 0 5px 15px rgba(0,0,0,0.1);
    }
    
    .info-grid {
        display: grid;
        grid-template-columns: repeat(auto-fit, minmax(300px, 1fr));
        gap: 25px;
    }
    
    .info-item {
        padding: 20px;
        background: #f8f9fa;
        border-radius: 10px;
        border-left: 4px solid #667eea;
    }
    
    .info-item label {
        display: block;
        font-weight: 600;
        color: #7f8c8d;
        font-size: 0.9rem;
        margin-bottom: 8px;
    }
    
    .info-item .value {
        font-size: 1.2rem;
        color: #2c3e50;
        font-weight: 500;
    }
    
    .badge-rol {
        padding: 8px 16px;
        border-radius: 20px;
        font-size: 0.9rem;
        font-weight: 600;
        display: inline-block;
        margin-top: 10px;
    }
    
    .badge-administrador {
        background: linear-gradient(135deg, #667eea, #764ba2);
        color: white;
    }
    
    .alert-info {
        background: #d1ecf1;
        border-left: 4px solid #0dcaf0;
        padding: 20px;
        border-radius: 10px;
        margin-top: 30px;
        color: #0c5460;
    }
</style>

<div class="container">
    <!-- Header del Perfil -->
    <div class="profile-header">
        <div class="profile-avatar">
            <?php echo strtoupper(substr($usuario_actual['nombre'], 0, 1)); ?>
        </div>
        <div class="profile-info">
            <h1><?php echo htmlspecialchars($usuario_actual['nombre_completo']); ?></h1>
            <p>
                <i class="fas fa-shield-alt"></i>
                <?php echo ucfirst(htmlspecialchars($usuario_actual['rol'])); ?>
            </p>
        </div>
    </div>
    
    <!-- Información del Perfil -->
    <div class="profile-content">
        <h2 style="color: #2c3e50; margin-bottom: 25px;">
            <i class="fas fa-user"></i> Información Personal
        </h2>
        
        <div class="info-grid">
            <div class="info-item">
                <label><i class="fas fa-user"></i> Nombre</label>
                <div class="value"><?php echo htmlspecialchars($usuario_actual['nombre']); ?></div>
            </div>
            
            <div class="info-item">
                <label><i class="fas fa-user"></i> Apellido</label>
                <div class="value"><?php echo htmlspecialchars($usuario_actual['apellido']); ?></div>
            </div>
            
            <div class="info-item">
                <label><i class="fas fa-envelope"></i> Correo Electrónico</label>
                <div class="value"><?php echo htmlspecialchars($usuario_actual['correo']); ?></div>
            </div>
            
            <div class="info-item">
                <label><i class="fas fa-id-badge"></i> ID de Usuario</label>
                <div class="value">#<?php echo htmlspecialchars($usuario_actual['id']); ?></div>
            </div>
            
            <div class="info-item">
                <label><i class="fas fa-user-shield"></i> Rol en el Sistema</label>
                <div class="value">
                    <?php echo ucfirst(htmlspecialchars($usuario_actual['rol'])); ?>
                    <span class="badge-rol badge-<?php echo $usuario_actual['rol']; ?>">
                        <?php 
                        $roles_nombres = [
                            'administrador' => 'Administrador del Sistema',
                            'tecnico' => 'Técnico de Soporte',
                            'docente' => 'Docente'
                        ];
                        echo $roles_nombres[$usuario_actual['rol']] ?? ucfirst($usuario_actual['rol']);
                        ?>
                    </span>
                </div>
            </div>
        </div>
        
        <div class="alert-info">
            <h3 style="margin: 0 0 10px 0;">
                <i class="fas fa-info-circle"></i> Sobre tu cuenta
            </h3>
            <p style="margin: 0;">
                Esta es tu información de perfil almacenada en el sistema. 
                Para cambiar tu contraseña o actualizar tus datos, contacta al administrador del sistema.
            </p>
        </div>
    </div>
</div>
