<?php
session_start();

// Si el usuario no está logueado, redirigirlo a la página de login
if (!isset($_SESSION['user_id'])) {
    header('Location: ../../login.php');
    exit();
}

// Incluir el archivo de configuración de la base de datos
require_once '../../includes/db_config.php';

$page_title = "Agregar Tipo de Equipo";

// Procesar el formulario si se ha enviado
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $tipo = trim($_POST['tipo'] ?? '');

    // Validar que el nombre del tipo no esté vacío
    if (empty($tipo)) {
        $_SESSION['message'] = "El nombre del tipo de equipo no puede estar vacío.";
        $_SESSION['message_type'] = "danger";
    } else {
        $conn = connectDB();

        // Verificar si ya existe el tipo
        $stmt = $conn->prepare("SELECT id FROM tipos_equipo WHERE tipo = ?");
        $stmt->bind_param("s", $tipo);
        $stmt->execute();
        $stmt->store_result();
        if ($stmt->num_rows > 0) {
            $_SESSION['message'] = "Ya existe un tipo de equipo con ese nombre.";
            $_SESSION['message_type'] = "danger";
        } else {
            $stmt->close();
            $stmt = $conn->prepare("INSERT INTO tipos_equipo (tipo) VALUES (?)");
            $stmt->bind_param("s", $tipo);
            if ($stmt->execute()) {
                $_SESSION['message'] = "Tipo de equipo '" . htmlspecialchars($tipo) . "' agregado exitosamente.";
                $_SESSION['message_type'] = "success";
                // Redirigir al listado para evitar reenvío de formulario
                header("Location: listar_tipos_equipo.php");
                exit();
            } else {
                $_SESSION['message'] = "Error al agregar el tipo de equipo: " . $stmt->error;
                $_SESSION['message_type'] = "danger";
            }
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
            Formulario de Nuevo Tipo de Equipo
        </div>
        <div class="card-body">
            <form action="<?php echo htmlspecialchars($_SERVER['PHP_SELF']); ?>" method="POST">
                <div class="mb-3">
                    <label for="tipo" class="form-label">Nombre del Tipo de Equipo:</label>
                    <input type="text" class="form-control" id="tipo" name="tipo" required>
                </div>
                <button type="submit" class="btn btn-success"><i class="bi bi-save me-2"></i>Guardar Tipo</button>
                <a href="listar_tipos_equipo.php" class="btn btn-secondary"><i class="bi bi-arrow-left-circle me-2"></i>Cancelar</a>
            </form>
        </div>
    </div>
</main>

<?php
require_once '../../includes/footer.php';
?>