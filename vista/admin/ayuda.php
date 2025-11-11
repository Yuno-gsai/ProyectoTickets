<?php
// Vista de Ayuda y Soporte
?>

<style>
    .help-header {
        background: linear-gradient(135deg, #27ae60, #229954);
        color: white;
        padding: 40px;
        border-radius: 15px;
        margin-bottom: 30px;
        box-shadow: 0 10px 30px rgba(39, 174, 96, 0.3);
        text-align: center;
    }
    
    .help-header h1 {
        margin: 0 0 10px 0;
        font-size: 2.5rem;
    }
    
    .help-grid {
        display: grid;
        grid-template-columns: repeat(auto-fit, minmax(300px, 1fr));
        gap: 25px;
        margin-bottom: 30px;
    }
    
    .help-card {
        background: white;
        padding: 30px;
        border-radius: 15px;
        box-shadow: 0 5px 15px rgba(0,0,0,0.1);
        transition: transform 0.3s ease;
    }
    
    .help-card:hover {
        transform: translateY(-5px);
    }
    
    .help-card-icon {
        width: 80px;
        height: 80px;
        border-radius: 50%;
        background: linear-gradient(135deg, #27ae60, #229954);
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 2rem;
        color: white;
        margin: 0 auto 20px;
    }
    
    .help-card h3 {
        color: #2c3e50;
        margin: 0 0 15px 0;
        text-align: center;
    }
    
    .help-card p {
        color: #7f8c8d;
        line-height: 1.6;
        text-align: center;
    }
    
    .faq-section {
        background: white;
        padding: 30px;
        border-radius: 15px;
        box-shadow: 0 5px 15px rgba(0,0,0,0.1);
    }
    
    .faq-item {
        border-bottom: 1px solid #ecf0f1;
        padding: 20px 0;
    }
    
    .faq-item:last-child {
        border-bottom: none;
    }
    
    .faq-question {
        font-weight: 600;
        color: #2c3e50;
        font-size: 1.1rem;
        margin-bottom: 10px;
        display: flex;
        align-items: center;
        gap: 10px;
    }
    
    .faq-answer {
        color: #7f8c8d;
        line-height: 1.6;
        padding-left: 30px;
    }
    
    .contact-box {
        background: linear-gradient(135deg, #f39c12, #e67e22);
        color: white;
        padding: 30px;
        border-radius: 15px;
        margin-top: 30px;
        text-align: center;
    }
    
    .contact-box h3 {
        margin: 0 0 20px 0;
    }
    
    .contact-info {
        display: flex;
        justify-content: center;
        gap: 40px;
        flex-wrap: wrap;
    }
    
    .contact-item {
        display: flex;
        flex-direction: column;
        align-items: center;
        gap: 10px;
    }
    
    .contact-item i {
        font-size: 2rem;
    }
</style>

<div class="container">
    <!-- Header de Ayuda -->
    <div class="help-header">
        <h1><i class="fas fa-question-circle"></i> Centro de Ayuda</h1>
        <p>Encuentra respuestas a tus preguntas y obtén soporte</p>
    </div>
    
    <!-- Tarjetas de Ayuda -->
    <div class="help-grid">
        <div class="help-card">
            <div class="help-card-icon">
                <i class="fas fa-book"></i>
            </div>
            <h3>Guías de Usuario</h3>
            <p>Consulta las guías completas del sistema en formato Markdown en la carpeta del proyecto.</p>
        </div>
        
        <div class="help-card">
            <div class="help-card-icon">
                <i class="fas fa-lightbulb"></i>
            </div>
            <h3>Cómo Funciona</h3>
            <p>Aprende el flujo completo del sistema paso a paso, desde crear un ticket hasta resolverlo.</p>
        </div>
        
        <div class="help-card">
            <div class="help-card-icon">
                <i class="fas fa-headset"></i>
            </div>
            <h3>Soporte Técnico</h3>
            <p>Contacta al equipo de soporte para resolver problemas o dudas específicas.</p>
        </div>
    </div>
    
    <!-- CÓMO FUNCIONA EL SISTEMA -->
    <div class="faq-section" style="margin-bottom: 30px;">
        <h2 style="color: #2c3e50; margin-bottom: 30px; text-align: center;">
            <i class="fas fa-cogs"></i> ¿Cómo Funciona el Sistema de Tickets?
        </h2>
        
        <div style="background: linear-gradient(135deg, #3498db, #2980b9); color: white; padding: 30px; border-radius: 10px; margin-bottom: 30px;">
            <h3 style="margin: 0 0 20px 0;"><i class="fas fa-info-circle"></i> Descripción General</h3>
            <p style="margin: 0; line-height: 1.8;">
                Este sistema permite gestionar incidencias técnicas de manera eficiente. Los <strong>docentes</strong> 
                reportan problemas, los <strong>administradores</strong> asignan los tickets a <strong>técnicos</strong>, 
                y estos últimos resuelven los problemas mientras todos pueden comunicarse en tiempo real.
            </p>
        </div>
        
        <!-- FLUJO COMPLETO -->
        <div style="background: #ecf0f1; padding: 30px; border-radius: 10px; margin-bottom: 30px;">
            <h3 style="color: #2c3e50; margin: 0 0 25px 0; text-align: center;">
                <i class="fas fa-project-diagram"></i> Flujo Completo del Sistema
            </h3>
            
            <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(250px, 1fr)); gap: 20px;">
                <!-- Paso 1 -->
                <div style="background: white; padding: 20px; border-radius: 10px; border-left: 5px solid #e74c3c;">
                    <div style="font-size: 2rem; color: #e74c3c; margin-bottom: 10px;">①</div>
                    <h4 style="color: #2c3e50; margin: 0 0 10px 0;">Docente Reporta</h4>
                    <p style="color: #7f8c8d; margin: 0; font-size: 0.9rem;">
                        El docente crea un ticket describiendo el problema técnico que tiene (PC no enciende, internet lento, etc.)
                    </p>
                </div>
                
                <!-- Paso 2 -->
                <div style="background: white; padding: 20px; border-radius: 10px; border-left: 5px solid #f39c12;">
                    <div style="font-size: 2rem; color: #f39c12; margin-bottom: 10px;">②</div>
                    <h4 style="color: #2c3e50; margin: 0 0 10px 0;">Admin Asigna</h4>
                    <p style="color: #7f8c8d; margin: 0; font-size: 0.9rem;">
                        El administrador revisa el ticket y lo asigna a un técnico disponible según la carga de trabajo
                    </p>
                </div>
                
                <!-- Paso 3 -->
                <div style="background: white; padding: 20px; border-radius: 10px; border-left: 5px solid #3498db;">
                    <div style="font-size: 2rem; color: #3498db; margin-bottom: 10px;">③</div>
                    <h4 style="color: #2c3e50; margin: 0 0 10px 0;">Técnico Atiende</h4>
                    <p style="color: #7f8c8d; margin: 0; font-size: 0.9rem;">
                        El técnico recibe notificación, cambia el estado a "en progreso" y comienza a resolver el problema
                    </p>
                </div>
                
                <!-- Paso 4 -->
                <div style="background: white; padding: 20px; border-radius: 10px; border-left: 5px solid #27ae60;">
                    <div style="font-size: 2rem; color: #27ae60; margin-bottom: 10px;">④</div>
                    <h4 style="color: #2c3e50; margin: 0 0 10px 0;">Resolución</h4>
                    <p style="color: #7f8c8d; margin: 0; font-size: 0.9rem;">
                        Una vez solucionado, el técnico marca el ticket como "resuelto" y todos reciben notificación
                    </p>
                </div>
            </div>
        </div>
        
        <!-- ROLES Y PERMISOS -->
        <div style="margin-bottom: 30px;">
            <h3 style="color: #2c3e50; margin: 0 0 20px 0; text-align: center;">
                <i class="fas fa-users-cog"></i> Roles en el Sistema
            </h3>
            
            <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(280px, 1fr)); gap: 20px;">
                <!-- Administrador -->
                <div style="background: linear-gradient(135deg, #8e44ad, #9b59b6); color: white; padding: 25px; border-radius: 10px;">
                    <div style="font-size: 3rem; margin-bottom: 15px; text-align: center;">👨‍💼</div>
                    <h4 style="margin: 0 0 15px 0; text-align: center; font-size: 1.3rem;">Administrador</h4>
                    <ul style="margin: 0; padding-left: 20px; line-height: 1.8;">
                        <li>Ver todos los tickets</li>
                        <li>Asignar tickets a técnicos</li>
                        <li>Gestionar usuarios</li>
                        <li>Ver reportes y estadísticas</li>
                        <li>Configurar categorías</li>
                        <li>Monitorear rendimiento</li>
                    </ul>
                </div>
                
                <!-- Técnico -->
                <div style="background: linear-gradient(135deg, #3498db, #2980b9); color: white; padding: 25px; border-radius: 10px;">
                    <div style="font-size: 3rem; margin-bottom: 15px; text-align: center;">🔧</div>
                    <h4 style="margin: 0 0 15px 0; text-align: center; font-size: 1.3rem;">Técnico</h4>
                    <ul style="margin: 0; padding-left: 20px; line-height: 1.8;">
                        <li>Ver tickets asignados</li>
                        <li>Cambiar estado de tickets</li>
                        <li>Comunicarse por chat</li>
                        <li>Recibir notificaciones</li>
                        <li>Ver mis estadísticas</li>
                        <li>Solo 1 ticket activo a la vez</li>
                    </ul>
                </div>
                
                <!-- Docente -->
                <div style="background: linear-gradient(135deg, #27ae60, #229954); color: white; padding: 25px; border-radius: 10px;">
                    <div style="font-size: 3rem; margin-bottom: 15px; text-align: center;">👨‍🏫</div>
                    <h4 style="margin: 0 0 15px 0; text-align: center; font-size: 1.3rem;">Docente</h4>
                    <ul style="margin: 0; padding-left: 20px; line-height: 1.8;">
                        <li>Crear nuevos tickets</li>
                        <li>Ver mis tickets</li>
                        <li>Chat con el técnico</li>
                        <li>Recibir actualizaciones</li>
                        <li>Ver historial de tickets</li>
                        <li>Evaluar la resolución</li>
                    </ul>
                </div>
            </div>
        </div>
        
        <!-- MÓDULOS PRINCIPALES -->
        <div style="margin-bottom: 30px;">
            <h3 style="color: #2c3e50; margin: 0 0 20px 0; text-align: center;">
                <i class="fas fa-th-large"></i> Módulos Principales
            </h3>
            
            <div style="display: grid; gap: 15px;">
                <!-- Dashboard -->
                <div style="background: white; border-left: 5px solid #3498db; padding: 20px; border-radius: 5px; display: flex; align-items: start; gap: 15px;">
                    <div style="font-size: 2rem; color: #3498db;">📊</div>
                    <div style="flex: 1;">
                        <h4 style="color: #2c3e50; margin: 0 0 10px 0;">Dashboard</h4>
                        <p style="color: #7f8c8d; margin: 0;">
                            Panel principal con estadísticas en tiempo real: total de tickets, pendientes, en progreso, resueltos. 
                            Muestra alertas de tickets antiguos y técnicos ocupados.
                        </p>
                    </div>
                </div>
                
                <!-- Gestión de Tickets -->
                <div style="background: white; border-left: 5px solid #27ae60; padding: 20px; border-radius: 5px; display: flex; align-items: start; gap: 15px;">
                    <div style="font-size: 2rem; color: #27ae60;">🎫</div>
                    <div style="flex: 1;">
                        <h4 style="color: #2c3e50; margin: 0 0 10px 0;">Gestión de Tickets</h4>
                        <p style="color: #7f8c8d; margin: 0;">
                            Lista completa de todos los tickets con filtros por estado, prioridad y fecha. 
                            Permite asignar técnicos y ver detalles de cada ticket con su chat.
                        </p>
                    </div>
                </div>
                
                <!-- Personal Técnico -->
                <div style="background: white; border-left: 5px solid #9b59b6; padding: 20px; border-radius: 5px; display: flex; align-items: start; gap: 15px;">
                    <div style="font-size: 2rem; color: #9b59b6;">👥</div>
                    <div style="flex: 1;">
                        <h4 style="color: #2c3e50; margin: 0 0 10px 0;">Personal Técnico</h4>
                        <p style="color: #7f8c8d; margin: 0;">
                            Visualiza todos los técnicos con su estado (disponible/ocupado), ticket activo en el que trabajan, 
                            y estadísticas de rendimiento individual.
                        </p>
                    </div>
                </div>
                
                <!-- Reportes -->
                <div style="background: white; border-left: 5px solid #e74c3c; padding: 20px; border-radius: 5px; display: flex; align-items: start; gap: 15px;">
                    <div style="font-size: 2rem; color: #e74c3c;">📈</div>
                    <div style="flex: 1;">
                        <h4 style="color: #2c3e50; margin: 0 0 10px 0;">Reportes y Resúmenes</h4>
                        <p style="color: #7f8c8d; margin: 0;">
                            Genera reportes detallados: general, mantenimiento preventivo, rendimiento de técnicos. 
                            También incluye resúmenes diarios/semanales y alertas de tickets antiguos.
                        </p>
                    </div>
                </div>
                
                <!-- Chat -->
                <div style="background: white; border-left: 5px solid #f39c12; padding: 20px; border-radius: 5px; display: flex; align-items: start; gap: 15px;">
                    <div style="font-size: 2rem; color: #f39c12;">💬</div>
                    <div style="flex: 1;">
                        <h4 style="color: #2c3e50; margin: 0 0 10px 0;">Chat en Tiempo Real</h4>
                        <p style="color: #7f8c8d; margin: 0;">
                            Comunicación directa entre docente y técnico dentro de cada ticket. 
                            Permite resolver dudas, solicitar información y dar seguimiento al problema.
                        </p>
                    </div>
                </div>
                
                <!-- Notificaciones -->
                <div style="background: white; border-left: 5px solid #16a085; padding: 20px; border-radius: 5px; display: flex; align-items: start; gap: 15px;">
                    <div style="font-size: 2rem; color: #16a085;">🔔</div>
                    <div style="flex: 1;">
                        <h4 style="color: #2c3e50; margin: 0 0 10px 0;">Notificaciones</h4>
                        <p style="color: #7f8c8d; margin: 0;">
                            Sistema automático de notificaciones: cuando se asigna un ticket, cambia de estado, 
                            hay mensajes nuevos, o hay tickets antiguos sin resolver.
                        </p>
                    </div>
                </div>
            </div>
        </div>
    </div>
    
    <!-- Preguntas Frecuentes -->
    <div class="faq-section">
        <h2 style="color: #2c3e50; margin-bottom: 30px;">
            <i class="fas fa-question"></i> Preguntas Frecuentes
        </h2>
        
        <div class="faq-item">
            <div class="faq-question">
                <i class="fas fa-chevron-right" style="color: #27ae60;"></i>
                ¿Cómo asigno un ticket a un técnico?
            </div>
            <div class="faq-answer">
                Ve a "Gestión de Tickets", busca el ticket que deseas asignar, haz clic en el botón "Asignar", 
                selecciona el técnico del dropdown y confirma. El técnico recibirá una notificación automáticamente.
            </div>
        </div>
        
        <div class="faq-item">
            <div class="faq-question">
                <i class="fas fa-chevron-right" style="color: #27ae60;"></i>
                ¿Cómo creo un nuevo técnico?
            </div>
            <div class="faq-answer">
                Ve a "Usuarios", haz clic en "Nuevo Usuario", completa el formulario y selecciona "Técnico" en el campo de rol.
                El técnico podrá iniciar sesión con las credenciales que configures.
            </div>
        </div>
        
        <div class="faq-item">
            <div class="faq-question">
                <i class="fas fa-chevron-right" style="color: #27ae60;"></i>
                ¿Cómo veo el rendimiento de mi equipo?
            </div>
            <div class="faq-answer">
                Puedes ver el rendimiento en dos lugares: "Personal Técnico" para una vista general con estadísticas,
                o "Reportes" > "Rendimiento Técnicos" para un análisis detallado exportable.
            </div>
        </div>
        
        <div class="faq-item">
            <div class="faq-question">
                <i class="fas fa-chevron-right" style="color: #27ae60;"></i>
                ¿Cómo exporto reportes?
            </div>
            <div class="faq-answer">
                Ve a "Reportes", selecciona el tipo de reporte, configura los filtros opcionales, 
                haz clic en "Vista Previa" y luego en "Exportar a Excel" o "Exportar a PDF".
            </div>
        </div>
        
        <div class="faq-item">
            <div class="faq-question">
                <i class="fas fa-chevron-right" style="color: #27ae60;"></i>
                ¿Qué significa cada prioridad de ticket?
            </div>
            <div class="faq-answer">
                <ul style="margin-top: 10px;">
                    <li><strong>Crítica:</strong> Problema que impide trabajar, requiere atención inmediata</li>
                    <li><strong>Alta:</strong> Problema importante que afecta el trabajo normal</li>
                    <li><strong>Media:</strong> Problema que causa inconvenientes pero tiene alternativas</li>
                    <li><strong>Baja:</strong> Mejora o problema menor que no afecta el trabajo</li>
                </ul>
            </div>
        </div>
        
        <div class="faq-item">
            <div class="faq-question">
                <i class="fas fa-chevron-right" style="color: #27ae60;"></i>
                ¿Cómo cambio mi contraseña?
            </div>
            <div class="faq-answer">
                Por seguridad, el cambio de contraseña debe ser realizado por un administrador del sistema. 
                Contacta al equipo de soporte con tu solicitud.
            </div>
        </div>
        
        <div class="faq-item">
            <div class="faq-question">
                <i class="fas fa-chevron-right" style="color: #27ae60;"></i>
                ¿Por qué un técnico no puede recibir más tickets?
            </div>
            <div class="faq-answer">
                El sistema tiene una regla: <strong>un técnico solo puede trabajar en un ticket a la vez</strong>. 
                Cuando un técnico tiene un ticket con estado "en progreso", aparecerá como "OCUPADO" y no se le pueden asignar más tickets 
                hasta que termine (cambie el estado a "resuelto" o "rechazado").
            </div>
        </div>
        
        <div class="faq-item">
            <div class="faq-question">
                <i class="fas fa-chevron-right" style="color: #27ae60;"></i>
                ¿Cómo funcionan las notificaciones?
            </div>
            <div class="faq-answer">
                El sistema envía notificaciones automáticas en estos casos:
                <ul style="margin-top: 10px;">
                    <li>Cuando un técnico recibe un ticket asignado</li>
                    <li>Cuando el estado de un ticket cambia</li>
                    <li>Cuando hay mensajes nuevos en el chat</li>
                    <li>Cuando hay tickets con más de 7 días sin resolver</li>
                </ul>
            </div>
        </div>
        
        <div class="faq-item">
            <div class="faq-question">
                <i class="fas fa-chevron-right" style="color: #27ae60;"></i>
                ¿Qué es el resumen diario/semanal?
            </div>
            <div class="faq-answer">
                Los resúmenes muestran estadísticas del sistema:<br>
                <strong>Resumen Diario:</strong> Tickets creados, resueltos y pendientes de un día específico.<br>
                <strong>Resumen Semanal:</strong> Estadísticas de toda la semana con rendimiento de técnicos, categorías más reportadas y gráficas de actividad.
            </div>
        </div>
        
        <div class="faq-item">
            <div class="faq-question">
                <i class="fas fa-chevron-right" style="color: #27ae60;"></i>
                ¿Qué diferencia hay entre "pendiente" y "en progreso"?
            </div>
            <div class="faq-answer">
                <strong>Pendiente:</strong> El ticket está asignado pero el técnico aún no ha empezado a trabajar en él.<br>
                <strong>En progreso:</strong> El técnico está activamente trabajando en resolver el problema. 
                Solo puede haber un ticket "en progreso" por técnico.
            </div>
        </div>
    </div>
    
    <!-- CASOS DE USO COMUNES -->
    <div class="faq-section" style="margin-top: 30px;">
        <h2 style="color: #2c3e50; margin-bottom: 30px; text-align: center;">
            <i class="fas fa-lightbulb"></i> Casos de Uso Comunes
        </h2>
        
        <div style="display: grid; gap: 20px;">
            <!-- Caso 1 -->
            <div style="background: linear-gradient(135deg, #e74c3c, #c0392b); color: white; padding: 25px; border-radius: 10px;">
                <h4 style="margin: 0 0 15px 0; font-size: 1.2rem;">🚨 Problema Crítico Urgente</h4>
                <div style="background: rgba(255,255,255,0.2); padding: 15px; border-radius: 5px; line-height: 1.8;">
                    <strong>Situación:</strong> PC del aula principal no enciende, clase en 10 minutos<br>
                    <strong>Pasos:</strong><br>
                    1. Docente crea ticket con prioridad "Crítica"<br>
                    2. Admin asigna inmediatamente a técnico disponible<br>
                    3. Técnico recibe notificación y cambia estado a "en progreso"<br>
                    4. Técnico usa chat para pedir detalles si es necesario<br>
                    5. Una vez resuelto, marca como "resuelto" y todos son notificados
                </div>
            </div>
            
            <!-- Caso 2 -->
            <div style="background: linear-gradient(135deg, #27ae60, #229954); color: white; padding: 25px; border-radius: 10px;">
                <h4 style="margin: 0 0 15px 0; font-size: 1.2rem;">📊 Generar Reporte Mensual</h4>
                <div style="background: rgba(255,255,255,0.2); padding: 15px; border-radius: 5px; line-height: 1.8;">
                    <strong>Situación:</strong> Necesitas presentar el rendimiento del mes<br>
                    <strong>Pasos:</strong><br>
                    1. Ir a "Reportes" > "Rendimiento de Técnicos"<br>
                    2. Seleccionar rango de fechas del mes<br>
                    3. Click en "Vista Previa" para revisar<br>
                    4. Click en "Exportar a Excel" o "Exportar a PDF"<br>
                    5. El archivo se descarga automáticamente
                </div>
            </div>
            
            <!-- Caso 3 -->
            <div style="background: linear-gradient(135deg, #3498db, #2980b9); color: white; padding: 25px; border-radius: 10px;">
                <h4 style="margin: 0 0 15px 0; font-size: 1.2rem;">🔍 Revisar Tickets Antiguos</h4>
                <div style="background: rgba(255,255,255,0.2); padding: 15px; border-radius: 5px; line-height: 1.8;">
                    <strong>Situación:</strong> Quieres ver qué tickets llevan mucho tiempo sin resolver<br>
                    <strong>Pasos:</strong><br>
                    1. Ir al "Dashboard" (verás alerta si hay tickets antiguos)<br>
                    2. O ir a "Resúmenes" para ver lista completa<br>
                    3. Los tickets con >7 días aparecen con alerta roja<br>
                    4. Puedes reasignar o darles prioridad<br>
                    5. El sistema te muestra cuántos días llevan abiertos
                </div>
            </div>
            
            <!-- Caso 4 -->
            <div style="background: linear-gradient(135deg, #9b59b6, #8e44ad); color: white; padding: 25px; border-radius: 10px;">
                <h4 style="margin: 0 0 15px 0; font-size: 1.2rem;">👥 Distribuir Carga de Trabajo</h4>
                <div style="background: rgba(255,255,255,0.2); padding: 15px; border-radius: 5px; line-height: 1.8;">
                    <strong>Situación:</strong> Tienes varios tickets y quieres asignarlos equitativamente<br>
                    <strong>Pasos:</strong><br>
                    1. Ir a "Personal Técnico" para ver quién está disponible<br>
                    2. Los técnicos con ✅ DISPONIBLE pueden recibir tickets<br>
                    3. Los técnicos con 🔴 OCUPADO ya están trabajando<br>
                    4. En "Gestión de Tickets", asignar solo a disponibles<br>
                    5. El sistema te impedirá asignar a técnicos ocupados
                </div>
            </div>
        </div>
    </div>
    
    <!-- Contacto -->
    <div class="contact-box">
        <h3><i class="fas fa-headset"></i> ¿Necesitas más ayuda?</h3>
        <p style="margin-bottom: 20px;">Nuestro equipo de soporte está aquí para ayudarte</p>
        <div class="contact-info">
            <div class="contact-item">
                <i class="fas fa-envelope"></i>
                <strong>Email</strong>
                <span>soporte@sistema.edu</span>
            </div>
            <div class="contact-item">
                <i class="fas fa-phone"></i>
                <strong>Teléfono</strong>
                <span>+52 (555) 123-4567</span>
            </div>
            <div class="contact-item">
                <i class="fas fa-clock"></i>
                <strong>Horario</strong>
                <span>Lun-Vie 8:00 - 18:00</span>
            </div>
        </div>
    </div>
    
    <!-- Documentación -->
    <div style="background: white; padding: 30px; border-radius: 15px; box-shadow: 0 5px 15px rgba(0,0,0,0.1); margin-top: 30px; text-align: center;">
        <h3 style="color: #2c3e50; margin-bottom: 20px;">
            <i class="fas fa-file-alt"></i> Documentación Completa
        </h3>
        <p style="color: #7f8c8d; margin-bottom: 20px;">
            Encuentra toda la documentación técnica y guías de uso en los archivos Markdown del proyecto:
        </p>
        <div style="display: flex; gap: 15px; justify-content: center; flex-wrap: wrap;">
            <span style="background: #ecf0f1; padding: 10px 20px; border-radius: 20px; font-weight: 600;">
                <i class="fas fa-book"></i> SISTEMA_TECNICOS_NOTIFICACIONES.md
            </span>
            <span style="background: #ecf0f1; padding: 10px 20px; border-radius: 20px; font-weight: 600;">
                <i class="fas fa-book"></i> NAVEGACION_ADMIN_ACTUALIZADA.md
            </span>
            <span style="background: #ecf0f1; padding: 10px 20px; border-radius: 20px; font-weight: 600;">
                <i class="fas fa-book"></i> REPORTE_RENDIMIENTO_TECNICOS.md
            </span>
        </div>
    </div>
</div>
