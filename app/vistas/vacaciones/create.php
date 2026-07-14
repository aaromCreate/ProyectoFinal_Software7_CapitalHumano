<section class="section-head">
    <h1>Nueva solicitud de vacaciones</h1>
    <p>El sistema calcula los dias generados automaticamente.</p>
</section>

<?php if (!empty($errors)): ?>
    <div class="alert alert-danger">
        <?php foreach ($errors as $error): ?><p><?= e($error) ?></p><?php endforeach; ?>
    </div>
<?php endif; ?>

<form method="post" action="<?= e(url('vacaciones.store')) ?>" class="form-grid">
    <?= csrf_field() ?>
    <label>Colaborador
        <select name="colaborador_id" required>
            <option value="">Seleccione</option>
            <?php foreach ($colaboradores as $c): ?>
                <option value="<?= e($c['id']) ?>" <?= selected($old['colaborador_id'] ?? '', $c['id']) ?>><?= e($c['nombre_completo']) ?></option>
            <?php endforeach; ?>
        </select>
    </label>
    <label>Fecha inicio
        <input type="date" name="fecha_inicio" required value="<?= e($old['fecha_inicio'] ?? '') ?>">
    </label>
    <label>Fecha fin
        <input type="date" name="fecha_fin" required value="<?= e($old['fecha_fin'] ?? '') ?>">
    </label>
    <label class="span-2">Observaciones
        <textarea name="observaciones" rows="2"><?= e($old['observaciones'] ?? '') ?></textarea>
    </label>
    <div class="form-actions span-2">
        <button class="btn primary" type="submit">Guardar solicitud</button>
        <a class="btn ghost" href="<?= e(url('vacaciones.index')) ?>">Cancelar</a>
    </div>
</form>
