<section class="login-section">
    <div class="login-card">
        <h1>Sistema de Capital Humano</h1>
        <p>Inicie sesion para continuar</p>

        <?php if (!empty($errors)): ?>
            <div class="alert alert-danger">
                <?php foreach ($errors as $error): ?>
                    <div><?= e($error) ?></div>
                <?php endforeach; ?>
            </div>
        <?php endif; ?>

        <form method="post" action="<?= e(url('login.process')) ?>">
            <?= csrf_field() ?>
            <div class="field">
                <label for="usuario">Usuario</label>
                <input type="text" id="usuario" name="usuario" value="<?= e($old['usuario'] ?? '') ?>" required autofocus>
            </div>
            <div class="field">
                <label for="password">Contraseña</label>
                <input type="password" id="password" name="password" required>
            </div>
            <button type="submit" class="btn primary w-full">Ingresar</button>
        </form>

        <div class="hint">
            <strong>Demo:</strong> admin / Admin123!
        </div>
    </div>
</section>
