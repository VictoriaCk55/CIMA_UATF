<?php $__env->startSection('title', 'Proformas'); ?>

<?php $__env->startSection('content'); ?>
<div class="container-main">
    <div class="page-header">
        <div class="d-flex justify-content-between align-items-center">
            <div>
                <h1>
                    <i class="fas fa-file-invoice-dollar" style="color: #ffc107;"></i>
                    Gestión de Proformas
                </h1>
                <p class="page-subtitle">
                    Listado de proformas generadas en el sistema CIMA
                </p>
            </div>
            
            <div class="d-flex gap-2">
                <?php if(auth()->guard()->check()): ?>
                    <?php if (app(\Illuminate\Contracts\Auth\Access\Gate::class)->check('crear proformas')): ?>
                        <a href="<?php echo e(route('proformas.create')); ?>" class="btn btn-primary" style="background-color: #ffc107; border-radius: 30px; padding: 10px 25px; color: #000; border: none; transition: all 0.3s ease;">
                            <i class="fas fa-plus-circle"></i>
                            Nueva Proforma
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

    <div class="card mb-4">
        <div class="card-body">
            <form method="GET" action="<?php echo e(route('proformas.index')); ?>" class="row">
                <div class="col-md-12 mb-3">
                    <label for="search" class="form-label fw-semibold">
                        <i class="fas fa-search me-2" style="color: #ffc107;"></i>
                        Buscar Proforma
                    </label>
                    <div class="input-group">
                        <span class="input-group-text" style="background-color: #ffc107; color: #000; border: none;">
                            <i class="fas fa-search"></i>
                        </span>
                        <input type="text" 
                               class="form-control" 
                               id="search" 
                               name="search" 
                               value="<?php echo e(request('search')); ?>" 
                               placeholder="Buscar por código, cliente o tipo..."
                               style="border-left: none;">
                        <?php if(request('search')): ?>
                        <a href="<?php echo e(route('proformas.index')); ?>" class="btn btn-outline-secondary" style="border-radius: 0 30px 30px 0;">
                            <i class="fas fa-times"></i> Limpiar
                        </a>
                        <?php endif; ?>
                    </div>
                    <small class="text-muted mt-1 d-block">
                        <i class="fas fa-info-circle me-1"></i>
                        Puede buscar por código de proforma, nombre del cliente o tipo (AMBIENTAL, ANÁLISIS QUÍMICO, INVESTIGACION). La búsqueda no distingue mayúsculas/minúsculas.
                    </small>
                </div>
                
                <div class="col-md-3">
                    <label for="mes" class="form-label">
                        <i class="fas fa-calendar-alt me-1" style="color: #ffc107;"></i> Mes
                    </label>
                    <select name="mes" id="mes" class="form-select">
                        <option value="">Todos los meses</option>
                        <?php $__currentLoopData = range(1, 12); $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $m): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                            <option value="<?php echo e($m); ?>" <?php echo e(request('mes') == $m ? 'selected' : ''); ?>>
                                <?php echo e(\Carbon\Carbon::create()->month($m)->locale('es')->monthName); ?>

                            </option>
                        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                    </select>
                </div>
                
                <div class="col-md-2">
                    <label for="anio" class="form-label">
                        <i class="fas fa-calendar me-1" style="color: #ffc107;"></i> Año
                    </label>
                    <select name="anio" id="anio" class="form-select">
                        <option value="">Todos los años</option>
                        <?php if(isset($añosDisponibles) && count($añosDisponibles) > 0): ?>
                            <?php $__currentLoopData = $añosDisponibles; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $año): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                <option value="<?php echo e($año); ?>" <?php echo e(request('anio') == $año ? 'selected' : ''); ?>>
                                    <?php echo e($año); ?>

                                </option>
                            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                        <?php else: ?>
                            <?php for($a = date('Y'); $a >= date('Y')-5; $a--): ?>
                                <option value="<?php echo e($a); ?>" <?php echo e(request('anio') == $a ? 'selected' : ''); ?>>
                                    <?php echo e($a); ?>

                                </option>
                            <?php endfor; ?>
                        <?php endif; ?>
                    </select>
                </div>
                
                <div class="col-md-3">
                    <label for="estado" class="form-label">
                        <i class="fas fa-flag me-1" style="color: #ffc107;"></i> Estado
                    </label>
                    <select name="estado" id="estado" class="form-select">
                        <option value="">Todos los estados</option>
                        <option value="BORRADOR" <?php echo e(request('estado') == 'BORRADOR' ? 'selected' : ''); ?>>📝 Borrador</option>
                        <option value="ENVIADA" <?php echo e(request('estado') == 'ENVIADA' ? 'selected' : ''); ?>>⏳ Enviada</option>
                        <option value="APROBADA" <?php echo e(request('estado') == 'APROBADA' ? 'selected' : ''); ?>>✅ Aprobada</option>
                        <option value="RECHAZADA" <?php echo e(request('estado') == 'RECHAZADA' ? 'selected' : ''); ?>>❌ Rechazada</option>
                        <option value="FINALIZADA" <?php echo e(request('estado') == 'FINALIZADA' ? 'selected' : ''); ?>>🏁 Finalizada</option>
                    </select>
                </div>
                
                <div class="col-md-4 d-flex align-items-end">
                    <button type="submit" class="btn btn-primary me-2" style="background-color: #ffc107; border-radius: 30px; padding: 8px 20px; color: #000; border: none; transition: all 0.3s ease;">
                        <i class="fas fa-filter"></i> Filtrar
                    </button>
                    <a href="<?php echo e(route('proformas.index')); ?>" 
                       class="btn btn-secondary" 
                       style="border-radius: 30px; padding: 8px 20px; transition: all 0.3s ease;">
                        <i class="fas fa-eraser"></i> Limpiar
                    </a>
                </div>
            </form>
        </div>
        
        <?php if(request()->has('search') || request()->has('mes') || request()->has('anio') || request()->has('estado')): ?>
            <div class="card-footer bg-light py-2">
                <small class="text-muted">
                    <i class="fas fa-info-circle me-1" style="color: #ffc107;"></i>
                    Mostrando resultados para:
                    <?php if(request('search')): ?>
                        búsqueda "<strong><?php echo e(request('search')); ?></strong>"
                    <?php endif; ?>
                    <?php if(request('mes') && request('anio')): ?>
                        <?php
                            $mesNumerico = (int)request('mes');
                            $anioNumerico = (int)request('anio');
                            $nombreMes = \Carbon\Carbon::createFromDate($anioNumerico, $mesNumerico, 1)->locale('es')->monthName;
                        ?>
                        de <?php echo e($nombreMes); ?> de <?php echo e(request('anio')); ?>

                    <?php elseif(request('mes')): ?>
                        <?php
                            $mesNumerico = (int)request('mes');
                            $nombreMes = \Carbon\Carbon::create()->month($mesNumerico)->locale('es')->monthName;
                        ?>
                        de <?php echo e($nombreMes); ?>

                    <?php elseif(request('anio')): ?>
                        del año <?php echo e(request('anio')); ?>

                    <?php endif; ?>
                    
                    <?php if(request('estado')): ?>
                        <?php
                            $estados = ['BORRADOR' => 'Borrador', 'ENVIADA' => 'Enviada', 'APROBADA' => 'Aprobada', 'RECHAZADA' => 'Rechazada', 'FINALIZADA' => 'Finalizada'];
                        ?>
                        con estado <strong><?php echo e($estados[request('estado')] ?? request('estado')); ?></strong>
                    <?php endif; ?>
                </small>
            </div>
        <?php endif; ?>
    </div>

    <div class="card">
        <div class="card-header d-flex justify-content-between align-items-center">
            <h5 class="mb-0">
                <i class="fas fa-list me-2" style="color: #ffc107;"></i>
                Listado de Proformas
            </h5>
            <span class="badge" style="background-color: #ffc107; color: #000; padding: 8px 15px; border-radius: 20px; font-weight: 500;">
                <?php echo e($proformas->total()); ?> registros
            </span>
        </div>
        <div class="card-body">
            <?php if(session('success')): ?>
                <div class="alert alert-success alert-dismissible fade show" role="alert">
                    <i class="fas fa-check-circle me-2"></i>
                    <?php echo e(session('success')); ?>

                    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                </div>
            <?php endif; ?>

            <?php if(session('error')): ?>
                <div class="alert alert-danger alert-dismissible fade show" role="alert">
                    <i class="fas fa-exclamation-triangle me-2"></i>
                    <?php echo e(session('error')); ?>

                    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                </div>
            <?php endif; ?>

            <?php if($proformas->count() > 0): ?>
                <div class="table-responsive">
                    <table class="table table-hover" style="font-size: 0.75rem;">
                        <thead>
                            <tr style="background-color: #ffc107; color: #000;">
                                <th width="100" style="background-color: #ffc107; color: #000; font-weight: 600; text-transform: uppercase; font-size: 0.85rem; letter-spacing: 0.5px; padding: 12px 15px;">Código</th>
                                <th style="background-color: #ffc107; color: #000; font-weight: 600; text-transform: uppercase; font-size: 0.85rem; letter-spacing: 0.5px; padding: 12px 15px;">Cliente</th>
                                <th width="100" style="background-color: #ffc107; color: #000; font-weight: 600; text-transform: uppercase; font-size: 0.85rem; letter-spacing: 0.5px; padding: 12px 15px;">Tipo</th>
                                <th width="120" style="background-color: #ffc107; color: #000; font-weight: 600; text-transform: uppercase; font-size: 0.85rem; letter-spacing: 0.5px; padding: 12px 15px;">Estado</th>
                                <th width="120" style="background-color: #ffc107; color: #000; font-weight: 600; text-transform: uppercase; font-size: 0.85rem; letter-spacing: 0.5px; padding: 12px 15px;">Tipo Muestra</th>
                                <th width="100" style="background-color: #ffc107; color: #000; font-weight: 600; text-transform: uppercase; font-size: 0.85rem; letter-spacing: 0.5px; padding: 12px 15px;">Fecha</th>
                                <th width="120" class="text-end" style="background-color: #ffc107; color: #000; font-weight: 600; text-transform: uppercase; font-size: 0.85rem; letter-spacing: 0.5px; padding: 12px 15px;">Total</th>
                                <th width="120" class="text-end" style="background-color: #ffc107; color: #000; font-weight: 600; text-transform: uppercase; font-size: 0.85rem; letter-spacing: 0.5px; padding: 12px 15px;">Adelanto</th>
                                <th width="140" class="text-center" style="background-color: #ffc107; color: #000; font-weight: 600; text-transform: uppercase; font-size: 0.85rem; letter-spacing: 0.5px; padding: 12px 15px;">Acciones</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php $__currentLoopData = $proformas; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $proforma): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                <tr>
                                    <td>
                                        <span style="font-weight: bold;">
                                            <?php echo e($proforma->codigo); ?>

                                        </span>
                                    </td>
                                    <td>
                                        <strong><?php echo e($proforma->cliente->razon_social); ?></strong>
                                        <br>
                                        <small class="text-muted">
                                            <i class="fas fa-user me-1"></i>
                                            <?php echo e($proforma->persona_contacto ?? $proforma->cliente->persona_contacto ?? 'N/A'); ?>

                                        </small>
                                    </td>
                                   <td>
                                        <span class="badge rounded-pill 
                                            <?php if($proforma->tipo == 'AMBIENTAL'): ?> bg-warning text-dark
                                            <?php elseif($proforma->tipo == 'ANALISIS_QUIMICO'): ?> bg-info text-white
                                            <?php else: ?> bg-secondary
                                            <?php endif; ?>">
                                            <i class="fas 
                                                <?php if($proforma->tipo == 'AMBIENTAL'): ?> fa-leaf
                                                <?php elseif($proforma->tipo == 'ANALISIS_QUIMICO'): ?> fa-flask
                                                <?php else: ?> fa-flask
                                                <?php endif; ?> me-1"></i>
                                            <?php echo e($proforma->tipo == 'ANALISIS_QUIMICO' ? 'ANÁLISIS QUÍMICO' : $proforma->tipo); ?>

                                        </span>
                                    </td>
                                    <td>
                                        <?php
                                            $estado = $proforma->estado;
                                            $bgColor = '';
                                            $textColor = '';
                                            $icono = '';
                                            
                                            switch($estado) {
                                                case 'BORRADOR':
                                                    $bgColor = 'bg-secondary';
                                                    $textColor = '#ffffff';
                                                    $icono = 'fa-pencil-alt';
                                                    break;
                                                case 'ENVIADA':
                                                    $bgColor = 'bg-info';
                                                    $textColor = '#ffffff';
                                                    $icono = 'fa-paper-plane';
                                                    break;
                                                case 'APROBADA':
                                                    $bgColor = 'bg-success';
                                                    $textColor = '#ffffff';
                                                    $icono = 'fa-check-circle';
                                                    break;
                                                case 'RECHAZADA':
                                                    $bgColor = 'bg-danger';
                                                    $textColor = '#ffffff';
                                                    $icono = 'fa-times-circle';
                                                    break;
                                                case 'FINALIZADA':
                                                    $bgColor = 'bg-white';
                                                    $textColor = '#000000';
                                                    $icono = 'fa-flag-checkered';
                                                    break;
                                                default:
                                                    $bgColor = 'bg-secondary';
                                                    $textColor = '#ffffff';
                                                    $icono = 'fa-question-circle';
                                            }
                                        ?>
                                        
                                        <span class="badge rounded-pill <?php echo e($bgColor); ?>" style="color: <?php echo e($textColor); ?>; padding: 8px 12px; <?php echo e($estado === 'FINALIZADA' ? 'border: 1px solid #ddd;' : ''); ?>">
                                            <?php echo e($estado); ?>

                                        </span>
                                    </td>
                                    <td>
                                        <small><?php echo e($proforma->tipo_muestra); ?></small>
                                    </td>
                                    <td>
                                        <small>
                                            <?php echo e($proforma->fecha_recepcion ? $proforma->fecha_recepcion->format('Y-m-d') : 'N/A'); ?>

                                        </small>
                                    </td>
                                    <td class="text-end fw-semibold text-success">
                                        <i class="fas fa-dollar-sign me-1"></i>
                                        <?php echo e(number_format($proforma->total, 2)); ?>

                                    </td>
                                    <td class="text-end fw-semibold" style="color: #ffc107;">
                                        <i class="fas fa-hand-holding-usd me-1"></i>
                                        <?php echo e(number_format($proforma->adelanto, 2)); ?>

                                    </td>
                                    <td class="text-center">
                                        <div class="dropdown">
                                            <button class="btn btn-xs btn-secondary dropdown-toggle" 
                                                    type="button" 
                                                    data-bs-toggle="dropdown" 
                                                    aria-expanded="false"
                                                    style="border-radius: 6px; padding: 0.25rem 0.6rem; font-size: 0.75rem; display: inline-flex; align-items: center; gap: 5px;">
                                                <i class="fas fa-bars-staggered" style="font-size: 0.7rem;"></i>
                                                Opciones
                                            </button>
                                            <ul class="dropdown-menu dropdown-menu-end">
                                                <li>
                                                    <a class="dropdown-item" 
                                                    href="<?php echo e(route('proformas.show', $proforma)); ?>"
                                                    title="Ver detalles de la proforma">
                                                        <i class="fas fa-eye me-2" style="color: #0dcaf0;"></i>
                                                        Ver detalles
                                                    </a>
                                                </li>
                                                
                                                <?php if (app(\Illuminate\Contracts\Auth\Access\Gate::class)->any(['editar proformas', 'eliminar proformas'])): ?>
                                                    <?php if($proforma->estado == 'BORRADOR'): ?>
                                                        <li>
                                                            <?php if (app(\Illuminate\Contracts\Auth\Access\Gate::class)->check('editar proformas')): ?>
                                                            <a class="dropdown-item" 
                                                            href="<?php echo e(route('proformas.edit', $proforma)); ?>"
                                                            title="Editar proforma">
                                                                <i class="fas fa-edit me-2" style="color: #ffc107;"></i>
                                                                Editar
                                                            </a>
                                                            <?php endif; ?>
                                                        </li>
                                                        
                                                        <li>
                                                            <?php if (app(\Illuminate\Contracts\Auth\Access\Gate::class)->check('eliminar proformas')): ?>
                                                            <form action="<?php echo e(route('proformas.destroy', $proforma)); ?>" 
                                                                method="POST" 
                                                                class="d-inline"
                                                                onsubmit="return confirm('¿Está seguro de eliminar la proforma <?php echo e($proforma->codigo); ?>?');">
                                                                <?php echo csrf_field(); ?>
                                                                <?php echo method_field('DELETE'); ?>
                                                                <button type="submit" 
                                                                        class="dropdown-item"
                                                                        style="background: none; border: none; width: 100%; text-align: left;">
                                                                    <i class="fas fa-trash me-2" style="color: #dc3545;"></i>
                                                                    Eliminar
                                                                </button>
                                                            </form>
                                                            <?php endif; ?>
                                                        </li>
                                                        <li><hr class="dropdown-divider"></li>
                                                    <?php endif; ?>
                                                <?php endif; ?>
                                                
                                                <li>
                                                    <a class="dropdown-item" 
                                                    href="<?php echo e(route('proformas.pdf', $proforma)); ?>"
                                                    target="_blank"
                                                    title="Generar PDF">
                                                        <i class="fas fa-file-pdf me-2" style="color: #dc3545;"></i>
                                                        Generar PDF
                                                    </a>
                                                </li>

                                                <?php if($proforma->tipo === 'AMBIENTAL'): ?>
                                                <?php if (\Illuminate\Support\Facades\Blade::check('hasanyrole', 'admin|tecnico')): ?>
                                                <li>
                                                    <a class="dropdown-item" href="<?php echo e(route('reportes.ambiental.index', $proforma)); ?>" title="Reporte Ambiental">
                                                        <i class="fas fa-file-signature me-2" style="color: #6f42c1;"></i>Reporte Ambiental
                                                    </a>
                                                </li>
                                                <?php endif; ?>
                                                <?php else: ?>
                                                <?php if (app(\Illuminate\Contracts\Auth\Access\Gate::class)->check('generar cadena custodia')): ?>
                                                <li>
                                                    <a class="dropdown-item" 
                                                    href="<?php echo e(route('proformas.cadena-custodia', $proforma)); ?>"
                                                    target="_blank"
                                                    title="Generar Cadena de Custodia">
                                                        <i class="fas fa-clipboard-list me-2" style="color: #198754;"></i>
                                                        Cadena de Custodia
                                                    </a>
                                                </li>
                                                <?php endif; ?>
                                                
                                                <?php if (app(\Illuminate\Contracts\Auth\Access\Gate::class)->check('ver resultados')): ?>
                                                <li>
                                                    <a class="dropdown-item" 
                                                    href="<?php echo e(route('resultados.index', $proforma->id)); ?>"
                                                    title="Abrir formulario de resultados">
                                                        <i class="fas fa-file-alt me-2" style="color: #0d6efd;"></i>
                                                        Formulario de resultados
                                                    </a>
                                                </li>
                                                <?php endif; ?>
                                                <?php endif; ?>
                                            </ul>
                                        </div>
                                    </td>
                                </tr>
                            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                        </tbody>
                    </table>
                </div>

                <div class="d-flex align-items-center justify-content-center position-relative mt-3">
                    <?php if(auth()->guard()->check()): ?>
                        <?php if (app(\Illuminate\Contracts\Auth\Access\Gate::class)->check('ver papelera proformas')): ?>
                            <a href="<?php echo e(route('proformas.trash')); ?>" 
                               class="btn btn-icon-circle position-absolute start-0"
                               style="width: 35px; height: 35px; border-radius: 50%; background-color: #6c757d; color: white; display: inline-flex; align-items: center; justify-content: center; transition: all 0.3s ease; text-decoration: none;"
                               data-bs-toggle="tooltip"
                               title="Ver proformas eliminadas"
                               onmouseover="this.style.backgroundColor='#5a6268'; this.style.transform='scale(1.1)';"
                               onmouseout="this.style.backgroundColor='#6c757d'; this.style.transform='scale(1)';">
                                <i class="fas fa-trash-alt" style="font-size: 1rem;"></i>
                            </a>
                        <?php endif; ?>
                    <?php endif; ?>
                    
                    <div style="color: #ffc107; font-weight: 500;">
                        <i class="fas fa-database me-1"></i> 
                        Mostrando <?php echo e($proformas->firstItem()); ?> a <?php echo e($proformas->lastItem()); ?> de <?php echo e($proformas->total()); ?> registros
                    </div>
                </div>
            <?php else: ?>
                <div class="text-center py-5">
                    <i class="fas fa-file-invoice-dollar fa-4x mb-4" style="color: #ffc107;"></i>
                    <h4 class="mb-3" style="color: #334155;">No hay proformas registradas</h4>
                    <p class="text-muted mb-4">
                        <?php if(request()->has('search') || request()->has('mes') || request()->has('anio') || request()->has('estado')): ?>
                            No hay proformas para los filtros seleccionados.
                            <a href="<?php echo e(route('proformas.index')); ?>">Ver todas</a>
                        <?php else: ?>
                            Comience creando su primera proforma.
                        <?php endif; ?>
                    </p>
                    
                    <?php if (app(\Illuminate\Contracts\Auth\Access\Gate::class)->check('crear proformas')): ?>
                        <a href="<?php echo e(route('proformas.create')); ?>" class="btn btn-primary" style="background-color: #ffc107; border-radius: 30px; padding: 10px 25px; color: #000; border: none; transition: all 0.3s ease;">
                            <i class="fas fa-plus-circle me-2"></i>
                            Crear primera proforma
                        </a>
                    <?php else: ?>
                        <div class="alert alert-info">
                            <i class="fas fa-info-circle me-2"></i>
                            Solo el administrador puede crear nuevas proformas.
                        </div>
                    <?php endif; ?>
                </div>
            <?php endif; ?>
        </div>
        
        <?php if($proformas->count() > 0): ?>
        <div class="card-footer bg-light">
            <div class="row">
                <div class="col-md-6">
                    <small class="text-muted">
                        <i class="fas fa-info-circle me-1" style="color: #ffc107;"></i>
                        Mostrando <?php echo e($proformas->count()); ?> de <?php echo e($proformas->total()); ?> proformas
                    </small>
                </div>
                <div class="col-md-6 text-end">
                    <small class="text-muted">
                        <i class="fas fa-calculator me-1" style="color: #ffc107;"></i>
                        Total General: 
                        <strong>Bs. <?php echo e(number_format($proformas->sum('total'), 2)); ?></strong>
                        | Adelantos: 
                        <strong>Bs. <?php echo e(number_format($proformas->sum('adelanto'), 2)); ?></strong>
                    </small>
                </div>
            </div>
        </div>
        <?php endif; ?>
    </div>
