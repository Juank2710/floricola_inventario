<?php
session_start();
if (!isset($_SESSION['user_id'])) {
    header('Location: ../../login.php');
    exit();
}
require_once '../../includes/db_config.php';
$page_title = "Listar Personas";
require_once '../../includes/header.php';
require_once '../../includes/navbar.php';
$conn = connectDB();

// Consulta para obtener las personas con los nombres de sus FKs
$sql = "SELECT
            p.id,
            p.cedula,
            p.nombres,
            p.apellidos,
            p.telefono,
            p.email,
            p.cargo,
            a.nombre_area AS area_trabajo,
            f.nombre_finca AS finca_trabajo
        FROM personas p
        JOIN areas a ON p.id_area = a.id
        JOIN fincas f ON p.id_finca_trabajo = f.id
        ORDER BY p.apellidos, p.nombres ASC";
$result = $conn->query($sql);
?>

<main class="container mt-4 flex-grow-1">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h2><?php echo $page_title; ?></h2>
        <a href="<?php echo BASE_URL; ?>pages/personas/agregar_persona.php" class="btn btn-primary"><i class="bi bi-plus-circle me-2"></i>Agregar Nueva Persona</a>
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
                    <th>Cédula</th>
                    <th>Nombres</th>
                    <th>Apellidos</th>
                    <th>Teléfono</th>
                    <th>Email</th>
                    <th>Cargo</th>
                    <th>Área de Trabajo</th> <th>Finca de Trabajo</th> <th>Acciones</th>
                </tr>
            </thead>
            <tbody>
                <?php
                if ($result->num_rows > 0) {
                    while($row = $result->fetch_assoc()) {
                        echo "<tr>";
                        echo "<td>" . htmlspecialchars($row['cedula']) . "</td>";
                        echo "<td>" . htmlspecialchars($row['nombres']) . "</td>";
                        echo "<td>" . htmlspecialchars($row['apellidos']) . "</td>";
                        echo "<td>" . htmlspecialchars($row['telefono']) . "</td>";
                        echo "<td>" . htmlspecialchars($row['email']) . "</td>";
                        echo "<td>" . htmlspecialchars($row['cargo']) . "</td>";
                        echo "<td>" . htmlspecialchars($row['area_trabajo']) . "</td>"; // Mostrar área
                        echo "<td>" . htmlspecialchars($row['finca_trabajo']) . "</td>"; // Mostrar finca
                        echo "<td>";
                        echo "<a href='" . BASE_URL . "pages/personas/editar_persona.php?id=" . $row['id'] . "' class='btn btn-warning btn-sm me-2'><i class='bi bi-pencil'></i> Editar</a>";
                        echo "<a href='" . BASE_URL . "pages/personas/eliminar_persona.php?id=" . $row['id'] . "' class='btn btn-danger btn-sm' onclick='return confirm(\"¿Estás seguro de que quieres eliminar a esta persona? Esto puede desvincular equipos asignados.\")'><i class='bi bi-trash'></i> Eliminar</a>";
                        echo "</td>";
                        echo "</tr>";
                    }
                } else {
                    echo "<tr><td colspan='9' class='text-center'>No hay personas registradas.</td></tr>"; // Actualizar colspan
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