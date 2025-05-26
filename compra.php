<?php
session_start();
require 'config.php';

if (!isset($_SESSION['usuario_id'])) {
    header("Location: login.php");
    exit();
}

// Obtener artículos disponibles
$query_articulos = "SELECT id, nombre, precio FROM Articulos";
$result_articulos = $conn->query($query_articulos);

if (!isset($_SESSION['carrito'])) {
    $_SESSION['carrito'] = [];
}

// Procesamiento del formulario
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    if (isset($_POST['id_articulo']) && isset($_POST['cantidad'])) {
        $id_articulo = intval($_POST['id_articulo']);
        $cantidad = intval($_POST['cantidad']);

        if ($id_articulo <= 0 || $cantidad <= 0) {
            echo "Por favor, ingresa un ID y cantidad válidos.";
        } else {
            if (isset($_SESSION['carrito'][$id_articulo])) {
                $_SESSION['carrito'][$id_articulo]['cantidad'] += $cantidad;
            } else {
                $stmt = $conn->prepare("SELECT precio FROM Articulos WHERE id = ?");
                $stmt->bind_param("i", $id_articulo);
                $stmt->execute();
                $stmt->bind_result($precio);
                if ($stmt->fetch()) {
                    $_SESSION['carrito'][$id_articulo] = [
                        'cantidad' => $cantidad,
                        'precio' => $precio
                    ];
                }
                $stmt->close();
            }
            echo "<p>Artículo añadido al carrito correctamente.</p>";
        }
    }

    // Eliminar artículo del carrito
    if (isset($_POST['eliminar_id'])) {
        $id_articulo = intval($_POST['eliminar_id']);
        unset($_SESSION['carrito'][$id_articulo]);
    }

    // Realizar compra (vaciar carrito)
    if (isset($_POST['realizar_compra'])) {
        $_SESSION['carrito'] = [];
        echo "<p style='background-color:green; color:white;'>Compra realizada con éxito. ¡Gracias por tu compra!</p>";
    }
}

// Calcular total
$total = 0;
foreach ($_SESSION['carrito'] as $item) {
    $total += $item['cantidad'] * $item['precio'];
}
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Carrito de Compras</title>
    <link rel="stylesheet" href="estilos/compra.css">
</head>
<body>

<header>
    <h1>Zona de Compra</h1>
    <section>
        <a href="gestion_articulos.php" class="boton-volver">Volver a Gestión de Artículos</a>
    </section>
</header>

<section class="formulario">
    <h2>Añadir al Carrito</h2>
    <form method="POST">
        <label for="id_articulo">ID del Artículo:</label>
        <input type="number" id="id_articulo" name="id_articulo" required>

        <label for="cantidad">Cantidad:</label>
        <input type="number" id="cantidad" name="cantidad" required min="1">

        <button type="submit">Añadir</button>
    </form>
</section>

<section class="carrito">
    <h2>Carrito de la Compra</h2>
    <?php if (!empty($_SESSION['carrito'])): ?>
        <table>
            <thead>
                <tr>
                    <th>Nombre</th>
                    <th>Cantidad</th>
                    <th>Precio Unitario</th>
                    <th>Total</th>
                    <th>Acciones</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($_SESSION['carrito'] as $id => $item): ?>
                    <?php
                    $stmt = $conn->prepare("SELECT nombre FROM Articulos WHERE id = ?");
                    $stmt->bind_param("i", $id);
                    $stmt->execute();
                    $stmt->bind_result($nombre);
                    $stmt->fetch();
                    $stmt->close();
                    ?>
                    <tr>
                        <td><?php echo htmlspecialchars($nombre); ?></td>
                        <td><?php echo $item['cantidad']; ?></td>
                        <td>$<?php echo number_format($item['precio'], 2); ?></td>
                        <td>$<?php echo number_format($item['cantidad'] * $item['precio'], 2); ?></td>
                        <td>
                            <form method="POST" style="display:inline;">
                                <input type="hidden" name="eliminar_id" value="<?php echo $id; ?>">
                                <button type="submit">Eliminar</button>
                            </form>
                        </td>
                    </tr>
                <?php endforeach; ?>
                <tr>
                    <td colspan="3"><strong>Total</strong></td>
                    <td><strong>$<?php echo number_format($total, 2); ?></strong></td>
                    <td>
                        <form method="POST">
                            <button type="submit" name="realizar_compra">Realizar Compra</button>
                        </form>
                    </td>
                </tr>
            </tbody>
        </table>
    <?php else: ?>
        <p>El carrito está vacío.</p>
    <?php endif; ?>
</section>

<section class="productos">
    <h2>Artículos Disponibles</h2>
    <table>
        <thead>
            <tr>
                <th>ID</th>
                <th>Nombre</th>
                <th>Precio</th>
            </tr>
        </thead>
        <tbody>
            <?php while ($row = $result_articulos->fetch_assoc()): ?>
                <tr>
                    <td><?php echo $row['id']; ?></td>
                    <td><?php echo htmlspecialchars($row['nombre']); ?></td>
                    <td>$<?php echo number_format($row['precio'], 2); ?></td>
                </tr>
            <?php endwhile; ?>
        </tbody>
    </table>
</section>

</body>
</html>
