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
                $_SESSION['rol'] = $user['rol'];  // <-- Guardamos el rol aquí

                // Mensaje de éxito y guardarlo en la sesión
                $_SESSION['mensaje_exito'] = "¡Enhorabuena! Has iniciado sesión con éxito.";

                // Redirigir a la página principal después de 2 segundos
                header("refresh:2;url=index.php"); // O la página que quieras
                exit();
            } else {
                $mensaje_error = "Contraseña incorrecta.";
            }
        } else {
            $mensaje_error = "No existe usuario con ese correo.";
        }

        $stmt->close();
    }

    $conn->close();
}
?>

<!-- El resto del código HTML se mantiene igual -->
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

            <form action="login.php" method="POST">
                <label for="email">Correo Electrónico</label>
                <input type="email" id="email" name="email" required>

                <label for="password">Contraseña</label>
                <input type="password" id="password" name="password" required>

                <button type="submit" class="btn">Iniciar Sesión</button>

                <p>¿No tienes una cuenta? <a href="registro.php">Regístrate aquí</a></p>
            </form>
        </section>
    </main>

    <footer>
        <!-- Footer igual -->
    </footer>
</body>
</html>
