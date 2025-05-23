<?php
session_start();
include 'config.php';

// Verificar si el usuario ha iniciado sesión
if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}

// Obtener datos del usuario
$user_id = $_SESSION['user_id'];
$stmt = $conn->prepare("SELECT foto_perfil FROM Usuarios WHERE id = ?");
$stmt->bind_param("i", $user_id);
$stmt->execute();
$result = $stmt->get_result();
$user = $result->fetch_assoc();
$current_photo = $user['foto_perfil'];

// Procesar subida de nueva foto
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $foto_perfil = $current_photo;

    if (isset($_FILES['foto_perfil']) && $_FILES['foto_perfil']['error'] == 0) {
        $file = $_FILES['foto_perfil'];
        $fileName = $file['name'];
        $fileTmpName = $file['tmp_name'];

        $fileExtension = strtolower(pathinfo($fileName, PATHINFO_EXTENSION));
        $validExtensions = ['jpg', 'jpeg', 'png', 'gif'];

        if (in_array($fileExtension, $validExtensions)) {
            $fileNewName = uniqid('', true) . "." . $fileExtension;
            $fileDestination = __DIR__ . '/uploads/usuarios/' . $fileNewName;

            if (move_uploaded_file($fileTmpName, $fileDestination)) {
                $foto_perfil = $fileNewName;
            } else {
                echo "error_move_file";
                exit();
            }
        } else {
            echo "error_ext";
            exit();
        }
    }

    $stmt = $conn->prepare("UPDATE Usuarios SET foto_perfil = ? WHERE id = ?");
    $stmt->bind_param("si", $foto_perfil, $user_id);
    if ($stmt->execute()) {
        echo "success";
    } else {
        echo "error_db";
    }
    $stmt->close();
    $conn->close();
    exit();
}
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1" />
    <title>Editar Perfil</title>
    <link rel="stylesheet" href="css/editar-perfil.css" />
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <style>
        /* Estilos para menú desplegable de usuario */
        .user-menu {
            position: relative;
            display: inline-block;
            margin-bottom: 20px;
        }

        .user-img {
            width: 40px;
            height: 40px;
            border-radius: 50%;
            cursor: pointer;
            vertical-align: middle;
        }

        .user-dropdown {
            display: none;
            position: absolute;
            top: 45px;
            right: 0;
            background-color: white;
            box-shadow: 0 8px 16px rgba(0,0,0,0.2);
            min-width: 160px;
            z-index: 1000;
            border-radius: 4px;
        }

        .user-dropdown ul {
            list-style: none;
            margin: 0;
            padding: 0;
        }

        .user-dropdown li {
            padding: 10px 16px;
        }

        .user-dropdown li a {
            text-decoration: none;
            color: black;
            display: block;
        }

        .user-dropdown li a:hover {
            background-color: #ddd;
        }

        /* Mensajes */
        #message p {
            font-weight: bold;
        }
    </style>
</head>
<body>

    <!-- Menú usuario -->
    <div class="user-menu">
        <img src="uploads/usuarios/<?= htmlspecialchars($current_photo) ?>" alt="Foto de Usuario" class="user-img" id="user-img" />
        <div id="user-dropdown" class="user-dropdown">
            <ul>
                <li><a href="editar-perfil.php">Editar Perfil</a></li>
                <li><a href="logout.php">Cerrar Sesión</a></li>
            </ul>
        </div>
    </div>

    <main>
        <section class="editar-perfil">
            <h2>Cambiar Foto de Perfil</h2>

            <div class="foto-actual">
                <h3>Foto actual:</h3>
                <img src="uploads/usuarios/<?= htmlspecialchars($current_photo) ?>" alt="Foto de Perfil" class="user-img" id="current-photo" />
            </div>

            <form id="foto-form" enctype="multipart/form-data">
                <label for="foto_perfil">Seleccionar nueva foto:</label>
                <input type="file" name="foto_perfil" id="foto_perfil" accept="image/*" />
                <button type="submit">Guardar Cambios</button>
                <br />
                <button type="button" id="cancelar">Cancelar</button>
            </form>

            <div id="message"></div>
        </section>
    </main>

    <script>
        // Toggle menú desplegable
        function toggleMenu() {
            const dropdown = document.getElementById('user-dropdown');
            dropdown.style.display = dropdown.style.display === 'block' ? 'none' : 'block';
        }

        // Abrir/cerrar menú al hacer click en la imagen
        document.getElementById('user-img').addEventListener('click', function(e) {
            e.stopPropagation(); // Evita que el click se propague y cierre inmediatamente el menú
            toggleMenu();
        });

        // Cerrar menú si se hace click fuera
        document.addEventListener('click', function(event) {
            const dropdown = document.getElementById('user-dropdown');
            const userImg = document.getElementById('user-img');
            if (dropdown.style.display === 'block' && 
                !dropdown.contains(event.target) && 
                event.target !== userImg) {
                dropdown.style.display = 'none';
            }
        });

        // AJAX para subir foto y mostrar mensajes + redirigir tras éxito
        $(document).ready(function() {
            $('#foto-form').on('submit', function(e) {
                e.preventDefault();

                var formData = new FormData(this);

                $.ajax({
                    url: 'editar-perfil.php',
                    type: 'POST',
                    data: formData,
                    contentType: false,
                    processData: false,
                    success: function(response) {
                        if (response === "success") {
                            $('#message').html('<p style="color:green;">Foto de perfil actualizada correctamente.</p>');
                            // Esperar 2 segundos y volver atrás
                            setTimeout(function() {
                                window.history.back();
                            }, 2000);
                        } else if (response === "error_move_file") {
                            $('#message').html('<p style="color:red;">Error moviendo el archivo.</p>');
                        } else if (response === "error_ext") {
                            $('#message').html('<p style="color:red;">Extensión no permitida. Usa jpg, jpeg, png o gif.</p>');
                        } else if (response === "error_db") {
                            $('#message').html('<p style="color:red;">Error al actualizar en la base de datos.</p>');
                        } else {
                            $('#message').html('<p style="color:red;">Hubo un error al actualizar la foto.</p>');
                        }
                    },
                    error: function() {
                        $('#message').html('<p style="color:red;">Hubo un error al procesar la solicitud.</p>');
                    }
                });
            });

            $('#cancelar').on('click', function() {
                window.history.back();
            });
        });
    </script>

</body>
</html>
