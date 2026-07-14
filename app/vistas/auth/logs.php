<section class="section-head row-between">
    <div>
        <h1>Bitacora de accesos</h1>
        <p>Registro de intentos de sesion y anomalias.</p>
    </div>
</section>

<div class="table-wrap">
    <table>
        <thead>
            <tr>
                <th>Fecha</th>
                <th>Usuario</th>
                <th>IP</th>
                <th>Intento</th>
                <th>Anomalia</th>
                <th>Detalle</th>
            </tr>
        </thead>
        <tbody>
        <?php foreach ($logs as $log): ?>
            <tr>
                <td><?= e($log['fecha']) ?></td>
                <td><?= e($log['usuario'] ?? 'N/A') ?></td>
                <td><?= e($log['ip']) ?></td>
                <td><span class="badge <?= $log['intento'] === 'exitoso' ? 'ok' : 'danger' ?>"><?= e($log['intento']) ?></span></td>
                <td><?= (int) $log['anomalia'] === 1 ? 'Si' : 'No' ?></td>
                <td><?= e($log['detalle'] ?? '') ?></td>
            </tr>
        <?php endforeach; ?>
        <?php if (empty($logs)): ?>
            <tr><td colspan="6" class="empty">No hay registros.</td></tr>
        <?php endif; ?>
        </tbody>
    </table>
</div>
