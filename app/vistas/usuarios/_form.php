<div class="user-form__panel">
    <div class="field">
        <label for="nombre">Nombre completo</label>
        <input type="text" id="nombre" name="nombre" value="<?= e($old['nombre'] ?? $usuario['nombre'] ?? '') ?>"
            required>
    </div>
    <div class="field">
        <label for="correo">Correo</label>
        <input type="email" id="correo" name="correo" value="<?= e($old['correo'] ?? $usuario['correo'] ?? '') ?>"
            required>
    </div>
    <div class="field">
        <label for="usuario">Usuario</label>
        <input type="text" id="usuario" name="usuario" value="<?= e($old['usuario'] ?? $usuario['usuario'] ?? '') ?>"
            required>
    </div>
    <div class="field">
        <label for="password">Contraseña <?= empty($usuario) ? '' : '(dejar en blanco para no cambiar)' ?></label>
        <input type="password" id="password" name="password" <?= empty($usuario) ? 'required' : '' ?>>
    </div>
</div>

<div class="field">
    <label class="user-form__section-title">Roles y estado</label>
    <div class="user-form__roles-grid">
        <?php foreach ($roles as $rol): ?>
            <?php
            $checked = '';
            $selected = $old['roles'] ?? $usuario['roles_ids'] ?? [];
            if (in_array((int) $rol['id'], array_map('intval', (array) $selected), true)) {
                $checked = 'checked';
            }
            ?>
            <label class="checkbox user-form__role-item">
                <input type="checkbox" name="roles[]" value="<?= e($rol['id']) ?>" <?= $checked ?>>
                <span><?= e($rol['nombre']) ?></span>
            </label>
        <?php endforeach; ?>
    </div>
</div>

<div class="field">
    <label class="user-form__switch">
        <input type="checkbox" name="activo" value="1" <?= (int) ($old['activo'] ?? $usuario['activo'] ?? 1) === 1 ? 'checked' : '' ?>>
        <span>Usuario activo</span>
    </label>
</div>