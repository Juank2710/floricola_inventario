<?php
session_start();

// Si el usuario no está logueado, redirigirlo a la página de login
if (!isset($_SESSION['user_id'])) {
    header('Location: ../../login.php');
    exit();
}

// Incluir el archivo de configuración de la base de datos
require_once '../../includes/db_config.php';

$page_title = "Editar Finca";
$conn = connectDB(); // Conectar a la base de datos

$finca_id = null;
$nombre_finca_actual = '';

// Lógica para cargar los datos de la finca si se recibe un ID
if (isset($_GET['id']) && is_numeric($_GET['id'])) {
    $finca_id = $_GET['id'];

    // Preparar y ejecutar consulta para obtener la finca
    $stmt = $conn->prepare("SELECT id, nombre_finca FROM fincas WHERE id = ?");
    $stmt->bind_param("i", $finca_id); // "i" para entero
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows == 1) {
        $finca = $result->fetch_assoc();
        $nombre_finca_actual = $finca['nombre_finca'];
    } else {
        $_SESSION['message'] = "Finca no encontrada.";
        $_SESSION['message_type'] = "danger";
        header("Location: " . BASE_URL . "pages/fincas/listar_fincas.php");
        exit();
    }
    $stmt->close();
} else if (isset($_POST['id']) && is_numeric($_POST['id'])) {
    // Si el formulario se envió, procesar la actualización
    $finca_id = $_POST['id'];
    $nombre_finca_nuevo = $_POST['nombre_finca'] ?? '';

    if (empty($nombre_finca_nuevo)) {
        $_SESSION['message'] = "El nombre de la finca no puede estar vacío.";
        $_SESSION['message_type'] = "danger";
        // Volver a cargar el nombre actual si hubo un error de validación
        $stmt = $conn->prepare("SELECT nombre_finca FROM fincas WHERE id = ?");
        $stmt->bind_param("i", $finca_id);
        $stmt->execute();
        $result = $stmt->get_result();
        if ($result->num_rows == 1) {
            $finca = $result->fetch_assoc();
            $nombre_finca_actual = $finca['nombre_finca'];
        }
        $stmt->close();

    } else {
        // Preparar la consulta SQL para actualizar
        $stmt = $conn->prepare("UPDATE fincas SET nombre_finca = ? WHERE id = ?");
        $stmt->bind_param("si", $nombre_finca_nuevo, $finca_id); // "s" para string, "i" para entero

        if ($stmt->execute()) {
            $_SESSION['message'] = "Finca '" . htmlspecialchars($nombre_finca_nuevo) . "' actualizada exitosamente.";
            $_SESSION['message_type'] = "success";
            header("Location: " . BASE_URL . "pages/fincas/listar_fincas.php");
            exit();
        } else {
            $_SESSION['message'] = "Error al actualizar la finca: " . $stmt->error;
            $_SESSION['message_type'] = "danger";
            // Mantener el nombre que se intentó guardar para que el usuario no lo pierda
            $nombre_finca_actual = $nombre_finca_nuevo;
        }
        $stmt->close();
    }
} else {
    $_SESSION['message'] = "ID de finca no proporcionado.";
    $_SESSION['message_type'] = "danger";
    header("Location: " . BASE_URL . "pages/fincas/listar_fincas.php");
    exit();
}

// Incluye el header y la barra de navegación
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
            Formulario de Edición de Finca
        </div>
        <div class="card-body">
            <form action="<?php echo htmlspecialchars($_SERVER['PHP_SELF']); ?>" method="POST">
                <input type="hidden" name="id" value="<?php echo htmlspecialchars($finca_id); ?>">
                <div class="mb-3">
                    <label for="nombre_finca" class="form-label">Nombre de la Finca:</label>
                    <input type="text" class="form-control" id="nombre_finca" name="nombre_finca" value="<?php echo htmlspecialchars($nombre_finca_actual); ?>" required>
                </div>
                <button type="submit" class="btn btn-success"><i class="bi bi-arrow-repeat me-2"></i>Actualizar Finca</button>
                <a href="<?php echo BASE_URL; ?>pages/fincas/listar_fincas.php" class="btn btn-secondary"><i class="bi bi-arrow-left-circle me-2"></i>Cancelar</a>
            </form>
        </div>
    </div>
</main>

<?php
$conn->close(); // Cerrar la conexión a la base de datos
require_once '../../includes/footer.php';
?>