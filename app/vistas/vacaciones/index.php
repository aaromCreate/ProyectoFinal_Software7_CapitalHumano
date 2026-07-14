<section class="section-head row-between">
    <div>
        <h1>Solicitudes de vacaciones</h1>
        <p>Gestion de dias generados y solicitados.</p>
    </div>
    <?php if (\App\Servicios\AuthService::can('vacaciones', 'crear')): ?>
        <a class="btn primary" href="<?= e(url('vacaciones.create')) ?>">Nueva solicitud</a>
    <?php endif; ?>
</section>

<div class="table-wrap">
    <table>
        <thead>
            <tr>
                <th>Colaborador</th>
                <th>Inicio</th>
                <th>Fin</th>
                <th>Dias solicitados</th>
                <th>Dias generados</th>
                <th>Estado</th>
                <th>Acciones</th>
            </tr>
        </thead>
        <tbody>
        <?php foreach ($solicitudes as $s): ?>
            <tr>
                <td><?= e($s['colaborador']) ?></td>
                <td><?= e($s['fecha_inicio']) ?></td>
                <td><?= e($s['fecha_fin']) ?></td>
                <td><?= e($s['dias_solicitados']) ?></td>
                <td><?= e($s['dias_generados']) ?></td>
                <td>
                    <span class="badge <?= $s['estado'] === 'Aprobada' ? 'ok' : ($s['estado'] === 'Rechazada' ? 'danger' : '') ?>">
                        <?= e($s['estado']) ?>
                    </span>
                </td>
                <td class="actions">
                    <?php if (\App\Servicios\AuthService::can('vacaciones', 'crear') && $s['estado'] === 'Pendiente'): ?>
                        <form method="post" action="<?= e(url('vacaciones.aprobar', ['id' => $s['id']])) ?>" class="inline">
                            <?= csrf_field() ?>
                            <button type="submit" class="action-btn ok">Aprobar</button>
                        </form>
                        <form method="post" action="<?= e(url('vacaciones.rechazar', ['id' => $s['id']])) ?>" class="inline">
                            <?= csrf_field() ?>
                            <button type="submit" class="action-btn danger-soft">Rechazar</button>
                        </form>
                    <?php endif; ?>
                </td>
            </tr>
        <?php endforeach; ?>
        <?php if (empty($solicitudes)): ?>
            <tr><td colspan="7" class="empty">No hay solicitudes.</td></tr>
        <?php endif; ?>
        </tbody>
    </table>
</div>
