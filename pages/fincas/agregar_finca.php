<?php
session_start();

// Si el usuario no está logueado, redirigirlo a la página de login
if (!isset($_SESSION['user_id'])) {
    header('Location: ../../login.php');
    exit();
}

// Incluir el archivo de configuración de la base de datos
require_once '../../includes/db_config.php';

$page_title = "Agregar Nueva Finca";

// Procesar el formulario si se ha enviado
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $nombre_finca = $_POST['nombre_finca'] ?? '';

    // Validar que el nombre de la finca no esté vacío
    if (empty($nombre_finca)) {
        $_SESSION['message'] = "El nombre de la finca no puede estar vacío.";
        $_SESSION['message_type'] = "danger";
    } else {
        $conn = connectDB(); // Conectar a la base de datos

        // Preparar la consulta SQL para evitar inyecciones SQL
        $stmt = $conn->prepare("INSERT INTO fincas (nombre_finca) VALUES (?)");
        $stmt->bind_param("s", $nombre_finca); // "s" indica que es un string

        if ($stmt->execute()) {
            $_SESSION['message'] = "Finca '" . htmlspecialchars($nombre_finca) . "' agregada exitosamente.";
            $_SESSION['message_type'] = "success";
            // Redirigir a la lista de fincas para evitar reenvío de formulario
            header("Location: " . BASE_URL . "pages/fincas/listar_fincas.php");
            exit();
        } else {
            $_SESSION['message'] = "Error al agregar la finca: " . $stmt->error;
            $_SESSION['message_type'] = "danger";
        }

        $stmt->close();
        $conn->close();
    }
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
            Formulario de Nueva Finca
        </div>
        <div class="card-body">
            <form action="<?php echo htmlspecialchars($_SERVER['PHP_SELF']); ?>" method="POST">
                <div class="mb-3">
                    <label for="nombre_finca" class="form-label">Nombre de la Finca:</label>
                    <input type="text" class="form-control" id="nombre_finca" name="nombre_finca" required>
                </div>
                <button type="submit" class="btn btn-success"><i class="bi bi-save me-2"></i>Guardar Finca</button>
                <a href="<?php echo BASE_URL; ?>pages/fincas/listar_fincas.php" class="btn btn-secondary"><i class="bi bi-arrow-left-circle me-2"></i>Cancelar</a>
            </form>
        </div>
    </div>
</main>

<?php
require_once '../../includes/footer.php';
?>