<?php
session_start();
require_once 'config.php';

// Comprobar si el formulario fue enviado
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    // Obtener los valores del formulario
    $email = $_POST['email'];
    $password = $_POST['password'];

    // Validar los datos
    if (empty($email) || empty($password)) {
        echo "Por favor, ingrese todos los campos.";
    } else {
        // Conexión a la base de datos
        $conn = new mysqli($servername, $username, $password, $dbname);

        // Verificar si la conexión fue exitosa
        if ($conn->connect_error) {
            die("La conexión a la base de datos ha fallado: " . $conn->connect_error);
        }

        // Consultar si el correo existe en la base de datos
        $sql = "SELECT * FROM Usuarios WHERE email = ?";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param('s', $email);
        $stmt->execute();
        $result = $stmt->get_result();

        // Verificar si el usuario existe
        if ($result->num_rows > 0) {
            $user = $result->fetch_assoc();

            // Verificar si la contraseña es correcta
            if (password_verify($password, $user['password'])) {
                // Iniciar sesión
                $_SESSION['user_id'] = $user['id'];
                $_SESSION['user_name'] = $user['nombre'];
                header("Location: dashboard.php"); // Redirigir a una página de bienvenida o al área de usuario
                exit();
            } else {
                echo "Contraseña incorrecta.";
            }
        } else {
            echo "Usuario no encontrado.";
        }

        // Cerrar la conexión
        $conn->close();
    }
}
?>

