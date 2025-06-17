<?php
session_start();

// Inicializa las sesiones si no existen
if (!isset($_SESSION['reservas'])) {
    $_SESSION['reservas'] = [];
}

// Procesar nueva reserva
if ($_SERVER["REQUEST_METHOD"] === "POST" && isset($_POST['fecha']) && isset($_POST['hora']) && isset($_POST['instalacion'])) {
    $fecha = $_POST['fecha'];
    $hora = $_POST['hora'];
    $instalacion = $_POST['instalacion'];  // Ahora viene del formulario

    // Verificar si esa fecha y hora ya está reservada para la misma instalación
    $ocupado = false;
    foreach ($_SESSION['reservas'] as $res) {
        if ($res['fecha'] === $fecha && $res['hora'] === $hora && $res['instalacion'] === $instalacion) {
            $ocupado = true;
            break;
        }
    }

    if (!$ocupado) {
        // Añadir a reservas generales
        $_SESSION['reservas'][] = [
            'instalacion' => $instalacion,
            'fecha' => $fecha,
            'hora' => $hora
        ];

        // Añadir también al carrito del usuario
        if (!isset($_SESSION['carrito'])) {
            $_SESSION['carrito'] = [];
        }

        $_SESSION['carrito'][] = [
            'instalacion' => $instalacion,
            'fecha' => $fecha,
            'hora' => $hora
        ];

        $_SESSION['mensaje_reserva'] = "Reserva agregada al carrito correctamente.";
    } else {
        $_SESSION['mensaje_reserva'] = "¡Error! Esa hora ya está reservada para la fecha e instalación seleccionadas.";
    }

    header("Location: reservas-baloncesto.php");
    exit();
}

// Pasar reservas a JS
$reservas_json = json_encode($_SESSION['reservas']);
?>

<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1" />
    <title>Reserva Campo de Fútbol</title>
    <style>
              /* Estilos generales para el cuerpo */
body {
    font-family: Arial, sans-serif;
    background-color: #f1f1f1;
    color: #000000;
    margin: 0;
    padding: 0;
    display: flex;
    flex-direction: column;
    min-height: 100vh;
    justify-content: center;
}

/* Cabecera */
header {
    background-color: #ffffff;
    color: rgb(255, 255, 255);
    padding: 10px 20px;
    display: flex;
    justify-content: space-between;
    align-items: center;
    box-shadow: 4px 4px 10px rgba(0, 0, 0, 0.5);
}

header .logo img {
    width: 120px;
    height: auto;
}

/* Contenedor de los íconos de Login y Carrito */
header .login-cart {
    display: flex;
    justify-content: space-between;
    align-items: center;
    width: 130px;
}

header .login-cart a {
    color: #e67c7a;
    text-decoration: none;
    margin: 20 20px;
}

header .login-cart a img {
    width: 50px;
    height: 50px;
}

/* Menú centrado */
header nav {
    flex-grow: 1;
    text-align: center;
}

header nav ul {
    list-style: none;
    padding: 0;
}

header nav ul li {
    display: inline-block;
    margin-right: 20px;
    background-color: #e67c7a;
    padding: 10px 20px;
    border-radius: 8px;
    box-shadow: 0 4px 8px rgba(0, 0, 0, 0.3);
    transition: background 0.3s, transform 0.2s, box-shadow 0.3s;
}

header nav ul li:hover {
    background-color: #e67c7a;
    transform: translateY(-2px);
    box-shadow: 0 6px 12px rgba(0, 0, 0, 0.4);
}

header nav ul li a {
    color: white;
    text-decoration: none;
    font-size: 16px;
    font-weight: bold;
    display: block;
}

header nav ul li a:hover {
    color: #fff;
}

/* Estilo de la reserva */
main {
    padding: 20px;
    width: 100%;
    max-width: 1200px;
    margin: 0 auto;
}

.descripcion {
    text-align: center;
    box-shadow: 4px 4px 10px rgba(0, 0, 0, 0.5);
    padding: 10px;
}

