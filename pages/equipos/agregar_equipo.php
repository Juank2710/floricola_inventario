<?php
session_start();
if (!isset($_SESSION['user_id'])) {
    header('Location: ../../login.php');
    exit();
}
require_once '../../includes/db_config.php';
$page_title = "Agregar Nuevo Equipo";

$conn = connectDB();

// Cargar datos para los selects en arrays
$tipos = [];
$result = $conn->query("SELECT id, tipo FROM tipos_equipo ORDER BY tipo");
while($row = $result->fetch_assoc()) $tipos[] = $row;

$estados = [];
$result = $conn->query("SELECT id, estado FROM estados_equipo ORDER BY estado");
while($row = $result->fetch_assoc()) $estados[] = $row;

$personas = [];
$result = $conn->query("SELECT id, nombres, apellidos FROM personas ORDER BY nombres, apellidos");
while($row = $result->fetch_assoc()) $personas[] = $row;

$fincas = [];
$result = $conn->query("SELECT id, nombre_finca FROM fincas ORDER BY nombre_finca");
while($row = $result->fetch_assoc()) $fincas[] = $row;

$areas = [];
$result = $conn->query("SELECT id, nombre_area FROM areas ORDER BY nombre_area");
while($row = $result->fetch_assoc()) $areas[] = $row;

$monitores = [];
$result = $conn->query("SELECT id, serial FROM equipos WHERE id_tipo_equipo = (SELECT id FROM tipos_equipo WHERE tipo LIKE '%monitor%')");
while($row = $result->fetch_assoc()) $monitores[] = $row;

// Inicializar variables para repoblar el formulario
$serial = $codigo_inventario = $marca = $modelo = $fecha_compra = $precio_compra = $observaciones = '';
$id_tipo_equipo = $id_estado = $id_persona_acargo = $id_finca_ubicacion = $id_area_ubicacion = 0;
$activo = 1;
$errores = [];

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Convertir a mayúsculas excepto observaciones
    $serial = mb_strtoupper(trim($_POST['serial'] ?? ''), 'UTF-8');
    $codigo_inventario = mb_strtoupper(trim($_POST['codigo_inventario'] ?? ''), 'UTF-8');
    $id_tipo_equipo = intval($_POST['id_tipo_equipo'] ?? 0);
    $marca = mb_strtoupper(trim($_POST['marca'] ?? ''), 'UTF-8');
    $modelo = mb_strtoupper(trim($_POST['modelo'] ?? ''), 'UTF-8');
    $fecha_compra = $_POST['fecha_compra'] ?? null;
    $precio_compra = $_POST['precio_compra'] ?? null;
    $id_estado = intval($_POST['id_estado'] ?? 0);
    $id_persona_acargo = !empty($_POST['id_persona_acargo']) ? intval($_POST['id_persona_acargo']) : null;
    $id_finca_ubicacion = intval($_POST['id_finca_ubicacion'] ?? 0);
    $id_area_ubicacion = intval($_POST['id_area_ubicacion'] ?? 0);
    $observaciones = trim($_POST['observaciones'] ?? ''); // No convertir a mayúsculas
    $activo = isset($_POST['activo']) ? 1 : 0;

    // Validación básica
    if (empty($serial)) $errores['serial'] = "El serial es obligatorio.";
    if (empty($id_tipo_equipo)) $errores['id_tipo_equipo'] = "Seleccione el tipo de equipo.";
    if (empty($id_estado)) $errores['id_estado'] = "Seleccione el estado.";
    if (empty($id_finca_ubicacion)) $errores['id_finca_ubicacion'] = "Seleccione la finca.";
    if (empty($id_area_ubicacion)) $errores['id_area_ubicacion'] = "Seleccione el área.";

    // Validar duplicados
    if ($serial) {
        $stmt = $conn->prepare("SELECT id FROM equipos WHERE serial = ?");
        $stmt->bind_param("s", $serial);
        $stmt->execute();
        $stmt->store_result();
        if ($stmt->num_rows > 0) $errores['serial'] = "El serial ya está registrado.";
        $stmt->close();
    }
    if ($codigo_inventario) {
        $stmt = $conn->prepare("SELECT id FROM equipos WHERE codigo_inventario = ?");
        $stmt->bind_param("s", $codigo_inventario);
        $stmt->execute();
        $stmt->store_result();
        if ($stmt->num_rows > 0) $errores['codigo_inventario'] = "El código de inventario ya está registrado.";
        $stmt->close();
    }

    if (empty($errores)) {
        $stmt = $conn->prepare("INSERT INTO equipos (serial, codigo_inventario, id_tipo_equipo, marca, modelo, fecha_compra, precio_compra, id_estado, id_persona_acargo, id_finca_ubicacion, id_area_ubicacion, observaciones, activo) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)");
        $stmt->bind_param(
            "ssisssdiiissi",
            $serial,
            $codigo_inventario,
            $id_tipo_equipo,
            $marca,
            $modelo,
            $fecha_compra,
            $precio_compra,
            $id_estado,
            $id_persona_acargo,
            $id_finca_ubicacion,
            $id_area_ubicacion,
            $observaciones,
            $activo
        );
        if ($stmt->execute()) {
            $id_equipo = $stmt->insert_id;
            $tipo = $conn->query("SELECT tipo FROM tipos_equipo WHERE id = $id_tipo_equipo")->fetch_assoc()['tipo'];
            if (stripos($tipo, 'computadora') !== false || stripos($tipo, 'laptop') !== false || stripos($tipo, 'tablet') !== false) {
                header("Location: agregar_computadora.php?id_equipo=$id_equipo&id_finca=$id_finca_ubicacion&id_persona=$id_persona_acargo&id_estado=$id_estado");
            } elseif (stripos($tipo, 'radio') !== false) {
                header("Location: agregar_radio.php?id_equipo=$id_equipo");
            } elseif (stripos($tipo, 'impresora') !== false) {
                header("Location: agregar_impresora.php?id_equipo=$id_equipo");
            } else {
                header("Location: listar_equipos.php");
            }
            exit();
        } else {
            $errores['general'] = "Error al guardar el equipo: " . $stmt->error;
        }
        $stmt->close();
    }
}

