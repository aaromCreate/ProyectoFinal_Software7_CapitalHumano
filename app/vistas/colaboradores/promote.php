<?php
$values = array_merge([
    'ocupacion_id' => '',
    'tipo_empleado_id' => '',
    'planilla_id' => '',
    'salario' => '',
    'fecha_inicio' => date('Y-m-d'),
    'fecha_fin' => '',
], $old ?? []);
?>
<section class="section-head">
    <h1>Registrar promocion</h1>
    <p><?= e($colaborador['nombre_completo']) ?></p>
</section>
<?php if (!empty($errors)): ?>
    <div class="alert alert-danger">
        <?php foreach ($errors as $error): ?><p><?= e($error) ?></p><?php endforeach; ?>
    </div>
<?php endif; ?>
<form method="post" action="<?= e(url('colaboradores.promote', ['id' => $colaborador['id']])) ?>" class="form-grid">
    <?= csrf_field() ?>
    <fieldset class="span-2 form-section">
        <legend>Nuevo perfil laboral</legend>
        <label>Puesto u ocupacion
            <select name="ocupacion_id" required>
                <option value="">Seleccione</option>
                <?php foreach ($catalogos['ocupaciones'] as $ocupacion): ?>
                    <option value="<?= e($ocupacion['id']) ?>" <?= selected($values['ocupacion_id'], $ocupacion['id']) ?>><?= e($ocupacion['nombre']) ?></option>
                <?php endforeach; ?>
            </select>
        </label>
        <label>Tipo de empleado
            <select name="tipo_empleado_id" required>
                <option value="">Seleccione</option>
                <?php foreach ($catalogos['tipos_empleado'] as $tipo): ?>
                    <option value="<?= e($tipo['id']) ?>" <?= selected($values['tipo_empleado_id'], $tipo['id']) ?>><?= e($tipo['nombre']) ?></option>
                <?php endforeach; ?>
            </select>
        </label>
        <label>Planilla
            <select name="planilla_id" required>
                <option value="">Seleccione</option>
                <?php foreach ($catalogos['tipos_planilla'] as $planilla): ?>
                    <option value="<?= e($planilla['id']) ?>" <?= selected($values['planilla_id'], $planilla['id']) ?>><?= e($planilla['nombre']) ?></option>
                <?php endforeach; ?>
            </select>
        </label>
        <label>Salario
            <input type="number" name="salario" min="0.01" step="0.01" required value="<?= e($values['salario']) ?>">
        </label>
        <label>Fecha inicio
            <input type="date" name="fecha_inicio" required value="<?= e($values['fecha_inicio']) ?>">
        </label>
    </fieldset>
    <div class="form-actions">
        <button class="btn primary" type="submit">Guardar promocion</button>
        <a class="btn ghost" href="<?= e(url('colaboradores.show', ['id' => $colaborador['id']])) ?>">Cancelar</a>
    </div>
</form>
