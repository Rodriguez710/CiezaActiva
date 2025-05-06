document.addEventListener('DOMContentLoaded', () => {
    const calendarContainer = document.createElement('div');
    calendarContainer.id = 'calendar';
    document.querySelector('.descripcion').appendChild(calendarContainer);

    const fechaInput = document.getElementById('fecha');
    const horaInicioSelect = document.getElementById('hora-inicio');
    const horaFinSelect = document.getElementById('hora-fin');
    const reservarBtn = document.getElementById('reservar-btn');

    // Generar el calendario del mes actual
    function generateCalendar() {
        const now = new Date();
        const currentMonth = now.getMonth(); // Mes actual
        const currentYear = now.getFullYear(); // Año actual

        const daysInMonth = new Date(currentYear, currentMonth + 1, 0).getDate();
        calendarContainer.innerHTML = ''; // Limpiar calendario

        // Crear celdas para cada día del mes
        for (let i = 1; i <= daysInMonth; i++) {
            const dayCell = document.createElement('div');
            dayCell.textContent = i;
            dayCell.classList.add('calendar-day');
            dayCell.dataset.day = i; // Guardamos el día como atributo

            // Agregar evento de clic para seleccionar el día
            dayCell.addEventListener('click', (e) => {
                const selectedDay = e.target.dataset.day;
                fechaInput.value = `${currentYear}-${currentMonth + 1 < 10 ? '0' : ''}${currentMonth + 1}-${selectedDay < 10 ? '0' : ''}${selectedDay}`;
                generateAvailableHours(selectedDay);
            });

            calendarContainer.appendChild(dayCell);
        }
    }

    // Genera las horas disponibles para el día seleccionado
    function generateAvailableHours(day) {
        // Este ejemplo simula las horas reservadas, deberías obtenerlo de una base de datos en un caso real
        const reservedHours = ['10:00', '12:00', '15:00']; 
        const allHours = ['09:00', '10:00', '11:00', '12:00', '13:00', '14:00', '15:00', '16:00', '17:00', '18:00', '19:00', '20:00', '21:00'];

        const availableHours = allHours.filter(hour => !reservedHours.includes(hour));
        
        // Llenar el select de hora de inicio
        horaInicioSelect.innerHTML = '';
        availableHours.forEach(hour => {
            const option = document.createElement('option');
            option.value = hour;
            option.textContent = hour;
            horaInicioSelect.appendChild(option);
        });

        horaInicioSelect.disabled = false; // Habilitar select de hora de inicio

        // Establecer el evento cuando cambia la hora de inicio
        horaInicioSelect.addEventListener('change', handleStartHourChange);
    }

    // Cuando cambia la hora de inicio, actualizar la hora de fin
    function handleStartHourChange() {
        const startHour = horaInicioSelect.value;
        if (!startHour) return;

        const endHour = getEndHour(startHour);
        fillEndHourSelect(endHour);
    }

    // Calcular la hora de fin (máximo 1 hora)
    function getEndHour(startHour) {
        const startTime = new Date(`01/01/2000 ${startHour}`);
        const endTime = new Date(startTime.getTime() + 60 * 60 * 1000); // Sumar 1 hora

        // Formatear la hora de fin
        const endHour = `${endTime.getHours()}:${endTime.getMinutes() < 10 ? '0' : ''}${endTime.getMinutes()}`;
        return endHour;
    }

    // Llenar el select de hora de fin
    function fillEndHourSelect(endHour) {
        horaFinSelect.innerHTML = ''; // Limpiar select de hora fin
        const option = document.createElement('option');
        option.value = endHour;
        option.textContent = endHour;
        horaFinSelect.appendChild(option);

        horaFinSelect.disabled = false; // Habilitar select de hora fin
    }

    // Generar el calendario al cargar la página
    generateCalendar();

    // Función de reserva (aquí agregas la lógica para manejar la reserva)
    reservarBtn.addEventListener('click', () => {
        const selectedDate = fechaInput.value;
        const startHour = horaInicioSelect.value;
        const endHour = horaFinSelect.value;

        if (!selectedDate || !startHour || !endHour) {
            alert('Por favor, selecciona una fecha y una hora válidas.');
        } else {
            alert(`Reserva confirmada para el ${selectedDate} de ${startHour} a ${endHour}.`);
            // Aquí puedes agregar la lógica para enviar los datos al servidor y guardar la reserva.
        }
    });
});
