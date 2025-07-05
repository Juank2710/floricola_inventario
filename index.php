<?php
// index.php
session_start();

// Si el usuario no está logueado, redirigirlo a la página de login
if (!isset($_SESSION['user_id'])) {
    header('Location: login.php');
    exit();
}

// Opcional: Define un título para esta página que se usará en el header.php
$page_title = "Dashboard Principal";

// Incluye el header (contiene la etiqueta <body> y el inicio del div principal)
require_once 'includes/header.php';
// Incluye la barra de navegación
require_once 'includes/navbar.php';
?>

<main class="container mt-4 flex-grow-1"> <div class="alert alert-success" role="alert">
        <h4 class="alert-heading">¡Bienvenido al Dashboard!</h4>
        <p>Hola, **<?php echo htmlspecialchars($_SESSION['username']); ?>** (Rol: <?php echo htmlspecialchars($_SESSION['user_role']); ?>).</p>
        <hr>
        <p class="mb-0">Aquí irán las opciones y resúmenes de tu sistema de control de equipos.</p>
    </div>

    <div class="row mt-4">
        <div class="col-md-4 mb-4">
            <div class="card text-white bg-primary mb-3">
                <div class="card-header">Equipos Activos</div>
                <div class="card-body">
                    <h5 class="card-title">150</h5>
                    <p class="card-text">Total de equipos en uso actualmente.</p>
                </div>
            </div>
        </div>
        <div class="col-md-4 mb-4">
            <div class="card text-white bg-warning mb-3">
                <div class="card-header">En Mantenimiento</div>
                <div class="card-body">
                    <h5 class="card-title">5</h5>
                    <p class="card-text">Equipos que requieren atención.</p>
                </div>
            </div>
        </div>
        <div class="col-md-4 mb-4">
            <div class="card text-white bg-info mb-3">
                <div class="card-header">Monitores Asignados</div>
                <div class="card-body">
                    <h5 class="card-title">80</h5>
                    <p class="card-text">Monitores actualmente en uso.</p>
                </div>
            </div>
        </div>
    </div>

    <div class="mt-4">
        <h4>Acciones Rápidas</h4>
        <div class="list-group">
            <a href="/floricola_inventario/pages/equipos/agregar_equipo.php" class="list-group-item list-group-item-action"><i class="bi bi-plus-circle me-2"></i>Agregar Nuevo Equipo</a>
            <a href="/floricola_inventario/pages/monitores/agregar_monitor.php" class="list-group-item list-group-item-action"><i class="bi bi-display me-2"></i>Agregar Nuevo Monitor</a>
            <a href="/floricola_inventario/pages/equipos/listar_equipos.php" class="list-group-item list-group-item-action"><i class="bi bi-list-ul me-2"></i>Ver Lista de Equipos</a>
            <a href="/floricola_inventario/pages/personas/listar_personas.php" class="list-group-item list-group-item-action"><i class="bi bi-people me-2"></i>Gestionar Personas</a>
        </div>
    </div>
</main> 
<?php
// Incluye el footer (contiene el cierre del div principal y </body> </html>)
require_once 'includes/footer.php';
?>