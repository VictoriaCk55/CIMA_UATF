@extends('layouts.app')

@section('title', 'Gestión de Informes')

@section('content')
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
            
            <div class="d-flex gap-2 flex-wrap">
                @auth
                     @can('crear parametros')
                        <a href="{{ route('parametros.create') }}" class="btn" style="background-color: #A31800; border-radius: 30px; padding: 10px 25px; color: white; border: none; transition: all 0.3s ease;"
                           onmouseover="this.style.backgroundColor='#7a1200'; this.style.transform='translateY(-2px)'; this.style.boxShadow='0 5px 15px rgba(163, 24, 0, 0.3)';"
                           onmouseout="this.style.backgroundColor='#A31800'; this.style.transform='translateY(0px)'; this.style.boxShadow='none';">
                            <i class="fas fa-plus-circle"></i>
                            Nuevo Informe
                        </a>
                    @else
                        <div class="alert alert-info mb-0 py-2 px-3">
                            <i class="fas fa-eye me-1"></i> Modo solo lectura
                        </div>
                    @endif
                @endauth

                <!-- BOTÓN NUEVO PARA ACTUALIZAR PRECIOS CON ANIMACIÓN -->
                @can('editar parametros')
                    <a href="{{ route('parametros.precios.masivos') }}" 
                       class="btn" 
                       style="background-color: #28a745; border-radius: 30px; padding: 10px 25px; color: white; border: none; transition: all 0.3s ease;"
                       onmouseover="this.style.backgroundColor='#218838'; this.style.transform='translateY(-2px)'; this.style.boxShadow='0 5px 15px rgba(40, 167, 69, 0.3)';"
                       onmouseout="this.style.backgroundColor='#28a745'; this.style.transform='translateY(0px)'; this.style.boxShadow='none';">
                        <i class="fas fa-dollar-sign me-1"></i>
                        Actualizar Precios
                    </a>
                @endcan
            </div>
        </div>
    </div>

    <!-- ============================================ -->
    <!-- BUSCADOR Y FILTROS                           -->
    <!-- ============================================ -->
    <div class="card mb-4">
        <div class="card-body">
            <form action="{{ route('parametros.index') }}" method="GET" id="searchForm">
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
                                   value="{{ request('search') }}" 
                                   placeholder="Buscar por ID, Nombre, Método o Categoría..."
                                   style="border-left: none;">
                            <button class="btn" type="submit" style="background-color: #A31800; color: white; border: none;">
                                <i class="fas fa-filter me-1"></i> Filtrar
                            </button>
                            @if(request('search'))
                                <a href="{{ route('parametros.index') }}" class="btn btn-outline-secondary" style="border-radius: 0 30px 30px 0;">
                                    <i class="fas fa-times"></i> Limpiar
                                </a>
                            @endif
                        </div>
                        <small class="text-muted mt-1 d-block">
                            <i class="fas fa-info-circle me-1"></i>
                            Puede buscar por ID, nombre del parámetro, método de análisis o categoría.
                        </small>
                    </div>
                    
                    <div class="col-md-4 text-md-end mt-3 mt-md-0">
                        <div class="d-flex align-items-center justify-content-md-end">
                            <span class="badge" style="background-color: #A31800; color: white; padding: 8px 15px; border-radius: 20px; font-weight: 500;">
                                <i class="fas fa-database me-1"></i> Total: {{ $parametros->total() }} parámetros
                            </span>
                        </div>
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
                        @foreach(range(1, 12) as $m)
                            <option value="{{ $m }}" {{ request('mes') == $m ? 'selected' : '' }}>
                                {{ \Carbon\Carbon::create()->month($m)->locale('es')->monthName }}
                            </option>
                        @endforeach
                    </select>
                </div>
                
                <div class="col-md-2">
                    <label for="anio" class="form-label">
                        <i class="fas fa-calendar me-1" style="color: #C2F527;"></i> Año
                    </label>
                    <select name="anio" id="anio" class="form-select">
                        <option value="">Todos los años</option>
                        @if(isset($añosDisponibles) && count($añosDisponibles) > 0)
                            @foreach($añosDisponibles as $año)
                                <option value="{{ $año }}" {{ request('anio') == $año ? 'selected' : '' }}>
                                    {{ $año }}
                                </option>
                            @endforeach
                        @else
                            @for($a = date('Y'); $a >= date('Y')-5; $a--)
                                <option value="{{ $a }}" {{ request('anio') == $a ? 'selected' : '' }}>
                                    {{ $a }}
                                </option>
                            @endfor
                        @endif
                    </select>
                </div>
                
                <div class="col-md-3">
                    <label for="estado" class="form-label">
                        <i class="fas fa-flag me-1" style="color: #C2F527;"></i> Estado
                    </label>
                    <select name="estado" id="estado" class="form-select">
                        <option value="">Todos los estados</option>
                        <option value="BORRADOR" {{ request('estado') == 'BORRADOR' ? 'selected' : '' }}>📝 Borrador</option>
                        <option value="EN_PROCESO" {{ request('estado') == 'EN_PROCESO' ? 'selected' : '' }}>⏳ En Proceso</option>
                        <option value="REVISADO" {{ request('estado') == 'REVISADO' ? 'selected' : '' }}>👁️ Revisado</option>
                        <option value="APROBADO" {{ request('estado') == 'APROBADO' ? 'selected' : '' }}>✅ Aprobado</option>
                        <option value="ENTREGADO" {{ request('estado') == 'ENTREGADO' ? 'selected' : '' }}>📤 Entregado</option>
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
                    <a href="{{ route('informes.index') }}" 
                       class="btn btn-secondary" 
                       style="border-radius: 30px; padding: 8px 20px;">
                        <i class="fas fa-eraser"></i> Limpiar
                    </a>
                </div>
            </form>
        </div>
        
        <!-- Footer informativo -->
        @if(request()->has('search') || request()->has('mes') || request()->has('anio') || request()->has('estado'))
            <div class="card-footer bg-light py-2">
                <small class="text-muted">
                    <i class="fas fa-info-circle me-1" style="color: #C2F527;"></i>
                    Mostrando resultados para:
                    @if(request('search'))
                        búsqueda "<strong>{{ request('search') }}</strong>"
                    @endif
                    @if(request('mes') && request('anio'))
                        @php
                            $mesNumerico = (int)request('mes');
                            $anioNumerico = (int)request('anio');
                            $nombreMes = \Carbon\Carbon::createFromDate($anioNumerico, $mesNumerico, 1)->locale('es')->monthName;
                        @endphp
                        de {{ $nombreMes }} de {{ request('anio') }}
                    @elseif(request('mes'))
                        @php
                            $mesNumerico = (int)request('mes');
                            $nombreMes = \Carbon\Carbon::create()->month($mesNumerico)->locale('es')->monthName;
                        @endphp
                        de {{ $nombreMes }}
                    @elseif(request('anio'))
                        del año {{ request('anio') }}
                    @endif
                    
                    @if(request('estado'))
                        @php
                            $estados = [
                                'BORRADOR' => 'Borrador', 
                                'EN_PROCESO' => 'En Proceso', 
                                'REVISADO' => 'Revisado', 
                                'APROBADO' => 'Aprobado',
                                'ENTREGADO' => 'Entregado'
                            ];
                        @endphp
                        con estado <strong>{{ $estados[request('estado')] ?? request('estado') }}</strong>
                    @endif
                </small>
            </div>
        @endif
    </div>

    <!-- ============================================ -->
    <!-- ESTADÍSTICAS                                 -->
    <!-- ============================================ -->
    @if(isset($estadisticas) && count($estadisticas) > 0)
    <div class="row mb-4">
        <div class="col-md-2 col-6">
            <div class="card text-white h-100" style="background-color: #C2F527; border: none;">
                <div class="card-body text-center py-3">
                    <h5 class="card-title mb-1" style="color: #000;">{{ $estadisticas['total'] ?? 0 }}</h5>
                    <p class="card-text small mb-0" style="color: #000;">Total</p>
                </div>
            </div>
        </div>
        <div class="col-md-2 col-6">
            <div class="card text-white h-100" style="background-color: #6c757d; border: none;">
                <div class="card-body text-center py-3">
                    <h5 class="card-title mb-1 text-white">{{ $estadisticas['borrador'] ?? 0 }}</h5>
                    <p class="card-text small mb-0 text-white">Borrador</p>
                </div>
            </div>
        </div>
        <div class="col-md-2 col-6">
            <div class="card text-white h-100" style="background-color: #ffc107; border: none;">
                <div class="card-body text-center py-3">
                    <h5 class="card-title mb-1" style="color: #000;">{{ $estadisticas['en_proceso'] ?? 0 }}</h5>
                    <p class="card-text small mb-0" style="color: #000;">En Proceso</p>
                </div>
            </div>
        </div>
        <div class="col-md-2 col-6">
            <div class="card text-white h-100" style="background-color: #0dcaf0; border: none;">
                <div class="card-body text-center py-3">
                    <h5 class="card-title mb-1 text-white">{{ $estadisticas['revisado'] ?? 0 }}</h5>
                    <p class="card-text small mb-0 text-white">Revisado</p>
                </div>
            </div>
        </div>
        <div class="col-md-2 col-6">
            <div class="card text-white h-100" style="background-color: #198754; border: none;">
                <div class="card-body text-center py-3">
                    <h5 class="card-title mb-1 text-white">{{ $estadisticas['aprobado'] ?? 0 }}</h5>
                    <p class="card-text small mb-0 text-white">Aprobado</p>
                </div>
            </div>
        </div>
        <div class="col-md-2 col-6">
            <div class="card text-white h-100" style="background-color: #212529; border: none;">
                <div class="card-body text-center py-3">
                    <h5 class="card-title mb-1 text-white">{{ $estadisticas['entregado'] ?? 0 }}</h5>
                    <p class="card-text small mb-0 text-white">Entregado</p>
                </div>
            </div>
        </div>
    </div>
    @endif

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
                {{ $informes->total() }} registros
            </span>
        </div>
        
        <div class="card-body">
            @if(session('success'))
                <div class="alert alert-success alert-dismissible fade show" role="alert">
                    <i class="fas fa-check-circle me-2"></i> {{ session('success') }}
                    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                </div>
            @endif

            @if(session('error'))
                <div class="alert alert-danger alert-dismissible fade show" role="alert">
                    <i class="fas fa-exclamation-triangle me-2"></i> {{ session('error') }}
                    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                </div>
            @endif

            @if($informes->count() > 0)
                <div class="table-responsive">
                    <table class="table table-hover">
                        <thead>
                            <tr style="background-color: #A31800; color: white;">
                                <th width="80" style="background-color: #A31800; color: white; font-weight: 600; text-transform: uppercase; font-size: 0.85rem; letter-spacing: 0.5px; padding: 12px 15px;">ID</th>
                                <th style="background-color: #A31800; color: white; font-weight: 600; text-transform: uppercase; font-size: 0.85rem; letter-spacing: 0.5px; padding: 12px 15px;">Parámetro</th>
                                <th style="background-color: #A31800; color: white; font-weight: 600; text-transform: uppercase; font-size: 0.85rem; letter-spacing: 0.5px; padding: 12px 15px;">Método</th>
                                <th width="140" class="text-end" style="background-color: #A31800; color: white; font-weight: 600; text-transform: uppercase; font-size: 0.85rem; letter-spacing: 0.5px; padding: 12px 15px;">Precio Unitario</th>
                                <th width="120" style="background-color: #A31800; color: white; font-weight: 600; text-transform: uppercase; font-size: 0.85rem; letter-spacing: 0.5px; padding: 12px 15px;">Categoría</th>
                                <th width="120" style="background-color: #A31800; color: white; font-weight: 600; text-transform: uppercase; font-size: 0.85rem; letter-spacing: 0.5px; padding: 12px 15px;">Tipo de Análisis</th>
                                <th width="100" class="text-center" style="background-color: #A31800; color: white; font-weight: 600; text-transform: uppercase; font-size: 0.85rem; letter-spacing: 0.5px; padding: 12px 15px;">Proformas</th>
                                <th width="140" class="text-center" style="background-color: #A31800; color: white; font-weight: 600; text-transform: uppercase; font-size: 0.85rem; letter-spacing: 0.5px; padding: 12px 15px;">Acciones</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($informes as $informe)
                                <tr>
                                    <td>
                                        <span class="badge bg-secondary">#{{ $parametro->id }}</span>
                                    </td>
                                    <td>
                                        <strong>{{ $parametro->nombre }}</strong>
                                    </td>
                                    <td>
                                        <small class="text-muted">
                                            <i class="fas fa-microscope me-1"></i>
                                            {{ $parametro->metodo }}
                                        </small>
                                    </td>
                                    <td class="text-end fw-semibold text-success">
                                        <i class="fas fa-dollar-sign me-1"></i>
                                        {{ number_format($parametro->precio_unitario, 2) }}
                                    </td>
                                    <td>
                                        <span class="badge 
                                            @if($parametro->tipo == 'AMBIENTAL') bg-warning text-dark
                                            @elseif($parametro->tipo == 'AGUA') bg-info
                                            @elseif($parametro->tipo == 'INVESTIGACION') bg-secondary
                                            @else bg-secondary
                                            @endif">
                                            <i class="fas 
                                                @if($parametro->tipo == 'AMBIENTAL') fa-leaf
                                                @elseif($parametro->tipo == 'AGUA') fa-tint
                                                @elseif($parametro->tipo == 'INVESTIGACION') fa-flask
                                                @else fa-tag
                                                @endif me-1"></i>
                                            {{ $parametro->tipo ?? 'N/A' }}
                                        </span>
                                    </td>
                                    <td>
                                        <span class="badge 
                                            @if($parametro->categoria == 'AIRE') bg-warning text-dark
                                            @elseif($parametro->categoria == 'RUIDO') bg-info
                                            @elseif($parametro->categoria == 'GASES') bg-danger
                                            @elseif($parametro->categoria == 'AGUA') bg-primary
                                            @elseif($parametro->categoria == 'SUELO') bg-success
                                            @else bg-secondary
                                            @endif">
                                            <i class="fas 
                                                @if($parametro->categoria == 'AIRE') fa-wind
                                                @elseif($parametro->categoria == 'RUIDO') fa-volume-up
                                                @elseif($parametro->categoria == 'GASES') fa-industry
                                                @elseif($parametro->categoria == 'AGUA') fa-tint
                                                @elseif($parametro->categoria == 'SUELO') fa-mountain
                                                @else fa-tag
                                                @endif me-1"></i>
                                            {{ $parametro->categoria ?? 'N/A' }}
                                        </span>
                                    </td>
                                    
                                    <td>
                                        @if($informe->proforma)
                                            <strong>
                                                <a href="{{ route('proformas.show', $informe->proforma_id) }}" 
                                                   class="text-decoration-none">
                                                    <span style="color: #000000; font-weight: 600;">
                                                        {{ $informe->proforma->codigo ?? 'N/A' }}
                                                    </span>
                                                </a>
                                            </strong>
                                            <br>
                                            <small class="text-muted">
                                                <i class="fas fa-user-tie me-1"></i>
                                                {{ $informe->proforma->cliente->razon_social ?? 'Cliente no disponible' }}
                                            </small>
                                        @else
                                            <span class="text-muted">Sin proforma asociada</span>
                                        @endif
                                        <br>
                                        <small class="text-muted">
                                            <i class="fas fa-user me-1"></i>
                                            {{ $informe->creador->name ?? 'Usuario no disponible' }}
                                        </small>
                                    </td>
                                    
                                    <td>
                                        <span class="badge bg-{{ $informe->estado_color ?? 'secondary' }}">
                                            <i class="fas 
                                                @if($informe->estado == 'BORRADOR') fa-edit
                                                @elseif($informe->estado == 'EN_PROCESO') fa-spinner
                                                @elseif($informe->estado == 'REVISADO') fa-eye
                                                @elseif($informe->estado == 'APROBADO') fa-check
                                                @elseif($informe->estado == 'ENTREGADO') fa-check-double
                                                @else fa-file
                                                @endif me-1"></i>
                                            {{ $informe->estado_texto ?? $informe->estado }}
                                        </span>
                                    </td>
                                    
                                    <td>
                                        <span class="badge bg-{{ $informe->prioridad_color ?? 'light' }}">
                                            <i class="fas 
                                                @if($informe->prioridad == 'URGENTE') fa-exclamation-triangle
                                                @elseif($informe->prioridad == 'ALTA') fa-arrow-up
                                                @elseif($informe->prioridad == 'MEDIA') fa-equals
                                                @else fa-arrow-down
                                                @endif me-1"></i>
                                            {{ $informe->prioridad_texto ?? $informe->prioridad }}
                                        </span>
                                    </td>
                                    
                                    <td>
                                        <small>
                                            <i class="far fa-calendar me-1" style="color: #C2F527;"></i>
                                            {{ $informe->fecha_emision->format('d/m/Y') }}
                                        </small>
                                    </td>
                                    
                                    <td class="text-center">
                                        <div class="btn-group" role="group">
                                            <a href="{{ route('parametros.show', $parametro) }}" 
                                               class="btn btn-sm"
                                               style="color: #0dcaf0; border: 1px solid #0dcaf0; background: transparent; border-radius: 6px; padding: 0.5rem; width: 38px; height: 38px; transition: all 0.2s ease; display: inline-flex; align-items: center; justify-content: center;"
                                               data-bs-toggle="tooltip"
                                               title="Ver detalles del informe"
                                               onmouseover="this.style.backgroundColor='#0dcaf0'; this.style.color='white';"
                                               onmouseout="this.style.backgroundColor='transparent'; this.style.color='#0dcaf0';">
                                                <i class="fas fa-eye"></i>
                                            </a>
                                            
                                            @auth
                                                @if(Auth::user()->hasAnyRole(['admin', 'tecnico', 'analista']))
                                                    @can('editar parametros')
                                                        <a href="{{ route('parametros.edit', $parametro) }}" 
                                                           class="btn btn-outline-warning btn-sm"
                                                           data-bs-toggle="tooltip" 
                                                           data-bs-placement="top"
                                                           title="Editar parámetro">
                                                            <i class="fas fa-edit"></i>
                                                    @endcan
                                                    </a>
                                                    @can('eliminar parametros')
                                                    <button type="button" 
                                                            class="btn btn-outline-danger btn-sm"
                                                            data-bs-toggle="tooltip" 
                                                            data-bs-placement="top"
                                                            title="Eliminar parámetro"
                                                            onclick="confirmarEliminacion({{ $parametro->id }}, '{{ $parametro->nombre }}', 'parámetro')">
                                                        <i class="fas fa-trash"></i>
                                                    </button>
                                                    @endcan
                                                @endif
                                            @endauth
                                        </div>
                                        
                                        @auth
                                            @if(Auth::user()->hasAnyRole(['admin', 'tecnico', 'analista']))
                                                <form id="delete-form-{{ $parametro->id }}" 
                                                      action="{{ route('parametros.destroy', $parametro) }}" 
                                                      method="POST" class="d-none">
                                                    @csrf
                                                    @method('DELETE')
                                                </form>
                                            @endif
                                        @endauth
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>

                @if($parametros->hasPages())
                    <div class="d-flex justify-content-center mt-4">
                        {{ $parametros->appends(request()->query())->links() }}
                    </div>
                @endif
                
                <div class="d-flex align-items-center justify-content-center position-relative mt-3">
                    @auth
                        @if(Auth::user()->hasAnyRole(['admin', 'tecnico']))
                            <a href="{{ route('informes.trash') }}" 
                               class="btn btn-icon-circle position-absolute start-0"
                               style="width: 35px; height: 35px; border-radius: 50%; background-color: #6c757d; color: white; display: inline-flex; align-items: center; justify-content: center; transition: all 0.3s ease; text-decoration: none;"
                               data-bs-toggle="tooltip"
                               title="Ver informes eliminados"
                               onmouseover="this.style.backgroundColor='#5a6268'; this.style.transform='scale(1.1)';"
                               onmouseout="this.style.backgroundColor='#6c757d'; this.style.transform='scale(1)';">
                                <i class="fas fa-trash-alt" style="font-size: 1rem;"></i>
                            </a>
                        @endif
                    @endauth
                    
                    <div style="color: #C2F527; font-weight: 500;">
                        <i class="fas fa-database me-1"></i> 
                        Mostrando {{ $informes->firstItem() }} a {{ $informes->lastItem() }} de {{ $informes->total() }} registros
                    </div>
                </div>
                
            @else
                <div class="text-center py-5">
                    <i class="fas fa-file-alt fa-4x mb-4" style="color: #C2F527;"></i>
                    <h4 class="mb-3" style="color: #334155;">No hay informes registrados</h4>
                    <p class="text-muted mb-4">
                        @if(request()->has('search') || request()->has('mes') || request()->has('anio') || request()->has('estado'))
                            No hay informes para los filtros seleccionados.
                            <a href="{{ route('informes.index') }}" style="color: #C2F527;">Ver todos</a>
                        @else
                            Comience creando su primer informe técnico.
                        @endif
                    </p>
                    
                    @auth
                        @if(Auth::user()->hasAnyRole(['admin', 'tecnico', 'analista']))
                            @if(request('search'))
                                <a href="{{ route('parametros.index') }}" class="btn btn-outline-secondary" style="border-radius: 30px; padding: 10px 25px;">
                                    <i class="fas fa-times me-2"></i>
                                    Limpiar búsqueda
                                </a>
                            @else
                                <a href="{{ route('parametros.create') }}" class="btn" style="background-color: #A31800; border-radius: 30px; padding: 10px 25px; color: white; border: none; transition: all 0.3s ease;"
                                   onmouseover="this.style.backgroundColor='#7a1200'; this.style.transform='translateY(-2px)'; this.style.boxShadow='0 5px 15px rgba(163, 24, 0, 0.3)';"
                                   onmouseout="this.style.backgroundColor='#A31800'; this.style.transform='translateY(0px)'; this.style.boxShadow='none';">
                                    <i class="fas fa-plus-circle me-2"></i>
                                    Crear primer parámetro
                                </a>
                            @endif
                        @else
                            <div class="alert alert-info">
                                <i class="fas fa-info-circle me-2"></i>
                                Solo el administrador puede crear nuevos parámetros.
                            </div>
                        @endif
                    @endauth
                </div>
            @endif
        </div>
        
        <div class="card-footer bg-light">
            <div class="row">
                <div class="col-md-6">
                    <small class="text-muted">
                        <i class="fas fa-info-circle me-1" style="color: #C2F527;"></i>
                        Mostrando {{ $informes->count() }} de {{ $informes->total() }} informes
                    </small>
                </div>
                <div class="col-md-6 text-end">
                    <small class="text-muted">
                        <i class="fas fa-calculator me-1" style="color: #C2F527;"></i>
                        Total: <strong>{{ $informes->total() }} registros</strong>
                    </small>
                </div>
            </div>
        </div>
        @endif
    </div>
