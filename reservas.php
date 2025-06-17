<?php
session_start();
include 'config.php';

$sql = "SELECT * FROM Instalaciones";
$result = mysqli_query($conn, $sql);
if (!$result) {
    die("Error en la consulta: " . mysqli_error($conn));
}
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
    <section class="descripcion">
        <h2>Reservar Instalación Deportiva</h2>
    </section>

    <section class="reservas-deportivas">

        <?php
       

        if (mysqli_num_rows($result) > 0) {
            while ($instalacion = mysqli_fetch_assoc($result)) {
                $rutaImagen = $instalacion['imagen'];
                $linkReserva = "reservar_instalacion.php?id=" . urlencode($instalacion['id']);
                ?>
                <div class="instalacion">
                    <img src="<?= htmlspecialchars($rutaImagen) ?>" alt="<?= htmlspecialchars($instalacion['nombre']) ?>" />
                    <h3><?= htmlspecialchars($instalacion['nombre']) ?></h3>
                    <p>Precio por hora: <?= number_format($instalacion['precio'], 2) ?> €</p>
                    <a href="<?= htmlspecialchars($linkReserva) ?>" class="btn">Reservar</a>
                </div>
                <?php
            }
        } else {
            echo "<p>No hay instalaciones disponibles.</p>";
        }
        ?>

    </section>
</main>

<?php include 'footer.php'; ?>

</body>
</html>
