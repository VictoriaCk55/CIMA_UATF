<?php $__env->startSection('title', 'Roles'); ?>

<?php $__env->startSection('content'); ?>
<div class="page-header">
    <h1>
        <i class="fas fa-user-tag text-primary"></i> Gestión de Roles
    </h1>
    <p class="page-subtitle">Administrar roles del sistema (Spatie)</p>
</div>

<div class="card">
    <div class="card-header d-flex justify-content-between align-items-center">
        <span><i class="fas fa-list me-2"></i> Roles</span>
        <a href="<?php echo e(route('roles.create')); ?>" class="btn btn-sm btn-primary">
            <i class="fas fa-plus me-1"></i> Nuevo Rol
        </a>
    </div>
    <div class="card-body">
        <?php if($roles->count() > 0): ?>
        <div class="table-responsive">
            <table class="table table-hover">
                <thead>
                    <tr>
                        <th>ID</th>
                        <th>Nombre</th>
                        <th>Guard</th>
                        <th>Permisos</th>
                        <th>Acciones</th>
                    </tr>
                </thead>
                <tbody>
                    <?php $__currentLoopData = $roles; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $role): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                    <tr>
                        <td><?php echo e($role->id); ?></td>
                        <td>
                            <?php if($role->name === 'admin'): ?>
                                <span class="badge bg-danger"><?php echo e($role->name); ?></span>
                            <?php elseif($role->name === 'tecnico'): ?>
                                <span class="badge bg-info"><?php echo e($role->name); ?></span>
                            <?php elseif($role->name === 'analista'): ?>
                                <span class="badge bg-success"><?php echo e($role->name); ?></span>
                            <?php else: ?>
                                <span class="badge bg-secondary"><?php echo e($role->name); ?></span>
                            <?php endif; ?>
                        </td>
                        <td><code><?php echo e($role->guard_name); ?></code></td>
                        <td>
                            <?php $__currentLoopData = $role->permissions; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $perm): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                <span class="badge bg-light text-dark me-1"><?php echo e($perm->name); ?></span>
                            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                            <?php if($role->permissions->count() === 0): ?>
                                <span class="text-muted">Sin permisos</span>
                            <?php endif; ?>
                        </td>
                        <td>
                            <a href="<?php echo e(route('roles.edit', $role)); ?>" class="btn btn-sm btn-warning" title="Editar">
                                <i class="fas fa-edit"></i>
                            </a>
                            <?php if($role->name !== 'admin'): ?>
                            <form action="<?php echo e(route('roles.destroy', $role)); ?>" method="POST" style="display: inline-block;" onsubmit="return confirm('¿Eliminar el rol &quot;<?php echo e($role->name); ?>&quot;? Los usuarios con este rol quedarán sin rol.')">
                                <?php echo csrf_field(); ?>
                                <?php echo method_field('DELETE'); ?>
                                <button type="submit" class="btn btn-sm btn-danger" title="Eliminar">
                                    <i class="fas fa-trash"></i>
                                </button>
                            </form>
                            <?php endif; ?>
                        </td>
                    </tr>
                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                </tbody>
            </table>
        </div>
        <?php echo e($roles->links()); ?>

        <?php else: ?>
        <div class="alert alert-info text-center mb-0">
            <i class="fas fa-info-circle me-2"></i> No hay roles registrados.
        </div>
        <?php endif; ?>
    </div>
</div>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.app', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH D:\pruebaSystem\System-CIMA\application\resources\views/roles/index.blade.php ENDPATH**/ ?>