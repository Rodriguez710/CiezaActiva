<?php
session_start();
include("config.php"); // asegúrate que este conecta con la BD

$mensaje = "";

// Procesar el formulario
if ($_SERVER["REQUEST_METHOD"] === "POST") {
    $nombre     = $_POST['nombre'];
    $ubicacion  = $_POST['ubicacion'];
    $tipo       = $_POST['tipo'];
    $precio     = $_POST['precio'];
    $imagen     = $_POST['imagen']; // suponemos que es una ruta tipo "img/tenis.jpg"

    $stmt = $conn->prepare("INSERT INTO Instalaciones (nombre, ubicacion, tipo, imagen, precio) VALUES (?, ?, ?, ?, ?)");
    $stmt->bind_param("ssssd", $nombre, $ubicacion, $tipo, $imagen, $precio);

    if ($stmt->execute()) {
        $mensaje = "Instalación añadida correctamente.";
    } else {
        $mensaje = "Error al insertar: " . $stmt->error;
    }

    $stmt->close();
}
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Nueva Instalación</title>
    <style>
        body { background: #111; color: #eee; font-family: sans-serif; }
        header, main { max-width: 1000px; margin: auto; padding: 1em; }
        header { display: flex; justify-content: space-between; align-items: center; background: #333; }
        nav a { margin: 0 10px; color: #fff; text-decoration: none; }
        form { background: #222; padding: 2em; border-radius: 10px; }
        input, select { width: 100%; padding: 10px; margin: 10px 0; border-radius: 4px; border: none; }
        button { background: #e60000; color: white; border: none; padding: 10px; border-radius: 4px; cursor: pointer; }
        .mensaje { margin-top: 1em; background: #333; padding: 1em; border-radius: 6px; }
    </style>
</head>
<body>
<header>
    <div><img src="img/logo.png" alt="Logo" style="height:50px;"></div>
    <nav>
        <a href="index.php">Inicio</a>
        <a href="instalaciones.php">Instalaciones</a>
        <a href="carrito.php">Carrito</a>
    </nav>
</header>

<main>
    <h2>Añadir nueva instalación</h2>
    <form method="POST">
        <label>Nombre:</label>
        <input type="text" name="nombre" required>

        <label>Ubicación:</label>
        <input type="text" name="ubicacion" required>

        <label>Tipo:</label>
        <select name="tipo" required>
            <option value="Fútbol">Fútbol</option>
            <option value="Tenis">Tenis</option>
            <option value="Baloncesto">Baloncesto</option>
            <option value="Otro">Otro</option>
        </select>

        <label>Precio por hora (€):</label>
        <input type="number" name="precio" step="0.01" required>

        <label>Ruta de imagen (ej: img/tenis.jpg):</label>
        <input type="text" name="imagen" required>

        <button type="submit">Guardar instalación</button>
    </form>

    <?php if (!empty($mensaje)): ?>
        <div class="mensaje"><?= htmlspecialchars($mensaje) ?></div>
    <?php endif; ?>
</main>
</body>
</html>
