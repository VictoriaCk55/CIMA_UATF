<?php $__env->startSection('title', 'Gestión de Informes'); ?>

<?php $__env->startSection('content'); ?>
<div class="container-main">
    <!-- ============================================ -->
    <!-- ENCABEZADO DE PÁGINA                         -->
    <!-- ============================================ -->
    <div class="page-header">
        <div class="d-flex justify-content-between align-items-center">
            <div>
                <h1>
                    <i class="fas fa-file-alt" style="color: #C2F527;"></i>
                    Gestión de Informes Técnicos
                </h1>
                <p class="page-subtitle">
                    Listado de informes técnicos generados en el sistema CIMA
                </p>
            </div>
            
            <div class="d-flex gap-2">
                <?php if(auth()->guard()->check()): ?>
                    <?php if(Auth::user()->hasAnyRole(['admin', 'tecnico'])): ?>
                        <a href="<?php echo e(route('informes.create')); ?>" 
                           class="btn" 
                           style="background-color: #C2F527; border-radius: 30px; padding: 10px 25px; color: #000000; border: none; transition: all 0.3s ease;"
                           onmouseover="this.style.backgroundColor='#a8d420'; this.style.transform='translateY(-2px)'; this.style.boxShadow='0 5px 15px rgba(194, 245, 39, 0.3)';"
                           onmouseout="this.style.backgroundColor='#C2F527'; this.style.transform='translateY(0)'; this.style.boxShadow='none';">
                            <i class="fas fa-plus-circle"></i>
                            Nuevo Informe
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

    <!-- ============================================ -->
    <!-- BUSCADOR Y FILTROS                           -->
    <!-- ============================================ -->
    <div class="card mb-4">
        <div class="card-body">
            <form method="GET" action="<?php echo e(route('informes.index')); ?>" class="row g-3">
                <!-- Buscador por código -->
                <div class="col-md-12 mb-3">
                    <label for="search" class="form-label fw-semibold">
                        <i class="fas fa-search me-2" style="color: #C2F527;"></i>
                        Buscar Informe
                    </label>
                    <div class="input-group">
                        <span class="input-group-text" style="background-color: #C2F527; color: #000; border: none;">
                            <i class="fas fa-search"></i>
                        </span>
                        <input type="text" 
                               class="form-control" 
                               id="search" 
                               name="search" 
                               value="<?php echo e(request('search')); ?>" 
                               placeholder="Buscar por código de informe (ej: INF-001)..."
                               style="border-left: none;">
                        <?php if(request('search')): ?>
                        <a href="<?php echo e(route('informes.index')); ?>" class="btn btn-outline-secondary" style="border-radius: 0 30px 30px 0;">
                            <i class="fas fa-times"></i> Limpiar
                        </a>
                        <?php endif; ?>
                    </div>
                    <small class="text-muted mt-1 d-block">
                        <i class="fas fa-info-circle me-1"></i>
                        Puede buscar por código de informe (formato INF-XXX).
                    </small>
                </div>
                
                <!-- Filtros existentes -->
                <div class="col-md-3">
                    <label for="mes" class="form-label">
                        <i class="fas fa-calendar-alt me-1" style="color: #C2F527;"></i> Mes
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
                        <i class="fas fa-calendar me-1" style="color: #C2F527;"></i> Año
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
                        <i class="fas fa-flag me-1" style="color: #C2F527;"></i> Estado
                    </label>
                    <select name="estado" id="estado" class="form-select">
                        <option value="">Todos los estados</option>
                        <option value="BORRADOR" <?php echo e(request('estado') == 'BORRADOR' ? 'selected' : ''); ?>>📝 Borrador</option>
                        <option value="EN_PROCESO" <?php echo e(request('estado') == 'EN_PROCESO' ? 'selected' : ''); ?>>⏳ En Proceso</option>
                        <option value="REVISADO" <?php echo e(request('estado') == 'REVISADO' ? 'selected' : ''); ?>>👁️ Revisado</option>
                        <option value="APROBADO" <?php echo e(request('estado') == 'APROBADO' ? 'selected' : ''); ?>>✅ Aprobado</option>
                        <option value="ENTREGADO" <?php echo e(request('estado') == 'ENTREGADO' ? 'selected' : ''); ?>>📤 Entregado</option>
                    </select>
                </div>
                
                <div class="col-md-4 d-flex align-items-end">
                    <button type="submit" 
                            class="btn me-2" 
                            style="background-color: #C2F527; border-radius: 30px; padding: 8px 20px; color: #000000; border: none; transition: all 0.3s ease;"
                            onmouseover="this.style.backgroundColor='#a8d420'; this.style.transform='translateY(-2px)'; this.style.boxShadow='0 5px 15px rgba(194, 245, 39, 0.3)';"
                            onmouseout="this.style.backgroundColor='#C2F527'; this.style.transform='translateY(0)'; this.style.boxShadow='none';">
                        <i class="fas fa-filter"></i> Filtrar
                    </button>
                    <a href="<?php echo e(route('informes.index')); ?>" 
                       class="btn btn-secondary" 
                       style="border-radius: 30px; padding: 8px 20px;">
                        <i class="fas fa-eraser"></i> Limpiar
                    </a>
                </div>
            </form>
        </div>
        
        <!-- Footer informativo -->
        <?php if(request()->has('search') || request()->has('mes') || request()->has('anio') || request()->has('estado')): ?>
            <div class="card-footer bg-light py-2">
                <small class="text-muted">
                    <i class="fas fa-info-circle me-1" style="color: #C2F527;"></i>
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
                            $estados = [
                                'BORRADOR' => 'Borrador', 
                                'EN_PROCESO' => 'En Proceso', 
                                'REVISADO' => 'Revisado', 
                                'APROBADO' => 'Aprobado',
                                'ENTREGADO' => 'Entregado'
                            ];
                        ?>
                        con estado <strong><?php echo e($estados[request('estado')] ?? request('estado')); ?></strong>
                    <?php endif; ?>
                </small>
            </div>
        <?php endif; ?>
    </div>

    <!-- ============================================ -->
    <!-- ESTADÍSTICAS                                 -->
    <!-- ============================================ -->
    <?php if(isset($estadisticas) && count($estadisticas) > 0): ?>
    <div class="row mb-4">
        <div class="col-md-2 col-6">
            <div class="card text-white h-100" style="background-color: #C2F527; border: none;">
                <div class="card-body text-center py-3">
                    <h5 class="card-title mb-1" style="color: #000;"><?php echo e($estadisticas['total'] ?? 0); ?></h5>
                    <p class="card-text small mb-0" style="color: #000;">Total</p>
                </div>
            </div>
        </div>
        <div class="col-md-2 col-6">
            <div class="card text-white h-100" style="background-color: #6c757d; border: none;">
                <div class="card-body text-center py-3">
                    <h5 class="card-title mb-1 text-white"><?php echo e($estadisticas['borrador'] ?? 0); ?></h5>
                    <p class="card-text small mb-0 text-white">Borrador</p>
                </div>
            </div>
        </div>
        <div class="col-md-2 col-6">
            <div class="card text-white h-100" style="background-color: #ffc107; border: none;">
                <div class="card-body text-center py-3">
                    <h5 class="card-title mb-1" style="color: #000;"><?php echo e($estadisticas['en_proceso'] ?? 0); ?></h5>
                    <p class="card-text small mb-0" style="color: #000;">En Proceso</p>
                </div>
            </div>
        </div>
        <div class="col-md-2 col-6">
            <div class="card text-white h-100" style="background-color: #0dcaf0; border: none;">
                <div class="card-body text-center py-3">
                    <h5 class="card-title mb-1 text-white"><?php echo e($estadisticas['revisado'] ?? 0); ?></h5>
                    <p class="card-text small mb-0 text-white">Revisado</p>
                </div>
            </div>
        </div>
        <div class="col-md-2 col-6">
            <div class="card text-white h-100" style="background-color: #198754; border: none;">
                <div class="card-body text-center py-3">
                    <h5 class="card-title mb-1 text-white"><?php echo e($estadisticas['aprobado'] ?? 0); ?></h5>
                    <p class="card-text small mb-0 text-white">Aprobado</p>
                </div>
            </div>
        </div>
        <div class="col-md-2 col-6">
            <div class="card text-white h-100" style="background-color: #212529; border: none;">
                <div class="card-body text-center py-3">
                    <h5 class="card-title mb-1 text-white"><?php echo e($estadisticas['entregado'] ?? 0); ?></h5>
                    <p class="card-text small mb-0 text-white">Entregado</p>
                </div>
            </div>
        </div>
    </div>
    <?php endif; ?>

    <!-- ============================================ -->
    <!-- TABLA DE INFORMES                            -->
    <!-- ============================================ -->
    <div class="card">
        <div class="card-header d-flex justify-content-between align-items-center">
            <h5 class="mb-0">
                <i class="fas fa-list me-2" style="color: #C2F527;"></i>
                Listado de Informes
            </h5>
            <span class="badge" 
                  style="background-color: #C2F527; color: #000; padding: 8px 15px; border-radius: 20px; font-weight: 500;">
                <?php echo e($informes->total()); ?> registros
            </span>
        </div>
        
        <div class="card-body">
            <?php if(session('success')): ?>
                <div class="alert alert-success alert-dismissible fade show" role="alert">
                    <i class="fas fa-check-circle me-2"></i> <?php echo e(session('success')); ?>

                    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                </div>
            <?php endif; ?>

            <?php if(session('error')): ?>
                <div class="alert alert-danger alert-dismissible fade show" role="alert">
                    <i class="fas fa-exclamation-triangle me-2"></i> <?php echo e(session('error')); ?>

                    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                </div>
            <?php endif; ?>

            <?php if($informes->count() > 0): ?>
                <div class="table-responsive">
                    <table class="table table-hover">
                        <thead>
                            <tr style="background-color: #C2F527; color: #000;">
                                <th width="100" style="background-color: #C2F527; color: #000; font-weight: 600; text-transform: uppercase; font-size: 0.85rem; letter-spacing: 0.5px; padding: 12px 15px;">Código</th>
                                <th style="background-color: #C2F527; color: #000; font-weight: 600; text-transform: uppercase; font-size: 0.85rem; letter-spacing: 0.5px; padding: 12px 15px;">Proforma / Cliente</th>
                                <th width="120" style="background-color: #C2F527; color: #000; font-weight: 600; text-transform: uppercase; font-size: 0.85rem; letter-spacing: 0.5px; padding: 12px 15px;">Estado</th>
                                <th width="100" style="background-color: #C2F527; color: #000; font-weight: 600; text-transform: uppercase; font-size: 0.85rem; letter-spacing: 0.5px; padding: 12px 15px;">Prioridad</th>
                                <th width="120" style="background-color: #C2F527; color: #000; font-weight: 600; text-transform: uppercase; font-size: 0.85rem; letter-spacing: 0.5px; padding: 12px 15px;">Emisión</th>
                                <th width="140" class="text-center" style="background-color: #C2F527; color: #000; font-weight: 600; text-transform: uppercase; font-size: 0.85rem; letter-spacing: 0.5px; padding: 12px 15px;">Acciones</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php $__currentLoopData = $informes; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $informe): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                <tr>
                                    <td>
                                        <span class="badge fs-6" style="background-color: #0dcaf0; color: #000000;">
                                            <i class="fas fa-hashtag me-1"></i>
                                            <?php echo e($informe->codigo); ?>

                                        </span>
                                    </td>
                                    
                                    <td>
                                        <?php if($informe->proforma): ?>
                                            <strong>
                                                <a href="<?php echo e(route('proformas.show', $informe->proforma_id)); ?>" 
                                                   class="text-decoration-none">
                                                    <span style="color: #000000; font-weight: 600;">
                                                        <?php echo e($informe->proforma->codigo ?? 'N/A'); ?>

                                                    </span>
                                                </a>
                                            </strong>
                                            <br>
                                            <small class="text-muted">
                                                <i class="fas fa-user-tie me-1"></i>
                                                <?php echo e($informe->proforma->cliente->razon_social ?? 'Cliente no disponible'); ?>

                                            </small>
                                        <?php else: ?>
                                            <span class="text-muted">Sin proforma asociada</span>
                                        <?php endif; ?>
                                        <br>
                                        <small class="text-muted">
                                            <i class="fas fa-user me-1"></i>
                                            <?php echo e($informe->creador->name ?? 'Usuario no disponible'); ?>

                                        </small>
                                    </td>
                                    
                                    <td>
                                        <span class="badge bg-<?php echo e($informe->estado_color ?? 'secondary'); ?>">
                                            <i class="fas 
                                                <?php if($informe->estado == 'BORRADOR'): ?> fa-edit
                                                <?php elseif($informe->estado == 'EN_PROCESO'): ?> fa-spinner
                                                <?php elseif($informe->estado == 'REVISADO'): ?> fa-eye
                                                <?php elseif($informe->estado == 'APROBADO'): ?> fa-check
                                                <?php elseif($informe->estado == 'ENTREGADO'): ?> fa-check-double
                                                <?php else: ?> fa-file
                                                <?php endif; ?> me-1"></i>
                                            <?php echo e($informe->estado_texto ?? $informe->estado); ?>

                                        </span>
                                    </td>
                                    
                                    <td>
                                        <span class="badge bg-<?php echo e($informe->prioridad_color ?? 'light'); ?>">
                                            <i class="fas 
                                                <?php if($informe->prioridad == 'URGENTE'): ?> fa-exclamation-triangle
                                                <?php elseif($informe->prioridad == 'ALTA'): ?> fa-arrow-up
                                                <?php elseif($informe->prioridad == 'MEDIA'): ?> fa-equals
                                                <?php else: ?> fa-arrow-down
                                                <?php endif; ?> me-1"></i>
                                            <?php echo e($informe->prioridad_texto ?? $informe->prioridad); ?>

                                        </span>
                                    </td>
                                    
                                    <td>
                                        <small>
                                            <i class="far fa-calendar me-1" style="color: #C2F527;"></i>
                                            <?php echo e($informe->fecha_emision->format('d/m/Y')); ?>

                                        </small>
                                    </td>
                                    
                                    <td class="text-center">
                                        <div class="btn-group" role="group">
                                            <!-- Botón VER -->
                                            <a href="<?php echo e(route('informes.show', $informe)); ?>" 
                                               class="btn btn-sm"
                                               style="color: #0dcaf0; border: 1px solid #0dcaf0; background: transparent; border-radius: 6px; padding: 0.5rem; width: 38px; height: 38px; transition: all 0.2s ease; display: inline-flex; align-items: center; justify-content: center;"
                                               data-bs-toggle="tooltip"
                                               title="Ver detalles del informe"
                                               onmouseover="this.style.backgroundColor='#0dcaf0'; this.style.color='white';"
                                               onmouseout="this.style.backgroundColor='transparent'; this.style.color='#0dcaf0';">
                                                <i class="fas fa-eye"></i>
                                            </a>
                                            
                                            <?php if(auth()->guard()->check()): ?>
                                                <?php if(Auth::user()->hasAnyRole(['admin', 'tecnico'])): ?>
                                                    <a href="<?php echo e(route('informes.edit', $informe)); ?>" 
                                                       class="btn btn-outline-warning btn-sm"
                                                       data-bs-toggle="tooltip"
                                                       title="Editar informe">
                                                        <i class="fas fa-edit"></i>
                                                    </a>
                                                    
                                                    <form action="<?php echo e(route('informes.destroy', $informe)); ?>" 
                                                          method="POST" 
                                                          class="d-inline"
                                                          onsubmit="return confirm('¿Está seguro de eliminar el informe <?php echo e($informe->codigo); ?>?');">
                                                        <?php echo csrf_field(); ?>
                                                        <?php echo method_field('DELETE'); ?>
                                                        <button type="submit" 
                                                                class="btn btn-outline-danger btn-sm"
                                                                data-bs-toggle="tooltip"
                                                                title="Eliminar informe">
                                                            <i class="fas fa-trash"></i>
                                                        </button>
                                                    </form>
                                                <?php endif; ?>
                                            <?php endif; ?>
                                            
                                            <a href="<?php echo e(route('informes.pdf', $informe)); ?>" 
                                               class="btn btn-outline-success btn-sm"
                                               data-bs-toggle="tooltip"
                                               title="Generar PDF"
                                               target="_blank">
                                                <i class="fas fa-file-pdf"></i>
                                            </a>
                                        </div>
                                    </td>
                                </tr>
                            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                        </tbody>
                    </table>
                </div>

                <!-- Botón de Papelera y texto de registros centrado -->
                <div class="d-flex align-items-center justify-content-center position-relative mt-3">
                    <?php if(auth()->guard()->check()): ?>
                        <?php if(Auth::user()->hasAnyRole(['admin', 'tecnico'])): ?>
                            <a href="<?php echo e(route('informes.trash')); ?>" 
                               class="btn btn-icon-circle position-absolute start-0"
                               style="width: 35px; height: 35px; border-radius: 50%; background-color: #6c757d; color: white; display: inline-flex; align-items: center; justify-content: center; transition: all 0.3s ease; text-decoration: none;"
                               data-bs-toggle="tooltip"
                               title="Ver informes eliminados"
                               onmouseover="this.style.backgroundColor='#5a6268'; this.style.transform='scale(1.1)';"
                               onmouseout="this.style.backgroundColor='#6c757d'; this.style.transform='scale(1)';">
                                <i class="fas fa-trash-alt" style="font-size: 1rem;"></i>
                            </a>
                        <?php endif; ?>
                    <?php endif; ?>
                    
                    <div style="color: #C2F527; font-weight: 500;">
                        <i class="fas fa-database me-1"></i> 
                        Mostrando <?php echo e($informes->firstItem()); ?> a <?php echo e($informes->lastItem()); ?> de <?php echo e($informes->total()); ?> registros
                    </div>
                </div>
                
            <?php else: ?>
                <div class="text-center py-5">
                    <i class="fas fa-file-alt fa-4x mb-4" style="color: #C2F527;"></i>
                    <h4 class="mb-3" style="color: #334155;">No hay informes registrados</h4>
                    <p class="text-muted mb-4">
                        <?php if(request()->has('search') || request()->has('mes') || request()->has('anio') || request()->has('estado')): ?>
                            No hay informes para los filtros seleccionados.
                            <a href="<?php echo e(route('informes.index')); ?>" style="color: #C2F527;">Ver todos</a>
                        <?php else: ?>
                            Comience creando su primer informe técnico.
                        <?php endif; ?>
                    </p>
                    
                    <?php if(auth()->guard()->check()): ?>
                        <?php if(Auth::user()->hasAnyRole(['admin', 'tecnico'])): ?>
                            <a href="<?php echo e(route('informes.create')); ?>" 
                               class="btn" 
                               style="background-color: #C2F527; border-radius: 30px; padding: 10px 25px; color: #000000; border: none; transition: all 0.3s ease;"
                               onmouseover="this.style.backgroundColor='#a8d420'; this.style.transform='translateY(-2px)'; this.style.boxShadow='0 5px 15px rgba(194, 245, 39, 0.3)';"
                               onmouseout="this.style.backgroundColor='#C2F527'; this.style.transform='translateY(0)'; this.style.boxShadow='none';">
                                <i class="fas fa-plus-circle me-2"></i>
                                Crear primer informe
                            </a>
                        <?php else: ?>
                            <div class="alert alert-info">
                                <i class="fas fa-info-circle me-2"></i>
                                Solo el administrador puede crear nuevos informes.
                            </div>
                        <?php endif; ?>
                    <?php endif; ?>
                </div>
            <?php endif; ?>
        </div>
        
        <?php if($informes->count() > 0): ?>
        <div class="card-footer bg-light">
            <div class="row">
                <div class="col-md-6">
                    <small class="text-muted">
                        <i class="fas fa-info-circle me-1" style="color: #C2F527;"></i>
                        Mostrando <?php echo e($informes->count()); ?> de <?php echo e($informes->total()); ?> informes
                    </small>
                </div>
                <div class="col-md-6 text-end">
                    <small class="text-muted">
                        <i class="fas fa-calculator me-1" style="color: #C2F527;"></i>
                        Total: <strong><?php echo e($informes->total()); ?> registros</strong>
                    </small>
                </div>
            </div>
        </div>
        <?php endif; ?>
    </div>