</div>

<style>
.btn-primary[style*="background-color: #ffc107"]:hover {
    background-color: #e6a800 !important;
    transform: translateY(-2px);
    box-shadow: 0 5px 15px rgba(255, 193, 7, 0.3);
}

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

.pagination .page-link {
    color: #ffc107 !important;
    border-radius: 8px;
    margin: 0 3px;
}

.pagination .page-item.active .page-link {
    background-color: #ffc107 !important;
    border-color: #ffc107 !important;
    color: #000 !important;
}

.btn-outline-warning,
.btn-outline-danger,
.btn-outline-success {
    transition: all 0.2s ease;
}

.btn-outline-warning:hover,
.btn-outline-danger:hover,
.btn-outline-success:hover {
    transform: translateY(-2px);
    box-shadow: 0 4px 10px rgba(0,0,0,0.1);
}

.input-group-text {
    border-radius: 30px 0 0 30px !important;
}

.form-control {
    border-radius: 0 !important;
}

.form-control:focus,
.form-select:focus,
.input-group-text:focus,
.btn:focus {
    border-color: #ffc107 !important;
    box-shadow: 0 0 0 3px rgba(255, 193, 7, 0.25) !important;
    outline: none !important;
}

#search:focus {
    border-color: #ffc107 !important;
    box-shadow: 0 0 0 3px rgba(255, 193, 7, 0.25) !important;
}

select.form-select:focus {
    border-color: #ffc107 !important;
    box-shadow: 0 0 0 3px rgba(255, 193, 7, 0.25) !important;
}

button[type="submit"]:focus {
    border-color: #ffc107 !important;
    box-shadow: 0 0 0 3px rgba(255, 193, 7, 0.25) !important;
}

.fa-file-invoice-dollar {
    color: #ffc107 !important;
}

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
        var tooltipTriggerList = [].slice.call(document.querySelectorAll('[data-bs-toggle="tooltip"]'));
        var tooltipList = tooltipTriggerList.map(function(tooltipTriggerEl) {
            return new bootstrap.Tooltip(tooltipTriggerEl, {
                placement: 'auto',
                trigger: 'hover',
                delay: { show: 50, hide: 50 },
                boundary: 'viewport'
            });
        });
        
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
</script>
<?php $__env->stopPush(); ?>
<?php $__env->stopSection(); ?>
<?php echo $__env->make('layouts.app', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH C:\Users\CORE I7\OneDrive\Escritorio\CIMA_v3_Local\resources\views/proformas/index.blade.php ENDPATH**/ ?>