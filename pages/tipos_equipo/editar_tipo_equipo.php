<?php
session_start();
if (!isset($_SESSION['user_id'])) {
    header('Location: ../../login.php');
    exit();
}
require_once '../../includes/db_config.php';

$page_title = "Editar Tipo de Equipo";

// Validar que venga el ID por GET
if (!isset($_GET['id']) || !is_numeric($_GET['id'])) {
    $_SESSION['message'] = "ID de tipo de equipo no válido.";
    $_SESSION['message_type'] = "danger";
    header("Location: listar_tipos_equipo.php");
    exit();
}

$id = intval($_GET['id']);
$conn = connectDB();

// Obtener el tipo actual
$stmt = $conn->prepare("SELECT tipo FROM tipos_equipo WHERE id = ?");
$stmt->bind_param("i", $id);
$stmt->execute();
$stmt->bind_result($tipo_actual);
if (!$stmt->fetch()) {
    $_SESSION['message'] = "Tipo de equipo no encontrado.";
    $_SESSION['message_type'] = "danger";
    $stmt->close();
    $conn->close();
    header("Location: listar_tipos_equipo.php");
    exit();
}
$stmt->close();

// Procesar el formulario si se ha enviado
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $nuevo_tipo = trim($_POST['tipo'] ?? '');

    if (empty($nuevo_tipo)) {
        $_SESSION['message'] = "El nombre del tipo de equipo no puede estar vacío.";
        $_SESSION['message_type'] = "danger";
    } else {
        // Verificar si ya existe otro tipo con ese nombre
        $stmt = $conn->prepare("SELECT id FROM tipos_equipo WHERE tipo = ? AND id != ?");
        $stmt->bind_param("si", $nuevo_tipo, $id);
        $stmt->execute();
        $stmt->store_result();
        if ($stmt->num_rows > 0) {
            $_SESSION['message'] = "Ya existe otro tipo de equipo con ese nombre.";
            $_SESSION['message_type'] = "danger";
        } else {
            $stmt->close();
            $stmt = $conn->prepare("UPDATE tipos_equipo SET tipo = ? WHERE id = ?");
            $stmt->bind_param("si", $nuevo_tipo, $id);
            if ($stmt->execute()) {
                $_SESSION['message'] = "Tipo de equipo actualizado correctamente.";
                $_SESSION['message_type'] = "success";
                $stmt->close();
                $conn->close();
                header("Location: listar_tipos_equipo.php");
                exit();
            } else {
                $_SESSION['message'] = "Error al actualizar el tipo de equipo: " . $stmt->error;
                $_SESSION['message_type'] = "danger";
            }
        }
        $stmt->close();
    }
    $conn->close();
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
            Editar Tipo de Equipo
        </div>
        <div class="card-body">
            <form action="<?php echo htmlspecialchars($_SERVER['PHP_SELF']) . '?id=' . $id; ?>" method="POST">
                <div class="mb-3">
                    <label for="tipo" class="form-label">Nombre del Tipo de Equipo:</label>
                    <input type="text" class="form-control" id="tipo" name="tipo" value="<?php echo htmlspecialchars($tipo_actual); ?>" required>
                </div>
                <button type="submit" class="btn btn-success"><i class="bi bi-save me-2"></i>Guardar Cambios</button>
                <a href="listar_tipos_equipo.php" class="btn btn-secondary"><i class="bi bi-arrow-left-circle me-2"></i>Cancelar</a>
            </form>
        </div>
    </div>
</main>

<?php
require_once '../../includes/footer.php';
?>