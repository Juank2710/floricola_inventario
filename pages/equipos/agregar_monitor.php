<?php
// filepath: pages/equipos/agregar_monitor.php
header('Content-Type: application/json');
require_once __DIR__ . '/../../includes/db_config.php';

$serial = $_POST['monitor_serial'] ?? '';
$marca = $_POST['monitor_marca'] ?? '';
$modelo = $_POST['monitor_modelo'] ?? '';
$pulgadas = $_POST['monitor_pulgadas'] ?? '';
$fecha_compra = $_POST['monitor_fecha_compra'] ?? '';
$observaciones = $_POST['monitor_observaciones'] ?? '';
$id_finca = $_POST['monitor_id_finca_ubicacion'] ?? '';
$id_persona = $_POST['monitor_id_persona_acargo'] ?? '';
$id_estado = $_POST['monitor_id_estado'] ?? '';

if (!$serial || !$id_finca || !$id_persona || !$id_estado) {
    echo json_encode(['success' => false, 'message' => 'Datos incompletos para el monitor']);
    exit;
}

// Validar que el serial no se repita
$stmt = $conn->prepare("SELECT id FROM monitores WHERE serial = ?");
$stmt->bind_param("s", $serial);
$stmt->execute();
$stmt->store_result();
if ($stmt->num_rows > 0) {
    echo json_encode(['success' => false, 'message' => 'El serial del monitor ya existe']);
    exit;
}
$stmt->close();

// Insertar monitor
$stmt = $conn->prepare("INSERT INTO monitores (serial, marca, modelo, pulgadas, fecha_compra, observaciones, id_finca_ubicacion, id_persona_acargo, id_estado) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?)");
$stmt->bind_param("ssssssiii", $serial, $marca, $modelo, $pulgadas, $fecha_compra, $observaciones, $id_finca, $id_persona, $id_estado);
if ($stmt->execute()) {
    $id = $stmt->insert_id;
    echo json_encode([
        'success' => true,
        'monitor' => [
            'id' => $id,
            'serial' => $serial
        ]
    ]);
} else {
    echo json_encode(['success' => false, 'message' => 'Error al guardar el monitor']);
}
$stmt->close();
$conn->close();