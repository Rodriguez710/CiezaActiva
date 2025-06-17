<?php
// Incluye configuración y conexión
include 'config.php';

// Consulta instalaciones
$sql = "SELECT * FROM Instalaciones";
$result = mysqli_query($conn, $sql);
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1" />
    <title>Reservas - Instalaciones Deportivas</title>
    <link rel="stylesheet" href="css/reservas.css" />
</head>
<body>
<?php include 'header.php'; ?>

<main>
    <section class="descripcion"><h2>Reservar Instalación Deportiva</h2></section>

    <section class="reservas-deportivas">
        <?php if ($result && mysqli_num_rows($result) > 0): ?>
            <?php while ($inst = mysqli_fetch_assoc($result)): ?>
                <?php
                    $rutaImagen = "img/" . basename($inst['imagen']);
                    $linkReserva = "reservar_instalacion.php?id=" . urlencode($instalacion['id']);

                ?>
                <div class="instalacion">
                    <img src="<?= htmlspecialchars($rutaImagen) ?>" alt="<?= htmlspecialchars($inst['nombre']) ?>" />
                    <h3><?= htmlspecialchars($inst['nombre']) ?></h3>
                    <p>Precio por hora: <?= number_format($inst['precio'], 2) ?> €</p>
                    <a href="<?= $linkReserva ?>" class="btn">Reservar</a>
                </div>
            <?php endwhile; ?>
        <?php else: ?>
            <p>No hay instalaciones disponibles.</p>
        <?php endif; ?>
    </section>
</main>

<?php include 'footer.php'; ?>
</body>
</html>