.detalles {
    display: flex;
    justify-content: center;
    align-items: center;
    margin-top: 30px;
    gap: 20px;
    flex-wrap: wrap;
}

.detalles .imagen {
    width: 50%;
    max-width: 500px;
    padding: 20px;
}

.detalles .imagen img {
    max-width: 100%;
    height: auto;
    border-radius: 8px;
    display: block;
    margin: 0 auto;
}

.detalles .informacion {
    width: 50%;
    max-width: 500px;
    padding: 20px;
    box-sizing: border-box;
}

.detalles .informacion h3 {
    color: #e67c7a;
    margin-bottom: 10px;
    font-size: 28px;
}

.detalles .informacion p {
    font-size: 16px;
    margin-bottom: 10px;
}

.detalles .informacion label {
    display: block;
    margin-top: 10px;
}

.detalles .informacion select,
.detalles .informacion input {
    width: 100%;
    padding: 8px;
    margin-top: 5px;
    font-size: 16px;
    box-sizing: border-box;
}

.detalles .informacion button {
    margin-top: 20px;
    padding: 10px 20px;
    background-color: #e67c7a;
    color: rgb(59, 59, 59);
    border: none;
    cursor: pointer;
    font-size: 16px;
    border-radius: 5px;
    transition: background-color 0.3s ease;
}

.detalles .informacion button:hover {
    background-color: #c35c5c;
}

/* Estilos generales */
body {
    font-family: Arial, sans-serif;
    background-color: #1c1c1c; /* Fondo oscuro */
    color: #fff;
    margin: 0;
    padding: 0;
    display: flex;
    flex-direction: column;
    min-height: 100vh;
    justify-content: center;
}

/* Cabecera */
header {
    background-color: #5e5e5e;
    color: white;
    padding: 10px 20px;
    display: flex;
    justify-content: space-between;
    align-items: center;
    box-shadow: 4px 4px 10px rgba(0, 0, 0, 0.5);
}

header .logo img {
    width: 120px;
    height: auto;
}

header .login-cart {
    display: flex;
    justify-content: space-between;
    align-items: center;
    width: 130px;
}

header .login-cart a {
    color: #ee0707;
    text-decoration: none;
    margin: 0 10px;
}

header .login-cart a img {
    width: 50px;
    height: 50px;
}

header nav {
    flex-grow: 1;
    text-align: center;
}

header nav ul {
    list-style: none;
    padding: 0;
}

header nav ul li {
    display: inline-block;
    margin-right: 20px;
    background-color: #e60000;
    padding: 10px 20px;
    border-radius: 8px;
}

header nav ul li:hover {
    background-color: #b30000;
}

header nav ul li a {
    color: white;
    text-decoration: none;
    font-size: 16px;
    font-weight: bold;
    display: block;
}

header nav ul li a:hover {
    color: #fff;
}

/* Cuerpo principal */
main {
    padding: 20px;
    width: 100%;
    max-width: 1200px;
    margin: 0 auto;
}

.descripcion {
    text-align: center;
    box-shadow: 4px 4px 10px rgba(0, 0, 0, 0.5);
    padding: 10px;
}

.detalles {
    display: flex;
    justify-content: center;
    align-items: center;
    margin-top: 30px;
    gap: 20px;
    flex-wrap: wrap;
}

.detalles .imagen {
    width: 50%;
    max-width: 500px;
    padding: 20px;
}

.detalles .imagen img {
    max-width: 100%;
    height: auto;
    border-radius: 8px;
    display: block;
    margin: 0 auto;
}

.detalles .informacion {
    width: 50%;
    max-width: 500px;
    padding: 20px;
    box-sizing: border-box;
}

.detalles .informacion h3 {
    color: #e60000;
    margin-bottom: 10px;
    font-size: 28px;
}

.detalles .informacion p {
    font-size: 16px;
    margin-bottom: 10px;
}

.detalles .informacion label {
    display: block;
    margin-top: 10px;
}

.detalles .informacion select,
.detalles .informacion input {
    width: 100%;
    padding: 8px;
    margin-top: 5px;
    font-size: 16px;
    box-sizing: border-box;
    background-color: #2e2e2e;
    color: white;
    border: 1px solid #444;
}

