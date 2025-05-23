<?php
session_start();
include 'config.php'; // Incluir la conexión a la base de datos

// Verificar si el usuario está logueado
if (!isset($_SESSION['user_id'])) {
    echo 'No estás logueado.';
    exit();
}

$user_id = $_SESSION['user_id'];

// Obtener los datos del usuario desde la base de datos
$stmt = $conn->prepare("SELECT nombre, apellidos, email, telefono, fecha_nacimiento, direccion, foto_perfil FROM Usuarios WHERE id = ?");
$stmt->bind_param("i", $user_id);
$stmt->execute();
$result = $stmt->get_result();
$user = $result->fetch_assoc();

// Cerrar la conexión
$stmt->close();
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Mi Perfil</title>
    <link rel="stylesheet" href="css/estilos.css"> <!-- Si tienes tu propio archivo CSS -->
</head>
<body>

    <div class="perfil-container">
        <h2>Mi Perfil</h2>

        <!-- Mostrar foto de perfil -->
        <div class="foto-perfil">
            <img src="uploads/usuarios/<?= $user['foto_perfil'] ?>" alt="Foto de Perfil" class="user-img">
        </div>

        <!-- Mostrar datos del usuario -->
        <p><strong>Nombre:</strong> <?= $user['nombre'] ?> <?= $user['apellidos'] ?></p>
        <p><strong>Email:</strong> <?= $user['email'] ?></p>
        <p><strong>Teléfono:</strong> <?= $user['telefono'] ?></p>
        <p><strong>Fecha de Nacimiento:</strong> <?= $user['fecha_nacimiento'] ?></p>
        <p><strong>Dirección:</strong> <?= $user['direccion'] ?: 'No especificada' ?></p>

        <!-- Botón para editar el perfil -->
        <button id="editarPerfilBtn">Editar Perfil</button>

        <!-- Formulario de edición de perfil oculto inicialmente -->
        <div id="formEditarPerfil" style="display: none;">
            <h3>Editar Perfil</h3>
            <form id="formularioEditarPerfil" method="POST" enctype="multipart/form-data">
                <label for="nombre">Nombre:</label>
                <input type="text" name="nombre" id="nombre" value="<?= $user['nombre'] ?>" required>

                <label for="apellidos">Apellidos:</label>
                <input type="text" name="apellidos" id="apellidos" value="<?= $user['apellidos'] ?>" required>

                <label for="email">Correo electrónico:</label>
                <input type="email" name="email" id="email" value="<?= $user['email'] ?>" required>

                <label for="telefono">Teléfono:</label>
                <input type="tel" name="telefono" id="telefono" value="<?= $user['telefono'] ?>" required>

                <label for="fecha_nacimiento">Fecha de Nacimiento:</label>
                <input type="date" name="fecha_nacimiento" id="fecha_nacimiento" value="<?= $user['fecha_nacimiento'] ?>" required>

                <label for="direccion">Dirección:</label>
                <input type="text" name="direccion" id="direccion" value="<?= $user['direccion'] ?>">

                <!-- Cambiar foto de perfil -->
                <label for="foto_perfil">Foto de perfil:</label>
                <input type="file" name="foto_perfil" id="foto_perfil">

                <button type="submit" id="guardarCambios">Guardar Cambios</button>
            </form>
        </div>
    </div>

    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script>
        // Mostrar el formulario de edición al hacer clic en el botón "Editar Perfil"
        $('#editarPerfilBtn').click(function() {
            $('#formEditarPerfil').toggle();
        });

        // Enviar el formulario de edición sin recargar la página (AJAX)
        $('#formularioEditarPerfil').submit(function(e) {
            e.preventDefault();  // Evitar que el formulario se envíe de forma tradicional

            var formData = new FormData(this);  // Capturar los datos del formulario

            $.ajax({
                url: 'editar-perfil.php',  // Archivo PHP donde se manejará la edición
                type: 'POST',
                data: formData,
                processData: false,
                contentType: false,
                success: function(response) {
                    alert('Perfil actualizado correctamente');
                    location.reload();  // Recargar la página para ver los cambios
                },
                error: function() {
                    alert('Error al actualizar el perfil');
                }
            });
        });
    </script>

</body>
</html>
