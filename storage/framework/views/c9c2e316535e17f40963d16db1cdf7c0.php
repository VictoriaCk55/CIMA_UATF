<?php $__env->startSection('content'); ?>
<div class="container-main">
    <!-- Encabezado de página con más espacio -->
    <div class="page-header mt-3">
        <div class="d-flex justify-content-between align-items-center">
            <div>
                <h1>
                    <i class="fas fa-microscope me-2" style="color: #A31800;"></i>
                    Detalles del Parámetro
                </h1>
                <p class="page-subtitle mt-2">
                    Información completa del parámetro de análisis
                </p>
            </div>
            <a href="<?php echo e(route('parametros.index')); ?>" class="btn btn-outline-secondary btn-volver" style="border-radius: 30px; padding: 8px 20px;">
                    <i class="fas fa-arrow-left me-2"></i>
                    Volver al listado
            </a>
        </div>
    </div>

    <!-- Información principal -->
    <div class="row">
        <div class="col-md-8">
            <div class="card mb-4">
                <div class="card-header" style="background-color: #A31800; border-bottom: none;">
                    <h5 class="mb-0 text-white">
                        <i class="fas fa-id-card me-2"></i>
                        Información del Parámetro
                    </h5>
                </div>
                <div class="card-body">
                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label class="form-label text-muted small">Nombre del Parámetro</label>
                            <p class="fs-5 fw-semibold"><?php echo e($parametro->nombre); ?></p>
                        </div>

                        <?php if($parametro->nombre_completo): ?>
                        <div class="col-md-6 mb-3">
                            <label class="form-label text-muted small">Nombre Completo</label>
                            <p class="fs-5"><?php echo e($parametro->nombre_completo); ?></p>
                        </div>
                        <?php endif; ?>

                        <div class="col-md-6 mb-3">
                            <label class="form-label text-muted small">Método de Análisis</label>
                            <p class="fs-5">
                                <i class="fas fa-microscope me-2" style="color: #A31800;"></i>
                                <?php echo e($parametro->metodo); ?>

                            </p>
                        </div>

                        <?php if($parametro->descripcion): ?>
                        <div class="col-md-6 mb-3">
                            <label class="form-label text-muted small">Descripción</label>
                            <p class="fs-5"><?php echo e($parametro->descripcion); ?></p>
                        </div>
                        <?php endif; ?>

                        <div class="col-md-6 mb-3">
                            <label class="form-label text-muted small">Precio Unitario</label>
                            <p class="fs-5 fw-bold text-success">
                                <i class="fas fa-dollar-sign me-2"></i>
                                Bs. <?php echo e(number_format($parametro->precio_unitario, 2)); ?>

                            </p>
                        </div>

                        <div class="col-md-6 mb-3">
                            <label class="form-label text-muted small">Tipo de Análisis</label>
                            <p class="fs-5">
                                <span class="badge 
                                    <?php if($parametro->tipo == 'AMBIENTAL'): ?> bg-warning text-dark
                                    <?php elseif($parametro->tipo == 'AGUA'): ?> bg-info
                                    <?php else: ?> bg-secondary
                                    <?php endif; ?> fs-6">
                                    <i class="fas 
                                        <?php if($parametro->tipo == 'AMBIENTAL'): ?> fa-leaf
                                        <?php elseif($parametro->tipo == 'AGUA'): ?> fa-tint
                                        <?php else: ?> fa-flask
                                        <?php endif; ?> me-1"></i>
                                    <?php echo e($parametro->tipo); ?>

                                </span>
                            </p>
                        </div>

                        <?php if($parametro->categoria): ?>
                        <div class="col-md-6 mb-3">
                            <label class="form-label text-muted small">Categoría</label>
                            <p class="fs-5"><span class="badge bg-secondary fs-6"><?php echo e($parametro->categoria); ?></span></p>
                        </div>
                        <?php endif; ?>

                        <?php if($parametro->unidad): ?>
                        <div class="col-md-6 mb-3">
                            <label class="form-label text-muted small">Unidad</label>
                            <p class="fs-5"><?php echo e($parametro->unidad); ?></p>
                        </div>
                        <?php endif; ?>

                        <?php if($parametro->limite_cuantificacion): ?>
                        <div class="col-md-6 mb-3">
                            <label class="form-label text-muted small">Límite de Cuantificación</label>
                            <p class="fs-5"><?php echo e($parametro->limite_cuantificacion); ?></p>
                        </div>
                        <?php endif; ?>

                        <?php if($parametro->codigo_poe): ?>
                        <div class="col-md-6 mb-3">
                            <label class="form-label text-muted small">Código POE</label>
                            <p class="fs-5"><?php echo e($parametro->codigo_poe); ?></p>
                        </div>
                        <?php endif; ?>

                        <?php if($parametro->tecnica): ?>
                        <div class="col-md-6 mb-3">
                            <label class="form-label text-muted small">Técnica</label>
                            <p class="fs-5"><?php echo e($parametro->tecnica); ?></p>
                        </div>
                        <?php endif; ?>

                        <?php if($parametro->matriz): ?>
                        <div class="col-md-6 mb-3">
                            <label class="form-label text-muted small">Matriz</label>
                            <p class="fs-5"><?php echo e($parametro->matriz); ?></p>
                        </div>
                        <?php endif; ?>

                        <?php if($parametro->tipo_medicion): ?>
                        <div class="col-md-6 mb-3">
                            <label class="form-label text-muted small">Tipo de Medición</label>
                            <p class="fs-5"><?php echo e($parametro->tipo_medicion); ?></p>
                        </div>
                        <?php endif; ?>

                        <div class="col-md-12 mb-3">
                            <label class="form-label text-muted small">ID del Parámetro</label>
                            <p>
                                <span class="badge bg-secondary fs-6">#<?php echo e($parametro->id); ?></span>
                            </p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        
        <div class="col-md-4">
            <!-- Información adicional -->
            <div class="card mb-4">
                <div class="card-header" style="background-color: #A31800; border-bottom: none;">
                    <h5 class="mb-0 text-white">
                        <i class="fas fa-info-circle me-2"></i>
                        Información Adicional
                    </h5>
                </div>
                <div class="card-body">
                    <div class="mb-3">
                        <label class="form-label text-muted small">Fecha de Registro</label>
                        <p>
                            <i class="far fa-calendar-plus me-2" style="color: #A31800;"></i>
                            <?php echo e($parametro->created_at->format('d/m/Y H:i')); ?>

                        </p>
                    </div>
                    
                    <div class="mb-3">
                        <label class="form-label text-muted small">Última Actualización</label>
                        <p>
                            <i class="far fa-calendar-check me-2" style="color: #A31800;"></i>
                            <?php echo e($parametro->updated_at->format('d/m/Y H:i')); ?>

                        </p>
                    </div>
                    
                    <div class="mb-3">
                        <label class="form-label text-muted small">Estado en el Sistema</label>
                        <div class="d-flex align-items-center">
                            <i class="fas fa-check-circle text-success me-2"></i>
                            <span>Activo y disponible para proformas</span>
                        </div>
                    </div>
                </div>
            </div>
            
            <!-- Acciones -->
            <?php if(auth()->guard()->check()): ?>
                <?php if(Auth::user()->hasAnyRole(['admin', 'tecnico', 'analista'])): ?>
                    <div class="card">
                        <div class="card-header" style="background-color: #A31800; border-bottom: none;">
                            <h5 class="mb-0 text-white">
                                <i class="fas fa-cogs me-2"></i>
                                Acciones
                            </h5>
                        </div>
                        <div class="card-body">
                            <div class="d-grid gap-2">
                                <?php if (app(\Illuminate\Contracts\Auth\Access\Gate::class)->check('editar parametros')): ?>
                                <a href="<?php echo e(route('parametros.edit', $parametro)); ?>" 
                                class="btn"
                       style="color: #000000; border: 2px solid #ffc107; background-color: transparent; border-radius: 30px; padding: 10px 25px; transition: all 0.3s ease; font-weight: 500;"
                       onmouseover="this.style.backgroundColor='#ffc107'; this.style.color='#000000'; this.style.borderColor='#ffc107';"
                       onmouseout="this.style.backgroundColor='transparent'; this.style.color='#000000'; this.style.borderColor='#ffc107';">
                        <i class="fas fa-edit me-2"></i>
                        Editar Parametro
                                </a>
                                <?php endif; ?>
                                
                                <?php if (app(\Illuminate\Contracts\Auth\Access\Gate::class)->check('eliminar parametros')): ?>
                                <button type="button" 
                                        class="btn btn-outline-danger" 
                                        style="border-radius: 30px; padding: 10px 25px;"
                                        onclick="confirmarEliminacion(<?php echo e($parametro->id); ?>, '<?php echo e($parametro->nombre); ?>', 'parámetro')">
                                    <i class="fas fa-trash me-2"></i>
                                    Eliminar Parámetro
                                </button>
                                <?php endif; ?>
                                
                                <!-- Formulario oculto para eliminar -->
                                <form id="delete-form-<?php echo e($parametro->id); ?>" 
                                      action="<?php echo e(route('parametros.destroy', $parametro)); ?>" 
                                      method="POST" class="d-none">
                                    <?php echo csrf_field(); ?>
                                    <?php echo method_field('DELETE'); ?>
                                </form>
                            </div>
                        </div>
                    </div>
                <?php endif; ?>
            <?php endif; ?>
        </div>
    </div>
    
    <!-- Advertencia importante - AHORA PERMANENTE (sin clase alert alert-warning que se auto-cierra) -->
    <div class="card mt-4 border-warning">
        <div class="card-header bg-warning text-white">
            <h6 class="mb-0">
                <i class="fas fa-exclamation-triangle me-2"></i>
                Advertencia Importante
            </h6>
        </div>
        <div class="card-body">
            <div class="mb-0">
                <i class="fas fa-info-circle me-2" style="color: #A31800;"></i>
                <strong>Nota:</strong> La eliminación de este parámetro lo removerá de todas las proformas donde esté asignado. 
                Se recomienda editar en lugar de eliminar, a menos que sea estrictamente necesario.
            </div>
        </div>
    </div>
