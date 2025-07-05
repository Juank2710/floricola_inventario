<?php
// includes/footer.php
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
?>
        </main>
        <footer class="mt-auto py-3 bg-dark text-white">
            <div class="container text-center">
                <span class="text-muted">© <?php echo date("Y"); ?> Control de Equipos Florícola. Todos los derechos reservados.</span>
            </div>
        </footer>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
    <script src="<?php echo BASE_URL; ?>js/script.js"></script>

    <script>
        document.addEventListener('DOMContentLoaded', function() {
            // Selecciona todas las alertas que tengan la clase 'alert'
            var alerts = document.querySelectorAll('.alert');

            alerts.forEach(function(alert) {
                // Solo si la alerta no tiene un botón de cerrar 'data-bs-dismiss="alert"'
                // o si queremos que se oculte automáticamente además del botón de cerrar
                // La he puesto para que se oculte siempre si es una alerta de mensaje flash
                if (alert.classList.contains('alert-dismissible')) { // Asegúrate de que es una alerta que se puede cerrar
                    setTimeout(function() {
                        // Añade la clase 'fade' y 'show' para iniciar la transición de Bootstrap
                        // Luego remueve la alerta del DOM
                        var bsAlert = new bootstrap.Alert(alert); // Inicializa el componente Alert de Bootstrap
                        bsAlert.close(); // Usa el método close() de Bootstrap para un desvanecimiento suave
                    }, 3000); // 3000 milisegundos = 3 segundos
                }
            });
        });
    </script>
</body>
</html>