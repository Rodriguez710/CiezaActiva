<?php
session_start();

// Inicializa variables
$mensaje = null;
$reservas = $_SESSION['carrito'] ?? [];

// Procesar acciones individuales
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (isset($_POST['accion']) && $_POST['accion'] === 'confirmar') {
        unset($_SESSION['carrito']);
        $mensaje = "✅ Su reserva ha sido confirmada con éxito.";
    }

    if (isset($_POST['accion']) && $_POST['accion'] === 'cancelar_individual' && isset($_POST['index'])) {
        $index = intval($_POST['index']);
        if (isset($_SESSION['carrito'][$index])) {
            unset($_SESSION['carrito'][$index]);
            $_SESSION['carrito'] = array_values($_SESSION['carrito']); // Reindexar array
            $mensaje = "❌ Se canceló la reserva con éxito.";
        }
    }
}

$reservas = $_SESSION['carrito'] ?? [];
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Carrito de Reservas</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            background-color: #f1f1f1;
            padding: 30px;
            text-align: center;
        }

        h2 { color: #333; }

        table {
            border-collapse: collapse;
            width: 100%;
            max-width: 700px;
            margin: auto;
        }

        th, td {
            border: 1px solid #ccc;
            padding: 10px;
            text-align: center;
        }

        th { background-color: #f4f4f4; }

        .acciones {
            margin-top: 20px;
        }

        .btn {
            padding: 8px 16px;
            font-size: 14px;
            cursor: pointer;
            border: none;
            border-radius: 4px;
            color: white;
        }

        .confirmar { background-color: #4caf50; }

        .cancelar { background-color: #e60000; }

        .cancelar-individual {
            background-color: #d9534f;
            font-size: 12px;
            padding: 5px 10px;
        }

        .btn-volver {
            display: inline-block;
            margin-top: 20px;
            padding: 10px 20px;
            background-color: #e67c7a;
            color: white;
            text-decoration: none;
            border-radius: 5px;
            font-weight: bold;
        }

        .mensaje {
            margin-bottom: 20px;
            padding: 12px;
            font-weight: bold;
            border-radius: 5px;
            display: inline-block;
            color: white;
        }

        .confirmado { background-color: #4caf50; }

        .cancelado { background-color: #e60000; }
    </style>
</head>
<body>

<h2>Reservas Pendientes</h2>

<?php if ($mensaje): ?>
    <div class="mensaje <?= str_contains($mensaje, 'confirmada') ? 'confirmado' : 'cancelado' ?>">
        <?= htmlspecialchars($mensaje) ?>
    </div>
<?php endif; ?>

<?php if (empty($reservas)): ?>
    <p>No tienes reservas pendientes.</p>
    <a href="reservas.php" class="btn-volver">Volver a Reservar</a>
<?php else: ?>
    <form method="POST">
        <input type="hidden" name="accion" value="confirmar">
        <button type="submit" class="btn confirmar">Confirmar Todas</button>
    </form>

    <table>
        <thead>
            <tr>
                <th>Instalación</th>
                <th>Fecha</th>
                <th>Hora</th>
                <th>Cancelar</th>
            </tr>
        </thead>
        <tbody>
            <?php foreach ($reservas as $i => $reserva): ?>
                <tr>
                    <td><?= htmlspecialchars($reserva['instalacion']) ?></td>
                    <td><?= htmlspecialchars($reserva['fecha']) ?></td>
                    <td><?= htmlspecialchars($reserva['hora']) ?></td>
                    <td>
                        <form method="POST" style="display:inline;">
                            <input type="hidden" name="accion" value="cancelar_individual">
                            <input type="hidden" name="index" value="<?= $i ?>">
                            <button type="submit" class="btn cancelar-individual">Cancelar</button>
                        </form>
                    </td>
                </tr>
            <?php endforeach; ?>
        </tbody>
    </table>
<?php endif; ?>

</body>
</html>
