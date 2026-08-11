<?php $__env->startSection('content'); ?>
<div class="container-main">
    <!-- Encabezado de página -->
    <div class="page-header">
        <div class="d-flex justify-content-between align-items-center">
            <div>
                <h1>
                    <i class="fas fa-users" style="color: #2798F5;"></i>
                    Gestión de Clientes
                </h1>
                <p class="page-subtitle">
                    Administre el registro de clientes del sistema CIMA
                </p>
            </div>
            
            <div class="d-flex gap-2">
                <?php if(auth()->guard()->check()): ?>
                    <?php if(Auth::user()->hasAnyRole(['admin', 'tecnico', 'analista'])): ?>
                        <a href="<?php echo e(route('clientes.create')); ?>" class="btn" style="background-color: #2798F5; border-radius: 30px; padding: 10px 25px; color: white; border: none; transition: all 0.3s ease;">
                            <i class="fas fa-plus-circle"></i>
                            Nuevo Cliente
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

    <!-- BUSCADOR Y ORDENAMIENTO -->
    <div class="card mb-4">
        <div class="card-body">
            <form action="<?php echo e(route('clientes.index')); ?>" method="GET" id="searchForm">
                <div class="row align-items-end">
                    <div class="col-md-6">
                        <label for="search" class="form-label fw-semibold">
                            <i class="fas fa-search me-2" style="color: #2798F5;"></i>
                            Buscar Cliente
                        </label>
                        <div class="input-group">
                            <span class="input-group-text" style="background-color: #2798F5; color: white; border: none;">
                                <i class="fas fa-search"></i>
                            </span>
                            <input type="text" 
                                   class="form-control" 
                                   id="search" 
                                   name="search" 
                                   value="<?php echo e(request('search')); ?>" 
                                   placeholder="Buscar por ID, Razón Social, Contacto, Teléfono o NIT..."
                                   style="border-left: none;">
                            <button class="btn" type="submit" style="background-color: #2798F5; color: white; border: none;">
                                <i class="fas fa-filter me-1"></i> Filtrar
                            </button>
                            <?php if(request('search')): ?>
                                <a href="<?php echo e(route('clientes.index')); ?>" class="btn btn-outline-secondary" style="border-radius: 0 30px 30px 0;">
                                    <i class="fas fa-times"></i> Limpiar
                                </a>
                            <?php endif; ?>
                        </div>
                        <small class="text-muted mt-1 d-block">
                            <i class="fas fa-info-circle me-1"></i>
                            Puede buscar por cualquier campo: ID, razón social, persona de contacto, teléfono o NIT.
                        </small>
                    </div>
                    
                    <div class="col-md-3">
                        <label for="orden" class="form-label fw-semibold">
                            <i class="fas fa-sort me-2" style="color: #2798F5;"></i>
                            Ordenar por Saldo
                        </label>
                        <select name="orden" id="orden" class="form-select" onchange="this.form.submit()">
                            <option value="">Sin ordenar</option>
                            <option value="saldo_desc" <?php echo e(request('orden') == 'saldo_desc' ? 'selected' : ''); ?>>Mayor deuda primero</option>
                            <option value="saldo_asc" <?php echo e(request('orden') == 'saldo_asc' ? 'selected' : ''); ?>>Menor deuda primero</option>
                            <option value="nombre_asc" <?php echo e(request('orden') == 'nombre_asc' ? 'selected' : ''); ?>>Nombre (A-Z)</option>
                            <option value="nombre_desc" <?php echo e(request('orden') == 'nombre_desc' ? 'selected' : ''); ?>>Nombre (Z-A)</option>
                        </select>
                    </div>
                    
                    <div class="col-md-3 text-md-end mt-3 mt-md-0">
                        <div class="d-flex align-items-center justify-content-md-end">
                            <span class="badge" style="background-color: #2798F5; color: white; padding: 8px 15px; border-radius: 20px; font-weight: 500;">
                                <i class="fas fa-database me-1"></i> Total: <?php echo e($clientes->total()); ?> clientes
                            </span>
                        </div>
                    </div>
                </div>
            </form>
        </div>
    </div>

    <!-- Tabla de clientes -->
    <div class="card">
        <div class="card-header d-flex justify-content-between align-items-center">
            <h5 class="mb-0">
                <i class="fas fa-list me-2" style="color: #2798F5;"></i>
                Listado de Clientes
            </h5>
            <span class="badge" style="background-color: #2798F5; color: white; padding: 8px 15px; border-radius: 20px; font-weight: 500;">
                <?php echo e($clientes->count()); ?> registros mostrados
            </span>
        </div>
        <div class="card-body">
            <?php if($clientes->count() > 0): ?>
                <div class="table-responsive">
                    <table class="table table-hover">
                        <thead>
                            <tr style="background-color: #2798F5; color: white;">
                                <th width="80" style="background-color: #2798F5; color: white; font-weight: 600; text-transform: uppercase; font-size: 0.85rem; letter-spacing: 0.5px; padding: 12px 15px;">ID</th>
                                <th style="background-color: #2798F5; color: white; font-weight: 600; text-transform: uppercase; font-size: 0.85rem; letter-spacing: 0.5px; padding: 12px 15px;">Razón Social</th>
                                <th style="background-color: #2798F5; color: white; font-weight: 600; text-transform: uppercase; font-size: 0.85rem; letter-spacing: 0.5px; padding: 12px 15px;">Persona de Contacto</th>
                                <th style="background-color: #2798F5; color: white; font-weight: 600; text-transform: uppercase; font-size: 0.85rem; letter-spacing: 0.5px; padding: 12px 15px;">Teléfono</th>
                                <th style="background-color: #2798F5; color: white; font-weight: 600; text-transform: uppercase; font-size: 0.85rem; letter-spacing: 0.5px; padding: 12px 15px;">NIT</th>
                                <th class="text-center" style="background-color: #2798F5; color: white; font-weight: 600; text-transform: uppercase; font-size: 0.85rem; letter-spacing: 0.5px; padding: 12px 15px;">Proformas</th>
                                <th class="text-end" style="background-color: #2798F5; color: white; font-weight: 600; text-transform: uppercase; font-size: 0.85rem; letter-spacing: 0.5px; padding: 12px 15px;">Saldo Total (Bs.)</th>
                                <th width="160" class="text-center" style="background-color: #2798F5; color: white; font-weight: 600; text-transform: uppercase; font-size: 0.85rem; letter-spacing: 0.5px; padding: 12px 15px;">Acciones</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php $__currentLoopData = $clientes; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $cliente): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                <?php
                                    $totalProformas = $cliente->proformas->count();
                                    $saldoTotal = $cliente->proformas->sum('saldo');
                                ?>
                                <tr>
                                    <td>
                                        <span class="badge bg-secondary">#<?php echo e($cliente->id); ?></span>
                                    </td>
                                    <td>
                                        <strong><?php echo e($cliente->razon_social); ?></strong>
                                    </td>
                                    <td>
                                        <small class="text-muted">
                                            <i class="fas fa-user me-1"></i>
                                            <?php echo e($cliente->persona_contacto); ?>

                                        </small>
                                    </td>
                                    <td>
                                        <?php if($cliente->telefono): ?>
                                            <span class="text-muted">
                                                <i class="fas fa-phone me-1"></i>
                                                <?php echo e($cliente->telefono); ?>

                                            </span>
                                        <?php else: ?>
                                            <span class="text-muted">N/A</span>
                                        <?php endif; ?>
                                    </td>
                                    <td>
                                        <?php if($cliente->nit): ?>
                                            <code><?php echo e($cliente->nit); ?></code>
                                        <?php else: ?>
                                            <span class="text-muted">N/A</span>
                                        <?php endif; ?>
                                    </td>
                                    <td class="text-center">
                                        <?php if($totalProformas > 0): ?>
                                            <span class="badge" style="background-color: #2798F5; color: white; padding: 6px 12px; border-radius: 20px;">
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
                                    <td class="text-end fw-bold">
                                        <?php if($saldoTotal > 0): ?>
                                            <span style="color: #dc3545;">
                                                <i class="fas fa-exclamation-triangle me-1"></i>
                                                Bs. <?php echo e(number_format($saldoTotal, 2)); ?>

                                            </span>
                                        <?php elseif($saldoTotal < 0): ?>
                                            <span style="color: #ffc107;">
                                                <i class="fas fa-arrow-up me-1"></i>
                                                Bs. <?php echo e(number_format($saldoTotal, 2)); ?>

                                            </span>
                                        <?php else: ?>
                                            <span style="color: #28a745;">
                                                <i class="fas fa-check-circle me-1"></i>
                                                Bs. 0.00
                                            </span>
                                        <?php endif; ?>
                                    </td>
                                    <td class="text-center">
                                        <div class="btn-group" role="group">
                                            <!-- Botón VER en color CELESTE -->
                                            <?php if (app(\Illuminate\Contracts\Auth\Access\Gate::class)->check('ver clientes')): ?>
                                            <a href="<?php echo e(route('clientes.show', $cliente)); ?>" 
                                               class="btn btn-sm"
                                               style="color: #0dcaf0; border: 1px solid #0dcaf0; background: transparent; border-radius: 6px; padding: 0.5rem; width: 38px; height: 38px; transition: all 0.2s ease; display: inline-flex; align-items: center; justify-content: center;"
                                               data-bs-toggle="tooltip" 
                                               data-bs-placement="top"
                                               title="Ver detalles del cliente"
                                               onmouseover="this.style.backgroundColor='#0dcaf0'; this.style.color='white';"
                                               onmouseout="this.style.backgroundColor='transparent'; this.style.color='#0dcaf0';">
                                                <i class="fas fa-eye"></i>
                                            </a>
                                            <?php endif; ?>
                                            
                                            <?php if(auth()->guard()->check()): ?>
                                                
                                                <?php if (app(\Illuminate\Contracts\Auth\Access\Gate::class)->check('editar clientes')): ?>
                                                    <!-- Botón EDITAR -->
                                                    <a href="<?php echo e(route('clientes.edit', $cliente)); ?>" 
                                                       class="btn btn-outline-warning btn-sm"
                                                       data-bs-toggle="tooltip" 
                                                       data-bs-placement="top"
                                                       title="Editar cliente">
                                                        <i class="fas fa-edit"></i>
                                                    </a>
                                                <?php endif; ?>
                                                <?php if (app(\Illuminate\Contracts\Auth\Access\Gate::class)->check('eliminar clientes')): ?>
                                                    <!-- Botón ELIMINAR -->
                                                    <button type="button" 
                                                            class="btn btn-outline-danger btn-sm"
                                                            data-bs-toggle="tooltip" 
                                                            data-bs-placement="top"
                                                            title="Eliminar cliente"
                                                            onclick="confirmarEliminacion(<?php echo e($cliente->id); ?>, '<?php echo e($cliente->razon_social); ?>', 'cliente')">
                                                        <i class="fas fa-trash"></i>
                                                    </button>
                                                <?php endif; ?>
                                                
                                            <?php endif; ?>
                                        </div>
                                        
                                        <!-- Formulario oculto para eliminar -->
                                        <?php if(auth()->guard()->check()): ?>
                                            <?php if (app(\Illuminate\Contracts\Auth\Access\Gate::class)->check('eliminar clientes')): ?>
                                                <form id="delete-form-<?php echo e($cliente->id); ?>" 
                                                      action="<?php echo e(route('clientes.destroy', $cliente)); ?>" 
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

                <!-- Resumen de saldos totales -->
                <div class="row mt-4">
                    <div class="col-md-4 offset-md-8">
                        <div class="card" style="background-color: #f8f9fa;">
                            <div class="card-body p-3">
                                <?php
                                    $saldoGeneral = $clientes->sum(function($cliente) {
                                        return $cliente->proformas->sum('saldo');
                                    });
                                    $clientesConDeuda = $clientes->filter(function($cliente) {
                                        return $cliente->proformas->sum('saldo') > 0;
                                    })->count();
                                ?>
                                <h6 class="card-title mb-3" style="color: #2798F5;">
                                    <i class="fas fa-chart-line me-2"></i>
                                    Resumen de Saldos
                                </h6>
                                <div class="d-flex justify-content-between mb-2">
                                    <span class="text-muted">Saldo Total General:</span>
                                    <span class="fw-bold fs-5" style="color: <?php echo e($saldoGeneral > 0 ? '#dc3545' : '#28a745'); ?>;">
                                        Bs. <?php echo e(number_format($saldoGeneral, 2)); ?>

                                    </span>
                                </div>
                                <div class="d-flex justify-content-between">
                                    <span class="text-muted">Clientes con deuda:</span>
                                    <span class="fw-bold"><?php echo e($clientesConDeuda); ?></span>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <?php if($clientes->hasPages()): ?>
                    <div class="d-flex justify-content-center mt-4">
                        <?php echo e($clientes->appends(request()->query())->links()); ?>

                    </div>
                <?php endif; ?>
                
                <!-- Botón de Papelera y texto de registros centrado -->
                <div class="d-flex align-items-center justify-content-center position-relative mt-3">
                    <?php if(auth()->guard()->check()): ?>
                        <?php if (app(\Illuminate\Contracts\Auth\Access\Gate::class)->check('ver papelera clientes')): ?>
                            <a href="<?php echo e(route('clientes.trash')); ?>" 
                               class="btn btn-icon-circle position-absolute start-0"
                               style="width: 35px; height: 35px; border-radius: 50%; background-color: #6c757d; color: white; display: inline-flex; align-items: center; justify-content: center; transition: all 0.3s ease; text-decoration: none;"
                               data-bs-toggle="tooltip"
                               title="Ver clientes eliminados"
                               onmouseover="this.style.backgroundColor='#5a6268'; this.style.transform='scale(1.1)';"
                               onmouseout="this.style.backgroundColor='#6c757d'; this.style.transform='scale(1)';">
                                <i class="fas fa-trash-alt" style="font-size: 1rem;"></i>
                            </a>
                        <?php endif; ?>
                    <?php endif; ?>
                    
                    <div style="color: #2798F5; font-weight: 500;">
                        <i class="fas fa-database me-1"></i> 
                        Mostrando <?php echo e($clientes->firstItem()); ?> a <?php echo e($clientes->lastItem()); ?> de <?php echo e($clientes->total()); ?> registros
                    </div>
                </div>
            <?php else: ?>
                <div class="text-center py-5">
                    <i class="fas fa-users fa-4x mb-4" style="color: #2798F5;"></i>
                    <h4 class="mb-3" style="color: #334155;">No hay clientes registrados</h4>
                    <p class="text-muted mb-4">
                        <?php if(request('search')): ?>
                            No se encontraron clientes con "<?php echo e(request('search')); ?>"
                        <?php else: ?>
                            Comience agregando su primer cliente al sistema.
                        <?php endif; ?>
                    </p>
                    
                    <?php if(auth()->guard()->check()): ?>
                        <?php if(Auth::user()->hasAnyRole(['admin', 'tecnico', 'analista'])): ?>
                            <?php if(request('search')): ?>
                                <a href="<?php echo e(route('clientes.index')); ?>" class="btn btn-outline-secondary" style="border-radius: 30px; padding: 10px 25px;">
                                    <i class="fas fa-times me-2"></i>
                                    Limpiar búsqueda
                                </a>
                            <?php else: ?>
                                <a href="<?php echo e(route('clientes.create')); ?>" class="btn" style="background-color: #2798F5; border-radius: 30px; padding: 10px 25px; color: white; border: none; transition: all 0.3s ease;">
                                    <i class="fas fa-plus-circle me-2"></i>
                                    Crear primer cliente
                                </a>
                            <?php endif; ?>
                        <?php else: ?>
                            <div class="alert alert-info">
                                <i class="fas fa-info-circle me-2"></i>
                                Solo el administrador puede crear nuevos clientes.
                            </div>
                        <?php endif; ?>
                    <?php endif; ?>
                </div>
            <?php endif; ?>
        </div>
    </div>
