<?php
// includes/header.php
if (session_status() == PHP_SESSION_NONE) {
    session_start();
}
// Asegurarse de que BASE_URL esté definida (viene de db_config.php)
if (!defined('BASE_URL')) {
    $protocol = isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] === 'on' ? "https" : "http";
    $host = $_SERVER['HTTP_HOST'];
    $script_name = $_SERVER['SCRIPT_NAME'];
    $project_folder_name = 'floricola_inventario'; // Asegúrate de que este nombre sea exacto
    $pos = strpos($script_name, '/' . $project_folder_name . '/');
    if ($pos !== false) {
        define('BASE_URL', $protocol . "://" . $host . substr($script_name, 0, $pos) . '/' . $project_folder_name . '/');
    } else {
        define('BASE_URL', $protocol . "://" . $host . '/');
    }
}
?><!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo isset($page_title) ? htmlspecialchars($page_title) . ' - Florícola' : 'Control de Equipos Florícola'; ?></title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">
    <link href="<?php echo BASE_URL; ?>css/style.css" rel="stylesheet"> </head>
<body>
    <div class="d-flex flex-column min-vh-100">