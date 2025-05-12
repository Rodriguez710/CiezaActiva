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
        // Insertar el nuevo usuario en la base de datos con la contraseña cifrada
        $stmt = $conn->prepare("INSERT INTO Usuarios (nombre, apellidos, email, telefono, fecha_nacimiento, direccion, password) 
                                VALUES (?, ?, ?, ?, ?, ?, ?)");
        if ($stmt === false) {
            die("Error en la preparación de la consulta de inserción: " . $conn->error);
        }
        $stmt->bind_param("sssssss", $nombre, $apellidos, $email, $telefono, $fecha_nacimiento, $direccion, $passwordHash);

        if ($stmt->execute()) {
            echo "Has logrado registrarte con éxito.";

            // Redirigir después de 2 segundos a la página de login
            echo '<script>
                    setTimeout(function() {
                        window.location.href = "login.html";
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
