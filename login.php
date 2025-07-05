<?php
session_start(); // Inicia la sesión PHP al principio de la página

// Incluye el archivo de configuración de la base de datos
require_once 'includes/db_config.php';

$error_message = ''; // Variable para almacenar mensajes de error

// Verifica si se envió el formulario de login
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $nombre_usuario = trim($_POST['nombre_usuario'] ?? '');
    $contrasena_ingresada = trim($_POST['contrasena'] ?? '');

    // Validación básica en el servidor
    if (empty($nombre_usuario) || empty($contrasena_ingresada)) {
        $error_message = "Por favor, ingresa tu usuario y contraseña.";
    } else {
        $conn = connectDB(); // Conecta a la base de datos

        // Prepara la consulta para evitar inyección SQL
        $stmt = $conn->prepare("SELECT id, nombre_usuario, contrasena, rol FROM usuarios WHERE nombre_usuario = ?");
        if ($stmt) {
            $stmt->bind_param("s", $nombre_usuario);
            $stmt->execute();
            $result = $stmt->get_result();

            if ($result->num_rows === 1) {
                $usuario = $result->fetch_assoc();

                // Verifica la contraseña encriptada
                if (password_verify($contrasena_ingresada, $usuario['contrasena'])) {
                    // Contraseña correcta, inicia la sesión
                    $_SESSION['user_id'] = $usuario['id'];
                    $_SESSION['username'] = $usuario['nombre_usuario'];
                    $_SESSION['user_role'] = $usuario['rol'];

                    // Redirige al dashboard (index.php)
                    header('Location: index.php');
                    exit();
                } else {
                    $error_message = "Usuario o contraseña incorrectos.";
                }
            } else {
                $error_message = "Usuario o contraseña incorrectos.";
            }

            $stmt->close();
        } else {
            $error_message = "Error en la preparación de la consulta: " . $conn->error;
        }
        $conn->close(); // Cierra la conexión a la base de datos
    }
}
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login - Control de Equipos Florícola</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-QWTKZyjpPEjISv5WaRU9Oer+rPASQnMqDVyOQYwQx3D1n1zQ+r5QkGfA0fK5S+f" crossorigin="anonymous">
    <link href="css/style.css" rel="stylesheet">
    <style>
        body {
            background-color: #f8f9fa;
            display: flex;
            justify-content: center;
            align-items: center;
            min-height: 100vh;
        }
        .login-container {
            background-color: #ffffff;
            padding: 30px;
            border-radius: 8px;
            box-shadow: 0 0 10px rgba(0, 0, 0, 0.1);
            width: 100%;
            max-width: 400px;
        }
        .login-container h2 {
            margin-bottom: 25px;
            color: #343a40;
            text-align: center;
        }
    </style>
</head>
<body>
    <div class="login-container">
        <h2>Acceso al Sistema</h2>
        <?php if (!empty($error_message)): ?>
            <div class="alert alert-danger" role="alert">
                <?php echo $error_message; ?>
            </div>
        <?php endif; ?>
        <form action="login.php" method="POST">
            <div class="mb-3">
                <label for="nombre_usuario" class="form-label">Usuario:</label>
                <input type="text" class="form-control" id="nombre_usuario" name="nombre_usuario" required>
            </div>
            <div class="mb-3">
                <label for="contrasena" class="form-label">Contraseña:</label>
                <input type="password" class="form-control" id="contrasena" name="contrasena" required>
            </div>
            <div class="d-grid gap-2">
                <button type="submit" class="btn btn-primary">Iniciar Sesión</button>
            </div>
        </form>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js" integrity="sha384-YvpcrYf0tY3lHB60NNkmXc5s9fDVZLESaAA55NDzOxhy9GkcIdslK1eN7N6jIeHz" crossorigin="anonymous"></script>
</body>
</html>