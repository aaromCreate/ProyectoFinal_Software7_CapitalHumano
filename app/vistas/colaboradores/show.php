<section class="section-head row-between">
    <div class="profile-title">
        <?php if (!empty($colaborador['fotografia'])): ?>
            <img src="<?= e($colaborador['fotografia']) ?>" alt="Foto" class="avatar">
        <?php endif; ?>
        <div>
            <h1><?= e(($colaborador['primer_nombre'] ?? '') . ' ' . ($colaborador['primer_apellido'] ?? '')) ?></h1>
            <p>Codigo de empleado <?= e($colaborador['id']) ?> | <?= e($colaborador['correo']) ?></p>
        </div>
    </div>
    <div class="actions">
        <?php if (\App\Servicios\AuthService::can('colaboradores', 'editar')): ?>
            <a class="action-btn edit large"
                href="<?= e(url('colaboradores.edit', ['id' => $colaborador['id']])) ?>">Editar</a>
        <?php endif; ?>
        <?php if (\App\Servicios\AuthService::can('colaboradores', 'promover') && (int) $colaborador['empleado_activo'] === 1): ?>
            <a class="action-btn promote large"
                href="<?= e(url('colaboradores.promoteForm', ['id' => $colaborador['id']])) ?>">Promocion</a>
        <?php endif; ?>
        <?php if (\App\Servicios\AuthService::can('colaboradores', 'baja') && (int) $colaborador['empleado_activo'] === 1): ?>
            <a class="action-btn danger-soft large"
                href="<?= e(url('colaboradores.bajaForm', ['id' => $colaborador['id']])) ?>">Baja</a>
        <?php endif; ?>
        <?php if (\App\Servicios\AuthService::can('colaboradores', 'editar') && (int) $colaborador['empleado_activo'] !== 1): ?>
                <form method="post" action="<?= e(url('colaboradores.reintegrar', ['id' => $colaborador['id']])) ?>" onsubmit="return confirm('¿Reintegrar este colaborador?')">
                    <?= csrf_field() ?>
                    <button class="btn primary" type="submit">Reintegrar</button>
                </form>
        <?php endif; ?>
        <?php if (\App\Servicios\AuthService::can('colaboradores', 'eliminar')): ?>
            <form method="post" action="<?= e(url('colaboradores.destroy', ['id' => $colaborador['id']])) ?>"
                onsubmit="return confirm('Desea eliminar este colaborador?')">
                <?= csrf_field() ?>
                <button class="btn danger" type="submit">Eliminar</button>
            </form>
        <?php endif; ?>
    </div>
</section>
<section class="details">
    <div><strong>Identidad</strong><span><?= e($colaborador['identidad']) ?></span></div>
    <div><strong>Nombre completo</strong><span><?= e($colaborador['nombre_completo']) ?></span></div>
    <div><strong>Fecha de nacimiento</strong><span><?= e($colaborador['fecha_nacimiento'] ?? 'No registrada') ?></span>
    </div>
    <div><strong>Edad</strong><span><?= e($colaborador['edad'] ?? 'No calculada') ?></span></div>
    <div><strong>Sexo</strong><span><?= e($colaborador['sexo']) ?></span></div>
    <div><strong>Direccion</strong><span><?= e($colaborador['direccion'] ?? 'No registrada') ?></span></div>
    <div><strong>Telefono</strong><span><?= e($colaborador['telefono'] ?? 'No registrado') ?></span></div>
    <div><strong>Celular</strong><span><?= e($colaborador['celular']) ?></span></div>
    <div><strong>Estado</strong><span><?= e($colaborador['estado_colaborador'] ?? 'Activo') ?></span></div>
    <div><strong>Empleado activo</strong><span><?= (int) $colaborador['empleado_activo'] === 1 ? 'Si' : 'No' ?></span>
    </div>
    <div class="span-2"><strong>Motivo de baja</strong><span><?= e($colaborador['motivo_baja'] ?? 'No aplica') ?></span>
    </div>
