<section class="section-head">
    <h1>Editar rol</h1>
    <p>Modifique los permisos del rol.</p>
</section>

<?php if (!empty($errors)): ?>
    <div class="alert alert-danger">
        <?php foreach ($errors as $error): ?>
            <div><?= e($error) ?></div>
        <?php endforeach; ?>
    </div>
<?php endif; ?>

<form method="post" action="<?= e(url('roles.update', ['id' => $rol['id']])) ?>" class="form-card">
    <?= csrf_field() ?>
    <?php require __DIR__ . '/_form.php'; ?>
    <div class="actions">
        <a href="<?= e(url('roles.index')) ?>" class="btn">Cancelar</a>
        <button type="submit" class="btn primary">Actualizar</button>
    </div>
</form>