</div>

<style>
.btn[style*="background-color: #A31800"]:hover {
    background-color: #7a1200 !important;
    transform: translateY(-2px);
    box-shadow: 0 5px 15px rgba(194, 245, 39, 0.3);
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
    color: #C2F527 !important;
    border-radius: 8px;
    margin: 0 3px;
}

.pagination .page-item.active .page-link {
    background-color: #C2F527 !important;
    border-color: #C2F527 !important;
    color: #000 !important;
}

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

.input-group-text {
    border-radius: 30px 0 0 30px !important;
}

.form-control {
    border-radius: 0 !important;
}

.btn[type="submit"] {
    border-radius: 0 30px 30px 0 !important;
}

.form-control:focus,
.form-select:focus,
.input-group-text:focus,
.btn:focus {
    border-color: #C2F527 !important;
    box-shadow: 0 0 0 3px rgba(194, 245, 39, 0.25) !important;
    outline: none !important;
}

#search:focus {
    border-color: #C2F527 !important;
    box-shadow: 0 0 0 3px rgba(194, 245, 39, 0.25) !important;
}

select.form-select:focus {
    border-color: #C2F527 !important;
    box-shadow: 0 0 0 3px rgba(194, 245, 39, 0.25) !important;
}

button[type="submit"]:focus {
    border-color: #C2F527 !important;
    box-shadow: 0 0 0 3px rgba(194, 245, 39, 0.25) !important;
}

.fa-file-alt {
    color: #C2F527 !important;
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

@push('scripts')
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

    function confirmarEliminacion(id, nombre, tipo) {
        if (confirm(`¿Está seguro de eliminar el ${tipo} "${nombre}"?\n\n⚠️ Esta acción moverá el registro a la papelera.`)) {
            document.getElementById(`delete-form-${id}`).submit();
        }
    }
</script>
@endpush
@endsection