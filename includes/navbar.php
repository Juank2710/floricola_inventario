<?php
// includes/navbar.php
// Asegurarse de que BASE_URL esté definida (viene de db_config.php)
if (!defined('BASE_URL')) {
    // Esto debería ejecutarse solo si db_config.php no fue incluido previamente.
    // En un entorno normal, db_config.php ya habrá definido BASE_URL.
    $protocol = isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] === 'on' ? "https" : "http";
    $host = $_SERVER['HTTP_HOST'];
    $script_name = $_SERVER['SCRIPT_NAME'];
    $project_folder_name = 'floricola_inventario'; // Asegúrate de que este nombre sea exacto
    $pos = strpos($script_name, '/' . $project_folder_name . '/');
    if ($pos !== false) {
        define('BASE_URL', $protocol . "://" . $host . substr($script_name, 0, $pos) . '/' . $project_folder_name . '/');
    } else {
        define('BASE_URL', $protocol . "://" . $host . '/');
    }
}

$username = $_SESSION['username'] ?? 'Invitado';
$user_role = $_SESSION['user_role'] ?? '';
?>
<nav class="navbar navbar-expand-lg navbar-dark bg-dark">
    <div class="container-fluid">
        <a class="navbar-brand" href="<?php echo BASE_URL; ?>index.php">
            <i class="bi bi-gear-fill me-2"></i>Control de Equipos
        </a>
        <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav" aria-controls="navbarNav" aria-expanded="false" aria-label="Toggle navigation">
            <span class="navbar-toggler-icon"></span>
        </button>
        <div class="collapse navbar-collapse" id="navbarNav">
            <ul class="navbar-nav me-auto mb-2 mb-lg-0">
                <li class="nav-item">
                    <a class="nav-link active" aria-current="page" href="<?php echo BASE_URL; ?>index.php">Dashboard</a>
                </li>
                <li class="nav-item dropdown">
                    <a class="nav-link dropdown-toggle" href="#" id="navbarDropdownInventario" role="button" data-bs-toggle="dropdown" aria-expanded="false">
                        <i class="bi bi-boxes me-1"></i>Inventario
                    </a>
                    <ul class="dropdown-menu" aria-labelledby="navbarDropdownInventario">
                        <li><a class="dropdown-item" href="<?php echo BASE_URL; ?>pages/equipos/listar_equipos.php">Gestionar Equipos</a></li>
                        <li><a class="dropdown-item" href="<?php echo BASE_URL; ?>pages/monitores/listar_monitores.php">Gestionar Monitores</a></li>
                    </ul>
                </li>
                <li class="nav-item dropdown">
                    <a class="nav-link dropdown-toggle" href="#" id="navbarDropdownMaestros" role="button" data-bs-toggle="dropdown" aria-expanded="false">
                        <i class="bi bi-book me-1"></i>Catálogos
                    </a>
                    <ul class="dropdown-menu" aria-labelledby="navbarDropdownMaestros">
                        <li><a class="dropdown-item" href="<?php echo BASE_URL; ?>pages/personas/listar_personas.php">Personas</a></li>
                        <li><a class="dropdown-item" href="<?php echo BASE_URL; ?>pages/fincas/listar_fincas.php">Fincas</a></li>
                        <li><a class="dropdown-item" href="<?php echo BASE_URL; ?>pages/areas/listar_areas.php">Áreas</a></li>
                        <li><a class="dropdown-item" href="<?php echo BASE_URL; ?>pages/tipos_equipo/listar_tipos_equipo.php">Tipos de Equipo</a></li>
                        <li><hr class="dropdown-divider"></li>
                        <li><a class="dropdown-item" href="<?php echo BASE_URL; ?>pages/estados_equipo/listar_estados_equipo.php">Estados de Equipo</a></li>
                    </ul>
                </li>
                </ul>
            <ul class="navbar-nav ms-auto">
                <li class="nav-item dropdown">
                    <a class="nav-link dropdown-toggle" href="#" id="navbarDropdownUser" role="button" data-bs-toggle="dropdown" aria-expanded="false">
                        <i class="bi bi-person-circle me-1"></i><?php echo htmlspecialchars($username); ?> (<?php echo htmlspecialchars($user_role); ?>)
                    </a>
                    <ul class="dropdown-menu dropdown-menu-end" aria-labelledby="navbarDropdownUser">
                        <li><a class="dropdown-item" href="#">Mi Perfil</a></li>
                        <li><hr class="dropdown-divider"></li>
                        <li><a class="dropdown-item" href="<?php echo BASE_URL; ?>logout.php"><i class="bi bi-box-arrow-right me-1"></i>Cerrar Sesión</a></li>
                    </ul>
                </li>
            </ul>
        </div>
    </div>
</nav>