<?php
// filepath: pages/equipos/form_computadora.php
include_once __DIR__ . '/../../includes/db_config.php';

// Obtener monitores disponibles
function obtener_monitores($conn) {
    $result = $conn->query("SELECT id, serial FROM monitores");
    $monitores = [];
    while ($row = $result->fetch_assoc()) $monitores[] = $row;
    return $monitores;
}
$monitores = obtener_monitores($conn);

// Si quieres cargar datos existentes para edición, puedes agregarlos aquí
$procesador = $_POST['procesador'] ?? '';
$ram_gb = $_POST['ram_gb'] ?? '';
$almacenamiento = $_POST['almacenamiento'] ?? '';
$sistema_operativo = $_POST['sistema_operativo'] ?? '';
$id_monitor_asignado = $_POST['id_monitor_asignado'] ?? '';
?>

<div class="mb-3">
    <label for="procesador" class="form-label">Procesador:</label>
    <input type="text" class="form-control" id="procesador" name="procesador" value="<?= htmlspecialchars($procesador) ?>" required>
</div>
<div class="mb-3">
    <label for="ram_gb" class="form-label">RAM (GB):</label>
    <input type="number" class="form-control" id="ram_gb" name="ram_gb" min="1" value="<?= htmlspecialchars($ram_gb) ?>" required>
</div>
<div class="mb-3">
    <label for="almacenamiento" class="form-label">Almacenamiento:</label>
    <input type="text" class="form-control" id="almacenamiento" name="almacenamiento" value="<?= htmlspecialchars($almacenamiento) ?>" required>
</div>
<div class="mb-3">
    <label for="sistema_operativo" class="form-label">Sistema Operativo:</label>
    <input type="text" class="form-control" id="sistema_operativo" name="sistema_operativo" value="<?= htmlspecialchars($sistema_operativo) ?>" required>
</div>
<div class="mb-3">
    <label for="monitor" class="form-label">Monitor asignado:</label>
    <select class="form-control" id="monitor" name="id_monitor_asignado">
        <option value="">Sin monitor</option>
        <?php foreach ($monitores as $monitor): ?>
            <option value="<?= $monitor['id'] ?>" <?= ($id_monitor_asignado == $monitor['id']) ? 'selected' : '' ?>>
                <?= htmlspecialchars($monitor['serial']) ?>
            </option>
        <?php endforeach; ?>
    </select>
    <button type="button" class="btn btn-outline-primary mt-2" data-bs-toggle="modal" data-bs-target="#modalNuevoMonitor" id="btnAgregarMonitor">
        Agregar Monitor
    </button>
</div>