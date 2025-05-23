<?php
session_start();
include 'config.php';

$mensaje_error = "";
$mensaje_exito = "";

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $email = $_POST['email'];
    $passwordInput = $_POST['password'];

    if (empty($email) || empty($passwordInput)) {
        $mensaje_error = "Por favor, complete todos los campos.";
    } else {
        $stmt = $conn->prepare("SELECT * FROM Usuarios WHERE email = ?");
        $stmt->bind_param("s", $email);
        $stmt->execute();
        $result = $stmt->get_result();

        if ($result->num_rows > 0) {
            $user = $result->fetch_assoc();

            if (password_verify($passwordInput, $user['password'])) {
                $_SESSION['user_id'] = $user['id'];
                $_SESSION['user_name'] = $user['nombre'];
                $_SESSION['user_email'] = $user['email'];

                // Mensaje de éxito y guardarlo en la sesión
                $_SESSION['mensaje_exito'] = "¡Enhorabuena! Has iniciado sesión con éxito.";

                // Redirigir a la página principal después de 2 segundos
                header("refresh:2;url=index.php"); // O la página que quieras
                exit();
            } else {
                $mensaje_error = "Contraseña incorrecta.";
            }
        } else {
            $mensaje_error = "Correo no registrado. <a href='registro.html'>Regístrate aquí</a>";
        }

        $stmt->close();
    }

    $conn->close();
}
?>

<!-- Formulario HTML -->
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Iniciar Sesión</title>
    <link rel="stylesheet" href="css/login.css">
</head>
<body>
    <header>
        <a href="index.php" class="logo">
            <img src="img/logo.png" alt="Logo">
        </a>
    </header>

    <main>
        <section class="formulario-login">
            <h2>Iniciar Sesión</h2>

            <!-- Mostrar mensajes de error o éxito -->
            <?php if ($mensaje_error): ?>
                <div class="error"><?= $mensaje_error ?></div>
            <?php endif; ?>

            <?php if ($mensaje_exito): ?>
                <div class="exito"><?= $mensaje_exito ?></div>
            <?php endif; ?>

            <!-- FORMULARIO ENLAZADO A login.php -->
            <form action="login.php" method="POST">
                <label for="email">Correo Electrónico</label>
                <input type="email" id="email" name="email" required>

                <label for="password">Contraseña</label>
                <input type="password" id="password" name="password">

                <button type="submit" class="btn">Iniciar Sesión</button>

              
            </form>
        </section>
    </main>

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
