<?php 

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

global $conn; 
?>

<link rel="stylesheet" href="css/sites.css">

<header>
    <div class="logo">
        <a href="index.php"><img src="img/logo.png" alt="Logo"></a>
    </div>
    <nav>
        <ul>
            <li><a href="index.php">Inicio</a></li>
            <li><a href="reservas.php">Instalaciones Deportivas</a></li>
            <li><a href="eventos.php">Eventos Públicos</a></li>
        </ul>
    </nav>
    <div class="login-cart">
        <?php if (isset($_SESSION['user_id']) && isset($conn)): ?>
            <?php 
                $user_id = $_SESSION['user_id'];
                $stmt = $conn->prepare("SELECT nombre, rol, foto_perfil FROM Usuarios WHERE id = ?");
                $stmt->bind_param("i", $user_id);
                $stmt->execute();
                $resultUser = $stmt->get_result();
                $user = $resultUser->fetch_assoc();
                $foto_perfil = isset($user['foto_perfil']) && $user['foto_perfil'] !== '' ? $user['foto_perfil'] : 'default-avatar.jpg';
                $nombre_usuario = htmlspecialchars($user['nombre']);
                $rol_usuario = htmlspecialchars($user['rol']);
            ?>
            <div class="user-menu">
                <img src="uploads/usuarios/<?= $foto_perfil ?>" alt="Foto de Usuario" class="user-img" onclick="toggleMenu()">
                <span class="user-name" onclick="toggleMenu()"><?= $nombre_usuario ?></span>
                <div id="user-dropdown" class="user-dropdown" style="display: none;">
                    <ul>
                        <li><strong>Rol: <?= ucfirst($rol_usuario) ?></strong></li>
                        <li><a href="editar-perfil.php">Editar Perfil</a></li>
                        <?php if ($rol_usuario === 'admin'): ?>
                            <li><a href="insertar_instalaciones.php">Gestionar Instalaciones</a></li>
                            <li><a href="nuevo-evento.php">Gestionar Eventos</a></li>
                        <?php endif; ?>
                        <li><a href="logout.php">Cerrar Sesión</a></li>
                    </ul>
                </div>
            </div>
        <?php else: ?>
            <a href="login.php"><img src="img/login.png" alt="Login"></a>
        <?php endif; ?>
        <a href="carrito-reservas.php">
            <img src="img/carrito.png" alt="Carrito" class="carrito-img">
        </a>
    </div>
</header>

<script>
    function toggleMenu() {
        const dropdown = document.getElementById("user-dropdown");
        if(dropdown.style.display === "block"){
            dropdown.style.display = "none";
        } else {
            dropdown.style.display = "block";
        }
    }

    // Opcional: cerrar el menú si haces clic fuera
    window.onclick = function(event) {
        const dropdown = document.getElementById("user-dropdown");
        const userImg = document.querySelector('.user-img');
        const userName = document.querySelector('.user-name');
        if (!userImg.contains(event.target) && !userName.contains(event.target) && !dropdown.contains(event.target)) {
            dropdown.style.display = "none";
        }
    }
</script>

<style>
    .user-menu {
        position: relative;
        display: inline-block;
        cursor: pointer;
        user-select: none;
        color: #333;
        font-weight: 600;
        margin-right: 15px;
        vertical-align: middle;
    }
    .user-img {
        width: 40px;
        height: 40px;
        border-radius: 50%;
        vertical-align: middle;
        margin-right: 8px;
        object-fit: cover;
        border: 2px solid #007BFF;
    }
    .user-name {
        vertical-align: middle;
    }
    .user-dropdown {
        position: absolute;
        right: 0;
        background-color: white;
        min-width: 160px;
        box-shadow: 0 4px 8px rgba(0,0,0,0.2);
        border-radius: 6px;
        z-index: 100;
        margin-top: 5px;
    }
    .user-dropdown ul {
        list-style: none;
        padding: 10px 0;
        margin: 0;
    }
    .user-dropdown ul li {
        padding: 8px 20px;
        border-bottom: 1px solid #eee;
    }
    .user-dropdown ul li:last-child {
        border-bottom: none;
    }
    .user-dropdown ul li strong {
        font-weight: bold;
        color: #555;
        cursor: default;
    }
    .user-dropdown ul li a {
        color: #007BFF;
        text-decoration: none;
        display: block;
    }
    .user-dropdown ul li a:hover {
        background-color: #f0f8ff;
    }
</style>
