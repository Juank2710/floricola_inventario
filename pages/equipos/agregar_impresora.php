<?php
session_start();
if (!isset($_SESSION['user_id'])) {
    header('Location: ../../login.php');
    exit();
}
require_once '../../includes/db_config.php';
$page_title = "Agregar Detalles de Impresora";

if (!isset($_GET['id_equipo']) || !is_numeric($_GET['id_equipo'])) {
    $_SESSION['message'] = "ID de equipo no válido.";
    $_SESSION['message_type'] = "danger";
    header("Location: listar_equipos.php");
    exit();
}
$id_equipo = intval($_GET['id_equipo']);

$conn = connectDB();

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $tipo_impresora = trim($_POST['tipo_impresora'] ?? '');
    $es_multifuncional = isset($_POST['es_multifuncional']) ? 1 : 0;
    $tiene_red = isset($_POST['tiene_red']) ? 1 : 0;
    $ip_red = trim($_POST['ip_red'] ?? '');
    $ubicacion_especifica = trim($_POST['ubicacion_especifica'] ?? '');
    $tipo_cartucho_toner = trim($_POST['tipo_cartucho_toner'] ?? '');

    $stmt = $conn->prepare("INSERT INTO equipos_impresoras (id_equipo, tipo_impresora, es_multifuncional, tiene_red, ip_red, ubicacion_especifica, tipo_cartucho_toner) VALUES (?, ?, ?, ?, ?, ?, ?)");
    $stmt->bind_param(
        "isissss",
        $id_equipo,
        $tipo_impresora,
        $es_multifuncional,
        $tiene_red,
        $ip_red,
        $ubicacion_especifica,
        $tipo_cartucho_toner
    );
    if ($stmt->execute()) {
        $_SESSION['message'] = "Detalles de impresora agregados correctamente.";
        $_SESSION['message_type'] = "success";
        header("Location: listar_equipos.php");
        exit();
    } else {
        $_SESSION['message'] = "Error al guardar los detalles: " . $stmt->error;
        $_SESSION['message_type'] = "danger";
    }
    $stmt->close();
}

require_once '../../includes/header.php';
require_once '../../includes/navbar.php';
?>

<main class="container mt-4 flex-grow-1">
    <h2><?php echo $page_title; ?></h2>
    <?php if (isset($_SESSION['message'])): ?>
        <div class="alert alert-<?php echo $_SESSION['message_type']; ?> alert-dismissible fade show" role="alert">
            <?php echo $_SESSION['message']; ?>
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
        <?php
        unset($_SESSION['message']);
        unset($_SESSION['message_type']);
        ?>
    <?php endif; ?>

    <div class="card">
        <div class="card-header">
            Formulario de Detalles de Impresora
        </div>
        <div class="card-body">
            <form action="" method="POST">
                <div class="mb-3">
                    <label for="tipo_impresora" class="form-label">Tipo de Impresora:</label>
                    <input type="text" class="form-control" id="tipo_impresora" name="tipo_impresora">
                </div>
                <div class="form-check mb-3">
                    <input class="form-check-input" type="checkbox" id="es_multifuncional" name="es_multifuncional">
                    <label class="form-check-label" for="es_multifuncional">¿Es multifuncional?</label>
                </div>
                <div class="form-check mb-3">
                    <input class="form-check-input" type="checkbox" id="tiene_red" name="tiene_red">
                    <label class="form-check-label" for="tiene_red">¿Tiene red?</label>
                </div>
                <div class="mb-3">
                    <label for="ip_red" class="form-label">IP de Red:</label>
                    <input type="text" class="form-control" id="ip_red" name="ip_red">
                </div>
                <div class="mb-3">
                    <label for="ubicacion_especifica" class="form-label">Ubicación Específica:</label>
                    <input type="text" class="form-control" id="ubicacion_especifica" name="ubicacion_especifica">
                </div>
                <div class="mb-3">
                    <label for="tipo_cartucho_toner" class="form-label">Tipo de Cartucho/Tóner:</label>
                    <input type="text" class="form-control" id="tipo_cartucho_toner" name="tipo_cartucho_toner">
                </div>
                <button type="submit" class="btn btn-success"><i class="bi bi-save me-2"></i>Guardar Detalles</button>
                <a href="listar_equipos.php" class="btn btn-secondary"><i class="bi bi-arrow-left-circle me-2"></i>Cancelar</a>
            </form>
        </div>
    </div>
</main>

<?php
require_once '../../includes/footer.php';
?>