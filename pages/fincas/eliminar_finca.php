<?php
session_start();

// Si el usuario no está logueado, redirigirlo a la página de login
if (!isset($_SESSION['user_id'])) {
    header('Location: ../../login.php');
    exit();
}

// Incluir el archivo de configuración de la base de datos
require_once '../../includes/db_config.php';

$conn = connectDB(); // Conectar a la base de datos

// Verificar si se recibió un ID de finca para eliminar
if (isset($_GET['id']) && is_numeric($_GET['id'])) {
    $finca_id = $_GET['id'];

    // Opcional: Obtener el nombre de la finca antes de eliminarla para el mensaje de éxito
    $stmt_select = $conn->prepare("SELECT nombre_finca FROM fincas WHERE id = ?");
    $stmt_select->bind_param("i", $finca_id);
    $stmt_select->execute();
    $result_select = $stmt_select->get_result();
    $finca_nombre = "desconocida";
    if ($result_select->num_rows > 0) {
        $row = $result_select->fetch_assoc();
        $finca_nombre = htmlspecialchars($row['nombre_finca']);
    }
    $stmt_select->close();

    // Preparar la consulta SQL para eliminar la finca
    $stmt_delete = $conn->prepare("DELETE FROM fincas WHERE id = ?");
    $stmt_delete->bind_param("i", $finca_id); // "i" para entero

    if ($stmt_delete->execute()) {
        $_SESSION['message'] = "Finca '" . $finca_nombre . "' eliminada exitosamente.";
        $_SESSION['message_type'] = "success";
    } else {
        $_SESSION['message'] = "Error al eliminar la finca: " . $stmt_delete->error;
        $_SESSION['message_type'] = "danger";
    }
    $stmt_delete->close();
} else {
    $_SESSION['message'] = "ID de finca no proporcionado para eliminar.";
    $_SESSION['message_type'] = "danger";
}

$conn->close(); // Cerrar la conexión a la base de datos

// Redirigir siempre de vuelta a la página de listar fincas
header("Location: " . BASE_URL . "pages/fincas/listar_fincas.php");
exit();
?>