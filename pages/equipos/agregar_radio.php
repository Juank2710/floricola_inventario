<?php
session_start();
if (!isset($_SESSION['user_id'])) {
    header('Location: ../../login.php');
    exit();
}
require_once '../../includes/db_config.php';
$page_title = "Agregar Detalles de Radio";

if (!isset($_GET['id_equipo']) || !is_numeric($_GET['id_equipo'])) {
    $_SESSION['message'] = "ID de equipo no válido.";
    $_SESSION['message_type'] = "danger";
    header("Location: listar_equipos.php");
    exit();
}
$id_equipo = intval($_GET['id_equipo']);

$conn = connectDB();

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $cantidad_frecuencias = !empty($_POST['cantidad_frecuencias']) ? intval($_POST['cantidad_frecuencias']) : null;
    $incluye_base_cargador = isset($_POST['incluye_base_cargador']) ? 1 : 0;

    $stmt = $conn->prepare("INSERT INTO equipos_radios (id_equipo, cantidad_frecuencias, incluye_base_cargador) VALUES (?, ?, ?)");
    $stmt->bind_param(
        "iii",
        $id_equipo,
        $cantidad_frecuencias,
        $incluye_base_cargador
    );
    if ($stmt->execute()) {
        $_SESSION['message'] = "Detalles de radio agregados correctamente.";
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
            Formulario de Detalles de Radio
        </div>
        <div class="card-body">
            <form action="" method="POST">
                <div class="mb-3">
                    <label for="cantidad_frecuencias" class="form-label">Cantidad de Frecuencias:</label>
                    <input type="number" class="form-control" id="cantidad_frecuencias" name="cantidad_frecuencias">
                </div>
                <div class="form-check mb-3">
                    <input class="form-check-input" type="checkbox" id="incluye_base_cargador" name="incluye_base_cargador">
                    <label class="form-check-label" for="incluye_base_cargador">¿Incluye base/cargador?</label>
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