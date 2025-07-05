<?php
session_start();
if (!isset($_SESSION['user_id'])) {
    header('Location: ../../login.php');
    exit();
}
require_once '../../includes/db_config.php';
$page_title = "Agregar Nueva Área";

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $nombre_area = $_POST['nombre_area'] ?? '';

    if (empty($nombre_area)) {
        $_SESSION['message'] = "El nombre del área no puede estar vacío.";
        $_SESSION['message_type'] = "danger";
    } else {
        $conn = connectDB();
        $stmt = $conn->prepare("INSERT INTO areas (nombre_area) VALUES (?)");
        $stmt->bind_param("s", $nombre_area);

        if ($stmt->execute()) {
            $_SESSION['message'] = "Área '" . htmlspecialchars($nombre_area) . "' agregada exitosamente.";
            $_SESSION['message_type'] = "success";
            header("Location: " . BASE_URL . "pages/areas/listar_areas.php");
            exit();
        } else {
            $_SESSION['message'] = "Error al agregar el área: " . $stmt->error;
            $_SESSION['message_type'] = "danger";
        }
        $stmt->close();
        $conn->close();
    }
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
            Formulario de Nueva Área
        </div>
        <div class="card-body">
            <form action="<?php echo htmlspecialchars($_SERVER['PHP_SELF']); ?>" method="POST">
                <div class="mb-3">
                    <label for="nombre_area" class="form-label">Nombre del Área:</label>
                    <input type="text" class="form-control" id="nombre_area" name="nombre_area" required>
                </div>
                <button type="submit" class="btn btn-success"><i class="bi bi-save me-2"></i>Guardar Área</button>
                <a href="<?php echo BASE_URL; ?>pages/areas/listar_areas.php" class="btn btn-secondary"><i class="bi bi-arrow-left-circle me-2"></i>Cancelar</a>
            </form>
        </div>
    </div>
</main>

<?php
require_once '../../includes/footer.php';
?>