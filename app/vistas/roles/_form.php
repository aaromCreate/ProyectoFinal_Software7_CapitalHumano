<div class="role-form__panel">
    <div class="field">
        <label for="nombre">Nombre del rol</label>
        <input type="text" id="nombre" name="nombre" value="<?= e($old['nombre'] ?? $rol['nombre'] ?? '') ?>" required>
    </div>
    <div class="field">
        <label for="descripcion">Descripción</label>
        <input type="text" id="descripcion" name="descripcion"
            value="<?= e($old['descripcion'] ?? $rol['descripcion'] ?? '') ?>">
    </div>
    <div class="field">
        <label class="role-form__switch">
            <input type="checkbox" name="activo" value="1" <?= (int) ($old['activo'] ?? $rol['activo'] ?? 1) === 1 ? 'checked' : '' ?>>
            <span>Rol activo</span>
        </label>
    </div>
</div>

<div class="field">
    <label class="role-form__section-title">Permisos por módulo</label>
    <div class="permissions-grid">
        <?php foreach ($modulos as $modulo => $permisos): ?>
            <div class="permission-module">
                <div class="permission-module__title">
                    <strong><?= e(ucfirst($modulo)) ?></strong>
                    <span><?= e(count($permisos)) ?> permisos</span>
                </div>
                <div class="permission-module__items">
                    <?php foreach ($permisos as $permiso): ?>
                        <?php
                        $checked = '';
                        $selected = $old['permisos'] ?? $rol['permisos_ids'] ?? [];
                        if (in_array((int) $permiso['id'], array_map('intval', (array) $selected), true)) {
                            $checked = 'checked';
                        }
                        ?>
                        <label class="checkbox permission-item">
                            <input type="checkbox" name="permisos[]" value="<?= e($permiso['id']) ?>" <?= $checked ?>>
                            <span><?= e($permiso['accion']) ?></span>
                        </label>
                    <?php endforeach; ?>
                </div>
            </div>
        <?php endforeach; ?>
    </div>
</div>