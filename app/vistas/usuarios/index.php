<section class="section-head row-between">
    <div>
        <h1>Usuarios administrativos</h1>
        <p>Gestion de cuentas y roles.</p>
    </div>
    <?php if (\App\Servicios\AuthService::can('usuarios', 'crear')): ?>
        <a class="btn primary" href="<?= e(url('usuarios.create')) ?>">Nuevo usuario</a>
    <?php endif; ?>
</section>

<div class="table-wrap">
    <table>
        <thead>
            <tr>
                <th>ID</th>
                <th>Nombre</th>
                <th>Usuario</th>
                <th>Correo</th>
                <th>Roles</th>
                <th>Estado</th>
                <th>Ultimo acceso</th>
                <th>Acciones</th>
            </tr>
        </thead>
        <tbody>
        <?php foreach ($usuarios as $u): ?>
            <tr>
                <td><?= e($u['id']) ?></td>
                <td><?= e($u['nombre']) ?></td>
                <td><?= e($u['usuario']) ?></td>
                <td><?= e($u['correo']) ?></td>
                <td><?= e($u['roles'] ?? 'Sin rol') ?></td>
                <td>
                    <span class="badge <?= (int) $u['activo'] === 1 ? 'ok' : 'danger' ?>">
                        <?= (int) $u['activo'] === 1 ? 'Activo' : 'Inactivo' ?>
                    </span>
                </td>
                <td><?= e($u['ultimo_acceso'] ?? 'Nunca') ?></td>
                <td class="actions">
                    <?php if (\App\Servicios\AuthService::can('usuarios', 'editar')): ?>
                        <a class="action-btn edit" href="<?= e(url('usuarios.edit', ['id' => $u['id']])) ?>">Editar</a>
                        <form method="post" action="<?= e(url('usuarios.toggle', ['id' => $u['id']])) ?>" class="inline">
                            <?= csrf_field() ?>
                            <button type="submit" class="action-btn <?= (int) $u['activo'] === 1 ? 'danger-soft' : 'ok' ?>">
                                <?= (int) $u['activo'] === 1 ? 'Desactivar' : 'Activar' ?>
                            </button>
                        </form>
                    <?php endif; ?>
                </td>
            </tr>
        <?php endforeach; ?>
        <?php if (empty($usuarios)): ?>
            <tr><td colspan="8" class="empty">No hay usuarios registrados.</td></tr>
        <?php endif; ?>
        </tbody>
    </table>
</div>