</section>

<?php if (\App\Servicios\AuthService::can('colaboradores', 'editar')): ?>
    <section class="section-head compact">
        <h2>Historial academico</h2>
        <p>Agregue titulos y archivos PDF.</p>
    </section>
    <form method="post" action="<?= e(url('colaboradores.historial.store', ['id' => $colaborador['id']])) ?>"
        enctype="multipart/form-data" class="form-grid">
        <?= csrf_field() ?>
        <label>Titulo
            <input type="text" name="titulo" required>
        </label>
        <label>Institucion
            <input type="text" name="institucion" required>
        </label>
        <label>Archivo PDF
            <input type="file" name="archivo_pdf" accept="application/pdf" required>
        </label>
        <div class="form-actions">
            <button type="submit" class="btn primary">Agregar</button>
        </div>
    </form>
<?php endif; ?>

<?php if (!empty($historial)): ?>
    <div class="table-wrap">
        <table>
            <thead>
                <tr>
                    <th>Titulo</th>
                    <th>Institucion</th>
                    <th>Archivo</th>
                    <th>Acciones</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($historial as $h): ?>
                    <tr>
                        <td><?= e($h['titulo']) ?></td>
                        <td><?= e($h['institucion']) ?></td>
                        <td><a href="<?= e($h['archivo_pdf']) ?>" target="_blank">Ver PDF</a></td>
                        <td>
                            <?php if (\App\Servicios\AuthService::can('colaboradores', 'editar')): ?>
                                <form method="post"
                                    action="<?= e(url('colaboradores.historial.destroy', ['id' => $h['id'], 'colaborador_id' => $colaborador['id']])) ?>"
                                    class="inline" onsubmit="return confirm('¿Eliminar?')">
                                    <?= csrf_field() ?>
                                    <button type="submit" class="action-btn danger-soft">Eliminar</button>
                                </form>
                            <?php endif; ?>
                        </td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    </div>
<?php endif; ?>

<section class="section-head compact">
    <h2>Historial laboral</h2>
    <p>El cargo activo queda marcado en verde. Los registros finalizados quedan como historicos.</p>
</section>
<div class="table-wrap">
    <table>
        <thead>
            <tr>
                <th>Puesto</th>
                <th>Tipo empleado</th>
                <th>Planilla</th>
                <th>Departamento</th>
                <th>Salario</th>
                <th>Fecha inicio</th>
                <th>Fecha fin</th>
                <th>Cargo activo</th>
                <th>Empleado activo</th>
                <th>Motivo</th>
                <th>Integridad</th>
            </tr>
        </thead>
        <tbody>
            <?php foreach ($colaborador['perfiles'] as $perfil): ?>
                <tr>
                    <td><?= e($perfil['ocupacion']) ?></td>
                    <td><?= e($perfil['tipo_empleado']) ?></td>
                    <td><?= e($perfil['planilla']) ?></td>
                    <td><?= e($perfil['departamento'] ?? 'No asignado') ?></td>
                    <td>B/. <?= e(number_format((float) $perfil['salario'], 2)) ?></td>
                    <td><?= e($perfil['fecha_inicio']) ?></td>
                    <td><?= e($perfil['fecha_fin'] ?? 'N/A') ?></td>
                    <td><?= (int) $perfil['cargo_activo'] === 1 ? 'Si' : 'No' ?></td>
                    <td><?= (int) $perfil['empleado_activo'] === 1 ? 'Si' : 'No' ?></td>
                    <td><?= e($perfil['motivo_baja'] ?? 'No aplica') ?></td>
                    <td>
                        <span class="badge <?= !empty($perfil['integrity_valid']) ? 'ok' : 'danger' ?>">
                            <?= e($perfil['integrity_message']) ?>
                        </span>
                    </td>
                </tr>
            <?php endforeach; ?>
        </tbody>
    </table>
</div>