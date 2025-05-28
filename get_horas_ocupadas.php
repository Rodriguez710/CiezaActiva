<?php
// Devuelve las horas ocupadas para una fecha en formato JSON
header('Content-Type: application/json');

if (!isset($_GET['fecha'])) {
    echo json_encode([]);
    exit();
}

$fecha = $_GET['fecha'];
$archivo = 'reservas_confirmadas.json';

if (!file_exists($archivo)) {
    echo json_encode([]);
    exit();
}

$data = json_decode(file_get_contents($archivo), true);
if (!$data) {
    echo json_encode([]);
    exit();
}

$ocupadas = [];
foreach ($data as $reserva) {
    if ($reserva['instalacion'] === 'Campo de Fútbol' && $reserva['fecha'] === $fecha) {
        $ocupadas[] = $reserva['hora'];
    }
}

echo json_encode($ocupadas);
