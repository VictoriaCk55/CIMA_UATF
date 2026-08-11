<?php $__env->startSection('content'); ?>
<div class="container-main">
    <!-- Encabezado de página -->
    <div class="page-header">
        <div class="d-flex justify-content-between align-items-center">
            <div>
                <h1>
                    <i class="fas fa-microscope me-2" style="color: #A31800;"></i>
                    Gestión de Parámetros
                </h1>
                <p class="page-subtitle">
                    Catálogo de parámetros de análisis para proformas CIMA
                </p>
            </div>
            
            <div class="d-flex gap-2">
                <?php if(auth()->guard()->check()): ?>
                     <?php if (app(\Illuminate\Contracts\Auth\Access\Gate::class)->check('crear parametros')): ?>
                        <a href="<?php echo e(route('parametros.create')); ?>" class="btn" style="background-color: #A31800; border-radius: 30px; padding: 10px 25px; color: white; border: none; transition: all 0.3s ease;">
                            <i class="fas fa-plus-circle"></i>
                            Nuevo Parámetro
                        </a>
                    <?php else: ?>
                        <div class="alert alert-info mb-0 py-2 px-3">
                            <i class="fas fa-eye me-1"></i> Modo solo lectura
                        </div>
                    <?php endif; ?>
                <?php endif; ?>
            </div>
        </div>
    </div>

    <!-- BUSCADOR -->
    <div class="card mb-4">
        <div class="card-body">
            <form action="<?php echo e(route('parametros.index')); ?>" method="GET" id="searchForm">
                <div class="row align-items-end">
                    <div class="col-md-8">
                        <label for="search" class="form-label fw-semibold">
                            <i class="fas fa-search me-2" style="color: #A31800;"></i>
                            Buscar Parámetro
                        </label>
                        <div class="input-group">
                            <span class="input-group-text" style="background-color: #A31800; color: white; border: none;">
                                <i class="fas fa-search"></i>
                            </span>
                            <input type="text" 
                                   class="form-control" 
                                   id="search" 
                                   name="search" 
                                   value="<?php echo e(request('search')); ?>" 
                                   placeholder="Buscar por ID, Nombre, Método o Tipo..."
                                   style="border-left: none;">
                            <button class="btn" type="submit" style="background-color: #A31800; color: white; border: none;">
                                <i class="fas fa-filter me-1"></i> Filtrar
                            </button>
                            <?php if(request('search')): ?>
                                <a href="<?php echo e(route('parametros.index')); ?>" class="btn btn-outline-secondary" style="border-radius: 0 30px 30px 0;">
                                    <i class="fas fa-times"></i> Limpiar
                                </a>
                            <?php endif; ?>
                        </div>
                        <small class="text-muted mt-1 d-block">
                            <i class="fas fa-info-circle me-1"></i>
                            Puede buscar por ID, nombre del parámetro, método de análisis o tipo.
                        </small>
                    </div>
                    
                    <div class="col-md-4 text-md-end mt-3 mt-md-0">
                        <div class="d-flex align-items-center justify-content-md-end">
                            <span class="badge" style="background-color: #A31800; color: white; padding: 8px 15px; border-radius: 20px; font-weight: 500;">
                                <i class="fas fa-database me-1"></i> Total: <?php echo e($parametros->total()); ?> parámetros
                            </span>
                        </div>
                    </div>
                </div>
            </form>
        </div>
    </div>

    <!-- Tabla de parámetros -->
    <div class="card">
        <div class="card-header d-flex justify-content-between align-items-center">
            <h5 class="mb-0">
                <i class="fas fa-list me-2" style="color: #A31800;"></i>
                Listado de Parámetros
            </h5>
            <span class="badge" style="background-color: #A31800; color: white; padding: 8px 15px; border-radius: 20px; font-weight: 500;">
                <?php echo e($parametros->count()); ?> registros mostrados
            </span>
        </div>
        <div class="card-body">
            <?php if($parametros->count() > 0): ?>
                <div class="table-responsive">
                    <table class="table table-hover">
                        <thead>
                            <tr style="background-color: #A31800; color: white;">
                                <th width="80" style="background-color: #A31800; color: white; font-weight: 600; text-transform: uppercase; font-size: 0.85rem; letter-spacing: 0.5px; padding: 12px 15px;">ID</th>
                                <th style="background-color: #A31800; color: white; font-weight: 600; text-transform: uppercase; font-size: 0.85rem; letter-spacing: 0.5px; padding: 12px 15px;">Parámetro</th>
                                <th style="background-color: #A31800; color: white; font-weight: 600; text-transform: uppercase; font-size: 0.85rem; letter-spacing: 0.5px; padding: 12px 15px;">Método</th>
                                <th width="140" class="text-end" style="background-color: #A31800; color: white; font-weight: 600; text-transform: uppercase; font-size: 0.85rem; letter-spacing: 0.5px; padding: 12px 15px;">Precio Unitario</th>
                                <th width="120" style="background-color: #A31800; color: white; font-weight: 600; text-transform: uppercase; font-size: 0.85rem; letter-spacing: 0.5px; padding: 12px 15px;">Tipo</th>
                                <th width="100" class="text-center" style="background-color: #A31800; color: white; font-weight: 600; text-transform: uppercase; font-size: 0.85rem; letter-spacing: 0.5px; padding: 12px 15px;">Proformas</th>
                                <th width="140" class="text-center" style="background-color: #A31800; color: white; font-weight: 600; text-transform: uppercase; font-size: 0.85rem; letter-spacing: 0.5px; padding: 12px 15px;">Acciones</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php $__currentLoopData = $parametros; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $parametro): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                <?php
                                    $totalProformas = $parametro->proformas->count();
                                ?>
                                <tr>
                                    <td>
                                        <span class="badge bg-secondary">#<?php echo e($parametro->id); ?></span>
                                    </td>
                                    <td>
                                        <strong><?php echo e($parametro->nombre); ?></strong>
                                    </td>
                                    <td>
                                        <small class="text-muted">
                                            <i class="fas fa-microscope me-1"></i>
                                            <?php echo e($parametro->metodo); ?>

                                        </small>
                                    </td>
                                    <td class="text-end fw-semibold text-success">
                                        <i class="fas fa-dollar-sign me-1"></i>
                                        <?php echo e(number_format($parametro->precio_unitario, 2)); ?>

                                    </td>
                                    <td>
                                        <span class="badge 
                                            <?php if($parametro->tipo == 'AMBIENTAL'): ?> bg-warning text-dark
                                            <?php elseif($parametro->tipo == 'AGUA'): ?> bg-info
                                            <?php else: ?> bg-secondary
                                            <?php endif; ?>">
                                            <i class="fas 
                                                <?php if($parametro->tipo == 'AMBIENTAL'): ?> fa-leaf
                                                <?php elseif($parametro->tipo == 'AGUA'): ?> fa-tint
                                                <?php else: ?> fa-flask
                                                <?php endif; ?> me-1"></i>
                                            <?php echo e($parametro->tipo); ?>

                                        </span>
                                    </td>
                                    <td class="text-center">
                                        <?php if($totalProformas > 0): ?>
                                            <span class="badge" style="background-color: #A31800; color: white; padding: 6px 12px; border-radius: 20px;">
                                                <i class="fas fa-file-invoice me-1"></i>
                                                <?php echo e($totalProformas); ?>

                                            </span>
                                        <?php else: ?>
                                            <span class="badge bg-secondary" style="padding: 6px 12px; border-radius: 20px;">
                                                <i class="fas fa-file-invoice me-1"></i>
                                                0
                                            </span>
                                        <?php endif; ?>
                                    </td>
                                    <td class="text-center">
                                        <div class="btn-group" role="group">
                                            <!-- Botón VER en color CELESTE -->
                                            <a href="<?php echo e(route('parametros.show', $parametro)); ?>" 
                                               class="btn btn-sm"
                                               style="color: #0dcaf0; border: 1px solid #0dcaf0; background: transparent; border-radius: 6px; padding: 0.5rem; width: 38px; height: 38px; transition: all 0.2s ease; display: inline-flex; align-items: center; justify-content: center;"
                                               data-bs-toggle="tooltip" 
                                               data-bs-placement="top"
                                               title="Ver detalles del parámetro"
                                               onmouseover="this.style.backgroundColor='#0dcaf0'; this.style.color='white';"
                                               onmouseout="this.style.backgroundColor='transparent'; this.style.color='#0dcaf0';">
                                                <i class="fas fa-eye"></i>
                                            </a>
                                            
                                            <?php if(auth()->guard()->check()): ?>
                                                <?php if(Auth::user()->hasAnyRole(['admin', 'tecnico', 'analista'])): ?>
                                                    <?php if (app(\Illuminate\Contracts\Auth\Access\Gate::class)->check('editar parametros')): ?>
                                                        <a href="<?php echo e(route('parametros.edit', $parametro)); ?>" 
                                                           class="btn btn-outline-warning btn-sm"
                                                           data-bs-toggle="tooltip" 
                                                           data-bs-placement="top"
                                                           title="Editar parámetro">
                                                            <i class="fas fa-edit"></i>
                                                    <?php endif; ?>
                                                    </a>
                                                    <?php if (app(\Illuminate\Contracts\Auth\Access\Gate::class)->check('eliminar parametros')): ?>
                                                    <button type="button" 
                                                            class="btn btn-outline-danger btn-sm"
                                                            data-bs-toggle="tooltip" 
                                                            data-bs-placement="top"
                                                            title="Eliminar parámetro"
                                                            onclick="confirmarEliminacion(<?php echo e($parametro->id); ?>, '<?php echo e($parametro->nombre); ?>', 'parámetro')">
                                                        <i class="fas fa-trash"></i>
                                                    </button>
                                                    <?php endif; ?>
                                                <?php endif; ?>
                                            <?php endif; ?>
                                        </div>
                                        
                                        <!-- Formulario oculto para eliminar -->
                                        <?php if(auth()->guard()->check()): ?>
                                            <?php if(Auth::user()->hasAnyRole(['admin', 'tecnico', 'analista'])): ?>
                                                <form id="delete-form-<?php echo e($parametro->id); ?>" 
                                                      action="<?php echo e(route('parametros.destroy', $parametro)); ?>" 
                                                      method="POST" class="d-none">
                                                    <?php echo csrf_field(); ?>
                                                    <?php echo method_field('DELETE'); ?>
                                                </form>
                                            <?php endif; ?>
                                        <?php endif; ?>
                                    </td>
                                </tr>
                            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                        </tbody>
                    </table>
                </div>

                <?php if($parametros->hasPages()): ?>
                    <div class="d-flex justify-content-center mt-4">
                        <?php echo e($parametros->appends(request()->query())->links()); ?>

                    </div>
                <?php endif; ?>
                
                <!-- Botón de Papelera y texto de registros centrado -->
                <div class="d-flex align-items-center justify-content-center position-relative mt-3">
                    <?php if(auth()->guard()->check()): ?>
                        <?php if (app(\Illuminate\Contracts\Auth\Access\Gate::class)->check('ver papelera parametros')): ?>
                            <a href="<?php echo e(route('parametros.trash')); ?>" 
                               class="btn btn-icon-circle position-absolute start-0"
                               style="width: 35px; height: 35px; border-radius: 50%; background-color: #6c757d; color: white; display: inline-flex; align-items: center; justify-content: center; transition: all 0.3s ease; text-decoration: none;"
                               data-bs-toggle="tooltip"
                               title="Ver parámetros eliminados"
                               onmouseover="this.style.backgroundColor='#5a6268'; this.style.transform='scale(1.1)';"
                               onmouseout="this.style.backgroundColor='#6c757d'; this.style.transform='scale(1)';">
                                <i class="fas fa-trash-alt" style="font-size: 1rem;"></i>
                            </a>
                        <?php endif; ?>
                    <?php endif; ?>
                    
                    <div style="color: #A31800; font-weight: 500;">
                        <i class="fas fa-database me-1"></i> 
                        Mostrando <?php echo e($parametros->firstItem()); ?> a <?php echo e($parametros->lastItem()); ?> de <?php echo e($parametros->total()); ?> registros
                    </div>
                </div>
            <?php else: ?>
                <div class="text-center py-5">
                    <i class="fas fa-flask fa-4x mb-4" style="color: #A31800;"></i>
                    <h4 class="mb-3" style="color: #334155;">No hay parámetros registrados</h4>
                    <p class="text-muted mb-4">
                        <?php if(request('search')): ?>
                            No se encontraron parámetros con "<?php echo e(request('search')); ?>"
                        <?php else: ?>
                            Comience agregando parámetros de análisis al sistema.
                        <?php endif; ?>
                    </p>
                    
                    <?php if(auth()->guard()->check()): ?>
                        <?php if(Auth::user()->hasAnyRole(['admin', 'tecnico', 'analista'])): ?>
                            <?php if(request('search')): ?>
                                <a href="<?php echo e(route('parametros.index')); ?>" class="btn btn-outline-secondary" style="border-radius: 30px; padding: 10px 25px;">
                                    <i class="fas fa-times me-2"></i>
                                    Limpiar búsqueda
                                </a>
                            <?php else: ?>
                                <a href="<?php echo e(route('parametros.create')); ?>" class="btn" style="background-color: #A31800; border-radius: 30px; padding: 10px 25px; color: white; border: none; transition: all 0.3s ease;">
                                    <i class="fas fa-plus-circle me-2"></i>
                                    Crear primer parámetro
                                </a>
                            <?php endif; ?>
                        <?php else: ?>
                            <div class="alert alert-info">
                                <i class="fas fa-info-circle me-2"></i>
                                Solo el administrador puede crear nuevos parámetros.
                            </div>
                        <?php endif; ?>
                    <?php endif; ?>
                </div>
            <?php endif; ?>
        </div>
        
        <!-- Resumen de precios -->
        <div class="card-footer bg-light">
            <div class="row">
                <div class="col-md-6">
                    <small class="text-muted">
                        <i class="fas fa-info-circle me-1"></i>
                        Mostrando <?php echo e($parametros->count()); ?> de <?php echo e($parametros->total()); ?> parámetros
                    </small>
                </div>
                <div class="col-md-6 text-end">
                    <small class="text-muted">
                        <i class="fas fa-calculator me-1"></i>
                        Precio promedio: 
                        <strong>Bs. <?php echo e(number_format($parametros->avg('precio_unitario'), 2)); ?></strong>
                    </small>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Estilos adicionales específicos para la página de parámetros -->
