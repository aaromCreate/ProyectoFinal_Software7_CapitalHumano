<section class="section-head row-between">
    <div>
        <h1>Sistema de colaboradores</h1>
        <p>Contrataciones, perfiles laborales, promociones y bajas.</p>
    </div>
    <?php if (\App\Servicios\AuthService::can('colaboradores', 'crear')): ?>
        <a class="btn primary" href="<?= e(url('colaboradores.create')) ?>">Nuevo colaborador</a>
    <?php endif; ?>
</section>
<form class="filters" method="get">
    <input type="hidden" name="route" value="colaboradores.index">
    <input name="q" placeholder="Buscar por documento, nombre, correo, direccion u ocupacion" value="<?= e($q) ?>">
    <button class="btn" type="submit">Buscar</button>
</form>
<div class="table-wrap">
    <table>
        <thead>
            <tr>
                <th>Codigo</th>
                <th>Documento</th>
                <th>Nombre</th>
                <th>Contacto</th>
                <th>Direccion</th>
                <th>Departamento</th>
                <th>Puesto actual</th>
                <th>Planilla</th>
                <th>Estado</th>
                <th>Acciones</th>
            </tr>
        </thead>
        <tbody>
        <?php foreach ($colaboradores as $row): ?>
            <tr>
                <td><?= e($row['id']) ?></td>
                <td><?= e($row['identidad']) ?></td>
                <td><?= e($row['nombre_completo']) ?></td>
                <td><?= e($row['correo']) ?><br><span class="muted"><?= e($row['celular']) ?></span></td>
                <td><?= e($row['direccion'] ?? 'Sin direccion') ?></td>
                <td><?= e($row['departamento'] ?? 'Sin departamento') ?></td>
                <td><?= e($row['ocupacion'] ?? 'Sin perfil') ?></td>
                <td><?= e($row['planilla'] ?? 'Sin planilla') ?></td>
                <td>
                    <span class="badge <?= (int) $row['empleado_activo'] === 1 ? 'ok' : 'danger' ?>">
                        <?= e($row['estado_colaborador'] ?? ((int) $row['empleado_activo'] === 1 ? 'Activo' : 'Inactivo')) ?>
                    </span>
                </td>
                <td class="actions">
                    <a class="action-btn view" href="<?= e(url('colaboradores.show', ['id' => $row['id']])) ?>">Ver</a>
                    <?php if (\App\Servicios\AuthService::can('colaboradores', 'editar')): ?>
                        <a class="action-btn edit" href="<?= e(url('colaboradores.edit', ['id' => $row['id']])) ?>">Editar</a>
                    <?php endif; ?>
                    <?php if (\App\Servicios\AuthService::can('colaboradores', 'promover') && (int) $row['empleado_activo'] === 1): ?>
                        <a class="action-btn promote" href="<?= e(url('colaboradores.promoteForm', ['id' => $row['id']])) ?>">Promocion</a>
                    <?php endif; ?>
                    <?php if (\App\Servicios\AuthService::can('colaboradores', 'baja') && (int) $row['empleado_activo'] === 1): ?>
                        <a class="action-btn danger-soft" href="<?= e(url('colaboradores.bajaForm', ['id' => $row['id']])) ?>">Baja</a>
                    <?php endif; ?>
                    <?php if (\App\Servicios\AuthService::can('colaboradores', 'eliminar')): ?>
                        <form method="post" action="<?= e(url('colaboradores.destroy', ['id' => $row['id']])) ?>" class="inline" onsubmit="return confirm('¿Eliminar colaborador?')">
                            <?= csrf_field() ?>
                            <button type="submit" class="action-btn danger">Eliminar</button>
                        </form>
                    <?php endif; ?>
                </td>
            </tr>
        <?php endforeach; ?>
        <?php if (empty($colaboradores)): ?>
            <tr><td colspan="10" class="empty">No hay colaboradores registrados.</td></tr>
        <?php endif; ?>
        </tbody>
    </table>
</div>
<?php if (!empty($paginacion) && $paginacion['pages'] > 1): ?>
<div class="pagination">
    <?php for ($i = 1; $i <= $paginacion['pages']; $i++): ?>
        <?php if ($i === $paginacion['page']): ?>
            <span class="btn small active"><?= e($i) ?></span>
        <?php else: ?>
            <a class="btn small" href="<?= e(url('colaboradores.index', ['q' => $q, 'page' => $i])) ?>"><?= e($i) ?></a>
        <?php endif; ?>
    <?php endfor; ?>
</div>
<?php endif; ?>