.detalles .informacion button {
    margin-top: 20px;
    padding: 10px 20px;
    background-color: #e60000;
    color: white;
    border: none;
    cursor: pointer;
    font-size: 16px;
    border-radius: 5px;
    transition: background-color 0.3s ease;
}

.detalles .informacion button:hover {
    background-color: #b30000;
}

/* Calendario */
/* Calendario */
#calendar {
    display: flex;
    flex-direction: column;
    align-items: center;
    width: 100%;
    max-width: 420px;
    margin: 20px auto;
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

/* Responsive */
@media (max-width: 768px) {
    .detalles {
        flex-direction: column;
        align-items: center;
    }

    .detalles .imagen,
    .detalles .informacion {
        width: 90%;
        max-width: none;
        padding: 10px;
    }

    header nav ul li {
        margin-right: 10px;
        padding: 8px 12px;
        font-size: 14px;
    }

    header .login-cart a img {
        width: 40px;
        height: 40px;
    }
}


/* Mensaje emergente */
.mensaje-reserva {
    position: fixed;
    top: 10px;
    right: 10px;
    background-color: #e67c7a;
    color: white;
    padding: 10px 15px;
    border-radius: 5px;
    box-shadow: 2px 2px 10px #b94b4b;
    z-index: 100;
    font-weight: bold;
}

/* Responsive */
@media (max-width: 768px) {
    .detalles {
        flex-direction: column;
        align-items: center;
    }

    .detalles .imagen,
    .detalles .informacion {
        width: 90%;
        max-width: none;
        padding: 10px;
    }

    header nav ul li {
        margin-right: 10px;
        padding: 8px 12px;
        font-size: 14px;
    }

    header .login-cart a img {
        width: 40px;
        height: 40px;
    }
}
    </style>
</head>