<style>
/* Estilos para el botón de nuevo parámetro */
.btn[style*="background-color: #A31800"]:hover {
    background-color: #7a1200 !important;
    transform: translateY(-2px);
    box-shadow: 0 5px 15px rgba(163, 24, 0, 0.3);
}

/* Estilo para el botón de papelera en círculo */
.btn-icon-circle {
    width: 35px !important;
    height: 35px !important;
    border-radius: 50% !important;
    padding: 0 !important;
    display: inline-flex !important;
    align-items: center !important;
    justify-content: center !important;
    transition: all 0.3s ease !important;
}

.btn-icon-circle:hover {
    transform: scale(1.1) !important;
}

/* Estilos para la paginación */
.pagination .page-link {
    color: #A31800 !important;
    border-radius: 8px;
    margin: 0 3px;
}

.pagination .page-item.active .page-link {
    background-color: #A31800 !important;
    border-color: #A31800 !important;
    color: white !important;
}

/* Estilo para los botones outline */
.btn-outline-warning,
.btn-outline-danger {
    transition: all 0.2s ease;
}

.btn-outline-warning:hover,
.btn-outline-danger:hover {
    transform: translateY(-2px);
    box-shadow: 0 4px 10px rgba(0,0,0,0.1);
}

.fa-microscope{
    color: #A31800!important;
}