</div>

<!-- Estilos adicionales específicos para la página de detalles -->
<style>

/* Estilo para el botón de editar */
.btn[style*="background-color: #A31800"]:hover {
    background-color: #7a1200 !important;
    transform: translateY(-2px);
    box-shadow: 0 5px 15px rgba(163, 24, 0, 0.3);
}

/* Estilo para el botón de eliminar */
.btn-outline-danger {
    border-radius: 30px !important;
    padding: 10px 25px !important;
    transition: all 0.3s ease !important;
}

.btn-outline-danger:hover {
    transform: translateY(-2px) !important;
    box-shadow: 0 5px 15px rgba(220, 53, 69, 0.3) !important;
}

.fa-microscope{
    color: #A31800!important;
}

.btn-volver {
    color: #000000 !important;
    border: 2px solid #ffffff !important;
    background-color: #ffffff !important;
    transition: all 0.3s ease !important;
    font-weight: 500 !important;
    box-shadow: 0 2px 4px rgba(0,0,0,0.05) !important;
}

.btn-volver:hover {
    background-color: #ffffff !important;
    color: #000000 !important;
    border-color: #ffffff !important;
    transform: translateY(-2px) !important;
    box-shadow: 0 8px 20px rgba(128, 128, 128, 0.3) !important; /* Sombra gris más pronunciada */
}

