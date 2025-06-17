<?php
// conexión a la BD
$servername = "localhost";
$username = "root";
$password = "";
$dbname = "afr_database";

$conn = new mysqli($servername, $username, $password, $dbname);
if ($conn->connect_error) {
    die("Conexión fallida: " . $conn->connect_error);
}

// datos del admin
$nombre = "Admin";
$apellidos = "Principal";
$email = "admin@ejemplo.com";
$telefono = "0000000000";
$fecha_nacimiento = "1990-01-01";
$direccion = "Calle Admin 1";
$password = password_hash("1234567890", PASSWORD_DEFAULT);
$rol = "admin";

$sql = "INSERT INTO Usuarios (nombre, apellidos, email, telefono, fecha_nacimiento, direccion, password, rol) VALUES (?, ?, ?, ?, ?, ?, ?, ?)";
$stmt = $conn->prepare($sql);
$stmt->bind_param("ssssssss", $nombre, $apellidos, $email, $telefono, $fecha_nacimiento, $direccion, $password, $rol);

if ($stmt->execute()) {
    echo "Usuario administrador creado correctamente.";
} else {
    echo "Error al crear usuario administrador: " . $stmt->error;
}

$stmt->close();
$conn->close();
?>
