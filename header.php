<header>
    <div class="logo">
        <a href="index.php"><img src="img/logo.png" alt="Logo"></a>
    </div>
    <nav>
        <ul>
            <li><a href="index.php">Inicio</a></li>
            <li><a href="reservas.html">Instalaciones Deportivas</a></li>
            <li><a href="eventos.html">Eventos Públicos</a></li>
        </ul>
    </nav>
    <div class="login-cart">
        <?php if (isset($_SESSION['user_id'])): ?>
            <!-- Si el usuario ha iniciado sesión, mostrar su foto y menú desplegable -->
            <div class="user-menu">
                <?php 
                    // Ya se incluye en algún otro lugar (ej: config.php)
                    include 'config.php'; 

                    $user_id = $_SESSION['user_id'];
                    // Consulta a la base de datos para obtener la foto de perfil
                    $stmt = $conn->prepare("SELECT foto_perfil FROM Usuarios WHERE id = ?");
                    $stmt->bind_param("i", $user_id);
                    $stmt->execute();
                    $result = $stmt->get_result();
                    $user = $result->fetch_assoc();
                    $foto_perfil = isset($user['foto_perfil']) ? $user['foto_perfil'] : 'default-avatar.jpg'; // Imagen por defecto si no tiene foto
                ?>
                <!-- Mostrar la foto del usuario -->
                <img src="uploads/usuarios/<?= $foto_perfil ?>" alt="Foto de Usuario" class="user-img" onclick="toggleMenu()">
                <div id="user-dropdown" class="user-dropdown">
                    <ul>
                        <li><a href="editar-perfil.php">Editar Perfil</a></li>
                        <li><a href="logout.php">Cerrar Sesión</a></li>
                    </ul>
                </div>
            </div>
        <?php else: ?>
            <!-- Si no ha iniciado sesión, mostrar el enlace de login -->
            <a href="login.php"><img src="img/login.png" alt="Login"></a>
        <?php endif; ?>
        <a href="carrito.php"><img src="img/carrito.png" alt="Carrito"></a>
    </div>
</header>

<!-- JavaScript para mostrar el menú desplegable -->
<script>
    function toggleMenu() {
        var dropdown = document.getElementById("user-dropdown");
        dropdown.style.display = (dropdown.style.display === "block") ? "none" : "block";
    }
</script>

<style>
    /* Estilo para el menú desplegable */
    .user-menu {
        position: relative;
    }
    
    .user-img {
        width: 40px;
        height: 40px;
        border-radius: 50%;
        cursor: pointer;
    }
    
    .user-dropdown {
        display: none;
        position: absolute;
        top: 50px;
        right: 0;
        background-color: white;
        box-shadow: 0px 8px 16px rgba(0, 0, 0, 0.2);
        min-width: 160px;
        z-index: 1;
    }

    .user-dropdown ul {
        list-style-type: none;
        padding: 0;
        margin: 0;
    }

    .user-dropdown li {
        padding: 8px 16px;
    }

    .user-dropdown li a {
        text-decoration: none;
        color: black;
        display: block;
    }

    .user-dropdown li a:hover {
        background-color: #ddd;
    }
</style>
