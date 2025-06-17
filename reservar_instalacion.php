<?php
session_start();
include 'config.php';

// Verificar usuario logueado
if (!isset($_SESSION['user_id'])) {
    die("Debes iniciar sesión para reservar.");
}

$usuario_id = intval($_SESSION['user_id']);

if (!isset($_GET['id']) || !intval($_GET['id'])) {
    die("No se especificó la instalación.");
}
$id = intval($_GET['id']);

// Obtener datos instalación
$inst = mysqli_fetch_assoc(
    mysqli_query($conn, "SELECT * FROM Instalaciones WHERE id = $id")
);
if (!$inst) {
    die("Instalación no encontrada.");
}

$nombreInst = $inst['nombre'];
$precioInst = $inst['precio'];
$imagenInst = $inst['imagen'];

// Procesar formulario
if ($_SERVER['REQUEST_METHOD'] === 'POST'
    && isset($_POST['fecha'], $_POST['hora'], $_POST['instalacion_id'])
    && intval($_POST['instalacion_id']) === $id) {

    $fecha = $_POST['fecha']; // yyyy-mm-dd
    $hora_inicio = $_POST['hora']; // hh:mm
    $hora_fin = date('H:i:s', strtotime($hora_inicio . ' +1 hour'));

    // Escapar para seguridad
    $fecha_esc = mysqli_real_escape_string($conn, $fecha);
    $hora_inicio_esc = mysqli_real_escape_string($conn, $hora_inicio);
    $hora_fin_esc = mysqli_real_escape_string($conn, $hora_fin);

    // Comprobar solape con reservas confirmadas (BD)
    $sql_check = "SELECT COUNT(*) AS cnt FROM Reservas 
                  WHERE instalacion_id = $id 
                    AND fecha = '$fecha_esc' 
                    AND NOT (hora_fin <= '$hora_inicio_esc' OR hora_inicio >= '$hora_fin_esc')";

    $res_check = mysqli_query($conn, $sql_check);
    $row_check = mysqli_fetch_assoc($res_check);

    if ($row_check['cnt'] > 0) {
        $_SESSION['mensaje_error'] = "Ya existe una reserva confirmada para esa instalación en la fecha y hora seleccionadas.";
    } else {
        // Añadir reserva al carrito en sesión
        if (!isset($_SESSION['carrito'])) {
            $_SESSION['carrito'] = [];
        }

        // Preparar array reserva
        $reserva = [
            'instalacion_id' => $id,
            'nombre' => $nombreInst,
            'precio' => $precioInst,
            'fecha' => $fecha,
            'hora' => $hora_inicio
        ];

        // Evitar duplicados en carrito (opcional)
        $duplicado = false;
        foreach ($_SESSION['carrito'] as $item) {
            if (
                $item['instalacion_id'] === $id &&
                $item['fecha'] === $fecha &&
                $item['hora'] === $hora_inicio
            ) {
                $duplicado = true;
                break;
            }
        }

        if (!$duplicado) {
            $_SESSION['carrito'][] = $reserva;
            $_SESSION['mensaje_ok'] = "Reserva añadida al carrito ";
        } else {
            $_SESSION['mensaje_error'] = "Esa reserva ya está en el carrito.";
        }
    }

    header("Location: reservar_instalacion.php?id=$id");
    exit();
}
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8" />
    <title>Reserva - <?= htmlspecialchars($nombreInst) ?></title>
    <link rel="stylesheet" href="css/sites.css">

    <style>
        /* Calendario */
        #calendar {
            display: flex;
            flex-direction: column;
            align-items: center;
            width: 100%;
            max-width: 420px;
            margin: 2em auto 0;
            background-color: #2b2b2b;
            color: #fff;
            border: 2px solid #e67c7a;
            border-radius: 10px;
            box-shadow: 0 0 10px #754040;
            font-size: 16px;
            padding: 1em;
        }

        .cal-header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 1em;
            font-weight: bold;
            color: #ffcc00;
            width: 100%;
        }

        .cal-header button {
            background-color: #e67c7a;
            color: white;
            border: none;
            padding: 0.4em 0.8em;
            border-radius: 5px;
            cursor: pointer;
            font-size: 16px;
        }

        .cal-header button:hover {
            background-color: #c35c5c;
        }

        .cal-days,
        .cal-dates {
            display: grid;
            grid-template-columns: repeat(7, 1fr);
            gap: 0.5em;
            width: 100%;
            text-align: center;
        }

        .cal-days div,
        .cal-dates div {
            aspect-ratio: 1 / 1;
            display: flex;
            justify-content: center;
            align-items: center;
            border-radius: 4px;
        }

        .cal-days div {
            font-weight: bold;
            color: #fff;
            background-color: #333;
        }

        .cal-day {
            background: #444;
            color: white;
            cursor: pointer;
            transition: background 0.3s;
        }

        .cal-day:hover:not(.disabled) {
            background: #666;
        }

        .cal-day.selected {
            background: #2a9d8f;
            color: white;
            font-weight: bold;
        }

        .cal-day.disabled {
            background-color: #000000;
            cursor: not-allowed;
            opacity: 0.5;
        }

        .empty {
            visibility: hidden;
            aspect-ratio: 1 / 1;
        }

        /* Mensaje */
        .mensaje-reserva {
            position: fixed;
            top: 10px;
            right: 10px;
            background-color: #e60000;
            color: white;
            padding: 10px 15px;
            border-radius: 5px;
            box-shadow: 2px 2px 10px #b94b4b;
            z-index: 100;
            font-weight: bold;
        }

        /* Layout detalles: imagen a la izquierda, formulario a la derecha */
        .detalles {
            display: flex;
            align-items: flex-start;
            gap: 1.5em;
            margin-top: 2em;
            flex-wrap: nowrap;
        }

        .imagen {
            flex: 0 0 320px;
            height: 220px;
            overflow: hidden;
            border-radius: 15px;
            box-shadow: 0 0 10px #754040;
            background-color: #1f1f1f;
            display: flex;
            justify-content: center;
            align-items: center;
        }

        .imagen img {
            width: 100%;
            height: 100%;
            object-fit: cover;
            border-radius: 15px;
        }

        .informacion {
            flex: 1 1 auto;
            max-width: 420px;
            color: #fff;
            display: flex;
            flex-direction: column;
            justify-content: center; /* centra verticalmente junto a imagen */
        }

        .reserva-container {
            width: 100%;
        }

        /* Campo hora y fecha seleccionada */
        #hora-container {
            width: 100%;
            margin-bottom: 1em;
            background-color: #3a3a3a;
            padding: 0.8em 1em;
            border-radius: 8px;
            box-shadow: inset 0 0 8px #754040;
            color: #fff;
            font-size: 18px;
            text-align: center;
            user-select: none;
        }

        #hora-select {
            margin-top: 0.5em;
            width: 100%;
            padding: 0.4em;
            font-size: 16px;
            border-radius: 5px;
            border: none;
            outline: none;
            background-color: #555;
            color: white;
            cursor: pointer;
        }

        form label {
            display: block;
            margin-bottom: 0.6em;
            font-weight: bold;
            color: #ffcc00;
        }

        form button[type="submit"] {
            margin-top: 1em;
            width: 100%;
            padding: 0.8em;
            background-color: #e67c7a;
            color: white;
            border: none;
            border-radius: 6px;
            font-size: 18px;
            cursor: pointer;
            transition: background-color 0.3s;
        }

        form button[type="submit"]:hover {
            background-color: #c35c5c;
        }

        /* Input fecha oculto (solo usado para enviar) */
        input[name="fecha"] {
            display: none;
        }
    </style>
