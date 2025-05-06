document.addEventListener("DOMContentLoaded", function () {
    const calendar = document.getElementById("calendar");
    const fechaInput = document.getElementById("fecha-seleccionada");

    const meses = [
        "Enero", "Febrero", "Marzo", "Abril", "Mayo", "Junio",
        "Julio", "Agosto", "Septiembre", "Octubre", "Noviembre", "Diciembre"
    ];

    let fechaActual = new Date();

    function renderCalendar(fecha) {
        const año = fecha.getFullYear();
        const mes = fecha.getMonth();

        const primerDiaMes = new Date(año, mes, 1);
        const ultimoDiaMes = new Date(año, mes + 1, 0);

        const primerDiaSemana = primerDiaMes.getDay(); // 0 (domingo) - 6 (sábado)

        calendar.innerHTML = `
            <div class="cal-header">
                <button id="prev">&lt;</button>
                <span>${meses[mes]} ${año}</span>
                <button id="next">&gt;</button>
            </div>
            <div class="cal-body">
                <div class="cal-days">
                    <div>Dom</div><div>Lun</div><div>Mar</div><div>Mié</div><div>Jue</div><div>Vie</div><div>Sáb</div>
                </div>
                <div class="cal-dates"></div>
            </div>
        `;

        const fechasContainer = calendar.querySelector(".cal-dates");

        for (let i = 0; i < primerDiaSemana; i++) {
            fechasContainer.innerHTML += `<div class="empty"></div>`;
        }

        for (let dia = 1; dia <= ultimoDiaMes.getDate(); dia++) {
            const diaElemento = document.createElement("div");
            diaElemento.className = "cal-day";
            diaElemento.textContent = dia;

            diaElemento.addEventListener("click", () => {
                document.querySelectorAll(".cal-day").forEach(d => d.classList.remove("selected"));
                diaElemento.classList.add("selected");

                const fechaSeleccionada = new Date(año, mes, dia);
                fechaInput.value = fechaSeleccionada.toISOString().split("T")[0];

                document.getElementById("hora-selection").style.display = "block";
                document.getElementById("reservar-btn").style.display = "block";
            });

            fechasContainer.appendChild(diaElemento);
        }

        document.getElementById("prev").onclick = () => {
            fechaActual.setMonth(fechaActual.getMonth() - 1);
            renderCalendar(fechaActual);
        };

        document.getElementById("next").onclick = () => {
            fechaActual.setMonth(fechaActual.getMonth() + 1);
            renderCalendar(fechaActual);
        };
    }

    renderCalendar(fechaActual);
});
