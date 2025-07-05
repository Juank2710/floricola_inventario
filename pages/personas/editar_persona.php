<?php
session_start();
if (!isset($_SESSION['user_id'])) {
    header('Location: ../../login.php');
    exit();
}
require_once '../../includes/db_config.php';
$page_title = "Editar Persona";
$conn = connectDB();

// Obtener datos para los selects de Fincas y Áreas
$fincas = $conn->query("SELECT id, nombre_finca FROM fincas ORDER BY nombre_finca ASC");
$areas = $conn->query("SELECT id, nombre_area FROM areas ORDER BY nombre_area ASC");


$persona_id = null;
$persona_data = [
    'cedula' => '',
    'nombres' => '',
    'apellidos' => '',
    'telefono' => '',
    'email' => '',
    'cargo' => '',
    'id_area' => '', // Nuevo
    'id_finca_trabajo' => '' // Nuevo
];

if (isset($_GET['id']) && is_numeric($_GET['id'])) {
    $persona_id = $_GET['id'];
    // Modificar la sentencia SELECT para incluir id_area y id_finca_trabajo
    $stmt = $conn->prepare("SELECT id, cedula, nombres, apellidos, telefono, email, cargo, id_area, id_finca_trabajo FROM personas WHERE id = ?");
    $stmt->bind_param("i", $persona_id);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows == 1) {
        $persona_data = $result->fetch_assoc();
    } else {
        $_SESSION['message'] = "Persona no encontrada.";
        $_SESSION['message_type'] = "danger";
        header("Location: " . BASE_URL . "pages/personas/listar_personas.php");
        exit();
    }
    $stmt->close();
} else if (isset($_POST['id']) && is_numeric($_POST['id'])) {
    $persona_id = $_POST['id'];
    $cedula = $_POST['cedula'] ?? '';
    $nombres = $_POST['nombres'] ?? '';
    $apellidos = $_POST['apellidos'] ?? '';
    $telefono = $_POST['telefono'] ?? '';
    $email = $_POST['email'] ?? '';
    $cargo = $_POST['cargo'] ?? '';
    $id_area = $_POST['id_area'] ?? ''; // Nuevo
    $id_finca_trabajo = $_POST['id_finca_trabajo'] ?? ''; // Nuevo


    // Re-fill form with submitted data in case of validation error
    $persona_data = [
        'id' => $persona_id,
        'cedula' => $cedula,
        'nombres' => $nombres,
        'apellidos' => $apellidos,
        'telefono' => $telefono,
        'email' => $email,
        'cargo' => $cargo,
        'id_area' => $id_area, // Nuevo
        'id_finca_trabajo' => $id_finca_trabajo // Nuevo
    ];

    // Validaciones: ahora incluye los nuevos campos
    if (empty($nombres) || empty($apellidos) || empty($cedula) || empty($id_area) || empty($id_finca_trabajo)) {
        $_SESSION['message'] = "Cédula, Nombres, Apellidos, Área y Finca de Trabajo son campos obligatorios.";
        $_SESSION['message_type'] = "danger";
    } else {
        // Check for duplicate cedula or email (excluding the current person's own values)
        $exists = false;
        $stmt_check = $conn->prepare("SELECT COUNT(*) FROM personas WHERE (cedula = ? AND id != ?) OR (email = ? AND email != '' AND id != ?)");
        $stmt_check->bind_param("sisi", $cedula, $persona_id, $email, $persona_id);
        $stmt_check->execute();
        $stmt_check->bind_result($count);
        $stmt_check->fetch();
        $stmt_check->close();

        if ($count > 0) {
            $_SESSION['message'] = "La cédula o el email ya existen para otra persona en el sistema.";
            $_SESSION['message_type'] = "danger";
        } else {
            // Modificar la sentencia UPDATE para incluir id_area y id_finca_trabajo
            $stmt = $conn->prepare("UPDATE personas SET cedula = ?, nombres = ?, apellidos = ?, telefono = ?, email = ?, cargo = ?, id_area = ?, id_finca_trabajo = ? WHERE id = ?");
            $stmt->bind_param("ssssssiii", $cedula, $nombres, $apellidos, $telefono, $email, $cargo, $id_area, $id_finca_trabajo, $persona_id); // 'iii' para los INTs y el ID

            if ($stmt->execute()) {
                $_SESSION['message'] = "Persona '" . htmlspecialchars($nombres) . " " . htmlspecialchars($apellidos) . "' actualizada exitosamente.";
                $_SESSION['message_type'] = "success";
                header("Location: " . BASE_URL . "pages/personas/listar_personas.php");
                exit();
            } else {
                $_SESSION['message'] = "Error al actualizar la persona: " . $stmt->error;
                $_SESSION['message_type'] = "danger";
            }
            $stmt->close();
        }
    }
} else {
    $_SESSION['message'] = "ID de persona no proporcionado.";
    $_SESSION['message_type'] = "danger";
    header("Location: " . BASE_URL . "pages/personas/listar_personas.php");
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
            Formulario de Edición de Persona
        </div>
        <div class="card-body">
            <form action="<?php echo htmlspecialchars($_SERVER['PHP_SELF']); ?>" method="POST">
                <input type="hidden" name="id" value="<?php echo htmlspecialchars($persona_data['id']); ?>">
                <div class="mb-3">
                    <label for="cedula" class="form-label">Cédula:</label>
                    <input type="text" class="form-control" id="cedula" name="cedula" value="<?php echo htmlspecialchars($persona_data['cedula']); ?>" required>
                </div>
                <div class="mb-3">
                    <label for="nombres" class="form-label">Nombres:</label>
                    <input type="text" class="form-control" id="nombres" name="nombres" value="<?php echo htmlspecialchars($persona_data['nombres']); ?>" required>
                </div>
                <div class="mb-3">
                    <label for="apellidos" class="form-label">Apellidos:</label>
                    <input type="text" class="form-control" id="apellidos" name="apellidos" value="<?php echo htmlspecialchars($persona_data['apellidos']); ?>" required>
                </div>
                <div class="mb-3">
                    <label for="telefono" class="form-label">Teléfono:</label>
                    <input type="tel" class="form-control" id="telefono" name="telefono" value="<?php echo htmlspecialchars($persona_data['telefono']); ?>">
                </div>
                <div class="mb-3">
                    <label for="email" class="form-label">Email:</label>
                    <input type="email" class="form-control" id="email" name="email" value="<?php echo htmlspecialchars($persona_data['email']); ?>">
                </div>
                <div class="mb-3">
                    <label for="cargo" class="form-label">Cargo:</label>
                    <input type="text" class="form-control" id="cargo" name="cargo" value="<?php echo htmlspecialchars($persona_data['cargo']); ?>">
                </div>

                <div class="mb-3">
                    <label for="id_area" class="form-label">Área:</label>
                    <select class="form-select" id="id_area" name="id_area" required>
                        <option value="">Seleccione un área</option>
                        <?php
                        $areas->data_seek(0);
                        while ($row = $areas->fetch_assoc()):
                            $selected = ($persona_data['id_area'] == $row['id']) ? 'selected' : '';
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
                        $fincas->data_seek(0);
                        while ($row = $fincas->fetch_assoc()):
                            $selected = ($persona_data['id_finca_trabajo'] == $row['id']) ? 'selected' : '';
                            echo "<option value='" . htmlspecialchars($row['id']) . "' " . $selected . ">" . htmlspecialchars($row['nombre_finca']) . "</option>";
                        endwhile;
                        ?>
                    </select>
                </div>

                <button type="submit" class="btn btn-success"><i class="bi bi-arrow-repeat me-2"></i>Actualizar Persona</button>
                <a href="<?php echo BASE_URL; ?>pages/personas/listar_personas.php" class="btn btn-secondary"><i class="bi bi-arrow-left-circle me-2"></i>Cancelar</a>
            </form>
        </div>
    </div>
</main>

<?php
$conn->close();
require_once '../../includes/footer.php';
?>