<?php
// Vista de Ayuda para Docentes
include 'header.php';
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
        <h1><i class="fas fa-question-circle"></i> Centro de Ayuda - Docentes</h1>
        <p>Aprende a usar el sistema de tickets de soporte técnico</p>
    </div>
    
    <!-- CÓMO FUNCIONA PARA DOCENTES -->
    <div class="faq-section" style="margin-bottom: 30px;">
        <h2 style="color: #2c3e50; margin-bottom: 30px; text-align: center;">
            <i class="fas fa-lightbulb"></i> ¿Cómo Usar el Sistema de Tickets?
        </h2>
        
        <div style="background: linear-gradient(135deg, #3498db, #2980b9); color: white; padding: 30px; border-radius: 10px; margin-bottom: 30px;">
            <h3 style="margin: 0 0 20px 0;"><i class="fas fa-info-circle"></i> Bienvenido al Sistema</h3>
            <p style="margin: 0; line-height: 1.8;">
                Este sistema te permite reportar problemas técnicos de forma fácil y rápida. 
                Crea un <strong>ticket</strong> cuando tengas algún problema con equipos, software o infraestructura, 
                y un técnico lo atenderá lo antes posible. Podrás dar seguimiento en tiempo real y comunicarte por chat.
            </p>
        </div>
        
        <!-- FLUJO PASO A PASO PARA DOCENTES -->
        <div style="background: #ecf0f1; padding: 30px; border-radius: 10px; margin-bottom: 30px;">
            <h3 style="color: #2c3e50; margin: 0 0 25px 0; text-align: center;">
                <i class="fas fa-list-ol"></i> Proceso Paso a Paso
            </h3>
            
            <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(280px, 1fr)); gap: 20px;">
                <!-- Paso 1 -->
                <div style="background: white; padding: 25px; border-radius: 10px; border-left: 5px solid #e74c3c;">
                    <div style="font-size: 2.5rem; color: #e74c3c; margin-bottom: 10px; text-align: center;">1️⃣</div>
                    <h4 style="color: #2c3e50; margin: 0 0 10px 0; text-align: center;">Reporta el Problema</h4>
                    <p style="color: #7f8c8d; margin: 0; font-size: 0.9rem; line-height: 1.6;">
                        Click en <strong>"Nuevo Ticket"</strong> en el menú. Describe tu problema, selecciona la categoría y prioridad.
                    </p>
                </div>
                
                <!-- Paso 2 -->
                <div style="background: white; padding: 25px; border-radius: 10px; border-left: 5px solid #f39c12;">
                    <div style="font-size: 2.5rem; color: #f39c12; margin-bottom: 10px; text-align: center;">2️⃣</div>
                    <h4 style="color: #2c3e50; margin: 0 0 10px 0; text-align: center;">Espera Asignación</h4>
                    <p style="color: #7f8c8d; margin: 0; font-size: 0.9rem; line-height: 1.6;">
                        Un administrador revisará tu ticket y lo asignará a un técnico. Recibirás una notificación.
                    </p>
                </div>
                
                <!-- Paso 3 -->
                <div style="background: white; padding: 25px; border-radius: 10px; border-left: 5px solid #3498db;">
                    <div style="font-size: 2.5rem; color: #3498db; margin-bottom: 10px; text-align: center;">3️⃣</div>
                    <h4 style="color: #2c3e50; margin: 0 0 10px 0; text-align: center;">Da Seguimiento</h4>
                    <p style="color: #7f8c8d; margin: 0; font-size: 0.9rem; line-height: 1.6;">
                        Ve a <strong>"Mis Tickets"</strong> para ver el estado. Puedes chatear con el técnico si necesitas aclarar algo.
                    </p>
                </div>
                
                <!-- Paso 4 -->
                <div style="background: white; padding: 25px; border-radius: 10px; border-left: 5px solid #27ae60;">
                    <div style="font-size: 2.5rem; color: #27ae60; margin-bottom: 10px; text-align: center;">4️⃣</div>
                    <h4 style="color: #2c3e50; margin: 0 0 10px 0; text-align: center;">Problema Resuelto</h4>
                    <p style="color: #7f8c8d; margin: 0; font-size: 0.9rem; line-height: 1.6;">
                        El técnico marcará el ticket como resuelto. Recibirás notificación cuando esté listo.
                    </p>
                </div>
            </div>
        </div>
        
        <!-- FUNCIONES DISPONIBLES -->
        <div style="margin-bottom: 30px;">
            <h3 style="color: #2c3e50; margin: 0 0 20px 0; text-align: center;">
                <i class="fas fa-tools"></i> Funciones Disponibles
            </h3>
            
            <div style="display: grid; gap: 15px;">
                <!-- Dashboard -->
                <div style="background: white; border-left: 5px solid #3498db; padding: 20px; border-radius: 5px; display: flex; align-items: start; gap: 15px;">
                    <div style="font-size: 2rem; color: #3498db;">📊</div>
                    <div style="flex: 1;">
                        <h4 style="color: #2c3e50; margin: 0 0 10px 0;">Dashboard</h4>
                        <p style="color: #7f8c8d; margin: 0;">
                            Vista rápida de tus estadísticas: total de tickets creados, pendientes, en progreso y resueltos.
                        </p>
                    </div>
                </div>
                
                <!-- Nuevo Ticket -->
                <div style="background: white; border-left: 5px solid #27ae60; padding: 20px; border-radius: 5px; display: flex; align-items: start; gap: 15px;">
                    <div style="font-size: 2rem; color: #27ae60;">➕</div>
                    <div style="flex: 1;">
                        <h4 style="color: #2c3e50; margin: 0 0 10px 0;">Nuevo Ticket</h4>
                        <p style="color: #7f8c8d; margin: 0;">
                            Crea un nuevo ticket cuando tengas un problema técnico. Incluye título, descripción detallada, categoría y prioridad.
                        </p>
                    </div>
                </div>
                
                <!-- Mis Tickets -->
                <div style="background: white; border-left: 5px solid #9b59b6; padding: 20px; border-radius: 5px; display: flex; align-items: start; gap: 15px;">
                    <div style="font-size: 2rem; color: #9b59b6;">📋</div>
                    <div style="flex: 1;">
                        <h4 style="color: #2c3e50; margin: 0 0 10px 0;">Mis Tickets</h4>
                        <p style="color: #7f8c8d; margin: 0;">
                            Lista completa de todos tus tickets con filtros por estado. Ve detalles, chatea con el técnico y da seguimiento.
                        </p>
                    </div>
                </div>
                
                <!-- Chat -->
                <div style="background: white; border-left: 5px solid #f39c12; padding: 20px; border-radius: 5px; display: flex; align-items: start; gap: 15px;">
                    <div style="font-size: 2rem; color: #f39c12;">💬</div>
                    <div style="flex: 1;">
                        <h4 style="color: #2c3e50; margin: 0 0 10px 0;">Chat en Tiempo Real</h4>
                        <p style="color: #7f8c8d; margin: 0;">
                            Comunícate directamente con el técnico asignado. Pregunta, aclara dudas o proporciona información adicional.
                        </p>
                    </div>
                </div>
                
                <!-- Notificaciones -->
                <div style="background: white; border-left: 5px solid #16a085; padding: 20px; border-radius: 5px; display: flex; align-items: start; gap: 15px;">
                    <div style="font-size: 2rem; color: #16a085;">🔔</div>
                    <div style="flex: 1;">
                        <h4 style="color: #2c3e50; margin: 0 0 10px 0;">Notificaciones</h4>
                        <p style="color: #7f8c8d; margin: 0;">
                            Recibe alertas cuando tu ticket sea asignado, cambie de estado o haya mensajes nuevos del técnico.
                        </p>
                    </div>
                </div>
            </div>
        </div>
        
        <!-- PRIORIDADES EXPLICADAS -->
        <div style="background: white; padding: 25px; border-radius: 10px; border: 2px solid #ecf0f1;">
            <h3 style="color: #2c3e50; margin: 0 0 20px 0; text-align: center;">
                <i class="fas fa-exclamation-triangle"></i> ¿Qué Prioridad Seleccionar?
            </h3>
            
            <div style="display: grid; gap: 15px;">
                <div style="background: linear-gradient(135deg, #e74c3c, #c0392b); color: white; padding: 20px; border-radius: 8px;">
                    <h4 style="margin: 0 0 10px 0; font-size: 1.2rem;">🔴 CRÍTICA</h4>
                    <p style="margin: 0; opacity: 0.95;">
                        Problema que te impide trabajar completamente. Ejemplo: PC no enciende antes de clase, proyector no funciona durante presentación.
                    </p>
                </div>
                
                <div style="background: linear-gradient(135deg, #f39c12, #e67e22); color: white; padding: 20px; border-radius: 8px;">
                    <h4 style="margin: 0 0 10px 0; font-size: 1.2rem;">🟠 ALTA</h4>
                    <p style="margin: 0; opacity: 0.95;">
                        Problema importante que afecta tu trabajo. Ejemplo: Internet muy lento, impresora no imprime documentos importantes.
                    </p>
                </div>
                
                <div style="background: linear-gradient(135deg, #3498db, #2980b9); color: white; padding: 20px; border-radius: 8px;">
                    <h4 style="margin: 0 0 10px 0; font-size: 1.2rem;">🔵 MEDIA</h4>
                    <p style="margin: 0; opacity: 0.95;">
                        Problema que causa inconvenientes pero tiene solución temporal. Ejemplo: Software lento pero funciona, mouse con fallas ocasionales.
                    </p>
                </div>
                
                <div style="background: linear-gradient(135deg, #95a5a6, #7f8c8d); color: white; padding: 20px; border-radius: 8px;">
                    <h4 style="margin: 0 0 10px 0; font-size: 1.2rem;">⚪ BAJA</h4>
                    <p style="margin: 0; opacity: 0.95;">
                        Mejora o problema menor que no afecta el trabajo. Ejemplo: Solicitud de nuevo software, ajustes de configuración.
                    </p>
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
                ¿Cómo creo un nuevo ticket?
            </div>
            <div class="faq-answer">
                Ve al menú y haz clic en <strong>"Nuevo Ticket"</strong>. Completa el formulario con:<br>
                • <strong>Título:</strong> Breve descripción del problema<br>
                • <strong>Descripción:</strong> Detalla qué pasa, cuándo empezó, qué has intentado<br>
                • <strong>Categoría:</strong> Tipo de problema (hardware, software, red, etc.)<br>
                • <strong>Prioridad:</strong> Qué tan urgente es<br>
                Luego haz clic en "Crear Ticket" y listo.
            </div>
        </div>
        
        <div class="faq-item">
            <div class="faq-question">
                <i class="fas fa-chevron-right" style="color: #27ae60;"></i>
                ¿Cómo veo el estado de mi ticket?
            </div>
            <div class="faq-answer">
                Ve a <strong>"Mis Tickets"</strong> en el menú. Verás una lista de todos tus tickets con su estado actual:
                <ul style="margin-top: 10px;">
                    <li><strong>Pendiente:</strong> Esperando asignación a un técnico</li>
                    <li><strong>En Progreso:</strong> El técnico está trabajando en resolverlo</li>
                    <li><strong>Resuelto:</strong> El problema fue solucionado</li>
                </ul>
            </div>
        </div>
        
        <div class="faq-item">
            <div class="faq-question">
                <i class="fas fa-chevron-right" style="color: #27ae60;"></i>
                ¿Cómo me comunico con el técnico?
            </div>
            <div class="faq-answer">
                En <strong>"Mis Tickets"</strong>, haz clic en "Ver Chat" del ticket que quieras. 
                Podrás enviar mensajes en tiempo real al técnico asignado. 
                Usa el chat para proporcionar información adicional, hacer preguntas o dar seguimiento.
            </div>
        </div>
        
        <div class="faq-item">
            <div class="faq-question">
                <i class="fas fa-chevron-right" style="color: #27ae60;"></i>
                ¿Cuánto tiempo tardan en atender mi ticket?
            </div>
            <div class="faq-answer">
                Depende de la <strong>prioridad</strong>:<br>
                • <strong>Crítica:</strong> Atención inmediata (minutos)<br>
                • <strong>Alta:</strong> El mismo día<br>
                • <strong>Media:</strong> 1-2 días<br>
                • <strong>Baja:</strong> 3-5 días<br>
                Recibirás notificaciones cuando sea asignado y cuando cambie de estado.
            </div>
        </div>
        
        <div class="faq-item">
            <div class="faq-question">
                <i class="fas fa-chevron-right" style="color: #27ae60;"></i>
                ¿Recibiré notificaciones?
            </div>
            <div class="faq-answer">
                Sí, recibirás notificaciones automáticas cuando:
                <ul style="margin-top: 10px;">
                    <li>Tu ticket sea asignado a un técnico</li>
                    <li>El estado de tu ticket cambie</li>
                    <li>El técnico te envíe un mensaje por chat</li>
                    <li>Tu ticket sea resuelto</li>
                </ul>
                Verás las notificaciones en el icono de campana 🔔 en el menú superior.
            </div>
        </div>
        
        <div class="faq-item">
            <div class="faq-question">
                <i class="fas fa-chevron-right" style="color: #27ae60;"></i>
                ¿Qué información debo incluir en mi ticket?
            </div>
            <div class="faq-answer">
                Para que el técnico pueda ayudarte mejor, incluye:
                <ul style="margin-top: 10px;">
                    <li><strong>Qué equipo o sistema:</strong> PC, proyector, impresora, software específico</li>
                    <li><strong>Qué está pasando:</strong> Descripción clara del problema</li>
                    <li><strong>Cuándo empezó:</strong> ¿Es nuevo o recurrente?</li>
                    <li><strong>Ubicación:</strong> Aula, oficina, laboratorio</li>
                    <li><strong>Mensaje de error:</strong> Si aparece alguno, cópialo</li>
                </ul>
            </div>
        </div>
        
        <div class="faq-item">
            <div class="faq-question">
                <i class="fas fa-chevron-right" style="color: #27ae60;"></i>
                ¿Puedo crear múltiples tickets?
            </div>
            <div class="faq-answer">
                Sí, puedes crear todos los tickets que necesites. 
                Te recomendamos crear <strong>un ticket por cada problema diferente</strong> 
                para que sea más fácil darles seguimiento y resolverlos de forma independiente.
            </div>
        </div>
        
        <div class="faq-item">
            <div class="faq-question">
                <i class="fas fa-chevron-right" style="color: #27ae60;"></i>
                ¿Qué hago si mi problema es muy urgente?
            </div>
            <div class="faq-answer">
                1. Crea el ticket con prioridad <strong>"Crítica"</strong><br>
                2. En la descripción explica por qué es urgente (ejemplo: "tengo clase en 15 minutos")<br>
                3. Si es posible, contacta también por teléfono al equipo de soporte<br>
                Los tickets críticos reciben atención inmediata.
            </div>
        </div>
    </div>
    
    <!-- EJEMPLOS PRÁCTICOS -->
    <div class="faq-section" style="margin-top: 30px;">
        <h2 style="color: #2c3e50; margin-bottom: 30px; text-align: center;">
            <i class="fas fa-clipboard-list"></i> Ejemplos de Tickets Bien Reportados
        </h2>
        
        <div style="display: grid; gap: 20px;">
            <!-- Ejemplo 1 -->
            <div style="background: #f8f9fa; padding: 20px; border-radius: 10px; border-left: 5px solid #e74c3c;">
                <div style="display: flex; justify-content: space-between; align-items: start; margin-bottom: 15px;">
                    <h4 style="color: #2c3e50; margin: 0;">Ejemplo: PC no enciende</h4>
                    <span style="background: #e74c3c; color: white; padding: 5px 15px; border-radius: 20px; font-size: 0.85rem; font-weight: 600;">CRÍTICA</span>
                </div>
                <div style="background: white; padding: 15px; border-radius: 5px;">
                    <p style="margin: 0 0 10px 0;"><strong>Título:</strong> PC del Aula 205 no enciende</p>
                    <p style="margin: 0 0 10px 0;"><strong>Descripción:</strong> 
                    La computadora del escritorio del docente en el Aula 205 no enciende. 
                    He intentado conectarla a otro enchufe pero sigue sin responder. 
                    Tengo clase en 20 minutos y necesito el proyector que está conectado a esta PC.
                    </p>
                    <p style="margin: 0;"><strong>Categoría:</strong> Hardware | <strong>Prioridad:</strong> Crítica</p>
                </div>
            </div>
            
            <!-- Ejemplo 2 -->
            <div style="background: #f8f9fa; padding: 20px; border-radius: 10px; border-left: 5px solid #3498db;">
                <div style="display: flex; justify-content: space-between; align-items: start; margin-bottom: 15px;">
                    <h4 style="color: #2c3e50; margin: 0;">Ejemplo: Software lento</h4>
                    <span style="background: #3498db; color: white; padding: 5px 15px; border-radius: 20px; font-size: 0.85rem; font-weight: 600;">MEDIA</span>
                </div>
                <div style="background: white; padding: 15px; border-radius: 5px;">
                    <p style="margin: 0 0 10px 0;"><strong>Título:</strong> Excel muy lento en PC de sala de profesores</p>
                    <p style="margin: 0 0 10px 0;"><strong>Descripción:</strong> 
                    La PC de la sala de profesores abre Excel pero tarda mucho en responder. 
                    Cuando abro archivos grandes (más de 10MB) se queda congelada por varios minutos. 
                    El problema empezó hace 2 días. Otros programas funcionan normal.
                    </p>
                    <p style="margin: 0;"><strong>Categoría:</strong> Software | <strong>Prioridad:</strong> Media</p>
                </div>
            </div>
            
            <!-- Ejemplo 3 -->
            <div style="background: #f8f9fa; padding: 20px; border-radius: 10px; border-left: 5px solid #27ae60;">
                <div style="display: flex; justify-content: space-between; align-items: start; margin-bottom: 15px;">
                    <h4 style="color: #2c3e50; margin: 0;">Ejemplo: Solicitud de instalación</h4>
                    <span style="background: #95a5a6; color: white; padding: 5px 15px; border-radius: 20px; font-size: 0.85rem; font-weight: 600;">BAJA</span>
                </div>
                <div style="background: white; padding: 15px; border-radius: 5px;">
                    <p style="margin: 0 0 10px 0;"><strong>Título:</strong> Instalar Zoom en PC del Laboratorio 3</p>
                    <p style="margin: 0 0 10px 0;"><strong>Descripción:</strong> 
                    Necesito que instalen Zoom en la PC del Laboratorio 3 para dar clases virtuales. 
                    Usaré el laboratorio los martes y jueves de 10am a 12pm. 
                    No es urgente, puede ser durante esta semana.
                    </p>
                    <p style="margin: 0;"><strong>Categoría:</strong> Software | <strong>Prioridad:</strong> Baja</p>
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
</div>

<?php include 'footer.php'; ?>
