<?php
session_start();
include 'config.php';

/* --- Traer eventos ordenados por fecha --- */
$sql    = "SELECT * FROM Eventos ORDER BY fecha ASC";
$result = mysqli_query($conn, $sql);
if (!$result) {
    die("Error en la consulta: " . mysqli_error($conn));
}
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1" />
    <title>Eventos Públicos – Cieza</title>

    <!-- Tu hoja de estilos principal -->
    <link rel="stylesheet" href="css/eventos.css" />

    <style>
        /* ---------- Tarjetas ---------- */
        .evento{
            display:block;
            margin:20px;
            padding:15px;
            background:#2b2b2b;
            border-radius:10px;
            text-align:center;
            box-shadow:0 0 6px rgba(0,0,0,.4);
        }
        .evento img{width:150px;border-radius:8px}
        .evento h3{margin:10px 0;color:#ffcc00}
        .evento p{margin:2px 0;color:#eee}
        .btn{display:inline-block;margin-top:8px;padding:6px 14px;background:#e60000;color:#fff;border-radius:5px;text-decoration:none}
        .btn:hover{background:#b30000}
        .evento.oculto{display:none}

        /* ---------- Modal ---------- */
        .modal{
            position:fixed;inset:0;
            display:flex;justify-content:center;align-items:center;
            background:rgba(0,0,0,.75);
            z-index:999
        }
        .modal.oculto{display:none}
        .modal-contenido{
            background:#fff;color:#111;
            padding:20px;width:90%;max-width:520px;
            border-radius:12px;position:relative;
            box-shadow:0 0 10px rgba(0,0,0,.5);
            text-align:center
        }
        .modal-contenido img{width:100%;border-radius:10px;margin-bottom:15px}
        .modal-contenido h3{margin-bottom:10px;color:#e60000}
        .modal-contenido p{margin:4px 0;font-size:15px}
        .cerrar{
            position:absolute;top:8px;right:12px;
            font-size:26px;font-weight:bold;
            background:none;border:none;color:#e60000;
            cursor:pointer;line-height:1
        }
        .cerrar:hover{color:#b30000}
    </style>
</head>
<body>

<?php include 'header.php'; ?>

<main>
    <!-- ------------------ Filtro ------------------ -->
    <section class="filtro-eventos">
        <h2>Eventos Públicos en Cieza</h2>
        <label for="categoria">Filtrar por categoría:</label>
        <select id="categoria">
            <option value="todos">Todos</option>
            <option value="concierto">Conciertos</option>
            <option value="feria">Ferias</option>
            <option value="teatro">Teatro</option>
        </select>
    </section>

    <!-- ------------------ Listado ------------------ -->
    <section id="lista-eventos" class="eventos-publicos">
    <?php
    mysqli_data_seek($result, 0);
    while ($evt = mysqli_fetch_assoc($result)): ?>
        <div class="evento"
             data-id="<?= (int)$evt['id'] ?>"
             data-categoria="<?= htmlspecialchars($evt['categoria']) ?>"
             data-imagen="/CiezaActiva-main/TFG/<?= htmlspecialchars($evt['imagen']) ?>"
             data-titulo="<?= htmlspecialchars($evt['titulo']) ?>"
             data-lugar="<?= htmlspecialchars($evt['lugar']) ?>"
             data-fecha="<?= date('d/m/Y', strtotime($evt['fecha'])) ?>"
             data-precio="<?= $evt['precio'] > 0 ? number_format($evt['precio'],2,',','.') . ' €' : 'Gratuita' ?>"
             data-descripcion="<?= htmlspecialchars($evt['descripcion'], ENT_QUOTES) ?>">
             
            <img src="/CiezaActiva-main/TFG/<?= htmlspecialchars($evt['imagen']) ?>" alt="<?= htmlspecialchars($evt['titulo']) ?>">
            <h3><?= htmlspecialchars($evt['titulo']) ?></h3>
            <p>📍 <?= htmlspecialchars($evt['lugar']) ?> - <?= date('d M', strtotime($evt['fecha'])) ?></p>
            <p>🎟️ Entrada: <?= $evt['precio'] > 0 ? number_format($evt['precio'],2,',','.') . '€' : 'Gratuita' ?></p>
            <a href="#" class="btn ver-mas" data-id="<?= (int)$evt['id'] ?>">Ver Más</a>
        </div>
    <?php endwhile; ?>
    </section>
</main>

<?php include 'footer.php'; ?>

<!--  Modal oculto -->
<div id="modal" class="modal oculto">
    <div class="modal-contenido">
        <button class="cerrar" id="cerrar-modal">&times;</button>
        <img id="modal-imagen" src="" alt="">
        <h3 id="modal-titulo"></h3>
        <p id="modal-lugar"></p>
        <p id="modal-fecha"></p>
        <p id="modal-precio"></p>
        <p id="modal-descripcion" style="margin-top:10px"></p>
    </div>
</div>

<script>
/* ---------- Filtro por categoría ---------- */
document.getElementById('categoria').addEventListener('change', e=>{
    const cat = e.target.value;
    document.querySelectorAll('#lista-eventos .evento').forEach(ev=>{
        ev.classList.toggle('oculto', !(cat==='todos' || ev.dataset.categoria===cat));
    });
});

/* ---------- Modal ---------- */
const modal        = document.getElementById('modal');
const modalImg     = document.getElementById('modal-imagen');
const modalTitulo  = document.getElementById('modal-titulo');
const modalLugar   = document.getElementById('modal-lugar');
const modalFecha   = document.getElementById('modal-fecha');
const modalPrecio  = document.getElementById('modal-precio');
const modalDesc    = document.getElementById('modal-descripcion');
const cerrarBtn    = document.getElementById('cerrar-modal');

document.querySelectorAll('.ver-mas').forEach(btn=>{
    btn.addEventListener('click', e=>{
        e.preventDefault();
        const card = btn.closest('.evento');

        modalImg.src      = card.dataset.imagen;
        modalImg.alt      = card.dataset.titulo;
        modalTitulo.textContent = card.dataset.titulo;
        modalLugar.textContent  = "📍 " + card.dataset.lugar;
        modalFecha.textContent  = "📅 " + card.dataset.fecha;
        modalPrecio.textContent = "🎟️ " + card.dataset.precio;

        const descripcion = card.dataset.descripcion || "Sin descripción disponible.";
        modalDesc.innerHTML = descripcion.replace(/\n/g,"<br>");

        modal.classList.remove('oculto');
    });
});

/* Cerrar modal */
cerrarBtn.onclick = ()=> modal.classList.add('oculto');
modal.onclick = e=>{ if(e.target===modal) modal.classList.add('oculto'); };
</script>
</body>
</html>
