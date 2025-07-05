<?php
session_start();
if (!isset($_SESSION['user_id'])) {
    header('Location: ../../login.php');
    exit();
}
require_once '../../includes/db_config.php';
$conn = connectDB();

if (isset($_GET['id']) && is_numeric($_GET['id'])) {
    $tipo_id = $_GET['id'];

    // Obtener el nombre del tipo de equipo
    $stmt_select = $conn->prepare("SELECT tipo FROM tipos_equipo WHERE id = ?");
    $stmt_select->bind_param("i", $tipo_id);
    $stmt_select->execute();
    $result_select = $stmt_select->get_result();
    $tipo_nombre = "desconocido";
    if ($result_select->num_rows > 0) {
        $row = $result_select->fetch_assoc();
        $tipo_nombre = htmlspecialchars($row['tipo']);
    }
    $stmt_select->close();

    // Eliminar el tipo de equipo
    $stmt_delete = $conn->prepare("DELETE FROM tipos_equipo WHERE id = ?");
    $stmt_delete->bind_param("i", $tipo_id);

    if ($stmt_delete->execute()) {
        $_SESSION['message'] = "Tipo de equipo '" . $tipo_nombre . "' eliminado exitosamente.";
        $_SESSION['message_type'] = "success";
    } else {
        $_SESSION['message'] = "Error al eliminar el tipo de equipo: " . $stmt_delete->error;
        $_SESSION['message_type'] = "danger";
    }
    $stmt_delete->close();
} else {
    $_SESSION['message'] = "ID de tipo de equipo no proporcionado para eliminar.";
    $_SESSION['message_type'] = "danger";
}

$conn->close();
header("Location: listar_tipos_equipo.php");
exit();
?>