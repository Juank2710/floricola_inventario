<?php
// filepath: pages/equipos/form_monitor.php
?>
<h6>Agregar Nuevo Monitor</h6>
<div class="mb-3">
    <label for="monitor_serial" class="form-label">Serial*:</label>
    <input type="text" class="form-control" id="monitor_serial" name="monitor_serial" required>
</div>
<div class="mb-3">
    <label for="monitor_marca" class="form-label">Marca:</label>
    <input type="text" class="form-control" id="monitor_marca" name="monitor_marca">
</div>
<div class="mb-3">
    <label for="monitor_modelo" class="form-label">Modelo:</label>
    <input type="text" class="form-control" id="monitor_modelo" name="monitor_modelo">
</div>
<div class="mb-3">
    <label for="monitor_pulgadas" class="form-label">Pulgadas:</label>
    <input type="number" step="0.1" class="form-control" id="monitor_pulgadas" name="monitor_pulgadas">
</div>
<div class="mb-3">
    <label for="monitor_fecha_compra" class="form-label">Fecha de Compra:</label>
    <input type="date" class="form-control" id="monitor_fecha_compra" name="monitor_fecha_compra">
</div>
<div class="mb-3">
    <label for="monitor_observaciones" class="form-label">Observaciones:</label>
    <textarea class="form-control" id="monitor_observaciones" name="monitor_observaciones"></textarea>
</div>
<input type="hidden" id="monitor_id_finca_ubicacion_hidden" name="monitor_id_finca_ubicacion">
<input type="hidden" id="monitor_id_persona_acargo_hidden" name="monitor_id_persona_acargo">