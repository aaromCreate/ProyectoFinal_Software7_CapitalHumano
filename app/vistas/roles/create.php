<section class="section-head">
    <h1>Nuevo rol</h1>
    <p>Defina el perfil de acceso y los permisos que tendrá el rol dentro del sistema.</p>
</section>

<?php if (!empty($errors)): ?>
    <div class="alert alert-danger">
        <?php foreach ($errors as $error): ?>
            <div><?= e($error) ?></div>
        <?php endforeach; ?>
    </div>
<?php endif; ?>

<form method="post" action="<?= e(url('roles.store')) ?>" class="role-form-card">
    <?= csrf_field() ?>
    <?php require __DIR__ . '/_form.php'; ?>
    <div class="role-form__actions">
        <a href="<?= e(url('roles.index')) ?>" class="btn ghost">Cancelar</a>
        <button type="submit" class="btn primary">Guardar rol</button>
    </div>
</form>