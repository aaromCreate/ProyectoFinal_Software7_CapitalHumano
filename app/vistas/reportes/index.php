<section class="section-head row-between">
    <div>
        <h1>Reporte de colaboradores</h1>
        <p>Perfiles laborales e integridad OpenSSL.</p>
    </div>
    <?php if (\App\Servicios\AuthService::can('reportes', 'exportar')): ?>
        <a class="btn primary" href="<?= e(url('reportes.export', ['q' => $q, 'sexo' => $sexo, 'edad' => $edad])) ?>">Exportar Excel</a>
    <?php endif; ?>
</section>

<form class="filters" method="get">
    <input type="hidden" name="route" value="reportes.index">
    <input name="q" placeholder="Buscar por documento, nombre, correo, direccion u ocupacion" value="<?= e($q) ?>">
    <select name="sexo">
        <option value="">Todos los sexos</option>
        <?php foreach (['Masculino', 'Femenino', 'Otro'] as $s): ?>
            <option value="<?= e($s) ?>" <?= selected($sexo, $s) ?>><?= e($s) ?></option>
        <?php endforeach; ?>
    </select>
    <select name="edad">
        <option value="">Todas las edades</option>
        <?php foreach (['18-25', '26-35', '36-45', '46-55', '56+'] as $r): ?>
            <option value="<?= e($r) ?>" <?= selected($edad, $r) ?>><?= e($r) ?></option>
        <?php endforeach; ?>
    </select>
    <button class="btn" type="submit">Buscar</button>
    <a class="btn ghost" href="<?= e(url('reportes.index')) ?>">Limpiar</a>
</form>

<section class="stats">
    <h2>Estadísticas</h2>
    <div class="stats-grid">
        <div class="stat-card stat-card--chart">
            <div class="stat-card__header">
                <strong>Por sexo</strong>
                <span><?= e(count($por_sexo) > 0 ? array_sum(array_column($por_sexo, 'total')) : 0) ?> registros</span>
            </div>
            <?php $maxSexo = max(array_column($por_sexo, 'total')) ?: 1; ?>
            <?php foreach ($por_sexo as $index => $s): ?>
                <?php $value = (int) $s['total']; $percent = $maxSexo > 0 ? (int) round(($value / $maxSexo) * 100) : 0; $tone = $index % 3 === 0 ? 'primary' : ($index % 3 === 1 ? 'accent' : 'warning'); ?>
                <div class="stat-row">
                    <div class="stat-row__meta">
                        <span><?= e($s['sexo']) ?></span>
                        <strong><?= e($value) ?></strong>
                    </div>
                    <div class="stat-bar stat-bar--<?= e($tone) ?>">
                        <div class="stat-bar__fill stat-bar__fill--<?= e($tone) ?>" style="width: <?= e($percent) ?>%"></div>
                    </div>
                </div>
            <?php endforeach; ?>
        </div>
        <div class="stat-card stat-card--chart">
            <div class="stat-card__header">
                <strong>Por rango de edad</strong>
                <span><?= e(count($por_edad) > 0 ? array_sum(array_column($por_edad, 'total')) : 0) ?> registros</span>
            </div>
            <?php $maxEdad = max(array_column($por_edad, 'total')) ?: 1; ?>
            <?php foreach ($por_edad as $index => $e): ?>
                <?php $value = (int) $e['total']; $percent = $maxEdad > 0 ? (int) round(($value / $maxEdad) * 100) : 0; $tone = $index % 3 === 0 ? 'primary' : ($index % 3 === 1 ? 'accent' : 'warning'); ?>
                <div class="stat-row">
                    <div class="stat-row__meta">
                        <span><?= e($e['rango']) ?></span>
                        <strong><?= e($value) ?></strong>
                    </div>
                    <div class="stat-bar stat-bar--<?= e($tone) ?>">
                        <div class="stat-bar__fill stat-bar__fill--<?= e($tone) ?>" style="width: <?= e($percent) ?>%"></div>
                    </div>
                </div>
            <?php endforeach; ?>
        </div>
    </div>
</section>

<div class="table-wrap report-table">
    <table>
        <thead>
            <tr>
                <th>Foto</th>
                <th>Codigo</th>
                <th>Documento</th>
                <th>Nombre</th>
                <th>Edad</th>
                <th>Sexo</th>
                <th>Direccion</th>
                <th>Correo</th>
                <th>Celular</th>
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
        <?php foreach ($rows as $row): ?>
            <tr>
                <td>
                    <?php if (!empty($row['fotografia'])): ?>
                        <img src="<?= e($row['fotografia']) ?>" alt="Foto" class="report-thumb">
                    <?php else: ?>
                        <span class="muted">Sin foto</span>
                    <?php endif; ?>
                </td>
                <td><?= e($row['codigo_empleado']) ?></td>
                <td><?= e($row['identidad']) ?></td>
                <td><?= e($row['nombre_completo']) ?></td>
                <td><?= e($row['edad'] ?? 'N/A') ?></td>
                <td><?= e($row['sexo']) ?></td>
                <td><?= e($row['direccion'] ?? '') ?></td>
                <td><?= e($row['correo']) ?></td>
                <td><?= e($row['celular']) ?></td>
                <td><?= e($row['ocupacion']) ?></td>
                <td><?= e($row['tipo_empleado']) ?></td>
                <td><?= e($row['planilla']) ?></td>
                <td><?= e($row['departamento'] ?? '') ?></td>
                <td>B/. <?= e(number_format((float) $row['salario'], 2)) ?></td>
                <td><?= e($row['fecha_inicio']) ?></td>
                <td><?= e($row['fecha_fin'] ?? 'N/A') ?></td>
                <td><?= (int) $row['cargo_activo'] === 1 ? 'Si' : 'No' ?></td>
                <td><?= (int) $row['empleado_activo'] === 1 ? 'Si' : 'No' ?></td>
                <td><?= e($row['motivo_baja'] ?? 'No aplica') ?></td>
                <td>
                    <span class="badge <?= !empty($row['integrity_valid']) ? 'ok' : 'danger' ?>">
                        <?= e($row['integrity_message']) ?>
                    </span>
                </td>
            </tr>
        <?php endforeach; ?>
        <?php if (empty($rows)): ?>
            <tr><td colspan="20" class="empty">No hay registros para el reporte.</td></tr>
        <?php endif; ?>
        </tbody>
    </table>
</div>
<?php if ($paginacion['pages'] > 1): ?>
<div class="pagination">
    <?php for ($i = 1; $i <= $paginacion['pages']; $i++): ?>
        <?php if ($i === $paginacion['page']): ?>
            <span class="btn small active"><?= e($i) ?></span>
        <?php else: ?>
            <a class="btn small" href="<?= e(url('reportes.index', ['q' => $q, 'sexo' => $sexo, 'edad' => $edad, 'page' => $i])) ?>"><?= e($i) ?></a>
        <?php endif; ?>
    <?php endfor; ?>
</div>
<?php endif; ?>

