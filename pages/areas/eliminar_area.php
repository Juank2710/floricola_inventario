<?php
session_start();
if (!isset($_SESSION['user_id'])) {
    header('Location: ../../login.php');
    exit();
}
require_once '../../includes/db_config.php';
$conn = connectDB();

if (isset($_GET['id']) && is_numeric($_GET['id'])) {
    $area_id = $_GET['id'];

    $stmt_select = $conn->prepare("SELECT nombre_area FROM areas WHERE id = ?");
    $stmt_select->bind_param("i", $area_id);
    $stmt_select->execute();
    $result_select = $stmt_select->get_result();
    $area_nombre = "desconocida";
    if ($result_select->num_rows > 0) {
        $row = $result_select->fetch_assoc();
        $area_nombre = htmlspecialchars($row['nombre_area']);
    }
    $stmt_select->close();

    $stmt_delete = $conn->prepare("DELETE FROM areas WHERE id = ?");
    $stmt_delete->bind_param("i", $area_id);

    if ($stmt_delete->execute()) {
        $_SESSION['message'] = "Área '" . $area_nombre . "' eliminada exitosamente.";
        $_SESSION['message_type'] = "success";
    } else {
        $_SESSION['message'] = "Error al eliminar el área: " . $stmt_delete->error;
        $_SESSION['message_type'] = "danger";
    }
    $stmt_delete->close();
} else {
    $_SESSION['message'] = "ID de área no proporcionado para eliminar.";
    $_SESSION['message_type'] = "danger";
}

$conn->close();
header("Location: " . BASE_URL . "pages/areas/listar_areas.php");
exit();
?>