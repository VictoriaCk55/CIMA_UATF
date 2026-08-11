<?php $__env->startSection('title', 'Editar Permiso'); ?>

<?php $__env->startSection('content'); ?>
<div class="page-header">
    <h1>
        <i class="fas fa-shield-alt text-warning"></i> Editar Permiso
    </h1>
    <p class="page-subtitle">Modificar permiso y asignación a roles</p>
</div>

<div class="card">
    <div class="card-header">
        <i class="fas fa-edit me-2"></i> Permiso: <?php echo e($permission->name); ?>

    </div>
    <div class="card-body">
        <form action="<?php echo e(route('permissions.update', $permission)); ?>" method="POST">
            <?php echo csrf_field(); ?>
            <?php echo method_field('PUT'); ?>

            <div class="row mb-4">
                <div class="col-md-6 mb-3">
                    <label for="name" class="form-label">Nombre del permiso *</label>
                    <input type="text" class="form-control <?php $__errorArgs = ['name'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> is-invalid <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>"
                           id="name" name="name" value="<?php echo e(old('name', $permission->name)); ?>" required>
                    <?php $__errorArgs = ['name'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?>
                        <div class="invalid-feedback"><?php echo e($message); ?></div>
                    <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>
                </div>
            </div>

            <h5 class="mb-3"><i class="fas fa-users me-2"></i> Asignar a roles</h5>
            <div class="row mb-3">
                <?php $__currentLoopData = $roles; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $role): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                <div class="col-md-4 mb-2">
                    <div class="form-check">
                        <input type="checkbox" name="roles[]" value="<?php echo e($role->id); ?>"
                               id="role_<?php echo e($role->id); ?>"
                               class="form-check-input"
                               <?php echo e(in_array($role->id, old('roles', $assignedRoles)) ? 'checked' : ''); ?>>
                        <label class="form-check-label" for="role_<?php echo e($role->id); ?>">
                            <?php echo e($role->name); ?>

                        </label>
                    </div>
                </div>
                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
            </div>

            <div class="mt-4">
                <button type="submit" class="btn btn-primary">
                    <i class="fas fa-save me-1"></i> Actualizar Permiso
                </button>
                <a href="<?php echo e(route('permissions.index')); ?>" class="btn btn-secondary">
                    <i class="fas fa-times me-1"></i> Cancelar
                </a>
            </div>
        </form>
    </div>
</div>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.app', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH D:\pruebaSystem\System-CIMA\application\resources\views/permissions/edit.blade.php ENDPATH**/ ?>