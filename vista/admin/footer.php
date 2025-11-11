    </main>

    <!-- FOOTER -->
    <footer class="admin-footer">
        <div class="footer-content">
            <div class="footer-section">
                <h3>Sistema de Soporte de Tickets</h3>
                <p>Plataforma de gestión de incidencias para instituciones educativas</p>
            
            </div>
            
            <div class="footer-section">
                <h4>Enlaces Rápidos</h4>
                <ul class="footer-links">
                   
                    <li><a href="tickets.php">Tickets</a></li>
                    <li><a href="usuarios.php">Usuarios</a></li>
                    <li><a href="reportes.php">Reportes</a></li>
                </ul>
            </div>
            
            <div class="footer-section">
                <h4>Soporte</h4>
                <ul class="footer-links">
                    <li><a href="#">Centro de Ayuda</a></li>
                    <li><a href="#">Documentación</a></li>
                    <li><a href="#">Contactar Soporte</a></li>
                    
                </ul>
            </div>
            
            <div class="footer-section">
                <h4>Contacto</h4>
                <div class="contact-info">
                    <p><i class="fas fa-map-marker-alt"></i> Av. Universidad 123, Ciudad</p>
                    <p><i class="fas fa-phone"></i> +1 234 567 890</p>
                    <p><i class="fas fa-envelope"></i> soporte@escuela.edu</p>
                </div>
            </div>
        </div>
        
        <div class="footer-bottom">
            <p>&copy; 2025 Sistema de Soporte de Tickets. Todos los derechos reservados.</p>
            
        </div>
    </footer>

    <style>
        /* FOOTER STYLES */
        .admin-footer {
            background-color: var(--secondary-color);
            color: white;
            margin-top: auto;
        }

        .footer-content {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(250px, 1fr));
            gap: 30px;
            padding: 40px 30px;
            max-width: 1400px;
            margin: 0 auto;
        }

        .footer-section h3 {
            font-size: 1.3rem;
            margin-bottom: 15px;
            color: var(--light-color);
        }

        .footer-section h4 {
            font-size: 1.1rem;
            margin-bottom: 15px;
            color: var(--light-color);
        }

        .footer-section p {
            line-height: 1.6;
            opacity: 0.8;
            margin-bottom: 15px;
        }

        .social-links {
            display: flex;
            gap: 10px;
        }

        .social-link {
            display: flex;
            align-items: center;
            justify-content: center;
            width: 40px;
            height: 40px;
            background-color: rgba(255, 255, 255, 0.1);
            border-radius: 50%;
            color: white;
            text-decoration: none;
            transition: background-color 0.3s;
        }

        .social-link:hover {
            background-color: var(--primary-color);
        }

        .footer-links {
            list-style: none;
        }

        .footer-links li {
            margin-bottom: 10px;
        }

        .footer-links a {
            color: rgba(255, 255, 255, 0.8);
            text-decoration: none;
            transition: color 0.3s;
        }

        .footer-links a:hover {
            color: var(--primary-color);
        }

        .contact-info p {
            display: flex;
            align-items: center;
            gap: 10px;
            margin-bottom: 10px;
        }

        .contact-info i {
            width: 20px;
            color: var(--primary-color);
        }

        .footer-bottom {
            background-color: rgba(0, 0, 0, 0.2);
            padding: 20px 30px;
            display: flex;
            justify-content: space-between;
            align-items: center;
            flex-wrap: wrap;
            gap: 15px;
        }

        .footer-bottom p {
            opacity: 0.8;
            margin: 0;
        }

        .footer-bottom .footer-links {
            display: flex;
            gap: 20px;
        }

        /* RESPONSIVE FOOTER */
        @media (max-width: 768px) {
            .footer-content {
                padding: 30px 20px;
                gap: 20px;
            }

            .footer-bottom {
                padding: 15px 20px;
                flex-direction: column;
                text-align: center;
            }

            .footer-bottom .footer-links {
                justify-content: center;
            }
        }
    </style>

    <!-- Bootstrap JS Bundle -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>