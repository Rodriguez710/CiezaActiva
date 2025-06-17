<?php
// conexiones y funciones
$servername = "localhost";
$username = "root";
$password = "";
$dbname = "afr_database";

$conn = new mysqli($servername, $username, $password, $dbname);
if ($conn->connect_error) {
    die("Conexión fallida: " . $conn->connect_error);
}

// Mensajes para mostrar después de acciones
$mensaje = "";
$error = "";

// BORRAR instalación
if (isset($_GET['borrar_id'])) {
    $borrar_id = intval($_GET['borrar_id']);
    $sql_borrar = "DELETE FROM Instalaciones WHERE id = ?";
    $stmt = $conn->prepare($sql_borrar);
    $stmt->bind_param("i", $borrar_id);
    if ($stmt->execute()) {
        $mensaje = "Instalación eliminada correctamente.";
    } else {
        $error = "Error al eliminar instalación: " . $stmt->error;
    }
    $stmt->close();
}

// AÑADIR nueva instalación
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['nombre'], $_POST['ubicacion'], $_POST['tipo'], $_POST['imagen'], $_POST['precio'])) {
    $nombre = trim($_POST['nombre']);
    $ubicacion = trim($_POST['ubicacion']);
    $tipo = trim($_POST['tipo']);
    $imagen = trim($_POST['imagen']);
    $precio = floatval($_POST['precio']);

    if ($nombre === "" || $ubicacion === "" || $tipo === "" || $imagen === "" || $precio <= 0) {
        $error = "Por favor, completa todos los campos correctamente.";
    } else {
        $sql_insert = "INSERT INTO Instalaciones (nombre, ubicacion, tipo, imagen, precio) VALUES (?, ?, ?, ?, ?)";
        $stmt = $conn->prepare($sql_insert);
        $stmt->bind_param("ssssd", $nombre, $ubicacion, $tipo, $imagen, $precio);
        if ($stmt->execute()) {
            $mensaje = "Instalación añadida correctamente.";
        } else {
            $error = "Error al añadir instalación: " . $stmt->error;
        }
        $stmt->close();
    }
}

// Obtener instalaciones para mostrar
$result = $conn->query("SELECT * FROM Instalaciones ORDER BY id DESC");
$instalaciones = [];
if ($result) {
    while ($row = $result->fetch_assoc()) {
        $instalaciones[] = $row;
    }
}
$conn->close();
?>

<!DOCTYPE html>
<html lang="es">
<head>
<meta charset="UTF-8" />
<meta name="viewport" content="width=device-width, initial-scale=1" />
<title>Gestión de Instalaciones</title>
<style>
    body { font-family: Arial, sans-serif; max-width: 900px; margin: 20px auto; padding: 10px; background: #f9f9f9; }
    h1 { text-align: center; }
    form { background: #fff; padding: 15px; border-radius: 8px; margin-bottom: 30px; box-shadow: 0 0 8px #ccc; }
    label { display: block; margin-top: 10px; }
    input[type=text], input[type=number] { width: 100%; padding: 8px; box-sizing: border-box; margin-top: 5px; }
    button { margin-top: 15px; padding: 10px 20px; background: #007BFF; border: none; color: white; border-radius: 5px; cursor: pointer; }
    button:hover { background: #0056b3; }
    table { width: 100%; border-collapse: collapse; background: #fff; box-shadow: 0 0 8px #ccc; }
    th, td { border: 1px solid #ddd; padding: 10px; text-align: center; }
    th { background: #007BFF; color: white; }
    img { max-width: 100px; border-radius: 6px; }
    .mensaje { background: #d4edda; color: #155724; padding: 10px; border-radius: 5px; margin-bottom: 20px; }
    .error { background: #f8d7da; color: #721c24; padding: 10px; border-radius: 5px; margin-bottom: 20px; }
    a.borrar { color: #dc3545; text-decoration: none; font-weight: bold; }
    a.borrar:hover { text-decoration: underline; }
</style>
</head>
<body>

<h1>Gestión de Instalaciones</h1>

<?php if ($mensaje): ?>
    <div class="mensaje"><?= htmlspecialchars($mensaje) ?></div>
<?php endif; ?>
<?php if ($error): ?>
    <div class="error"><?= htmlspecialchars($error) ?></div>
<?php endif; ?>

<form method="POST" action="">
    <h2>Añadir nueva instalación</h2>
    <label for="nombre">Nombre:</label>
    <input type="text" id="nombre" name="nombre" required />

    <label for="ubicacion">Ubicación:</label>
    <input type="text" id="ubicacion" name="ubicacion" required />

    <label for="tipo">Tipo:</label>
    <input type="text" id="tipo" name="tipo" required />

    <label for="imagen">Ruta imagen (ejemplo: img/ejemplo.jpg):</label>
    <input type="text" id="imagen" name="imagen" required />

    <label for="precio">Precio (€):</label>
    <input type="number" step="0.01" min="0" id="precio" name="precio" required />

    <button type="submit">Añadir instalación</button>
</form>

<h2>Instalaciones existentes</h2>
<?php if (count($instalaciones) === 0): ?>
    <p>No hay instalaciones registradas.</p>
<?php else: ?>
<table>
    <thead>
        <tr>
            <th>ID</th>
            <th>Nombre</th>
            <th>Ubicación</th>
            <th>Tipo</th>
            <th>Imagen</th>
            <th>Precio (€)</th>
            <th>Acciones</th>
        </tr>
    </thead>
    <tbody>
    <?php foreach ($instalaciones as $inst): ?>
        <tr>
            <td><?= $inst['id'] ?></td>
            <td><?= htmlspecialchars($inst['nombre']) ?></td>
            <td><?= htmlspecialchars($inst['ubicacion']) ?></td>
            <td><?= htmlspecialchars($inst['tipo']) ?></td>
            <td><img src="<?= htmlspecialchars($inst['imagen']) ?>" alt="<?= htmlspecialchars($inst['nombre']) ?>"></td>
            <td><?= number_format($inst['precio'], 2, ',', '.') ?></td>
            <td><a href="?borrar_id=<?= $inst['id'] ?>" class="borrar" onclick="return confirm('¿Seguro que quieres borrar esta instalación?');">Borrar</a></td>
        </tr>
    <?php endforeach; ?>
    </tbody>
</table>
<?php endif; ?>

</body>
</html>
