<?php
$defaults = [
    'identidad' => '',
    'primer_nombre' => '',
    'segundo_nombre' => '',
    'primer_apellido' => '',
    'segundo_apellido' => '',
    'fecha_nacimiento' => '',
    'sexo' => '',
    'direccion' => '',
    'correo' => '',
    'telefono' => '',
    'celular' => '',
    'estado_colaborador_id' => 1,
    'ocupacion_id' => '',
    'tipo_empleado_id' => '',
    'planilla_id' => '',
    'departamento_id' => '',
    'salario' => '',
    'fecha_inicio' => '',
    'fecha_fin' => '',
];
$values = array_merge($defaults, $colaborador ?? [], $old ?? []);

// Si el SELECT principal no trajo el perfil activo, usar el perfil mas reciente.
if (!empty($colaborador['perfiles'][0])) {
    $perfilReciente = $colaborador['perfiles'][0];
    foreach (['ocupacion_id', 'tipo_empleado_id', 'planilla_id', 'departamento_id', 'salario', 'fecha_inicio', 'fecha_fin'] as $campo) {
        if (($values[$campo] ?? '') === '' && isset($perfilReciente[$campo]) && $perfilReciente[$campo] !== null) {
            $values[$campo] = $perfilReciente[$campo];
        }
    }
}
?>
<?php if (!empty($errors)): ?>
    <div class="alert alert-danger">
        <?php foreach ($errors as $error): ?><p><?= e($error) ?></p><?php endforeach; ?>
    </div>
<?php endif; ?>
<form method="post" action="<?= e($action) ?>" class="form-grid" enctype="multipart/form-data">
    <?= csrf_field() ?>
    <fieldset class="span-2 form-section">
        <legend>Datos personales</legend>
        <label>Fotografia
            <input type="file" name="fotografia" accept="image/*">
        </label>
        <label>Identidad o documento
            <input name="identidad" required value="<?= e($values['identidad']) ?>">
        </label>
        <label>Primer nombre
            <input name="primer_nombre" required value="<?= e($values['primer_nombre']) ?>">
        </label>
        <label>Segundo nombre
            <input name="segundo_nombre" value="<?= e($values['segundo_nombre']) ?>">
        </label>
        <label>Primer apellido
            <input name="primer_apellido" required value="<?= e($values['primer_apellido']) ?>">
        </label>
        <label>Segundo apellido
            <input name="segundo_apellido" value="<?= e($values['segundo_apellido']) ?>">
        </label>
        <label>Fecha de nacimiento
            <input type="date" name="fecha_nacimiento" value="<?= e($values['fecha_nacimiento'] ?? '') ?>">
        </label>
        <label>Sexo
            <select name="sexo" required>
                <option value="">Seleccione</option>
                <?php foreach (['Masculino', 'Femenino', 'Otro'] as $sexo): ?>
                    <option value="<?= e($sexo) ?>" <?= selected($values['sexo'], $sexo) ?>><?= e($sexo) ?></option>
                <?php endforeach; ?>
            </select>
        </label>
    </fieldset>

    <fieldset class="span-2 form-section">
        <legend>Contacto y ubicacion</legend>
        <label>Direccion
            <textarea name="direccion" rows="2"><?= e($values['direccion'] ?? '') ?></textarea>
        </label>
        <label>Correo
            <input type="email" name="correo" required value="<?= e($values['correo']) ?>">
        </label>
        <label>Telefono
            <input name="telefono" value="<?= e($values['telefono'] ?? '') ?>">
        </label>
        <label>Celular
            <input name="celular" required value="<?= e($values['celular']) ?>">
        </label>
        <label>Estado del colaborador
            <select name="estado_colaborador_id">
                <?php foreach ($catalogos['estados_colaborador'] as $estado): ?>
                    <option value="<?= e($estado['id']) ?>" <?= selected($values['estado_colaborador_id'], $estado['id']) ?>><?= e($estado['nombre']) ?></option>
                <?php endforeach; ?>
            </select>
        </label>
    </fieldset>

    <fieldset class="span-2 form-section">
        <legend>Perfil laboral</legend>
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
        <label>Departamento
            <select name="departamento_id">
                <option value="">Seleccione</option>
                <?php foreach ($catalogos['departamentos'] as $dep): ?>
                    <option value="<?= e($dep['id']) ?>" <?= selected($values['departamento_id'], $dep['id']) ?>><?= e($dep['nombre']) ?></option>
                <?php endforeach; ?>
            </select>
        </label>
        <label>Salario
            <input type="number" name="salario" min="0.01" step="0.01" required value="<?= e($values['salario']) ?>">
        </label>
        <label>Fecha inicio
            <input type="date" name="fecha_inicio" required value="<?= e($values['fecha_inicio']) ?>">
        </label>
        <label>Fecha fin
            <input type="date" name="fecha_fin" value="<?= e($values['fecha_fin'] ?? '') ?>">
        </label>
    </fieldset>
    <div class="form-actions">
        <button class="btn primary" type="submit"><?= e($button) ?></button>
        <a class="btn ghost" href="<?= e(url('colaboradores.index')) ?>">Cancelar</a>
    </div>
</form>
