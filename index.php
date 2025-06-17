<?php
// Incluir el archivo de configuración para manejar sesiones y base de datos
include 'config.php';

// Verificar si el usuario ha iniciado sesión

?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Página de Reservas</title>
    <link rel="stylesheet" href="css/index.css">
</head>
<body>
    <!-- Incluir el archivo de la cabecera -->
    <?php include 'header.php'; ?>

    <!-- Imagen campo de fútbol encima de Instalaciones Deportivas -->

    <!-- Sección de Instalaciones Deportivas y Eventos Públicos (lado a lado) -->
    <section class="secciones">
        <!-- Instalaciones Deportivas -->
        <div class="instalaciones">
            <img src="img/ComplejoDeportivo.png" alt="Instalaciones Deportivas">
            <h2>Instalaciones Deportivas</h2>
            <a href="reservas.php">
                <button class="btn">Ver Reservas</button>
            </a>
        </div>

        <!-- Eventos Públicos -->
        <div class="eventos">
            <img src="img/eventos.jpg" alt="Eventos Públicos">
            <h2>Eventos Públicos</h2>
            <a href="eventos.php">
                <button class="btn">Ver Eventos</button>
            </a>
        </div>
    </section>

    <footer>
        <div class="footer-contenido">
            <div class="contacto">
                <h3>Contacto</h3>
                <p>📞 Teléfono: 123-456-789</p>
                <p>📧 Email: contacto@empresa.com</p>
                <p>📍 Dirección: Calle Ejemplo 123, Ciudad</p>
                <p>🕒 Horario: Lunes - Viernes, 9 AM - 6 PM</p>
            </div>
            
            <div class="redes-sociales">
                <h3>Redes Sociales</h3>
                <div class="redes">
                    <a href="https://www.instagram.com" target="_blank">
                        <img src="img/instagram.png" alt="Instagram">
                        <p>Instagram</p>
                    </a>
                    <a href="https://www.twitter.com" target="_blank">
                        <img src="img/twitter.png" alt="Twitter">
                        <p>Twitter</p>
                    </a>
                    <a href="https://www.tiktok.com" target="_blank">
                        <img src="img/tiktok.png" alt="TikTok">
                        <p>TikTok</p>
                    </a>
                </div>
            </div>
            
            <div class="enlaces-adicionales">
                <h3>Enlaces Rápidos</h3>
                <ul>
                    <li><a href="terminos.html">Términos y Condiciones</a></li>
                    <li><a href="politica-privacidad.html">Política de Privacidad</a></li>
                    <li><a href="ayuda.html">Ayuda</a></li>
                    <li><a href="quienes-somos.html">Sobre Nosotros</a></li>
                </ul>
            </div>
        </div>
    
        <div class="suscripcion">
            <h3>Suscríbete a nuestro boletín</h3>
            <form action="#" method="POST">
                <input type="email" placeholder="Ingresa tu correo" required>
                <button type="submit">Suscribirse</button>
            </form>
        </div>
    </footer>
</body>
</html>
