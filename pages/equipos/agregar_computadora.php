<?php
// filepath: pages/equipos/agregar_computadora.php
session_start();
if (!isset($_SESSION['user_id'])) {
    header('Location: ../../login.php');
    exit();
}
require_once '../../includes/db_config.php'; // Asegúrate de que connectDB() esté aquí

// Si la función no existe, defínela aquí como respaldo
if (!function_exists('sanitize_input')) {
    function sanitize_input($data) {
        return htmlspecialchars(trim($data), ENT_QUOTES, 'UTF-8');
    }
}

$page_title = "Agregar Detalles de Computadora";

$conn = connectDB(); // Conexión a la base de datos

// Validar que se recibió un id_equipo válido
if (!isset($_GET['id_equipo']) || !is_numeric($_GET['id_equipo'])) {
    $_SESSION['message'] = "ID de equipo no válido.";
    $_SESSION['message_type'] = "danger";
    header("Location: listar_equipos.php");
    exit();
}
$id_equipo = intval($_GET['id_equipo']);

// Cargar los detalles de la computadora si ya existen (para pre-llenar)
$computadora_details = $conn->query("SELECT * FROM equipos_computadoras WHERE id_equipo = $id_equipo")->fetch_assoc();
if (!$computadora_details) {
    // Si no existe, inserta un registro básico para evitar errores de FK
    $conn->query("INSERT INTO equipos_computadoras (id_equipo) VALUES ($id_equipo)");
    $computadora_details = $conn->query("SELECT * FROM equipos_computadoras WHERE id_equipo = $id_equipo")->fetch_assoc();
}

// Cargar monitores existentes para el select
$monitores_disponibles = [];
// Solo monitores que no estén asignados a otra computadora
$stmt_monitores = $conn->prepare("SELECT m.id, m.serial FROM monitores m LEFT JOIN equipos_computadoras ec ON m.id = ec.id_monitor_asignado WHERE ec.id_monitor_asignado IS NULL OR ec.id_equipo = ?");
$stmt_monitores->bind_param("i", $id_equipo); // Incluir el monitor si ya está asignado a esta PC
$stmt_monitores->execute();
$result_monitores = $stmt_monitores->get_result();
while($row = $result_monitores->fetch_assoc()) $monitores_disponibles[] = $row;
$stmt_monitores->close();


// Obtener el ID del tipo 'Monitor' para el modal
$stmt_tipo_monitor = $conn->query("SELECT id FROM tipos_equipo WHERE tipo LIKE '%monitor%' LIMIT 1");
$id_tipo_monitor_global = $stmt_tipo_monitor->fetch_assoc()['id'] ?? null;
if (!$id_tipo_monitor_global) {
    // Manejar el error si el tipo 'Monitor' no existe en la BD
    $_SESSION['message'] = "Error: El tipo de equipo 'Monitor' no está configurado en la base de datos.";
    $_SESSION['message_type'] = "danger";
    header("Location: listar_equipos.php");
    exit();
}


// Cargar datos del equipo principal para obtener finca y persona (si es necesario)
$equipo_principal = $conn->query("SELECT id_finca_ubicacion, id_persona_acargo, id_estado FROM equipos WHERE id = $id_equipo")->fetch_assoc();
$finca_principal_id = $equipo_principal['id_finca_ubicacion'] ?? '';
$persona_principal_id = $equipo_principal['id_persona_acargo'] ?? '';
$estado_principal_id = $equipo_principal['id_estado'] ?? '';


// Cargar estados para el modal del monitor
$estados_monitor = [];
$result_estados = $conn->query("SELECT id, estado FROM estados_equipo ORDER BY estado");
while($row = $result_estados->fetch_assoc()) $estados_monitor[] = $row;


