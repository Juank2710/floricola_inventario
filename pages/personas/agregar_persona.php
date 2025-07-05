<?php
session_start();
if (!isset($_SESSION['user_id'])) {
    header('Location: ../../login.php');
    exit();
}
require_once '../../includes/db_config.php';
$page_title = "Agregar Nueva Persona";
$conn = connectDB();

// Obtener datos para los selects de Fincas y Áreas
$fincas = $conn->query("SELECT id, nombre_finca FROM fincas ORDER BY nombre_finca ASC");
$areas = $conn->query("SELECT id, nombre_area FROM areas ORDER BY nombre_area ASC");

// Variables para mantener los valores del formulario en caso de error
$cedula = '';
$nombres = '';
$apellidos = '';
$telefono = '';
$email = '';
$cargo = '';
$id_area = ''; // Nuevo
$id_finca_trabajo = ''; // Nuevo


if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $cedula = $_POST['cedula'] ?? '';
    $nombres = $_POST['nombres'] ?? '';
    $apellidos = $_POST['apellidos'] ?? '';
    $telefono = $_POST['telefono'] ?? '';
    $email = $_POST['email'] ?? '';
    $cargo = $_POST['cargo'] ?? '';
    $id_area = $_POST['id_area'] ?? ''; // Nuevo
    $id_finca_trabajo = $_POST['id_finca_trabajo'] ?? ''; // Nuevo

    // Validación básica: ahora incluye los nuevos campos
    if (empty($nombres) || empty($apellidos) || empty($cedula) || empty($id_area) || empty($id_finca_trabajo)) {
        $_SESSION['message'] = "Cédula, Nombres, Apellidos, Área y Finca de Trabajo son campos obligatorios.";
        $_SESSION['message_type'] = "danger";
    } else {
        // Verificar si la cédula o el email ya existen (si el email no está vacío)
        $exists = false;
        $stmt_check = $conn->prepare("SELECT COUNT(*) FROM personas WHERE cedula = ? OR (email = ? AND email != '')");
        $stmt_check->bind_param("ss", $cedula, $email);
        $stmt_check->execute();
        $stmt_check->bind_result($count);
        $stmt_check->fetch();
        $stmt_check->close();

        if ($count > 0) {
            $_SESSION['message'] = "La cédula o el email ya existen en el sistema.";
            $_SESSION['message_type'] = "danger";
        } else {
            // Modificar la sentencia INSERT para incluir id_area y id_finca_trabajo
            $stmt = $conn->prepare("INSERT INTO personas (cedula, nombres, apellidos, telefono, email, cargo, id_area, id_finca_trabajo) VALUES (?, ?, ?, ?, ?, ?, ?, ?)");
            $stmt->bind_param("ssssssii", $cedula, $nombres, $apellidos, $telefono, $email, $cargo, $id_area, $id_finca_trabajo); // 'ii' para los INTs

            if ($stmt->execute()) {
                $_SESSION['message'] = "Persona '" . htmlspecialchars($nombres) . " " . htmlspecialchars($apellidos) . "' agregada exitosamente.";
                $_SESSION['message_type'] = "success";
                header("Location: " . BASE_URL . "pages/personas/listar_personas.php");
                exit();
            } else {
                $_SESSION['message'] = "Error al agregar la persona: " . $stmt->error;
                $_SESSION['message_type'] = "danger";
            }
            $stmt->close();
        }
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
            Formulario de Nueva Persona
        </div>
        <div class="card-body">
            <form action="<?php echo htmlspecialchars($_SERVER['PHP_SELF']); ?>" method="POST">
                <div class="mb-3">
                    <label for="cedula" class="form-label">Cédula:</label>
                    <input type="text" class="form-control" id="cedula" name="cedula" value="<?php echo htmlspecialchars($cedula); ?>" required>
                </div>
                <div class="mb-3">
                    <label for="nombres" class="form-label">Nombres:</label>
                    <input type="text" class="form-control" id="nombres" name="nombres" value="<?php echo htmlspecialchars($nombres); ?>" required>
                </div>
                <div class="mb-3">
                    <label for="apellidos" class="form-label">Apellidos:</label>
                    <input type="text" class="form-control" id="apellidos" name="apellidos" value="<?php echo htmlspecialchars($apellidos); ?>" required>
                </div>
                <div class="mb-3">
                    <label for="telefono" class="form-label">Teléfono:</label>
                    <input type="tel" class="form-control" id="telefono" name="telefono" value="<?php echo htmlspecialchars($telefono); ?>">
                </div>
                <div class="mb-3">
                    <label for="email" class="form-label">Email:</label>
                    <input type="email" class="form-control" id="email" name="email" value="<?php echo htmlspecialchars($email); ?>">
                </div>
                <div class="mb-3">
                    <label for="cargo" class="form-label">Cargo:</label>
                    <input type="text" class="form-control" id="cargo" name="cargo" value="<?php echo htmlspecialchars($cargo); ?>">
                </div>

                <div class="mb-3">
                    <label for="id_area" class="form-label">Área:</label>
                    <select class="form-select" id="id_area" name="id_area" required>
                        <option value="">Seleccione un área</option>
                        <?php
                        $areas->data_seek(0); // Asegura que el puntero esté al inicio
                        while ($row = $areas->fetch_assoc()):
                            $selected = ($id_area == $row['id']) ? 'selected' : '';
                            echo "<option value='" . htmlspecialchars($row['id']) . "' " . $selected . ">" . htmlspecialchars($row['nombre_area']) . "</option>";
                        endwhile;
                        ?>
                    </select>
                </div>
                <div class="mb-3">
                    <label for="id_finca_trabajo" class="form-label">Finca de Trabajo:</label>
                    <select class="form-select" id="id_finca_trabajo" name="id_finca_trabajo" required>
                        <option value="">Seleccione una finca</option>
                        <?php
                        $fincas->data_seek(0); // Asegura que el puntero esté al inicio
                        while ($row = $fincas->fetch_assoc()):
                            $selected = ($id_finca_trabajo == $row['id']) ? 'selected' : '';
                            echo "<option value='" . htmlspecialchars($row['id']) . "' " . $selected . ">" . htmlspecialchars($row['nombre_finca']) . "</option>";
                        endwhile;
                        ?>
                    </select>
                </div>

                <button type="submit" class="btn btn-success"><i class="bi bi-save me-2"></i>Guardar Persona</button>
                <a href="<?php echo BASE_URL; ?>pages/personas/listar_personas.php" class="btn btn-secondary"><i class="bi bi-arrow-left-circle me-2"></i>Cancelar</a>
            </form>
        </div>
    </div>
</main>

<?php
$conn->close(); // Cerrar la conexión aquí
require_once '../../includes/footer.php';
?>