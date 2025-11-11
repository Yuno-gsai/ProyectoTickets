<?php
// Vista de Configuración
?>

<style>
    .config-header {
        background: linear-gradient(135deg, #3498db, #2980b9);
        color: white;
        padding: 30px;
        border-radius: 15px;
        margin-bottom: 30px;
        box-shadow: 0 10px 30px rgba(52, 152, 219, 0.3);
    }
    
    .config-header h1 {
        margin: 0 0 10px 0;
        font-size: 2rem;
        display: flex;
        align-items: center;
        gap: 15px;
    }
    
    .config-section {
        background: white;
        padding: 30px;
        border-radius: 15px;
        box-shadow: 0 5px 15px rgba(0,0,0,0.1);
        margin-bottom: 25px;
    }
    
    .config-section h3 {
        color: #2c3e50;
        margin: 0 0 20px 0;
        padding-bottom: 15px;
        border-bottom: 2px solid #ecf0f1;
        display: flex;
        align-items: center;
        gap: 10px;
    }
    
    .config-options {
        display: grid;
        gap: 20px;
    }
    
    .config-option {
        display: flex;
        justify-content: space-between;
        align-items: center;
        padding: 20px;
        background: #f8f9fa;
        border-radius: 10px;
        transition: all 0.3s ease;
    }
    
    .config-option:hover {
        background: #e9ecef;
    }
    
    .option-info h4 {
        margin: 0 0 5px 0;
        color: #2c3e50;
    }
    
    .option-info p {
        margin: 0;
        color: #7f8c8d;
        font-size: 0.9rem;
    }
    
    .toggle-switch {
        position: relative;
        width: 60px;
        height: 30px;
        background: #ecf0f1;
        border-radius: 30px;
        cursor: pointer;
        transition: background 0.3s ease;
    }
    
    .toggle-switch.active {
        background: #27ae60;
    }
    
    .toggle-switch::after {
        content: '';
        position: absolute;
        width: 26px;
        height: 26px;
        background: white;
        border-radius: 50%;
        top: 2px;
        left: 2px;
        transition: transform 0.3s ease;
    }
    
    .toggle-switch.active::after {
        transform: translateX(30px);
    }
    
    .alert-warning {
        background: #fff3cd;
        border-left: 4px solid #f39c12;
        padding: 20px;
        border-radius: 10px;
        margin-top: 20px;
        color: #856404;
    }
</style>

<div class="container">
    <div class="config-header">
        <h1>
            <i class="fas fa-cog"></i>
            Configuración del Sistema
        </h1>
        <p>Personaliza el comportamiento del sistema según tus preferencias</p>
    </div>
    
    <!-- Notificaciones -->
    <div class="config-section">
        <h3>
            <i class="fas fa-bell"></i>
            Notificaciones
        </h3>
        <div class="config-options">
            <div class="config-option">
                <div class="option-info">
                    <h4>Notificaciones de Nuevos Tickets</h4>
                    <p>Recibir notificaciones cuando se creen nuevos tickets</p>
                </div>
                <div class="toggle-switch active" onclick="this.classList.toggle('active')"></div>
            </div>
            
            <div class="config-option">
                <div class="option-info">
                    <h4>Notificaciones de Asignación</h4>
                    <p>Recibir notificaciones cuando se te asigne un ticket</p>
                </div>
                <div class="toggle-switch active" onclick="this.classList.toggle('active')"></div>
            </div>
            
            <div class="config-option">
                <div class="option-info">
                    <h4>Notificaciones de Mensajes</h4>
                    <p>Recibir notificaciones de nuevos mensajes en tickets</p>
                </div>
                <div class="toggle-switch active" onclick="this.classList.toggle('active')"></div>
            </div>
        </div>
    </div>
    
    <!-- Preferencias de Visualización -->
    <div class="config-section">
        <h3>
            <i class="fas fa-eye"></i>
            Preferencias de Visualización
        </h3>
        <div class="config-options">
            <div class="config-option">
                <div class="option-info">
                    <h4>Modo Compacto</h4>
                    <p>Mostrar más información en menos espacio</p>
                </div>
                <div class="toggle-switch" onclick="this.classList.toggle('active')"></div>
            </div>
            
            <div class="config-option">
                <div class="option-info">
                    <h4>Auto-actualización</h4>
                    <p>Actualizar automáticamente la lista de tickets cada 30 segundos</p>
                </div>
                <div class="toggle-switch" onclick="this.classList.toggle('active')"></div>
            </div>
        </div>
    </div>
    
    <!-- Seguridad -->
    <div class="config-section">
        <h3>
            <i class="fas fa-shield-alt"></i>
            Seguridad
        </h3>
        <div class="config-options">
            <div class="config-option">
                <div class="option-info">
                    <h4>Cerrar Sesión Automática</h4>
                    <p>Cerrar sesión después de 30 minutos de inactividad</p>
                </div>
                <div class="toggle-switch active" onclick="this.classList.toggle('active')"></div>
            </div>
        </div>
        
        <div class="alert-warning">
            <h4 style="margin: 0 0 10px 0;">
                <i class="fas fa-exclamation-triangle"></i> Cambio de Contraseña
            </h4>
            <p style="margin: 0 0 10px 0;">
                Para cambiar tu contraseña, contacta al administrador del sistema.
            </p>
            <a href="?url=ayuda" style="color: #856404; font-weight: 600; text-decoration: underline;">
                Ver información de contacto
            </a>
        </div>
    </div>
    
    <div style="text-align: center; padding: 20px; color: #7f8c8d;">
        <p><i class="fas fa-info-circle"></i> Los cambios se guardan automáticamente</p>
    </div>
</div>
