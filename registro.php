<?php
session_start(); // Iniciar sesión
include 'config.php'; // Incluir la conexión a la base de datos

// Verificar si el formulario fue enviado
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Obtener los datos del formulario
    $nombre = trim($_POST['nombre']);
    $apellidos = trim($_POST['apellidos']);
    $email = trim($_POST['email']);
    $telefono = trim($_POST['telefono']);
    $fecha_nacimiento = $_POST['fecha-nacimiento'];
    $direccion = isset($_POST['direccion']) ? trim($_POST['direccion']) : ''; // Si no se ingresa, se deja vacío
    $password = $_POST['password'];
    $confirmar_password = $_POST['confirmar-password'];

    // Verificar si los campos no están vacíos
    if (empty($nombre) || empty($apellidos) || empty($email) || empty($telefono) || empty($fecha_nacimiento) || empty($password) || empty($confirmar_password)) {
        echo "Por favor, completa todos los campos.";
        exit();
    }

    // Validar que las contraseñas coincidan
    if ($password !== $confirmar_password) {
        echo "Las contraseñas no coinciden.";
        exit();
    }

    // Validar el formato del correo electrónico
    if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        echo "El correo electrónico no es válido.";
        exit();
    }

    // Cifrar la contraseña usando password_hash
    $passwordHash = password_hash($password, PASSWORD_DEFAULT);

    // Verificar si el correo ya está registrado
    $stmt = $conn->prepare("SELECT * FROM usuarios WHERE email = ?");
    if ($stmt === false) {
        die("Error en la preparación de la consulta: " . $conn->error);
    }
    $stmt->bind_param("s", $email);
    $stmt->execute();
    $resultado = $stmt->get_result();

    if ($resultado->num_rows > 0) {
        echo "El correo electrónico ya está registrado.";
    } else {
        // Procesar la foto de perfil
        $foto_perfil = 'foto-default.png'; // Foto por defecto

        // Verificar si el usuario sube una foto
        if (isset($_FILES['foto_perfil']) && $_FILES['foto_perfil']['error'] == 0) {
            $file = $_FILES['foto_perfil'];
            $fileName = $_FILES['foto_perfil']['name'];
            $fileTmpName = $_FILES['foto_perfil']['tmp_name'];
            $fileSize = $_FILES['foto_perfil']['size'];
            $fileError = $_FILES['foto_perfil']['error'];

            // Verificar si no hay errores en la carga del archivo
            if ($fileError === 0) {
                $fileExtension = strtolower(pathinfo($fileName, PATHINFO_EXTENSION));
                $validExtensions = ['jpg', 'jpeg', 'png', 'gif'];

                if (in_array($fileExtension, $validExtensions)) {
                    // Crear un nombre único para la foto
                    $fileNewName = uniqid('', true) . "." . $fileExtension;
                    $fileDestination = 'uploads/usuarios/' . $fileNewName;

                    // Mover la foto a la carpeta de imágenes
                    if (move_uploaded_file($fileTmpName, $fileDestination)) {
                        $foto_perfil = $fileNewName; // Cambiar foto de perfil
                    } else {
                        echo "Error al subir la imagen.";
                        exit();
                    }
                } else {
                    echo "La extensión de la foto no es válida. Por favor sube una imagen en formato JPG, JPEG, PNG o GIF.";
                    exit();
                }
            } else {
                echo "Hubo un error al subir la foto de perfil.";
                exit();
            }
        }

        // Insertar el nuevo usuario en la base de datos con la foto de perfil y la contraseña cifrada
        $stmt = $conn->prepare("INSERT INTO Usuarios (nombre, apellidos, email, telefono, fecha_nacimiento, direccion, password, foto_perfil) 
                                VALUES (?, ?, ?, ?, ?, ?, ?, ?)");
        if ($stmt === false) {
            die("Error en la preparación de la consulta de inserción: " . $conn->error);
        }
        $stmt->bind_param("ssssssss", $nombre, $apellidos, $email, $telefono, $fecha_nacimiento, $direccion, $passwordHash, $foto_perfil);

        if ($stmt->execute()) {
            echo "Has logrado registrarte con éxito.";

            // Redirigir después de 2 segundos a la página de login
            echo '<script>
                    setTimeout(function() {
                        window.location.href = "login.php";
                    }, 2000);
                  </script>';
            exit();
        } else {
            echo "Error al registrar el usuario: " . $stmt->error;
        }
    }

    // Cerrar la conexión después de todas las operaciones
    $stmt->close();
    $conn->close();
}
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Registro</title>
    <link rel="stylesheet" href="css/registro.css">
</head>
<body>
    <header>
        <a href="index.php" class="logo">
            <img src="img/logo.png" alt="Logo">
        </a>
    </header>
    
    <main>
        <section class="formulario-registro">
            <h2>Registrarse</h2>
            <!-- El formulario ahora tiene la acción apuntando a 'registro.php' -->
            <form action="registro.php" method="POST" enctype="multipart/form-data">
               
                <!-- Nombre -->
                <label for="nombre">Nombre</label>
                <input type="text" id="nombre" name="nombre" required>

                <!-- Apellidos -->
                <label for="apellidos">Apellidos</label>
                <input type="text" id="apellidos" name="apellidos" required>

                <!-- Correo electrónico -->
                <label for="email">Correo Electrónico</label>
                <input type="email" id="email" name="email" required>

                <!-- Teléfono -->
                <label for="telefono">Teléfono</label>
                <input type="tel" id="telefono" name="telefono" placeholder="Ej:  609 594 078" required>

                <!-- Fecha de Nacimiento -->
                <label for="fecha-nacimiento">Fecha de Nacimiento</label>
                <input type="date" id="fecha-nacimiento" name="fecha-nacimiento" required>

                <!-- Dirección -->
                <label for="direccion">Dirección (opcional)</label>
                <input type="text" id="direccion" name="direccion" placeholder="Ej: Calle Falsa 123">

                <!-- Contraseña -->
                <label for="password">Contraseña</label>
                <input type="password" id="password" name="password" required>

                <!-- Confirmar Contraseña -->
                <label for="confirmar-password">Confirmar Contraseña</label>
                <input type="password" id="confirmar-password" name="confirmar-password" required>

                <!-- Foto de perfil -->
                <label for="foto_perfil">Foto de Perfil (opcional)</label>
                <input type="file" name="foto_perfil" id="foto_perfil" accept="image/*">

                <!-- Botón de Registro -->
                <button type="submit" class="btn">Registrarse</button>

                <!-- Enlace a Login -->
                <p class="login">¿Ya tienes cuenta? <a href="login.php">Inicia sesión</a></p>
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
