<?php
include 'config.php'; // Incluir la conexión a la base de datos

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Obtener los datos del formulario
    $nombre = $_POST['nombre'];
    $apellidos = $_POST['apellidos'];
    $email = $_POST['email'];
    $telefono = $_POST['telefono'];
    $fecha_nacimiento = $_POST['fecha-nacimiento'];
    $direccion = $_POST['direccion'] ?? ''; // Si no se ingresa, se deja vacío
    $password = $_POST['password'];
    $confirmar_password = $_POST['confirmar-password'];

    // Validar que las contraseñas coincidan
    if ($password !== $confirmar_password) {
        echo "Las contraseñas no coinciden.";
        exit();
    }

    // Cifrar la contraseña
    $passwordHash = password_hash($password, PASSWORD_DEFAULT);

    // Verificar si el correo ya está registrado
    $stmt = $conn->prepare("SELECT * FROM Usuarios WHERE email = ?");
    $stmt->bind_param("s", $email);
    $stmt->execute();
    $resultado = $stmt->get_result();

    if ($resultado->num_rows > 0) {
        echo "El correo electrónico ya está registrado.";
    } else {
        // Insertar el nuevo usuario en la base de datos
        $stmt = $conn->prepare("INSERT INTO Usuarios (nombre, apellidos, email, telefono, fecha_nacimiento, direccion, password) 
                                VALUES (?, ?, ?, ?, ?, ?, ?)");
        $stmt->bind_param("sssssss", $nombre, $apellidos, $email, $telefono, $fecha_nacimiento, $direccion, $passwordHash);

        if ($stmt->execute()) {
            echo "Usuario registrado con éxito.";
            header("Location: login.html"); // Redirigir al login
            exit();
        } else {
            echo "Error al registrar el usuario.";
        }
    }

    // Cerrar la conexión
    $stmt->close();
    $conn->close();
}
?>
