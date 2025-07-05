<?php
session_start();
if (!isset($_SESSION['user_id'])) {
    header('Location: ../../login.php');
    exit();
}
require_once '../../includes/db_config.php';
$page_title = "Listar Equipos";
require_once '../../includes/header.php';
require_once '../../includes/navbar.php';

$conn = connectDB();

// Obtener todos los tipos de equipo
$tipos_result = $conn->query("SELECT id, tipo FROM tipos_equipo ORDER BY tipo");
$tipos = [];
while ($row = $tipos_result->fetch_assoc()) {
    $tipos[] = $row;
}

// Para cada tipo, obtener los equipos asociados
$equipos_por_tipo = [];
foreach ($tipos as $tipo) {
    $stmt = $conn->prepare("SELECT * FROM equipos WHERE id_tipo_equipo = ?");
    $stmt->bind_param("i", $tipo['id']);
    $stmt->execute();
    $result = $stmt->get_result();
    $equipos = [];
    while ($equipo = $result->fetch_assoc()) {
        $equipos[] = $equipo;
    }
    $equipos_por_tipo[$tipo['id']] = $equipos;
    $stmt->close();
}
$conn->close();
?>

<main class="container mt-4 flex-grow-1">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h2><?php echo $page_title; ?></h2>
        <a href="agregar_equipo.php" class="btn btn-primary"><i class="bi bi-plus-circle me-2"></i>Agregar Equipo</a>
    </div>

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

    <ul class="nav nav-tabs" id="tipoEquipoTabs" role="tablist">
        <?php foreach ($tipos as $i => $tipo): ?>
            <li class="nav-item" role="presentation">
                <button class="nav-link <?php if ($i === 0) echo 'active'; ?>" id="tab-<?= $tipo['id'] ?>" data-bs-toggle="tab" data-bs-target="#tipo-<?= $tipo['id'] ?>" type="button" role="tab">
                    <?= htmlspecialchars($tipo['tipo']) ?>
                </button>
            </li>
        <?php endforeach; ?>
    </ul>

    <div class="tab-content mt-3">
        <?php foreach ($tipos as $i => $tipo): ?>
            <div class="tab-pane fade <?php if ($i === 0) echo 'show active'; ?>" id="tipo-<?= $tipo['id'] ?>" role="tabpanel">
                <div class="table-responsive">
                    <?php if (count($equipos_por_tipo[$tipo['id']]) > 0): ?>
                        <table class="table table-striped table-hover">
                            <thead>
                                <tr>
                                    <th>Serial</th>
                                    <th>Código Inventario</th>
                                    <th>Marca</th>
                                    <th>Modelo</th>
                                    <th>Estado</th>
                                    <th>Responsable</th>
                                    <th>Finca</th>
                                    <th>Acciones</th>
                                </tr>
                            </thead>
                            <tbody>
                            <?php foreach ($equipos_por_tipo[$tipo['id']] as $equipo): ?>
                                <tr>
                                    <td><?= htmlspecialchars($equipo['serial']) ?></td>
                                    <td><?= htmlspecialchars($equipo['codigo_inventario']) ?></td>
                                    <td><?= htmlspecialchars($equipo['marca']) ?></td>
                                    <td><?= htmlspecialchars($equipo['modelo']) ?></td>
                                    <td><?= htmlspecialchars($equipo['id_estado']) ?></td>
                                    <td><?= htmlspecialchars($equipo['id_persona_acargo']) ?></td>
                                    <td><?= htmlspecialchars($equipo['id_finca_ubicacion']) ?></td>
                                    <td>
                                        <a href="ver_historial.php?id=<?= $equipo['id'] ?>" class="btn btn-info btn-sm me-2"><i class="bi bi-journal-text"></i> Hoja de Vida</a>
                                        <a href="editar_equipo.php?id=<?= $equipo['id'] ?>" class="btn btn-warning btn-sm me-2"><i class="bi bi-pencil"></i> Editar</a>
                                        <a href="eliminar_equipo.php?id=<?= $equipo['id'] ?>" class="btn btn-danger btn-sm" onclick="return confirm('¿Estás seguro de que quieres eliminar este equipo? Esta acción no se puede deshacer.');"><i class="bi bi-trash"></i> Eliminar</a>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                            </tbody>
                        </table>
                    <?php else: ?>
                        <div class="alert alert-info">No hay equipos registrados para este tipo.</div>
                    <?php endif; ?>
                </div>
            </div>
        <?php endforeach; ?>
    </div>
</main>

<?php require_once '../../includes/footer.php'; ?>