</head>

<body>
<?php include 'header.php'; ?>
<main>
    <section class="descripcion">
        <h2><?= htmlspecialchars($nombreInst) ?></h2>
        <p>Precio por hora: <?= number_format($precioInst,2) ?> €</p>
    </section>

    <div class="detalles">
        <div class="imagen">
            <img src="<?= htmlspecialchars($imagenInst) ?>" alt="<?= htmlspecialchars($nombreInst) ?>" />
        </div>

        <div class="informacion">

            <?php if (isset($_SESSION['mensaje_ok'])): ?>
                <p style="color:lightgreen"><?= $_SESSION['mensaje_ok']; unset($_SESSION['mensaje_ok']); ?></p>
            <?php elseif (isset($_SESSION['mensaje_error'])): ?>
                <p style="color:#f88"><?= $_SESSION['mensaje_error']; unset($_SESSION['mensaje_error']); ?></p>
            <?php endif; ?>

            <div class="reserva-container">

                <!-- Campo fecha oculto -->
                <form method="post" id="reserva-form">
                    <input type="hidden" name="instalacion_id" value="<?= $id ?>">
                    <input type="hidden" name="fecha" id="fecha" required>
                    <div id="hora-container">
                        <div id="fecha-seleccionada">Fecha: <em>No seleccionada</em></div>
                        <label for="hora-select">Hora:</label>
                        <select id="hora-select" name="hora" required>
                            <?php for ($h=10;$h<=17;$h++): ?>
                                <option value="<?= sprintf('%02d:00',$h) ?>">
                                    <?= sprintf('%02d:00 - %02d:00',$h,$h+1) ?>
                                </option>
                            <?php endfor; ?>
                        </select>
                    </div>
                    <button type="submit">Reservar</button>
                </form>

            </div>
        </div>
    </div>

    <div id="calendar"></div>
