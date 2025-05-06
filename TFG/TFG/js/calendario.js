// Definir las variables necesarias
const calendarElement = document.getElementById('calendar');
const prevButton = document.getElementById('calendar-prev');
const nextButton = document.getElementById('calendar-next');
const currentMonthLabel = document.getElementById('current-month');
const horaInicioSelect = document.getElementById('hora-inicio');
const horaFinSelect = document.getElementById('hora-fin');
const horaSelection = document.getElementById('hora-selection');
const reservarButton = document.getElementById('reservar-btn');

let currentDate = new Date();
let selectedDate = null;

// Función para renderizar el calendario
function renderCalendar() {
    const month = currentDate.getMonth();
    const year = currentDate.getFullYear();
    const firstDayOfMonth = new Date(year, month, 1);
    const lastDayOfMonth = new Date(year, month + 1, 0);
    const daysInMonth = lastDayOfMonth.getDate();
    const startingDay = firstDayOfMonth.getDay();

    // Limpiar calendario previo
    calendarElement.innerHTML = '';
    
    // Mostrar el mes actual
    currentMonthLabel.textContent = `${firstDayOfMonth.toLocaleString('es', { month: 'long' })} ${year}`;
    
    // Crear celdas para los días del mes
    for (let i = 0; i < startingDay; i++) {
        const emptyCell = document.createElement('div');
        calendarElement.appendChild(emptyCell);
    }

    // Crear celdas para cada día del mes
    for (let i = 1; i <= daysInMonth; i++) {
        const dayCell = document.createElement('div');
        dayCell.textContent = i;
        dayCell.classList.add('calendar-day');
        
        const currentDay = new Date(year, month, i);

        // Deshabilitar días anteriores a la fecha actual
        if (currentDay < new Date()) {
            dayCell.classList.add('disabled');
        }

        // Al seleccionar un día
        dayCell.addEventListener('click', function () {
            if (dayCell.classList.contains('disabled')) return;

            // Resaltar el día seleccionado
            const previousSelected = document.querySelector('.calendar-day.selected');
            if (previousSelected) {
                previousSelected.classList.remove('selected');
            }
            dayCell.classList.add('selected');

            // Establecer la fecha seleccionada
            selectedDate = new Date(year, month, i);
            showHoraSelection();
        });

        calendarElement.appendChild(dayCell);
    }
}

// Mostrar horas disponibles
function showHoraSelection() {
    if (!selectedDate) return;
    horaSelection.style.display = 'block';

    // Limpiar las opciones de hora
    horaInicioSelect.innerHTML = '';
    horaFinSelect.innerHTML = '';

    const startHour = 9; // Hora de inicio a las 9 AM
    const endHour = 20; // Hora de fin a las 8 PM

    for (let h = startHour; h <= endHour; h++) {
        const horaInicioOption = document.createElement('option');
        const horaFinOption = document.createElement('option');

        const hourLabel = `${h}:00`;
        horaInicioOption.value = hourLabel;
        horaFinOption.value = `${h + 1}:00`;

        horaInicioOption.textContent = hourLabel;
        horaFinOption.textContent = `${h + 1}:00`;

        horaInicioSelect.appendChild(horaInicioOption);
        horaFinSelect.appendChild(horaFinOption);
    }

    horaFinSelect.addEventListener('change', function () {
        const selectedStart = parseInt(horaInicioSelect.value.split(':')[0]);
        const selectedEnd = parseInt(horaFinSelect.value.split(':')[0]);
        
        if (selectedEnd <= selectedStart) {
            horaFinSelect.setCustomValidity('La hora de fin debe ser mayor que la hora de inicio.');
        } else {
            horaFinSelect.setCustomValidity('');
        }
    });
}

renderCalendar();
