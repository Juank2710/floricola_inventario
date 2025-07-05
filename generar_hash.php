<?php
// generar_hash.php
$password = 'admin123';
$hashed_password = password_hash($password, PASSWORD_DEFAULT);
echo "El hash para 'admin123' es: " . $hashed_password;
?>