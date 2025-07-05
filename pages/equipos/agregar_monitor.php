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

// Si los campos visibles están vacíos, intentar con los campos ocultos (compatibilidad)
if (empty($id_finca)) $id_finca = $_POST['monitor_id_finca_ubicacion_hidden'] ?? '';
if (empty($id_persona)) $id_persona = $_POST['monitor_id_persona_acargo_hidden'] ?? '';
if (empty($id_estado)) $id_estado = $_POST['monitor_id_estado_hidden'] ?? '';

if (!$serial || !$id_finca || !$id_persona || !$id_estado) {
    echo json_encode(['success' => false, 'message' => 'Datos incompletos para el monitor']);
    exit;
}

// Validar que el serial no se repita en la tabla equipos
$stmt = $conn->prepare("SELECT id FROM equipos WHERE serial = ?");
$stmt->bind_param("s", $serial);
$stmt->execute();
$stmt->store_result();
if ($stmt->num_rows > 0) {
    echo json_encode(['success' => false, 'message' => 'El serial del monitor ya existe']);
    exit;
}
$stmt->close();

// Obtener el tipo de equipo para monitor
$id_tipo_equipo = $_POST['monitor_id_tipo_equipo'] ?? '';

// Insertar primero en la tabla equipos
$stmt = $conn->prepare("INSERT INTO equipos (serial, id_tipo_equipo, marca, modelo, fecha_compra, id_estado, id_persona_acargo, id_finca_ubicacion, observaciones, activo) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, 1)");
$stmt->bind_param("sisssiiis", $serial, $id_tipo_equipo, $marca, $modelo, $fecha_compra, $id_estado, $id_persona, $id_finca, $observaciones);

if ($stmt->execute()) {
    $id_equipo = $stmt->insert_id;
    $stmt->close();
    
    // Ahora insertar en la tabla monitores
    $stmt = $conn->prepare("INSERT INTO monitores (id_equipo, serial, marca, modelo, pulgadas, fecha_compra, observaciones, id_finca_ubicacion, id_persona_acargo, id_estado) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?)");
    $stmt->bind_param("issssssiii", $id_equipo, $serial, $marca, $modelo, $pulgadas, $fecha_compra, $observaciones, $id_finca, $id_persona, $id_estado);
    
    if ($stmt->execute()) {
        $id_monitor = $stmt->insert_id;
        echo json_encode([
            'success' => true,
            'monitor' => [
                'id' => $id_monitor,
                'serial' => $serial
            ]
        ]);
    } else {
        // Si falla la inserción en monitores, eliminar el equipo creado
        $conn->query("DELETE FROM equipos WHERE id = $id_equipo");
        echo json_encode(['success' => false, 'message' => 'Error al guardar el monitor']);
    }
    $stmt->close();
} else {
    echo json_encode(['success' => false, 'message' => 'Error al guardar el equipo']);
    $stmt->close();
}
$conn->close();