</div>

<!-- Estilos adicionales específicos para la página de clientes -->
<style>
/* Estilos para el botón de nuevo cliente */
.btn[style*="background-color: #2798F5"]:hover {
    background-color: #1a7ac9 !important;
    transform: translateY(-2px);
    box-shadow: 0 5px 15px rgba(39, 152, 245, 0.3);
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
    color: #2798F5 !important;
    border-radius: 8px;
    margin: 0 3px;
}

.pagination .page-item.active .page-link {
    background-color: #2798F5 !important;
    border-color: #2798F5 !important;
    color: white !important;
}

/* Estilo para el icono de clientes en el encabezado */
.fa-users {
    color: #2798F5 !important;
}

/* Estilos para botones outline */
.btn-outline-warning,
.btn-outline-danger {
    transition: all 0.2s ease;
}

.btn-outline-warning:hover,
.btn-outline-danger:hover {
    transform: translateY(-2px);
    box-shadow: 0 4px 10px rgba(0,0,0,0.1);
}

/* Asegurar que el botón VER tenga el mismo estilo */
.btn[style*="color: #0dcaf0"] {
    transition: all 0.2s ease !important;
}

.btn[style*="color: #0dcaf0"]:hover {
    background-color: #0dcaf0 !important;
    color: white !important;
    transform: translateY(-2px);
    box-shadow: 0 4px 10px rgba(13, 202, 240, 0.3);
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

/* Estilo para el foco de inputs y selects */
.form-control:focus,
.form-select:focus,
.input-group-text:focus,
.btn:focus {
    border-color: #2798F5 !important;
    box-shadow: 0 0 0 3px rgba(39, 152, 245, 0.25) !important;
    outline: none !important;
}

#search:focus,
#orden:focus {
    border-color: #2798F5 !important;
    box-shadow: 0 0 0 3px rgba(39, 152, 245, 0.25) !important;
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
    
    .col-md-4.offset-md-8 {
        margin-left: 0 !important;
        width: 100% !important;
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
<?php echo $__env->make('layouts.app', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH D:\pruebaSystem\System-CIMA\application\resources\views/clientes/index.blade.php ENDPATH**/ ?>