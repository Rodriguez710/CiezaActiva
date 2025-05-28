<?php
session_start();

// Aquí procesas la confirmación de la reserva, por ejemplo, vaciar el carrito o marcar reserva como confirmada

if (!isset($_SESSION['carrito']) || empty($_SESSION['carrito'])) {
    // Si no hay reserva, redirige a la página principal o carrito
    header("Location: reservas-futbol.php");
    exit();
}

// Por ejemplo, vaciamos el carrito para simular que la reserva se confirmó
unset($_SESSION['carrito']);

?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8" />
    <title>Reserva Confirmada</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            background-color: #f1f1f1;
            text-align: center;
            padding: 50px;
        }
        .mensaje-confirmacion {
            background-color: #4caf50;
            color: white;
            padding: 20px 40px;
            border-radius: 10px;
            box-shadow: 0 4px 8px rgba(0,0,0,0.3);
            display: inline-block;
        }
        .btn-volver {
            margin-top: 20px;
            display: inline-block;
            padding: 10px 20px;
            background-color: #e67c7a;
            color: white;
            text-decoration: none;
            border-radius: 5px;
            font-weight: bold;
            transition: background-color 0.3s ease;
        }
        .btn-volver:hover {
            background-color: #c35c5c;
        }
    </style>
</head>
<body>

    <div class="mensaje-confirmacion">
        <h1>¡Enhorabuena!</h1>
        <p>Su reserva se ha realizado correctamente.</p>
        <a href="reservas-futbol.php" class="btn-volver">Volver a Reservar</a>
    </div>

</body>
</html>
