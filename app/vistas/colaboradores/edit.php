<section class="section-head">
    <h1>Editar colaborador</h1>
    <p><?= e(($colaborador['primer_nombre'] ?? '') . ' ' . ($colaborador['primer_apellido'] ?? '')) ?></p>
</section>
<?php
$action = url('colaboradores.update', ['id' => $colaborador['id']]);
$button = 'Actualizar colaborador';
require __DIR__ . DIRECTORY_SEPARATOR . '_form.php';
?>
