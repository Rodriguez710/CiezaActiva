<?php
$servername = "localhost";
$username = "root";
$password = "";
$dbname = "afr_database";

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
mysqli_select_db($conn, $dbname);

// Motor y codificación por defecto
$engine = "ENGINE=InnoDB DEFAULT CHARSET=utf8mb4";

// Crear tabla Usuarios
$sql = "CREATE TABLE IF NOT EXISTS Usuarios (
    id INT AUTO_INCREMENT PRIMARY KEY,
    nombre VARCHAR(100) NOT NULL,
    apellidos VARCHAR(100) NOT NULL,
    email VARCHAR(100) UNIQUE NOT NULL,
    telefono VARCHAR(20) NOT NULL,
    fecha_nacimiento DATE NOT NULL,
    direccion VARCHAR(255),
    password VARCHAR(255) NOT NULL,
    foto_perfil VARCHAR(255) DEFAULT NULL
) $engine";
mysqli_query($conn, $sql) or die("Error creando tabla Usuarios: " . mysqli_error($conn));

// Añadir columna rol si no existe
// Aquí comprobamos si la columna existe antes de añadirla
$result = mysqli_query($conn, "SHOW COLUMNS FROM Usuarios LIKE 'rol'");
if (mysqli_num_rows($result) == 0) {
    $sql = "ALTER TABLE Usuarios ADD COLUMN rol ENUM('usuario','admin') NOT NULL DEFAULT 'usuario'";
    mysqli_query($conn, $sql) or die("Error añadiendo columna rol: " . mysqli_error($conn));
}

// Crear tabla Instalaciones
$sql = "CREATE TABLE IF NOT EXISTS Instalaciones (
    id INT AUTO_INCREMENT PRIMARY KEY,
    nombre VARCHAR(100) NOT NULL,
    ubicacion VARCHAR(100),
    tipo VARCHAR(50),
    imagen VARCHAR(255),
    precio DECIMAL(6,2)
) $engine";
mysqli_query($conn, $sql) or die("Error creando tabla Instalaciones: " . mysqli_error($conn));

// Crear tabla Eventos con IF NOT EXISTS
$sql = "CREATE TABLE IF NOT EXISTS Eventos (
    id INT AUTO_INCREMENT PRIMARY KEY,
    titulo VARCHAR(255) NOT NULL,
    categoria VARCHAR(100) NOT NULL,
    fecha DATE NOT NULL,
    lugar VARCHAR(255) NOT NULL,
    precio DECIMAL(8,2) NOT NULL,
    descripcion VARCHAR(255) NOT NULL,
    imagen VARCHAR(255) NOT NULL
) $engine";
mysqli_query($conn, $sql) or die("Error creando tabla Eventos: " . mysqli_error($conn));

// Crear tabla Reservas
$sql = "CREATE TABLE IF NOT EXISTS Reservas (
    id INT AUTO_INCREMENT PRIMARY KEY,
    usuario_id INT,
    instalacion_id INT,
    fecha DATE,
    hora_inicio TIME,
    hora_fin TIME,
    FOREIGN KEY (usuario_id) REFERENCES Usuarios(id)
        ON DELETE CASCADE ON UPDATE CASCADE,
    FOREIGN KEY (instalacion_id) REFERENCES Instalaciones(id)
        ON DELETE CASCADE ON UPDATE CASCADE
) $engine";
mysqli_query($conn, $sql) or die("Error creando tabla Reservas: " . mysqli_error($conn));
?>
