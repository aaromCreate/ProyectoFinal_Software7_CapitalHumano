<section class="section-head">
    <h1>Nuevo colaborador</h1>
    <p>Registra los datos personales, contacto y perfil laboral inicial.</p>
</section>
<?php
$colaborador = null;
$action = url('colaboradores.store');
$button = 'Guardar colaborador';
require __DIR__ . DIRECTORY_SEPARATOR . '_form.php';
?>
