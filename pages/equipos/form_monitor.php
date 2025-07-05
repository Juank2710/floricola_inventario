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

<!-- Campos heredados del equipo principal -->
<div class="mb-3">
    <label for="monitor_id_persona_acargo" class="form-label">Responsable:</label>
    <select class="form-select" id="monitor_id_persona_acargo" name="monitor_id_persona_acargo">
        <option value="">Sin responsable</option>
        <?php if (isset($personas_modal)): ?>
            <?php foreach($personas_modal as $row): ?>
                <option value="<?= $row['id'] ?>" <?= (isset($persona_seleccionada) && $persona_seleccionada == $row['id']) ? 'selected' : '' ?>>
                    <?= htmlspecialchars(mb_strtoupper($row['nombres'] . ' ' . $row['apellidos'], 'UTF-8')) ?>
                </option>
            <?php endforeach; ?>
        <?php endif; ?>
    </select>
</div>

<div class="mb-3">
    <label for="monitor_id_finca_ubicacion" class="form-label">Finca*:</label>
    <select class="form-select" id="monitor_id_finca_ubicacion" name="monitor_id_finca_ubicacion" required>
        <option value="">Seleccione...</option>
        <?php if (isset($fincas_modal)): ?>
            <?php foreach($fincas_modal as $row): ?>
                <option value="<?= $row['id'] ?>" <?= (isset($finca_seleccionada) && $finca_seleccionada == $row['id']) ? 'selected' : '' ?>>
                    <?= htmlspecialchars(mb_strtoupper($row['nombre_finca'], 'UTF-8')) ?>
                </option>
            <?php endforeach; ?>
        <?php endif; ?>
    </select>
</div>

<div class="mb-3">
    <label for="monitor_id_estado" class="form-label">Estado*:</label>
    <select class="form-select" id="monitor_id_estado" name="monitor_id_estado" required>
        <option value="">Seleccione...</option>
        <?php if (isset($estados_modal)): ?>
            <?php foreach($estados_modal as $row): ?>
                <option value="<?= $row['id'] ?>" <?= (isset($estado_seleccionado) && $estado_seleccionado == $row['id']) ? 'selected' : '' ?>>
                    <?= htmlspecialchars(mb_strtoupper($row['estado'], 'UTF-8')) ?>
                </option>
            <?php endforeach; ?>
        <?php endif; ?>
    </select>
</div>

<div class="mb-3">
    <label for="monitor_observaciones" class="form-label">Observaciones:</label>
    <textarea class="form-control" id="monitor_observaciones" name="monitor_observaciones"></textarea>
</div>

<!-- Campos ocultos para compatibilidad con el JavaScript existente -->
<input type="hidden" id="monitor_id_finca_ubicacion_hidden" name="monitor_id_finca_ubicacion_hidden">
<input type="hidden" id="monitor_id_persona_acargo_hidden" name="monitor_id_persona_acargo_hidden">
<input type="hidden" id="monitor_id_estado_hidden" name="monitor_id_estado_hidden">
<input type="hidden" id="monitor_id_tipo_equipo" name="monitor_id_tipo_equipo" value="<?= $id_tipo_monitor_global_val ?? '' ?>">