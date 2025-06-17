<?php
$servername = "localhost";
$username = "root";
$password = "";
$dbname = "modelo_er";

// Conexión al servidor
$conn = mysqli_connect($servername, $username, $password);

if (!$conn) {
    die("Conexión fallida: " . mysqli_connect_error());
}

// Crear base de datos si no existe
$sql = "CREATE DATABASE IF NOT EXISTS $dbname";
if (!mysqli_query($conn, $sql)) {
    die("Error creando base de datos: " . mysqli_error($conn));
}

// Seleccionar la base de datos
mysqli_select_db($conn, $dbname);

// Crear tabla Usuarios
$sql = "CREATE TABLE IF NOT EXISTS Usuarios (
    id INT AUTO_INCREMENT PRIMARY KEY,         -- Identificador único para cada usuario
    nombre VARCHAR(100) NOT NULL,               -- Nombre del usuario
    apellidos VARCHAR(100) NOT NULL,            -- Apellidos del usuario
    email VARCHAR(100) UNIQUE NOT NULL,         -- Correo electrónico único
    telefono VARCHAR(20) NOT NULL,              -- Número de teléfono
    fecha_nacimiento DATE NOT NULL,             -- Fecha de nacimiento
    direccion VARCHAR(255) DEFAULT NULL,        -- Dirección (opcional)
    password VARCHAR(255) NOT NULL              -- Contraseña cifrada
)";
mysqli_query($conn, $sql) or die("Error creando tabla Usuarios: " . mysqli_error($conn));

// Crear tabla Instalaciones
$sql = "CREATE TABLE IF NOT EXISTS Instalaciones (
    id INT AUTO_INCREMENT PRIMARY KEY,
    nombre VARCHAR(100),
    ubicacion VARCHAR(100),
    tipo VARCHAR(50)A
)";
mysqli_query($conn, $sql) or die("Error creando tabla Instalaciones: " . mysqli_error($conn));

// Crear tabla Eventos
$sql = "CREATE TABLE IF NOT EXISTS Eventos (
    id INT AUTO_INCREMENT PRIMARY KEY,
    nombre VARCHAR(100),
    fecha DATE,
    instalacion_id INT,
    FOREIGN KEY (instalacion_id) REFERENCES Instalaciones(id)
)";
mysqli_query($conn, $sql) or die("Error creando tabla Eventos: " . mysqli_error($conn));

// Crear tabla Reservas
$sql = "CREATE TABLE IF NOT EXISTS Reservas (
    id INT AUTO_INCREMENT PRIMARY KEY,
    usuario_id INT,
    instalacion_id INT,
    fecha DATE,
    hora_inicio TIME,
    hora_fin TIME,
    FOREIGN KEY (usuario_id) REFERENCES Usuarios(id),
    FOREIGN KEY (instalacion_id) REFERENCES Instalaciones(id)
)";
mysqli_query($conn, $sql) or die("Error creando tabla Reservas: " . mysqli_error($conn));
?>
