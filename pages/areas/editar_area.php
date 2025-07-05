<?php
session_start();
if (!isset($_SESSION['user_id'])) {
    header('Location: ../../login.php');
    exit();
}
require_once '../../includes/db_config.php';
$page_title = "Editar Área";
$conn = connectDB();

$area_id = null;
$nombre_area_actual = '';

if (isset($_GET['id']) && is_numeric($_GET['id'])) {
    $area_id = $_GET['id'];
    $stmt = $conn->prepare("SELECT id, nombre_area FROM areas WHERE id = ?");
    $stmt->bind_param("i", $area_id);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows == 1) {
        $area = $result->fetch_assoc();
        $nombre_area_actual = $area['nombre_area'];
    } else {
        $_SESSION['message'] = "Área no encontrada.";
        $_SESSION['message_type'] = "danger";
        header("Location: " . BASE_URL . "pages/areas/listar_areas.php");
        exit();
    }
    $stmt->close();
} else if (isset($_POST['id']) && is_numeric($_POST['id'])) {
    $area_id = $_POST['id'];
    $nombre_area_nuevo = $_POST['nombre_area'] ?? '';

    if (empty($nombre_area_nuevo)) {
        $_SESSION['message'] = "El nombre del área no puede estar vacío.";
        $_SESSION['message_type'] = "danger";
        $stmt = $conn->prepare("SELECT nombre_area FROM areas WHERE id = ?");
        $stmt->bind_param("i", $area_id);
        $stmt->execute();
        $result = $stmt->get_result();
        if ($result->num_rows == 1) {
            $area = $result->fetch_assoc();
            $nombre_area_actual = $area['nombre_area'];
        }
        $stmt->close();

    } else {
        $stmt = $conn->prepare("UPDATE areas SET nombre_area = ? WHERE id = ?");
        $stmt->bind_param("si", $nombre_area_nuevo, $area_id);

        if ($stmt->execute()) {
            $_SESSION['message'] = "Área '" . htmlspecialchars($nombre_area_nuevo) . "' actualizada exitosamente.";
            $_SESSION['message_type'] = "success";
            header("Location: " . BASE_URL . "pages/areas/listar_areas.php");
            exit();
        } else {
            $_SESSION['message'] = "Error al actualizar el área: " . $stmt->error;
            $_SESSION['message_type'] = "danger";
            $nombre_area_actual = $nombre_area_nuevo;
        }
        $stmt->close();
    }
} else {
    $_SESSION['message'] = "ID de área no proporcionado.";
    $_SESSION['message_type'] = "danger";
    header("Location: " . BASE_URL . "pages/areas/listar_areas.php");
    exit();
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
            Formulario de Edición de Área
        </div>
        <div class="card-body">
            <form action="<?php echo htmlspecialchars($_SERVER['PHP_SELF']); ?>" method="POST">
                <input type="hidden" name="id" value="<?php echo htmlspecialchars($area_id); ?>">
                <div class="mb-3">
                    <label for="nombre_area" class="form-label">Nombre del Área:</label>
                    <input type="text" class="form-control" id="nombre_area" name="nombre_area" value="<?php echo htmlspecialchars($nombre_area_actual); ?>" required>
                </div>
                <button type="submit" class="btn btn-success"><i class="bi bi-arrow-repeat me-2"></i>Actualizar Área</button>
                <a href="<?php echo BASE_URL; ?>pages/areas/listar_areas.php" class="btn btn-secondary"><i class="bi bi-arrow-left-circle me-2"></i>Cancelar</a>
            </form>
        </div>
    </div>
</main>

<?php
$conn->close();
require_once '../../includes/footer.php';
?>