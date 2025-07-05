<?php
session_start();
if (!isset($_SESSION['user_id'])) {
    header('Location: ../../login.php');
    exit();
}
require_once '../../includes/db_config.php';
$conn = connectDB();

if (isset($_GET['id']) && is_numeric($_GET['id'])) {
    $persona_id = $_GET['id'];

    // Obtener el nombre de la persona antes de eliminarla
    $stmt_select = $conn->prepare("SELECT nombres, apellidos FROM personas WHERE id = ?");
    $stmt_select->bind_param("i", $persona_id);
    $stmt_select->execute();
    $result_select = $stmt_select->get_result();
    $persona_nombre_completo = "desconocida";
    if ($result_select->num_rows > 0) {
        $row = $result_select->fetch_assoc();
        $persona_nombre_completo = htmlspecialchars($row['nombres'] . " " . $row['apellidos']);
    }
    $stmt_select->close();

    // Eliminar la persona (si la FK a equipos es ON DELETE SET NULL, los equipos quedarán sin asignar)
    $stmt_delete = $conn->prepare("DELETE FROM personas WHERE id = ?");
    $stmt_delete->bind_param("i", $persona_id);

    if ($stmt_delete->execute()) {
        $_SESSION['message'] = "Persona '" . $persona_nombre_completo . "' eliminada exitosamente.";
        $_SESSION['message_type'] = "success";
    } else {
        $_SESSION['message'] = "Error al eliminar la persona: " . $stmt_delete->error;
        $_SESSION['message_type'] = "danger";
    }
    $stmt_delete->close();
} else {
    $_SESSION['message'] = "ID de persona no proporcionado para eliminar.";
    $_SESSION['message_type'] = "danger";
}

$conn->close();
header("Location: " . BASE_URL . "pages/personas/listar_personas.php");
exit();
?>