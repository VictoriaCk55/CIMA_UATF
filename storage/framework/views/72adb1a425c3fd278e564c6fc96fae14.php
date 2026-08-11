<div class="row mb-3">
    <div class="col-md-5 col-lg-4 col-xl-3">
        <div class="input-group">
            <span class="input-group-text"><i class="fas fa-search"></i></span>
            <input type="text" id="buscarPermiso" class="form-control" placeholder="Buscar permiso..."
                   value="<?php echo e(request('search')); ?>" autocomplete="off">
        </div>
    </div>
</div>

<?php if($permissions->count() > 0): ?>
<div class="table-responsive">
    <table class="table table-hover">
        <thead>
            <tr>
                <th>ID</th>
                <th>Nombre</th>
                <th>Guard</th>
                <th>Roles asignados</th>
                <th>Acciones</th>
            </tr>
        </thead>
        <tbody>
            <?php $__currentLoopData = $permissions; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $perm): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
            <tr>
                <td><?php echo e($perm->id); ?></td>
                <td><code><?php echo e($perm->name); ?></code></td>
                <td><code><?php echo e($perm->guard_name); ?></code></td>
                <td>
                    <?php $__currentLoopData = $perm->roles; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $role): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                        <span class="badge bg-secondary me-1"><?php echo e($role->name); ?></span>
                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                    <?php if($perm->roles->count() === 0): ?>
                        <span class="text-muted">Sin asignar</span>
                    <?php endif; ?>
                </td>
                <td>
                    <a href="<?php echo e(route('permissions.edit', $perm)); ?>" class="btn btn-sm btn-warning" title="Editar">
                        <i class="fas fa-edit"></i>
                    </a>
                    <form action="<?php echo e(route('permissions.destroy', $perm)); ?>" method="POST" style="display: inline-block;" onsubmit="return confirm('¿Eliminar el permiso &quot;<?php echo e($perm->name); ?>&quot;?')">
                        <?php echo csrf_field(); ?>
                        <?php echo method_field('DELETE'); ?>
                        <button type="submit" class="btn btn-sm btn-danger" title="Eliminar">
                            <i class="fas fa-trash"></i>
                        </button>
                    </form>
                </td>
            </tr>
            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
        </tbody>
    </table>
</div>
<?php echo e($permissions->links()); ?>

<?php elseif(request()->filled('search')): ?>
<div class="alert alert-warning text-center mb-0">
    <i class="fas fa-search me-2"></i> No se encontraron permisos.
</div>
<?php else: ?>
<div class="alert alert-info text-center mb-0">
    <i class="fas fa-info-circle me-2"></i> No hay permisos registrados.
</div>
<?php endif; ?>
<?php /**PATH D:\pruebaSystem\System-CIMA\application\resources\views/permissions/_tabla.blade.php ENDPATH**/ ?>