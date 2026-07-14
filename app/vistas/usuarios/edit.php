<section class="section-head">
    <h1>Editar usuario</h1>
    <p>Modifique los datos o roles del usuario.</p>
</section>

<?php if (!empty($errors)): ?>
    <div class="alert alert-danger">
        <?php foreach ($errors as $error): ?>
            <div><?= e($error) ?></div>
        <?php endforeach; ?>
    </div>
<?php endif; ?>

<form method="post" action="<?= e(url('usuarios.update', ['id' => $usuario['id']])) ?>" class="form-card">
    <?= csrf_field() ?>
    <?php require __DIR__ . '/_form.php'; ?>
    <div class="actions">
        <a href="<?= e(url('usuarios.index')) ?>" class="btn">Cancelar</a>
        <button type="submit" class="btn primary">Actualizar</button>
    </div>
</form>