// Procesar el formulario POST (cuando se guardan los detalles de la computadora)
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $procesador = sanitize_input($_POST['procesador'] ?? '');
    $ram_gb = intval($_POST['ram_gb'] ?? 0);
    $almacenamiento = sanitize_input($_POST['almacenamiento'] ?? '');
    $sistema_operativo = sanitize_input($_POST['sistema_operativo'] ?? '');
    $id_monitor_asignado = !empty($_POST['id_monitor_asignado']) ? intval($_POST['id_monitor_asignado']) : null;

    $stmt = $conn->prepare("UPDATE equipos_computadoras SET procesador = ?, ram_gb = ?, almacenamiento = ?, sistema_operativo = ?, id_monitor_asignado = ? WHERE id_equipo = ?");
    $stmt->bind_param("sisii", $procesador, $ram_gb, $almacenamiento, $sistema_operativo, $id_monitor_asignado, $id_equipo);

    if ($stmt->execute()) {
        $_SESSION['message'] = "Detalles de computadora guardados correctamente.";
        $_SESSION['message_type'] = "success";
        header("Location: listar_equipos.php");
        exit();
    } else {
        $_SESSION['message'] = "Error al guardar detalles de la computadora: " . $stmt->error;
        $_SESSION['message_type'] = "danger";
    }
    $stmt->close();
}

require_once '../../includes/header.php';
require_once '../../includes/navbar.php'; // Si tienes un navbar separado
?>

<main class="container mt-4 flex-grow-1">
    <h2><?php echo $page_title; ?></h2>
    <?php if (isset($_SESSION['message'])): ?>
        <div class="alert alert-<?php echo $_SESSION['message_type']; ?> alert-dismissible fade show" role="alert" id="msg-alert">
            <?php echo $_SESSION['message']; ?>
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
        <?php
        unset($_SESSION['message']);
        unset($_SESSION['message_type']);
        ?>
    <?php endif; ?>
    <script>
    document.addEventListener('DOMContentLoaded', function() {
        var msg = document.getElementById('msg-alert');
        if(msg) setTimeout(function(){ msg.classList.remove('show'); }, 3000);
    });
    </script>

    <div class="card">
        <div class="card-header">
            Detalles Específicos de Computadora para Equipo ID: <?php echo htmlspecialchars($id_equipo); ?>
        </div>
        <div class="card-body">
            <form action="" method="POST">
                <!-- Incluye el formulario parcial para la computadora -->
                <?php
                // Pasa las variables necesarias al formulario parcial
                $procesador_val = $computadora_details['procesador'] ?? '';
                $ram_gb_val = $computadora_details['ram_gb'] ?? '';
                $almacenamiento_val = $computadora_details['almacenamiento'] ?? '';
                $sistema_operativo_val = $computadora_details['sistema_operativo'] ?? '';
                $id_monitor_asignado_val = $computadora_details['id_monitor_asignado'] ?? null;

                include 'form_computadora.php';
                ?>
                <button type="submit" class="btn btn-success"><i class="bi bi-save me-2"></i>Guardar Detalles</button>
                <a href="listar_equipos.php" class="btn btn-secondary"><i class="bi bi-arrow-left-circle me-2"></i>Volver a Equipos</a>
            </form>
        </div>
    </div>
</main>

<!-- Modal Bootstrap para agregar monitor -->
<div class="modal fade" id="modalNuevoMonitor" tabindex="-1" aria-labelledby="modalNuevoMonitorLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <!-- La acción del formulario del modal apunta a agregar_monitor.php -->
            <form action="agregar_monitor.php?return_to=agregar_computadora.php&id_equipo=<?= urlencode($id_equipo) ?>" method="POST" id="formMonitorNuevo">
                <div class="modal-header">
                    <h5 class="modal-title" id="modalNuevoMonitorLabel">Agregar Nuevo Monitor</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Cerrar"></button>
                </div>
                <div class="modal-body">
                    <!-- Incluye el formulario parcial para el monitor -->
                    <?php
                    // Pasa las variables necesarias al formulario parcial del monitor
                    // Estas variables se usarán para los dropdowns dentro del modal si los añades
                    $fincas_modal = $fincas; // Ya cargadas en agregar_equipo.php
                    $personas_modal = $personas; // Ya cargadas en agregar_equipo.php
                    $estados_modal = $estados_monitor; // Específicos para el modal del monitor
                    $id_tipo_monitor_global_val = $id_tipo_monitor_global; // ID del tipo 'Monitor'
                    ?>
                    <?php include 'form_monitor.php'; ?>
                </div>
                <div class="modal-footer">
                    <button type="submit" class="btn btn-primary btn-sm">Guardar Monitor</button>
                    <button type="button" class="btn btn-secondary btn-sm" data-bs-dismiss="modal">Cancelar</button>
                </div>
            </form>
        </div>
    </div>
</div>

<?php
require_once '../../includes/footer.php';
?>

