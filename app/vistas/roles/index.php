<section class="section-head row-between">
    <div>
        <h1>Roles</h1>
        <p>Perfiles de permisos del sistema.</p>
    </div>
    <?php if (\App\Servicios\AuthService::can('roles', 'crear')): ?>
        <a class="btn primary" href="<?= e(url('roles.create')) ?>">Nuevo rol</a>
    <?php endif; ?>
</section>

<div class="table-wrap">
    <table>
        <thead>
            <tr>
                <th>ID</th>
                <th>Nombre</th>
                <th>Descripcion</th>
                <th>Estado</th>
                <th>Acciones</th>
            </tr>
        </thead>
        <tbody>
        <?php foreach ($roles as $r): ?>
            <tr>
                <td><?= e($r['id']) ?></td>
                <td><?= e($r['nombre']) ?></td>
                <td><?= e($r['descripcion'] ?? '') ?></td>
                <td>
                    <span class="badge <?= (int) $r['activo'] === 1 ? 'ok' : 'danger' ?>">
                        <?= (int) $r['activo'] === 1 ? 'Activo' : 'Inactivo' ?>
                    </span>
                </td>
                <td class="actions">
                    <?php if (\App\Servicios\AuthService::can('roles', 'editar')): ?>
                        <a class="action-btn edit" href="<?= e(url('roles.edit', ['id' => $r['id']])) ?>">Editar</a>
                        <form method="post" action="<?= e(url('roles.toggle', ['id' => $r['id']])) ?>" class="inline">
                            <?= csrf_field() ?>
                            <button type="submit" class="action-btn <?= (int) $r['activo'] === 1 ? 'danger-soft' : 'ok' ?>">
                                <?= (int) $r['activo'] === 1 ? 'Desactivar' : 'Activar' ?>
                            </button>
                        </form>
                    <?php endif; ?>
                </td>
            </tr>
        <?php endforeach; ?>
        <?php if (empty($roles)): ?>
            <tr><td colspan="5" class="empty">No hay roles registrados.</td></tr>
        <?php endif; ?>
        </tbody>
    </table>
</div>
