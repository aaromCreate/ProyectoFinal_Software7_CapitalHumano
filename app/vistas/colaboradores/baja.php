<?php
$values = array_merge([
    'fecha_fin' => date('Y-m-d'),
    'motivo_terminacion_id' => '',
], $old ?? []);
?>
<section class="section-head">
    <h1>Registrar baja</h1>
    <p><?= e($colaborador['nombre_completo']) ?></p>
</section>
<?php if (!empty($errors)): ?>
    <div class="alert alert-danger">
        <?php foreach ($errors as $error): ?><p><?= e($error) ?></p><?php endforeach; ?>
    </div>
<?php endif; ?>
<form method="post" action="<?= e(url('colaboradores.baja', ['id' => $colaborador['id']])) ?>" class="form-grid">
    <?= csrf_field() ?>
    <label>Fecha fin
        <input type="date" name="fecha_fin" required value="<?= e($values['fecha_fin']) ?>">
    </label>
    <label>Motivo
        <select name="motivo_terminacion_id" required>
            <option value="">Seleccione</option>
            <?php foreach ($catalogos['motivos_terminacion'] as $motivo): ?>
                <option value="<?= e($motivo['id']) ?>" <?= selected($values['motivo_terminacion_id'], $motivo['id']) ?>><?= e($motivo['nombre']) ?></option>
            <?php endforeach; ?>
        </select>
    </label>
    <div class="form-actions span-2">
        <button class="btn danger" type="submit">Confirmar baja</button>
        <a class="btn ghost" href="<?= e(url('colaboradores.show', ['id' => $colaborador['id']])) ?>">Cancelar</a>
    </div>
</form>
