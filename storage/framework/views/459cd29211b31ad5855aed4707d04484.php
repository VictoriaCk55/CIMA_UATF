<?php $__env->startSection('title', 'Permisos'); ?>

<?php $__env->startSection('content'); ?>
<div class="page-header">
    <h1>
        <i class="fas fa-shield-alt text-primary"></i> Gestión de Permisos
    </h1>
    <p class="page-subtitle">Administrar permisos del sistema (Spatie)</p>
</div>

<div class="card">
    <div class="card-header d-flex justify-content-between align-items-center">
        <span><i class="fas fa-list me-2"></i> Permisos</span>
        <a href="<?php echo e(route('permissions.create')); ?>" class="btn btn-sm btn-primary">
            <i class="fas fa-plus me-1"></i> Nuevo Permiso
        </a>
    </div>
    <div class="card-body">
        <div id="tablaPermisos">
            <?php echo $__env->make('permissions._tabla', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?>
        </div>
    </div>
</div>
<?php $__env->stopSection(); ?>

<?php $__env->startPush('scripts'); ?>
<script>
document.addEventListener('DOMContentLoaded', function () {
    var contenedor = document.getElementById('tablaPermisos');
    if (!contenedor) return;

    var timeout = null;

    contenedor.addEventListener('input', function (e) {
        if (!e.target || e.target.id !== 'buscarPermiso') return;

        clearTimeout(timeout);
        timeout = setTimeout(function () {
            var input = contenedor.querySelector('#buscarPermiso');
            var texto = input.value.trim();
            var url = '<?php echo e(route('permissions.index')); ?>' + (texto ? '?search=' + encodeURIComponent(texto) : '');

            fetch(url, {
                headers: {
                    'X-Requested-With': 'XMLHttpRequest',
                    'Accept': 'application/json'
                }
            })
                .then(function (respuesta) { return respuesta.json(); })
                .then(function (data) {
                    contenedor.innerHTML = data.html;
                });
        }, 400);
    });
});
</script>
<?php $__env->stopPush(); ?>

<?php echo $__env->make('layouts.app', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH D:\pruebaSystem\System-CIMA\application\resources\views/permissions/index.blade.php ENDPATH**/ ?>