/* Estilo para el input de búsqueda */
.input-group-text {
    border-radius: 30px 0 0 30px !important;
}

.form-control {
    border-radius: 0 !important;
}

.btn[type="submit"] {
    border-radius: 0 30px 30px 0 !important;
}


/* Estilo para el foco de inputs y selects en parámetros */
.form-control:focus,
.form-select:focus,
.input-group-text:focus,
.btn:focus {
    border-color: #A31800 !important;
    box-shadow: 0 0 0 3px rgba(163, 24, 0, 0.25) !important;
    outline: none !important;
}

/* Específico para el input de búsqueda */
#search:focus {
    border-color: #A31800 !important;
    box-shadow: 0 0 0 3px rgba(163, 24, 0, 0.25) !important;
}

/* Para los selects de filtros (si existen) */
select.form-select:focus {
    border-color: #A31800 !important;
    box-shadow: 0 0 0 3px rgba(163, 24, 0, 0.25) !important;
}

/* ========== CORRECCIÓN PARA MÓVIL ========== */
@media (max-width: 768px) {
    .page-header .d-flex {
        flex-direction: column !important;
        align-items: flex-start !important;
        gap: 15px !important;
    }
    
    .page-header .btn {
        width: 100% !important;
        justify-content: center !important;
    }
    
    .page-header h1 {
        font-size: 1.5rem !important;
    }
    
    .page-subtitle {
        font-size: 0.9rem !important;
    }
    
    .card-header {
        flex-direction: column !important;
        gap: 10px !important;
        align-items: flex-start !important;
    }
    
    .btn-group {
        flex-wrap: wrap !important;
        justify-content: center !important;
    }
    
    .btn-group .btn {
        margin: 2px !important;
    }
    
    .input-group {
        flex-wrap: wrap !important;
    }
    
    .input-group .form-control,
    .input-group .btn {
        width: 100% !important;
        border-radius: 30px !important;
        margin: 5px 0 !important;
    }
    
    .input-group-text {
        display: none !important;
    }
    
    /* Ajuste para móvil - papelera y texto */
    .d-flex.align-items-center.justify-content-center.position-relative {
        flex-direction: column !important;
        padding-top: 40px !important;
    }
    
    .btn-icon-circle.position-absolute.start-0 {
        position: relative !important;
        left: auto !important;
        margin-bottom: 10px !important;
    }
}
</style>