<script>
document.addEventListener('DOMContentLoaded', function() {
    // Referencia al modal
    var modalNuevoMonitor = document.getElementById('modalNuevoMonitor');

    // Escuchar el evento 'show.bs.modal' para cuando el modal se va a mostrar
    modalNuevoMonitor.addEventListener('show.bs.modal', function (event) {
        // Obtener los valores seleccionados en el formulario principal de agregar_equipo.php
        // NOTA: Estos IDs ('id_finca_ubicacion', 'id_persona_acargo', 'id_estado')
        // deben existir en el formulario de agregar_equipo.php y estar disponibles en el DOM.
        // Si este script se ejecuta DESPUÉS de la redirección desde agregar_equipo.php,
        // estos valores NO estarán directamente en el DOM a menos que los pases por GET/SESSION
        // o los cargues de nuevo.
        // Para este escenario, asumimos que agregar_computadora.php es el *siguiente paso*
        // y que los datos de la computadora principal (finca, persona, estado)
        // se cargan de alguna manera (ej. desde la BD o pasados por GET/SESSION).
        // Para simplificar, los pasaremos directamente desde PHP a JS.

        // Valores que vienen de PHP (asegúrate de que estas variables PHP estén disponibles)
        const fincaPrincipalId = "<?php echo htmlspecialchars($finca_principal_id); ?>";
        const personaPrincipalId = "<?php echo htmlspecialchars($persona_principal_id); ?>";
        const estadoPrincipalId = "<?php echo htmlspecialchars($estado_principal_id); ?>"; // Para el estado del monitor

        // Asignar estos valores a los campos ocultos dentro del formulario del modal
        document.getElementById('monitor_id_finca_ubicacion_hidden').value = fincaPrincipalId;
        document.getElementById('monitor_id_persona_acargo_hidden').value = personaPrincipalId;
        document.getElementById('monitor_id_estado_hidden').value = estadoPrincipalId; // Pasar el estado

        // Opcional: Si quieres que los selects del modal se pre-seleccionen (si los haces visibles)
        // var selectFincaModal = document.getElementById('monitor_id_finca_ubicacion');
        // if (selectFincaModal) selectFincaModal.value = fincaPrincipalId;
        // var selectPersonaModal = document.getElementById('monitor_id_persona_acargo');
        // if (selectPersonaModal) selectPersonaModal.value = personaPrincipalId;
        // var selectEstadoModal = document.getElementById('monitor_id_estado');
        // if (selectEstadoModal) selectEstadoModal.value = estadoPrincipalId;
    });

    // Manejar el envío del formulario del modal (formMonitorNuevo) vía AJAX
    var formMonitorNuevo = document.getElementById('formMonitorNuevo');
    formMonitorNuevo.addEventListener('submit', function(e) {
        e.preventDefault(); // Previene el envío normal del formulario

        var actionUrl = this.getAttribute('action');
        var formData = new FormData(this);

        fetch(actionUrl, {
            method: 'POST',
            body: formData
        })
        .then(response => {
            // Clonar la respuesta para poder leerla dos veces (una para checkear ok, otra para json)
            const clonedResponse = response.clone();
            return response.json().catch(() => {
                // Si no es JSON, intentar leer como texto para depuración
                return clonedResponse.text().then(text => {
                    console.error("Respuesta no JSON:", text);
                    throw new Error("Respuesta del servidor no es JSON. Revisa el archivo 'agregar_monitor.php'.");
                });
            });
        })
        .then(data => {
            if (data.success) {
                alert('Monitor agregado con éxito: ' + data.monitor.serial);
                // Actualizar el select de monitores en el formulario principal
                var selectMonitor = document.getElementById('id_monitor_asignado');
                var newOption = new Option(data.monitor.serial, data.monitor.id, true, true);
                selectMonitor.add(newOption);
                selectMonitor.value = data.monitor.id; // Selecciona el nuevo monitor

                var monitorModal = bootstrap.Modal.getInstance(modalNuevoMonitor);
                if (monitorModal) {
                    monitorModal.hide(); // Cierra el modal
                }
            } else {
                alert('Error al agregar monitor: ' + data.message);
            }
        })
        .catch(error => {
            console.error('Error en la petición Fetch:', error);
            alert('Hubo un error de conexión o del servidor al agregar el monitor: ' + error.message);
        });
    });
});
</script>