<?php
/* ------------------ conexión ------------------ */
$servername = "localhost";
$username   = "root";
$password   = "";
$dbname     = "afr_database";

$conn = new mysqli($servername, $username, $password, $dbname);
if ($conn->connect_error) {
    die("Conexión fallida: " . $conn->connect_error);
}

/* --------------- asegurar columna descripción --------------- */
$conn->query("ALTER TABLE Eventos 
              ADD COLUMN IF NOT EXISTS descripcion TEXT NULL
              AFTER imagen");

/* ---------- mensajes ---------- */
$mensaje = $error = "";

/* ---------- BORRAR ---------- */
if (isset($_GET['borrar_id'])) {
    $id = intval($_GET['borrar_id']);
    $stmt = $conn->prepare("DELETE FROM Eventos WHERE id = ?");
    $stmt->bind_param("i", $id);
    $stmt->execute() ? $mensaje = "Evento eliminado." 
                     : $error   = "Error al eliminar: ".$stmt->error;
    $stmt->close();
}

/* ---------- INSERTAR ---------- */
if ($_SERVER['REQUEST_METHOD']==='POST'
    && isset($_POST['titulo'],$_POST['categoria'],$_POST['fecha'],
             $_POST['lugar'],$_POST['precio'])) {

    $titulo      = trim($_POST['titulo']);
    $categoria   = trim($_POST['categoria']);
    $fecha       = trim($_POST['fecha']);
    $lugar       = trim($_POST['lugar']);
    $precio      = floatval($_POST['precio']);
    $descripcion = trim($_POST['descripcion'] ?? '');

    if ($titulo===""||$categoria===""||$fecha===""||$lugar===""||$precio<0){
        $error = "Completa todos los campos obligatorios.";
    } else {
        /* ------ subir imagen ------ */
        if (isset($_FILES['imagen']) && $_FILES['imagen']['error']===UPLOAD_ERR_OK){
            $ext = strtolower(pathinfo($_FILES['imagen']['name'],PATHINFO_EXTENSION));
            if (!in_array($ext,['jpg','jpeg','png','gif'])){
                $error = "Formato de imagen no válido.";
            } else {
                if(!is_dir('uploads/eventos')) mkdir('uploads/eventos',0777,true);
                $rutaRelativa = 'uploads/eventos/'.uniqid().'.'.$ext;
                if (move_uploaded_file($_FILES['imagen']['tmp_name'],$rutaRelativa)){
                    /* ------ insertar ------ */
                    $stmt=$conn->prepare(
                      "INSERT INTO Eventos
                       (titulo,categoria,fecha,lugar,precio,imagen,descripcion)
                       VALUES (?,?,?,?,?,?,?)");

                    /* ----------  ¡CORREGIDO!  ---------- */
                    //  s s s s d s s
                    $stmt->bind_param("ssssdss",
                        $titulo,$categoria,$fecha,$lugar,$precio,$rutaRelativa,$descripcion);

                    if ($stmt->execute()){
                        $mensaje="Evento añadido correctamente.";
                    }else{
                        $error="Error al añadir evento: ".$stmt->error;
                        unlink($rutaRelativa);
                    }
                    $stmt->close();
                }else $error="Error al mover la imagen.";
            }
        } else $error="Sube una imagen válida.";
    }
}

/* ---------- listado ---------- */
$eventos = [];
$res = $conn->query("SELECT * FROM Eventos ORDER BY id DESC");
while($row = $res? $res->fetch_assoc():[]) $eventos[] = $row;

$conn->close();
?>
<!DOCTYPE html>
<html lang="es">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width,initial-scale=1">
<title>Gestión de Eventos</title>
<style>
    body{font-family:Arial, sans-serif;max-width:900px;margin:20px auto;padding:10px;background:#f9f9f9}
    h1{text-align:center}
    form{background:#fff;padding:15px;border-radius:8px;margin-bottom:30px;box-shadow:0 0 8px #ccc}
    label{display:block;margin-top:10px}
    input[type=text],input[type=date],input[type=number],select,textarea
        {width:100%;padding:8px;box-sizing:border-box;margin-top:5px}
    textarea{resize:vertical;min-height:80px}
    input[type=file]{margin-top:8px}
    button{margin-top:15px;padding:10px 20px;background:#007BFF;border:none;color:#fff;border-radius:5px;cursor:pointer}
    button:hover{background:#0056b3}
    table{width:100%;border-collapse:collapse;background:#fff;box-shadow:0 0 8px #ccc}
    th,td{border:1px solid #ddd;padding:10px;text-align:center}
    th{background:#007BFF;color:#fff}
    img{max-width:100px;border-radius:6px}
    .mensaje{background:#d4edda;color:#155724;padding:10px;border-radius:5px;margin-bottom:20px}
    .error{background:#f8d7da;color:#721c24;padding:10px;border-radius:5px;margin-bottom:20px}
    a.borrar{color:#dc3545;font-weight:bold;text-decoration:none}
    a.borrar:hover{text-decoration:underline}
    .truncate{max-width:200px;white-space:nowrap;overflow:hidden;text-overflow:ellipsis}
</style>
</head>
<body>

<h1>Gestión de Eventos</h1>

<?php if($mensaje):?><div class="mensaje"><?=htmlspecialchars($mensaje)?></div><?php endif;?>
<?php if($error):  ?><div class="error"><?=htmlspecialchars($error)?></div><?php endif;?>

<form method="post" enctype="multipart/form-data">
    <h2>Añadir nuevo evento</h2>

    <label>Título*:</label>
    <input name="titulo" required>

    <label>Categoría*:</label>
    <select name="categoria" required>
        <option value="">-- Selecciona --</option>
        <option value="concierto">Concierto</option>
        <option value="feria">Feria</option>
        <option value="teatro">Teatro</option>
        <option value="otro">Otro</option>
    </select>

    <label>Fecha*:</label>
    <input type="date" name="fecha" required>

    <label>Lugar*:</label>
    <input name="lugar" required>

    <label>Precio (€):</label>
    <input type="number" step="0.01" min="0" name="precio" required>

    <label>Descripción:</label>
    <textarea name="descripcion" placeholder="Información adicional (opcional)"></textarea>

    <label>Imagen*:</label>
    <input type="file" name="imagen" accept=".jpg,.jpeg,.png,.gif" required>

    <button type="submit">Añadir evento</button>
</form>

<h2>Eventos existentes</h2>
<?php if(!$eventos): ?>
    <p>No hay eventos registrados.</p>
<?php else: ?>
<table>
 <thead>
  <tr>
    <th>ID</th><th>Título</th><th>Categoría</th><th>Fecha</th><th>Lugar</th>
    <th>Imagen</th><th>Precio (€)</th><th>Descripción</th><th>Acciones</th>
  </tr>
 </thead>
 <tbody>
<?php foreach($eventos as $e): ?>
  <tr>
    <td><?=$e['id']?></td>
    <td><?=htmlspecialchars($e['titulo'])?></td>
    <td><?=htmlspecialchars($e['categoria'])?></td>
    <td><?=htmlspecialchars($e['fecha'])?></td>
    <td><?=htmlspecialchars($e['lugar'])?></td>
    <td>
      <?php $ruta=__DIR__.'/'.$e['imagen']; ?>
      <?php if($e['imagen'] && file_exists($ruta)): ?>
        <img src="/CiezaActiva-main/TFG/<?=htmlspecialchars($e['imagen'])?>" alt="">
      <?php else: ?>Sin imagen<?php endif;?>
    </td>
    <td><?=number_format($e['precio'],2,',','.')?></td>
    <td class="truncate"><?=htmlspecialchars($e['descripcion'])?></td>
    <td><a href="?borrar_id=<?=$e['id']?>" class="borrar"
           onclick="return confirm('¿Borrar este evento?');">Borrar</a></td>
  </tr>
<?php endforeach;?>
 </tbody>
</table>
<?php endif;?>

</body>
</html>