<?php $__env->startPush('scripts'); ?>
<script>
    document.addEventListener('DOMContentLoaded', function() {
        // Inicializar tooltips de Bootstrap
        var tooltipTriggerList = [].slice.call(document.querySelectorAll('[data-bs-toggle="tooltip"]'));
        var tooltipList = tooltipTriggerList.map(function(tooltipTriggerEl) {
            return new bootstrap.Tooltip(tooltipTriggerEl, {
                placement: 'auto',
                trigger: 'hover',
                delay: { show: 50, hide: 50 },
                boundary: 'viewport'
            });
        });
        
        // Auto-submit del formulario de búsqueda al escribir
        let searchTimeout;
        const searchInput = document.getElementById('search');
        const searchForm = document.getElementById('searchForm');
        
        if (searchInput && searchForm) {
            searchInput.addEventListener('keyup', function() {
                clearTimeout(searchTimeout);
                searchTimeout = setTimeout(function() {
                    if (searchInput.value.length >= 2 || searchInput.value.length === 0) {
                        searchForm.submit();
                    }
                }, 800);
            });
        }
    });

    // Función para confirmar eliminación
    function confirmarEliminacion(id, nombre, tipo) {
        if (confirm(`¿Está seguro de eliminar el ${tipo} "${nombre}"?\n\n⚠️ Esta acción moverá el registro a la papelera.`)) {
            document.getElementById(`delete-form-${id}`).submit();
        }
    }
</script>
<?php $__env->stopPush(); ?>
<?php $__env->stopSection(); ?>
<?php echo $__env->make('layouts.app', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH D:\pruebaSystem\System-CIMA\application\resources\views/parametros/index.blade.php ENDPATH**/ ?>