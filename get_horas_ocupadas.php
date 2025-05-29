<?php
// Devuelve las horas ocupadas para una fecha y una instalación en formato JSON
header('Content-Type: application/json');

if (!isset($_GET['fecha']) || !isset($_GET['instalacion'])) {
    echo json_encode([]);
    exit();
}

$fecha = $_GET['fecha'];
$instalacion = $_GET['instalacion'];
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
    // Filtramos por la instalación y fecha que vienen por GET
    if ($reserva['instalacion'] === $instalacion && $reserva['fecha'] === $fecha) {
        $ocupadas[] = $reserva['hora'];
    }
}

echo json_encode($ocupadas);
