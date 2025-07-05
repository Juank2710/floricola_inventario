<?php
// includes/db_config.php

// Define las constantes de conexión a la base de datos
define('DB_SERVER', 'localhost');
define('DB_USER', 'root');
define('DB_PASS', '');
define('DB_NAME', 'floricola_inventario');

// --- CÁLCULO DINÁMICO DE LA URL BASE ---
$protocol = isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] === 'on' ? "https" : "http";
$host = $_SERVER['HTTP_HOST'];
$script_name = $_SERVER['SCRIPT_NAME'];

// Define el nombre de la carpeta de tu proyecto. ¡Asegúrate de que este nombre sea exacto!
$project_folder_name = 'floricola_inventario';

// Intenta encontrar la posición de la carpeta del proyecto en la URL.
$pos = strpos($script_name, '/' . $project_folder_name . '/');

if ($pos !== false) {
    // Si el proyecto está en un subdirectorio (ej. localhost/floricola_inventario/)
    $base_url = $protocol . "://" . $host . substr($script_name, 0, $pos) . '/' . $project_folder_name . '/';
} else {
    // Si el proyecto está en la raíz del dominio (ej. midominio.com/)
    $base_url = $protocol . "://" . $host . '/';
}

define('BASE_URL', $base_url);
// --- FIN CÁLCULO DINÁMICO DE LA URL BASE ---

// Función para establecer la conexión a la base de datos
function connectDB() {
    $conn = new mysqli(DB_SERVER, DB_USER, DB_PASS, DB_NAME);
    if ($conn->connect_error) {
        die("Error de conexión a la base de datos: " . $conn->connect_error);
    }
    $conn->set_charset("utf8mb4");
    return $conn;
}
// No hay ?> 