<body>
    <?php include 'header.php'; ?>

    <main>
        <div class="descripcion">
            <h2>Reserva de la pista de baloncesto</h2>
            <p>¡Reserva tu horario para disfrutar de la pista de baloncesto!</p>
        </div>

        <div class="detalles">
            <div class="imagen">
                <img src="img/Baloncesto.jpg" alt="Pista de baloncesto" />
            </div>

            <div class="informacion">
                <form method="POST" id="reserva-form" action="reservas-baloncesto.php">
                    <!-- Campo oculto para pasar la instalación -->
                    <input type="hidden" name="instalacion" value="Pista de baloncesto" />

                    <!-- Campo oculto con el precio de esta instalación -->
                    <input type="hidden" name="precio" value="12.50" />

                    <label for="fecha">Selecciona la fecha:</label>
                    <input type="text" id="fecha" name="fecha" readonly required />

                    <label for="hora">Selecciona la hora:</label>
                    <select name="hora" id="hora" required>
                        <option value="" disabled selected>Selecciona una hora</option>
                        <option value="10:00">10:00 - 11:00</option>
                        <option value="11:00">11:00 - 12:00</option>
                        <option value="12:00">12:00 - 13:00</option>
                        <option value="13:00">13:00 - 14:00</option>
                        <option value="14:00">14:00 - 15:00</option>
                        <option value="15:00">15:00 - 16:00</option>
                        <option value="16:00">16:00 - 17:00</option>
                        <option value="17:00">17:00 - 18:00</option>
                    </select>

                    <button type="submit">Agregar al carrito</button>
                </form>
            </div>
        </div>

        <section id="calendar">
            <div class="cal-header">
                <button id="prev-month">&#8592;</button>
                <div id="month-year"></div>
                <button id="next-month">&#8594;</button>
            </div>
            <div class="cal-days">
                <div>Lun</div>
                <div>Mar</div>
                <div>Mié</div>
                <div>Jue</div>
                <div>Vie</div>
                <div>Sáb</div>
                <div>Dom</div>
            </div>
            <div class="cal-dates"></div>
        </section>
    </main>

    <?php if (isset($_SESSION['mensaje_reserva'])) : ?>
        <div class="mensaje-reserva" id="mensajeReserva"><?= htmlspecialchars($_SESSION['mensaje_reserva']) ?></div>
        <?php unset($_SESSION['mensaje_reserva']); ?>
    <?php endif; ?>

    <script>
        const reservas = <?= $reservas_json ?>;
        const calDates = document.querySelector(".cal-dates");
        const monthYear = document.getElementById("month-year");
        const prevMonthBtn = document.getElementById("prev-month");
        const nextMonthBtn = document.getElementById("next-month");
        const fechaInput = document.getElementById("fecha");
        const horaSelect = document.getElementById("hora");

        let today = new Date();
        let selectedDate = null;

        // Meses y días para el calendario
        const diasSemana = ['Dom', 'Lun', 'Mar', 'Mié', 'Jue', 'Vie', 'Sáb'];

        // Función para que el calendario empiece lunes
        function getFirstDayOfMonth(year, month) {
            let day = new Date(year, month, 1).getDay();
            return day === 0 ? 6 : day - 1; // Domingo 0 pasa a 6, lunes 1 pasa a 0
        }

        let currentMonth = today.getMonth();
        let currentYear = today.getFullYear();

        function renderCalendar(month, year) {
            calDates.innerHTML = "";

            // Mostrar mes y año en formato largo
            monthYear.textContent = new Date(year, month).toLocaleDateString('es-ES', {
                month: 'long',
                year: 'numeric'
            });

            let firstDay = getFirstDayOfMonth(year, month);
            let daysInMonth = new Date(year, month + 1, 0).getDate();

            // Espacios vacíos para empezar en el día correcto (lunes como primer día)
            for (let i = 0; i < firstDay; i++) {
                const emptyDiv = document.createElement('div');
                emptyDiv.classList.add('empty');
                calDates.appendChild(emptyDiv);
            }

            // Agregar días del mes
            for (let day = 1; day <= daysInMonth; day++) {
                const dateDiv = document.createElement('div');
                dateDiv.classList.add('cal-day');
                dateDiv.textContent = day;

                const fullDate = new Date(year, month, day);

                // Deshabilitar fechas anteriores a hoy
                if (fullDate < new Date(today.getFullYear(), today.getMonth(), today.getDate())) {
                    dateDiv.classList.add('disabled');
                }

                // Marcar día seleccionado
                if (selectedDate &&
                    fullDate.getDate() === selectedDate.getDate() &&
                    fullDate.getMonth() === selectedDate.getMonth() &&
                    fullDate.getFullYear() === selectedDate.getFullYear()) {
                    dateDiv.classList.add('selected');
                }

                
                if (!dateDiv.classList.contains('disabled')) {
                    dateDiv.addEventListener('click', () => {
                        selectedDate = fullDate;
                        fechaInput.value = selectedDate.toISOString().split('T')[0];
                        renderCalendar(currentMonth, currentYear);
                    });
                }

                calDates.appendChild(dateDiv);
            }
        }

        prevMonthBtn.addEventListener('click', () => {
            currentMonth--;
            if (currentMonth < 0) {
                currentMonth = 11;
                currentYear--;
            }
            renderCalendar(currentMonth, currentYear);
        });

        nextMonthBtn.addEventListener('click', () => {
            currentMonth++;
            if (currentMonth > 11) {
                currentMonth = 0;
                currentYear++;
            }
            renderCalendar(currentMonth, currentYear);
        });

        // Iniciar calendario
        renderCalendar(currentMonth, currentYear);

        // Validar formulario antes de enviar
        document.getElementById("reserva-form").addEventListener("submit", function(e) {
            if (!fechaInput.value) {
                alert("Por favor, selecciona una fecha en el calendario.");
                e.preventDefault();
                return;
            }
            if (!horaSelect.value) {
                alert("Por favor, selecciona una hora.");
                e.preventDefault();
            }
        });

        // Ocultar mensaje tras 3 segundos
        const mensaje = document.getElementById('mensajeReserva');
        if (mensaje) {
            setTimeout(() => {
                mensaje.style.display = 'none';
            }, 3000);
        }
    </script>
</body>

</html>
