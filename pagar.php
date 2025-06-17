<?php
session_start();
include 'config.php';

if (!isset($_SESSION['user_id'])) {
    die("Debes iniciar sesión para pagar.");
}

$usuario_id = intval($_SESSION['user_id']);
$carrito = $_SESSION['carrito'] ?? [];

if (empty($carrito)) {
    $_SESSION['mensaje_error'] = "El carrito está vacío.";
    header("Location: carrito-reservas.php");
    exit;
}

$errores = [];
$ok = true;

// Iniciar transacción para insertar reservas
$conn->begin_transaction();

foreach ($carrito as $item) {
    $id = intval($item['instalacion_id']);
    $fecha = $conn->real_escape_string($item['fecha']);
    $hora_inicio = $conn->real_escape_string($item['hora']);
    $hora_fin = date('H:i:s', strtotime($hora_inicio . ' +1 hour'));

    // Verificar solape con reservas confirmadas
    $sql_check = "SELECT COUNT(*) as cnt FROM Reservas 
                  WHERE instalacion_id = $id 
                    AND fecha = '$fecha' 
                    AND NOT (hora_fin <= '$hora_inicio' OR hora_inicio >= '$hora_fin')";
    $result = $conn->query($sql_check);
    $row = $result->fetch_assoc();

    if ($row['cnt'] > 0) {
        $errores[] = "Conflicto con reserva existente para " . htmlspecialchars($item['nombre']) . " el " . $fecha . " a las " . $hora_inicio;
        $ok = false;
        break; // Opcional: podrías seguir intentando insertar las otras o parar aquí
    }

    // Insertar reserva
    $sql_insert = "INSERT INTO Reservas (usuario_id, instalacion_id, fecha, hora_inicio, hora_fin) 
                   VALUES ($usuario_id, $id, '$fecha', '$hora_inicio', '$hora_fin')";
    if (!$conn->query($sql_insert)) {
        $errores[] = "Error al guardar la reserva para " . htmlspecialchars($item['nombre']);
        $ok = false;
        break;
    }
}

if ($ok) {
    $conn->commit();
    $_SESSION['carrito'] = []; // Vaciar carrito
    $_SESSION['mensaje_ok'] = "Reservas confirmadas con éxito.";
} else {
    $conn->rollback();
    $_SESSION['mensaje_error'] = implode("<br>", $errores);
}

header("Location: carrito-reservas.php");
exit;