@media (max-width: 768px) {
    /* Reorganizar el encabezado en móvil */
    .page-header .d-flex {
        flex-direction: column !important;
        align-items: flex-start !important;
        gap: 15px !important;
    }
    
    /* El botón volver ocupa todo el ancho en móvil */
    .page-header .btn-volver {
        width: 100% !important;
        justify-content: center !important;
        margin-top: 10px !important;
    }
    
    /* Ajustar el título y badge */
    .d-flex.align-items-center.gap-3 {
        flex-wrap: wrap !important;
        gap: 10px !important;
    }
    
    .d-flex.align-items-center.gap-3 h1 {
        font-size: 1.5rem !important;
        width: 100% !important;
    }
    
    /* Badge ocupa su espacio */
    .badge.fs-6 {
        font-size: 0.85rem !important;
        padding: 5px 12px !important;
    }
    
    /* Ajustar columnas en móvil */
    .col-md-8, .col-md-4 {
        width: 100% !important;
    }
    
    /* Botones en el panel lateral */
    .d-grid.gap-2 .btn {
        width: 100% !important;
        margin-bottom: 5px !important;
    }
    
    /* Ajustar tablas en móvil */
    .table-responsive {
        overflow-x: auto !important;
    }
    
    /* Ajustar texto en tarjetas */
    .card-body .row .col-md-6 {
        width: 100% !important;
    }
    
    /* Ajustar iconos */
    .fa-2x {
        font-size: 1.5rem !important;
    }
}

/* Ajuste para tablets */
@media (min-width: 769px) and (max-width: 991px) {
    .col-md-8, .col-md-4 {
        width: 100% !important;
    }
}
</style>
<?php $__env->stopSection(); ?>
<?php echo $__env->make('layouts.app', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH D:\pruebaSystem\System-CIMA\application\resources\views/parametros/show.blade.php ENDPATH**/ ?>