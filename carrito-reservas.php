<?php
session_start();

if (!isset($_SESSION['user_id'])) {
    // Omito este check si no usas login
    // header("Location: login.php");
    // exit();
}

$reservas = $_SESSION['carrito'] ?? [];
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8" />
    <title>Carrito de Reservas</title>
    <style>
        table { border-collapse: collapse; width: 100%; max-width: 600px; margin: auto; }
        th, td { border: 1px solid #ccc; padding: 8px; text-align: center; }
        th { background-color: #f4f4f4; }
        button { padding: 10px 20px; font-size: 16px; margin-top: 20px; cursor: pointer; }
    </style>
</head>
<body>
<h2>Reservas Pendientes</h2>

<?php if (empty($reservas)): ?>
    <p>No tienes reservas pendientes.</p>
<?php else: ?>
    <table>
        <thead>
            <tr>
                <th>Instalación</th>
                <th>Fecha</th>
                <th>Hora</th>
            </tr>
        </thead>
        <tbody>
            <?php foreach ($reservas as $reserva): ?>
                <tr>
                    <td><?php echo htmlspecialchars($reserva['instalacion']); ?></td>
                    <td><?php echo htmlspecialchars($reserva['fecha']); ?></td>
                    <td><?php echo htmlspecialchars($reserva['hora']); ?></td>
                </tr>
            <?php endforeach; ?>
        </tbody>
    </table>

    <form method="POST" action="confirmar-reservas.php">
        <button type="submit" name="confirmar">Confirmar Reservas</button>
    </form>
<?php endif; ?>

</body>
</html>