</div>

<!-- ============================================ -->
<!-- ESTILOS ADICIONALES                           -->
<!-- ============================================ -->
<style>
/* Estilo para el botón de nuevo informe */
.btn[style*="background-color: #C2F527"]:hover {
    background-color: #a8d420 !important;
    transform: translateY(-2px);
    box-shadow: 0 5px 15px rgba(194, 245, 39, 0.3);
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
    color: #C2F527 !important;
    border-radius: 8px;
    margin: 0 3px;
}

.pagination .page-item.active .page-link {
    background-color: #C2F527 !important;
    border-color: #C2F527 !important;
    color: #000 !important;
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

/* Estilo para el foco de inputs y selects en informes */
.form-control:focus,
.form-select:focus,
.input-group-text:focus,
.btn:focus {
    border-color: #C2F527 !important;
    box-shadow: 0 0 0 3px rgba(194, 245, 39, 0.25) !important;
    outline: none !important;
}

/* Específico para el input de búsqueda */
#search:focus {
    border-color: #C2F527 !important;
    box-shadow: 0 0 0 3px rgba(194, 245, 39, 0.25) !important;
}

/* Para los selects de filtros */
select.form-select:focus {
    border-color: #C2F527 !important;
    box-shadow: 0 0 0 3px rgba(194, 245, 39, 0.25) !important;
}

/* Para el botón de filtrar */
button[type="submit"]:focus {
    border-color: #C2F527 !important;
    box-shadow: 0 0 0 3px rgba(194, 245, 39, 0.25) !important;
}

/* Reforzar color del icono de informes */
.fa-file-alt {
    color: #C2F527 !important;
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
</script>
<?php $__env->stopPush(); ?>
<?php $__env->stopSection(); ?>
<?php echo $__env->make('layouts.app', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH D:\pruebaSystem\System-CIMA\application\resources\views/informes/index.blade.php ENDPATH**/ ?>