require_once '../../includes/header.php';
require_once '../../includes/navbar.php';
?>

<main class="container mt-4 flex-grow-1">
    <h2><?php echo $page_title; ?></h2>
    <?php if (!empty($errores['general'])): ?>
        <div class="alert alert-danger"><?php echo $errores['general']; ?></div>
    <?php endif; ?>
    <div class="card">
        <div class="card-header">
            Formulario de Nuevo Equipo
        </div>
        <div class="card-body">
            <form action="" method="POST" id="formEquipo" autocomplete="off">
                <div class="mb-3">
                    <label for="serial" class="form-label">Serial*:</label>
                    <input type="text" class="form-control <?= isset($errores['serial']) ? 'is-invalid' : '' ?>" id="serial" name="serial" value="<?= htmlspecialchars($serial) ?>" required>
                    <span class="text-danger small"><?= $errores['serial'] ?? '' ?></span>
                </div>
                <div class="mb-3">
                    <label for="codigo_inventario" class="form-label">Código Inventario:</label>
                    <input type="text" class="form-control <?= isset($errores['codigo_inventario']) ? 'is-invalid' : '' ?>" id="codigo_inventario" name="codigo_inventario" value="<?= htmlspecialchars($codigo_inventario) ?>">
                    <span class="text-danger small"><?= $errores['codigo_inventario'] ?? '' ?></span>
                </div>
                <div class="mb-3">
                    <label for="id_tipo_equipo" class="form-label">Tipo de Equipo*:</label>
                    <select class="form-select <?= isset($errores['id_tipo_equipo']) ? 'is-invalid' : '' ?>" id="id_tipo_equipo" name="id_tipo_equipo" required>
                        <option value="">Seleccione...</option>
                        <?php foreach($tipos as $row): ?>
                            <option value="<?= $row['id'] ?>" <?= $id_tipo_equipo == $row['id'] ? 'selected' : '' ?>><?= htmlspecialchars(mb_strtoupper($row['tipo'], 'UTF-8')) ?></option>
                        <?php endforeach; ?>
                    </select>
                    <span class="text-danger small"><?= $errores['id_tipo_equipo'] ?? '' ?></span>
                </div>
                <div class="mb-3">
                    <label for="marca" class="form-label">Marca:</label>
                    <input type="text" class="form-control" id="marca" name="marca" value="<?= htmlspecialchars($marca) ?>">
                </div>
                <div class="mb-3">
                    <label for="modelo" class="form-label">Modelo:</label>
                    <input type="text" class="form-control" id="modelo" name="modelo" value="<?= htmlspecialchars($modelo) ?>">
                </div>
                <div class="mb-3">
                    <label for="fecha_compra" class="form-label">Fecha de Compra:</label>
                    <input type="date" class="form-control" id="fecha_compra" name="fecha_compra" value="<?= htmlspecialchars($fecha_compra) ?>">
                </div>
                <div class="mb-3">
                    <label for="precio_compra" class="form-label">Precio de Compra:</label>
                    <input type="number" step="0.01" class="form-control" id="precio_compra" name="precio_compra" value="<?= htmlspecialchars($precio_compra) ?>">
                </div>
                <div class="mb-3">
                    <label for="id_estado" class="form-label">Estado*:</label>
                    <select class="form-select <?= isset($errores['id_estado']) ? 'is-invalid' : '' ?>" id="id_estado" name="id_estado" required>
                        <option value="">Seleccione...</option>
                        <?php foreach($estados as $row): ?>
                            <option value="<?= $row['id'] ?>" <?= $id_estado == $row['id'] ? 'selected' : '' ?>><?= htmlspecialchars(mb_strtoupper($row['estado'], 'UTF-8')) ?></option>
                        <?php endforeach; ?>
                    </select>
                    <span class="text-danger small"><?= $errores['id_estado'] ?? '' ?></span>
                </div>
                <div class="mb-3">
                    <label for="id_persona_acargo" class="form-label">Responsable:</label>
                    <select class="form-select" id="id_persona_acargo" name="id_persona_acargo">
                        <option value="">Sin responsable</option>
                        <?php foreach($personas as $row): ?>
                            <option value="<?= $row['id'] ?>" <?= $id_persona_acargo == $row['id'] ? 'selected' : '' ?>><?= htmlspecialchars(mb_strtoupper($row['nombres'] . ' ' . $row['apellidos'], 'UTF-8')) ?></option>
                        <?php endforeach; ?>
                    </select>
                </div>
                <div class="mb-3">
                    <label for="id_finca_ubicacion" class="form-label">Finca*:</label>
                    <select class="form-select <?= isset($errores['id_finca_ubicacion']) ? 'is-invalid' : '' ?>" id="id_finca_ubicacion" name="id_finca_ubicacion" required>
                        <option value="">Seleccione...</option>
                        <?php foreach($fincas as $row): ?>
                            <option value="<?= $row['id'] ?>" <?= $id_finca_ubicacion == $row['id'] ? 'selected' : '' ?>><?= htmlspecialchars(mb_strtoupper($row['nombre_finca'], 'UTF-8')) ?></option>
                        <?php endforeach; ?>
                    </select>
                    <span class="text-danger small"><?= $errores['id_finca_ubicacion'] ?? '' ?></span>
                </div>
                <div class="mb-3">
                    <label for="id_area_ubicacion" class="form-label">Área*:</label>
                    <select class="form-select <?= isset($errores['id_area_ubicacion']) ? 'is-invalid' : '' ?>" id="id_area_ubicacion" name="id_area_ubicacion" required>
                        <option value="">Seleccione...</option>
                        <?php foreach($areas as $row): ?>
                            <option value="<?= $row['id'] ?>" <?= $id_area_ubicacion == $row['id'] ? 'selected' : '' ?>><?= htmlspecialchars(mb_strtoupper($row['nombre_area'], 'UTF-8')) ?></option>
                        <?php endforeach; ?>
                    </select>
                    <span class="text-danger small"><?= $errores['id_area_ubicacion'] ?? '' ?></span>
                </div>
                <div class="mb-3">
                    <label for="observaciones" class="form-label">Observaciones:</label>
                    <textarea class="form-control" id="observaciones" name="observaciones"><?= htmlspecialchars($observaciones) ?></textarea>
                </div>
                <div class="form-check mb-3">
                    <input class="form-check-input" type="checkbox" id="activo" name="activo" <?= $activo ? 'checked' : '' ?>>
                    <label class="form-check-label" for="activo">Activo</label>
                </div>
                <button type="submit" class="btn btn-success" id="btnGuardar"><i class="bi bi-save me-2"></i>Guardar Equipo</button>
                <a href="listar_equipos.php" class="btn btn-secondary"><i class="bi bi-arrow-left-circle me-2"></i>Cancelar</a>
            </form>
        </div>
    </div>
</main>
<?php
require_once '../../includes/footer.php';
?>

