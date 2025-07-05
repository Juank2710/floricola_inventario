<?php
session_start();
if (!isset($_SESSION['user_id'])) {
    header('Location: ../../login.php');
    exit();
}
require_once '../../includes/db_config.php';
$page_title = "Listar Tipos de Equipo";
require_once '../../includes/header.php';
require_once '../../includes/navbar.php';
$conn = connectDB();

$sql = "SELECT id, tipo FROM tipos_equipo ORDER BY tipo ASC";
$result = $conn->query($sql);
?>

<main class="container mt-4 flex-grow-1">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h2><?php echo $page_title; ?></h2>
        <a href="agregar_tipo_equipo.php" class="btn btn-primary"><i class="bi bi-plus-circle me-2"></i>Agregar Tipo de Equipo</a>
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

    <div class="table-responsive">
        <table class="table table-striped table-hover">
            <thead>
                <tr>
                    <th>Tipo de Equipo</th>
                    <th>Acciones</th>
                </tr>
            </thead>
            <tbody>
                <?php
                if ($result->num_rows > 0) {
                    while($row = $result->fetch_assoc()) {
                        echo "<tr>";
                        echo "<td>" . htmlspecialchars($row['tipo']) . "</td>";
                        echo "<td>";
                        echo "<a href='editar_tipo_equipo.php?id=" . $row['id'] . "' class='btn btn-warning btn-sm me-2'><i class='bi bi-pencil'></i> Editar</a>";
                        echo "<a href='eliminar_tipo_equipo.php?id=" . $row['id'] . "' class='btn btn-danger btn-sm' onclick='return confirm(\"¿Estás seguro de que quieres eliminar este tipo de equipo? Esta acción no se puede deshacer.\")'><i class='bi bi-trash'></i> Eliminar</a>";
                        echo "</td>";
                        echo "</tr>";
                    }
                } else {
                    echo "<tr><td colspan='2' class='text-center'>No hay tipos de equipo registrados.</td></tr>";
                }
                ?>
            </tbody>
        </table>
    </div>
</main>

<?php
$conn->close();
require_once '../../includes/footer.php';
?>