</main>

<script>
(() => {
    const calendarEl = document.getElementById('calendar');
    const fechaInput = document.getElementById('fecha');
    const fechaSeleccionada = document.getElementById('fecha-seleccionada');
    const form = document.getElementById('reserva-form');

    // Configuración inicial: día actual, sin selección
    let currentDate = new Date();
    let selectedDate = null;

    function nombreMes(num) {
        return [
            "Enero", "Febrero", "Marzo", "Abril",
            "Mayo", "Junio", "Julio", "Agosto",
            "Septiembre", "Octubre", "Noviembre", "Diciembre"
        ][num];
    }

    function renderCalendar(year, month) {
        calendarEl.innerHTML = '';

        // Cabecera con botones
        const header = document.createElement('div');
        header.className = 'cal-header';

        const prevBtn = document.createElement('button');
        prevBtn.textContent = '<';
        prevBtn.onclick = () => {
            if (month === 0) {
                year--;
                month = 11;
            } else {
                month--;
            }
            renderCalendar(year, month);
        };

        const nextBtn = document.createElement('button');
        nextBtn.textContent = '>';
        nextBtn.onclick = () => {
            if (month === 11) {
                year++;
                month = 0;
            } else {
                month++;
            }
            renderCalendar(year, month);
        };

        const monthYear = document.createElement('div');
        monthYear.textContent = nombreMes(month) + ' ' + year;

        header.appendChild(prevBtn);
        header.appendChild(monthYear);
        header.appendChild(nextBtn);
        calendarEl.appendChild(header);

        // Días de la semana
        const daysDiv = document.createElement('div');
        daysDiv.className = 'cal-days';
        ['Lun','Mar','Mié','Jue','Vie','Sáb','Dom'].forEach(dia => {
            const day = document.createElement('div');
            day.textContent = dia;
            daysDiv.appendChild(day);
        });
        calendarEl.appendChild(daysDiv);

        // Fechas
        const datesDiv = document.createElement('div');
        datesDiv.className = 'cal-dates';

        // Día de la semana del primer día (lunes=1 ... domingo=7)
        let firstDay = new Date(year, month, 1).getDay();
        // Ajuste: JS getDay() domingo=0, lunes=1... 
        firstDay = firstDay === 0 ? 7 : firstDay;

        // Número de días en el mes
        const daysInMonth = new Date(year, month+1, 0).getDate();

        // Rellenar huecos previos con empty
        for (let i=1; i<firstDay; i++) {
            const emptyDiv = document.createElement('div');
            emptyDiv.className = 'empty';
            datesDiv.appendChild(emptyDiv);
        }

        // Generar días
        for (let d=1; d<=daysInMonth; d++) {
            const dateDiv = document.createElement('div');
            dateDiv.className = 'cal-day';
            dateDiv.textContent = d;

            const dateStr = `${year}-${String(month+1).padStart(2,'0')}-${String(d).padStart(2,'0')}`;
            const todayStr = new Date().toISOString().slice(0,10);

            // Deshabilitar fechas pasadas
            if (dateStr < todayStr) {
                dateDiv.classList.add('disabled');
            }

            dateDiv.onclick = () => {
                if (dateDiv.classList.contains('disabled')) return;
                selectedDate = dateStr;

                // Deseleccionar todos y seleccionar este
                document.querySelectorAll('.cal-day.selected').forEach(el => {
                    el.classList.remove('selected');
                });
                dateDiv.classList.add('selected');

                // Actualizar campo oculto y texto visible
                fechaInput.value = selectedDate;
                fechaSeleccionada.innerHTML = `Fecha: <strong>${selectedDate}</strong>`;
            };

            datesDiv.appendChild(dateDiv);
        }

        calendarEl.appendChild(datesDiv);
    }

    renderCalendar(currentDate.getFullYear(), currentDate.getMonth());

    // Prevenir enviar sin fecha seleccionada
    form.addEventListener('submit', e => {
        if (!fechaInput.value) {
            e.preventDefault();
            alert('Debes seleccionar una fecha en el calendario.');
        }
    });
})();
</script>

<?php include 'footer.php'; ?>
</body>
</html>
