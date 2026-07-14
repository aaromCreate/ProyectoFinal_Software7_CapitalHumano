<section class="section-head">
    <h1>Nuevo usuario</h1>
    <p>Complete los datos del usuario administrativo.</p>
</section>

<?php if (!empty($errors)): ?>
    <div class="alert alert-danger">
        <?php foreach ($errors as $error): ?>
            <div><?= e($error) ?></div>
        <?php endforeach; ?>
    </div>
<?php endif; ?>

<form method="post" action="<?= e(url('usuarios.store')) ?>" class="user-form-card">
    <?= csrf_field() ?>
    <?php require __DIR__ . '/_form.php'; ?>
    <div class="user-form__actions">
        <a href="<?= e(url('usuarios.index')) ?>" class="btn">Cancelar</a>
        <button type="submit" class="btn primary">Guardar</button>
    </div>
</form>