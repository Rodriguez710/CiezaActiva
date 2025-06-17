<?php
session_start();
include 'config.php'; // conexión a base de datos

if (!isset($_SESSION['user_id'])) {
    die("Debes iniciar sesión para ver tus reservas.");
}

$usuario_id = intval($_SESSION['user_id']);
$carrito = $_SESSION['carrito'] ?? [];

// Eliminar item del carrito si viene el request
if (isset($_POST['eliminar']) && isset($_POST['indice'])) {
    $indice = (int)$_POST['indice'];
    if (isset($carrito[$indice])) {
        unset($carrito[$indice]);
        $_SESSION['carrito'] = array_values($carrito);
        $carrito = $_SESSION['carrito'];
    }
    header("Location: carrito-reservas.php");
    exit;
}

// Obtener reservas confirmadas desde la base de datos
$stmt = $conn->prepare("SELECT r.fecha, r.hora_inicio, r.hora_fin, i.nombre, r.id FROM Reservas r JOIN Instalaciones i ON r.instalacion_id = i.id WHERE r.usuario_id = ? ORDER BY r.fecha DESC, r.hora_inicio DESC");
$stmt->bind_param("i", $usuario_id);
$stmt->execute();
$resultado = $stmt->get_result();
$reservas_confirmadas = $resultado->fetch_all(MYSQLI_ASSOC);
$stmt->close();

$total = 0;
foreach ($carrito as $item) {
    $total += $item['precio'];
}
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8" />
    <title>Carrito de Reservas</title>
    <style>
        /* Estilos para botones y tablas */
        .btn-pagar {
            background-color: #e60000;
            color: white;
            border: none;
            padding: 12px 24px;
            font-size: 16px;
            border-radius: 5px;
            cursor: pointer;
            margin-top: 15px;
            display: inline-block;
            text-decoration: none;
        }
        .btn-pagar:hover {
            background-color: #b30000;
        }
        table {
            border-collapse: collapse;
            margin-top: 15px;
            width: 100%;
            max-width: 700px;
        }
        th, td {
            padding: 8px 12px;
            text-align: left;
        }
        .btn-eliminar {
            background-color: #555;
            color: white;
            border: none;
            padding: 6px 12px;
            border-radius: 4px;
            cursor: pointer;
        }
        .btn-eliminar:hover {
            background-color: #333;
        }
        h2 {
            margin-top: 40px;
        }

        /* Contenedor general del carrito */
body {
    background-color: #1c1c1c;
    color: #fff;
    font-family: Arial, sans-serif;
    margin: 20px;
    padding-bottom: 40px;
}

h1, h2 {
    color: #e60000; /* Rojo para títulos */
    margin-bottom: 15px;
}

/* Tabla de reservas */
table {
    width: 100%;
    max-width: 700px;
    border-collapse: collapse;
    margin-bottom: 20px;
    background-color: #2a2a2a; /* fondo oscuro de tabla */
    border-radius: 8px;
    overflow: hidden;
    box-shadow: 0 0 8px rgba(230, 0, 0, 0.3);
}

thead tr {
    background-color: #e60000; /* rojo para encabezado */
}

thead th {
    padding: 12px;
    color: white;
    font-weight: bold;
    text-align: left;
}

tbody tr {
    border-bottom: 1px solid #444;
}

tbody tr:hover {
    background-color: #3d0000; /* rojo oscuro al pasar el mouse */
}

tbody td {
    padding: 12px;
    color: #ddd;
}

/* Botones */
.btn-pagar {
    background-color: #e60000;
    color: white;
    border: none;
    padding: 14px 28px;
    font-size: 16px;
    border-radius: 5px;
    cursor: pointer;
    margin-top: 10px;
    transition: background-color 0.3s ease;
    display: inline-block;
    text-decoration: none;
}

.btn-pagar:hover {
    background-color: #b30000;
}

.btn-eliminar {
    background-color: #555;
    color: white;
    border: none;
    padding: 8px 14px;
    border-radius: 5px;
    cursor: pointer;
    transition: background-color 0.3s ease;
}

.btn-eliminar:hover {
    background-color: #333;
}

/* Mensajes de éxito o error (suponiendo que uses flash messages) */
.mensaje-exito {
    background-color: #1f4d1f;
    border: 1px solid #4caf50;
    color: #c8e6c9;
    padding: 10px 15px;
    border-radius: 5px;
    margin-bottom: 20px;
    max-width: 700px;
}

.mensaje-error {
    background-color: #4d1f1f;
    border: 1px solid #f44336;
    color: #ffcdd2;
    padding: 10px 15px;
    border-radius: 5px;
    margin-bottom: 20px;
    max-width: 700px;
}

/* Enlaces */
a {
    color: #e60000;
    text-decoration: none;
}

a:hover {
    text-decoration: underline;
}

/* Ajustes responsivos para móvil */
@media (max-width: 768px) {
    table, .mensaje-exito, .mensaje-error {
        max-width: 100%;
    }

    tbody td, thead th {
        padding: 10px 8px;
        font-size: 14px;
    }

    .btn-pagar, .btn-eliminar {
        font-size: 14px;
        padding: 10px 20px;
    }
}

    </style>
</head>
<body>
    <h1>Carrito de Reservas</h1>

    <h2>Reservas no confirmadas (en carrito)</h2>
    <?php if (empty($carrito)): ?>
        <p>No tienes reservas en el carrito.</p>
    <?php else: ?>
        <table border="1">
            <thead>
                <tr>
                    <th>Instalación</th>
                    <th>Fecha</th>
                    <th>Hora</th>
                    <th>Precio (€)</th>
                    <th>Acción</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($carrito as $indice => $item): ?>
                    <tr>
                        <td><?= htmlspecialchars($item['nombre']) ?></td>
                        <td><?= (new DateTime($item['fecha']))->format('d/m/Y') ?></td>
                        <td><?= htmlspecialchars($item['hora']) ?></td>
                        <td><?= number_format($item['precio'], 2, ',', '.') ?></td>
                        <td>
                            <form method="post" style="margin:0;">
                                <input type="hidden" name="indice" value="<?= $indice ?>">
                                <button type="submit" name="eliminar" class="btn-eliminar">Eliminar</button>
                            </form>
                        </td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
        <p><strong>Total a pagar: €<?= number_format($total, 2, ',', '.') ?></strong></p>

        <!-- Botón Pagar -->
        <form action="pagar.php" method="post">
            <button type="submit" class="btn-pagar">Pagar</button>
        </form>
    <?php endif; ?>

    <h2>Reservas confirmadas</h2>
    <?php if (empty($reservas_confirmadas)): ?>
        <p>No tienes reservas confirmadas aún.</p>
    <?php else: ?>
        <table border="1">
            <thead>
                <tr>
                    <th>Instalación</th>
                    <th>Fecha</th>
                    <th>Hora inicio</th>
                    <th>Hora fin</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($reservas_confirmadas as $reserva): ?>
                    <tr>
                        <td><?= htmlspecialchars($reserva['nombre']) ?></td>
                        <td><?= (new DateTime($reserva['fecha']))->format('d/m/Y') ?></td>
                        <td><?= htmlspecialchars($reserva['hora_inicio']) ?></td>
                        <td><?= htmlspecialchars($reserva['hora_fin']) ?></td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    <?php endif; ?>

    <p><a href="reservas.php">Volver a instalaciones</a></p>
</body>
</html>
