@extends('layouts.app')

@section('content')
<div class="container-main">
    <!-- Encabezado de página -->
    <div class="page-header">
        <div class="d-flex justify-content-between align-items-center">
            <div>
                <h1>
                    <i class="fas fa-edit" style="color: #A31800;"></i>
                    Editar Parámetro
                </h1>
                <p class="page-subtitle">
                    Actualice la información del parámetro: {{ $parametro->nombre }}
                </p>
            </div>

            <a href="{{ route('parametros.index') }}" class="btn btn-outline-secondary btn-volver" style="border-radius: 30px; padding: 8px 20px;">
                    <i class="fas fa-arrow-left me-2"></i>
                    Volver al listado
            </a>

        </div>
    </div>

    <!-- Formulario -->
    <div class="card">
        <div class="card-header" style="background-color: #A31800; border-bottom: none;">
            <h5 class="mb-0 text-white">
                <i class="fas fa-edit me-2"></i>
                Formulario de Edición
            </h5>
        </div>
        <div class="card-body">
            <form action="{{ route('parametros.update', $parametro) }}" method="POST">
                @csrf
                @method('PUT')
                
                <div class="row">
                    <div class="col-md-12 mb-3">
                        <label for="nombre" class="form-label">
                            Nombre del Parámetro *
                        </label>
                        <input type="text" 
                               class="form-control @error('nombre') is-invalid @enderror" 
                               id="nombre" 
                               name="nombre" 
                               value="{{ old('nombre', $parametro->nombre) }}" 
                               required 
                               autofocus
                               placeholder="Ej: PST, pH, Ruido, DBO5, Coliformes Fecales">
                        @error('nombre')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="col-md-12 mb-3">
                        <label for="nombre_completo" class="form-label">
                            Nombre Completo
                        </label>
                        <input type="text" 
                               class="form-control @error('nombre_completo') is-invalid @enderror" 
                               id="nombre_completo" 
                               name="nombre_completo" 
                               value="{{ old('nombre_completo', $parametro->nombre_completo) }}" 
                               placeholder="Ej: Partículas Totales Suspendidas">
                        @error('nombre_completo')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                        <small class="text-muted">Nombre completo o descriptivo del parámetro</small>
                    </div>

                    <div class="col-md-12 mb-3">
                        <label for="metodo" class="form-label">
                            Método de Análisis *
                        </label>
                        <input type="text" 
                               class="form-control @error('metodo') is-invalid @enderror" 
                               id="metodo" 
                               name="metodo" 
                               value="{{ old('metodo', $parametro->metodo) }}" 
                               required
                               placeholder="Ej: TAS 080-2, Potenciometría, Gravimetría">
                        @error('metodo')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="col-md-12 mb-3">
                        <label for="descripcion" class="form-label">
                            Descripción
                        </label>
                        <input type="text" 
                               class="form-control @error('descripcion') is-invalid @enderror" 
                               id="descripcion" 
                               name="descripcion" 
                               value="{{ old('descripcion', $parametro->descripcion) }}" 
                               placeholder="Ej: Partículas Totales Suspendidas - método TAS USA">
                        @error('descripcion')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                        <small class="text-muted">Descripción breve del parámetro y su método</small>
                    </div>

                    <div class="col-md-4 mb-3">
                        <label for="precio_unitario" class="form-label">
                            Precio Unitario (Bs.) *
                        </label>
                        <div class="input-group">
                            <span class="input-group-text bg-light">
                                <i class="fas fa-dollar-sign text-success"></i>
                            </span>
                            <input type="number" 
                                   step="0.01" 
                                   min="0" 
                                   class="form-control @error('precio_unitario') is-invalid @enderror" 
                                   id="precio_unitario" 
                                   name="precio_unitario" 
                                   value="{{ old('precio_unitario', $parametro->precio_unitario) }}" 
                                   required
                                   placeholder="0.00">
                            <span class="input-group-text bg-light">Bs.</span>
                        </div>
                        @error('precio_unitario')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="col-md-4 mb-3">
<<<<<<< HEAD
                        <label for="tipo" class="form-label">
                            Tipo de Análisis *
                        </label>
                        <select class="form-select @error('tipo') is-invalid @enderror" 
                                id="tipo" 
                                name="tipo" 
                                required>
                            <option value="">Seleccionar tipo...</option>
                            <option value="AMBIENTAL" {{ old('tipo', $parametro->tipo) == 'AMBIENTAL' ? 'selected' : '' }}>AMBIENTAL</option>
                            <option value="AGUA" {{ old('tipo', $parametro->tipo) == 'AGUA' ? 'selected' : '' }}>AGUA</option>
                            <option value="INVESTIGACION" {{ old('tipo', $parametro->tipo) == 'INVESTIGACION' ? 'selected' : '' }}>INVESTIGACIÓN</option>
                        </select>
                        @error('tipo')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="col-md-4 mb-3">
=======
>>>>>>> actualizacion
                        <label for="categoria" class="form-label">
                            Categoría
                        </label>
                        <select class="form-select @error('categoria') is-invalid @enderror" 
                                id="categoria" 
                                name="categoria">
                            <option value="">Seleccionar categoría...</option>
                            @if(old('categoria', $parametro->categoria) && !in_array(old('categoria', $parametro->categoria), ['AIRE', 'RUIDO', 'GASES', 'AGUA', 'SUELO']))
                                <option value="{{ old('categoria', $parametro->categoria) }}" selected>{{ old('categoria', $parametro->categoria) }}</option>
                            @endif
                            @foreach(['AIRE', 'RUIDO', 'GASES', 'AGUA', 'SUELO'] as $cat)
                                <option value="{{ $cat }}" {{ old('categoria', $parametro->categoria) == $cat ? 'selected' : '' }}>{{ $cat }}</option>
                            @endforeach
                        </select>
                        @error('categoria')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                        <small class="text-muted">Ej: AIRE, RUIDO, GASES, AGUA</small>
                    </div>

<<<<<<< HEAD
=======
                    <div class="col-md-4 mb-3">
                        <label for="tipo" class="form-label">
                            Tipo de Análisis *
                        </label>
                        <select class="form-select @error('tipo') is-invalid @enderror" 
                                id="tipo" 
                                name="tipo" 
                                required>
                            <option value="">Seleccionar tipo...</option>
                            <option value="AMBIENTAL" {{ old('tipo', $parametro->tipo) == 'AMBIENTAL' ? 'selected' : '' }}>AMBIENTAL</option>
                            <option value="AGUA" {{ old('tipo', $parametro->tipo) == 'AGUA' ? 'selected' : '' }}>AGUA</option>
                            <option value="INVESTIGACION" {{ old('tipo', $parametro->tipo) == 'INVESTIGACION' ? 'selected' : '' }}>INVESTIGACIÓN</option>
                        </select>
                        @error('tipo')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                        <small class="text-muted">Categoría del análisis según formato CIMA</small>
                    </div>

>>>>>>> actualizacion
                    <div class="col-md-6 mb-3">
                        <label for="unidad" class="form-label">
                            Unidad
                        </label>
                        <input type="text" 
                               class="form-control @error('unidad') is-invalid @enderror" 
                               id="unidad" 
                               name="unidad" 
                               value="{{ old('unidad', $parametro->unidad ?? $parametro->unidad_default) }}" 
                               placeholder="Ej: µg/m³, mg/l, dB(A), unid pH">
                        @error('unidad')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                        <small class="text-muted">Unidad de medición del parámetro</small>
                    </div>

                    <div class="col-md-6 mb-3">
                        <label for="limite_cuantificacion" class="form-label">
                            Límite de Cuantificación
                        </label>
                        <input type="text" 
                               class="form-control @error('limite_cuantificacion') is-invalid @enderror" 
                               id="limite_cuantificacion" 
                               name="limite_cuantificacion" 
                               value="{{ old('limite_cuantificacion', $parametro->limite_cuantificacion) }}" 
                               placeholder="Ej: 4,00 a 10,00">
                        @error('limite_cuantificacion')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="col-md-6 mb-3">
                        <label for="codigo_poe" class="form-label">
                            Código POE
                        </label>
                        <input type="text" 
                               class="form-control @error('codigo_poe') is-invalid @enderror" 
                               id="codigo_poe" 
                               name="codigo_poe" 
                               value="{{ old('codigo_poe', $parametro->codigo_poe) }}" 
                               placeholder="Ej: POE 1-014">
                        @error('codigo_poe')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                        <small class="text-muted">Código del procedimiento operativo estandarizado</small>
                    </div>

                    <div class="col-md-6 mb-3">
                        <label for="tecnica" class="form-label">
                            Técnica
                        </label>
<<<<<<< HEAD
                        <select class="form-select @error('tecnica') is-invalid @enderror" 
                                id="tecnica" 
                                name="tecnica">
                            <option value="">Seleccionar técnica...</option>
                            @if(old('tecnica', $parametro->tecnica) && !in_array(old('tecnica', $parametro->tecnica), ['POTENCIOMETRIA', 'ABSORCION ATOMICA', 'FOTOMETRIA', 'UV-VISIBLE', 'IONOMETRIA', 'VOLUMETRIA', 'GRAVIMETRIA', 'NEFELOMÉTRICO', 'BACTEREOLOGIA', 'OTROS']))
                                <option value="{{ old('tecnica', $parametro->tecnica) }}" selected>{{ old('tecnica', $parametro->tecnica) }}</option>
                            @endif
                            @foreach(['POTENCIOMETRIA', 'ABSORCION ATOMICA', 'FOTOMETRIA', 'UV-VISIBLE', 'IONOMETRIA', 'VOLUMETRIA', 'GRAVIMETRIA', 'NEFELOMÉTRICO', 'BACTEREOLOGIA', 'OTROS'] as $tec)
                                <option value="{{ $tec }}" {{ old('tecnica', $parametro->tecnica) == $tec ? 'selected' : '' }}>{{ $tec }}</option>
                            @endforeach
                        </select>
                        @error('tecnica')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                        <small class="text-muted">Técnica analítica utilizada</small>
=======
                        <input type="text" 
                               class="form-control @error('tecnica') is-invalid @enderror" 
                               id="tecnica" 
                               name="tecnica" 
                               value="{{ old('tecnica', $parametro->tecnica) }}" 
                               placeholder="Ej: Potenciometría, Absorción Atómica, Volumetría...">
                        @error('tecnica')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                        <small class="text-muted">Técnica analítica utilizada (texto libre)</small>
>>>>>>> actualizacion
                    </div>

                    <div class="col-md-6 mb-4">
                        <label for="matriz" class="form-label">
                            Matriz
                        </label>
                        <select class="form-select @error('matriz') is-invalid @enderror" 
                                id="matriz" 
                                name="matriz">
                            <option value="">Seleccionar matriz...</option>
                            @if(old('matriz', $parametro->matriz) && !in_array(old('matriz', $parametro->matriz), ['AGUA', 'AIRE', 'SUELO', 'OTROS']))
                                <option value="{{ old('matriz', $parametro->matriz) }}" selected>{{ old('matriz', $parametro->matriz) }}</option>
                            @endif
                            @foreach(['AGUA', 'AIRE', 'SUELO', 'OTROS'] as $mat)
                                <option value="{{ $mat }}" {{ old('matriz', $parametro->matriz) == $mat ? 'selected' : '' }}>{{ $mat }}</option>
                            @endforeach
                        </select>
                        @error('matriz')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                        <small class="text-muted">Matriz de análisis: AGUA, AIRE, SUELO</small>
                    </div>

                    <div class="col-md-6 mb-4">
                        <label for="tipo_medicion" class="form-label">
                            Tipo de Medición
                        </label>
                        <select class="form-select @error('tipo_medicion') is-invalid @enderror" 
                                id="tipo_medicion" 
                                name="tipo_medicion">
                            <option value="">Seleccionar tipo de medición...</option>
                            @if(old('tipo_medicion', $parametro->tipo_medicion) && !in_array(old('tipo_medicion', $parametro->tipo_medicion), ['Ambiental', 'Industrial']))
                                <option value="{{ old('tipo_medicion', $parametro->tipo_medicion) }}" selected>{{ old('tipo_medicion', $parametro->tipo_medicion) }}</option>
                            @endif
                            @foreach(['Ambiental', 'Industrial'] as $tm)
                                <option value="{{ $tm }}" {{ old('tipo_medicion', $parametro->tipo_medicion) == $tm ? 'selected' : '' }}>{{ $tm }}</option>
                            @endforeach
                        </select>
                        @error('tipo_medicion')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                        <small class="text-muted">Ej: Ambiental, Industrial</small>
                    </div>
                </div>

                <div class="d-flex justify-content-between pt-3 border-top">
                    <a href="{{ route('parametros.show', $parametro) }}" class="btn btn-secondary" style="border-radius: 30px; padding: 10px 25px;">
                        <i class="fas fa-times me-2"></i>
                        Cancelar
                    </a>
                    <button type="submit" class="btn" style="background-color: #A31800; border-radius: 30px; padding: 10px 25px; color: white; border: none; transition: all 0.3s ease;">
                        <i class="fas fa-save me-2"></i>
                        Actualizar Parámetro
                    </button>
                </div>
            </form>
        </div>
    </div>
    
    <!-- Impacto de cambios -->
    <div class="card mt-4">
        <div class="card-header bg-light">
            <h6 class="mb-0">
                <i class="fas fa-exclamation-triangle me-2" style="color: #A31800;"></i>
                Impacto de los Cambios
            </h6>
        </div>
        <div class="card-body">
            <div class="alert alert-warning">
                <i class="fas fa-info-circle me-2"></i>
                <strong>Importante:</strong> Al modificar el precio unitario, este cambio afectará a todas las proformas futuras que incluyan este parámetro. Las proformas existentes mantendrán el precio original al momento de su creación.
            </div>
        </div>
    </div>
</div>

<!-- Estilos adicionales específicos para la página de edición -->
<style>

/* Estilo para el botón de actualizar */
button[type="submit"][style*="background-color: #A31800"]:hover {
    background-color: #7a1200 !important;
    transform: translateY(-2px);
    box-shadow: 0 5px 15px rgba(163, 24, 0, 0.3);
}

/* Estilo para el botón de cancelar */
.btn-secondary {
    background-color: #6c757d;
    border: none;
    transition: all 0.3s ease;
}

.btn-secondary:hover {
    background-color: #5a6268;
    transform: translateY(-2px);
    box-shadow: 0 5px 15px rgba(108, 117, 125, 0.3);
}

/* Enfocar inputs con el color rojo */
.form-control:focus, .form-select:focus {
    border-color: #A31800 !important;
    box-shadow: 0 0 0 3px rgba(163, 24, 0, 0.15) !important;
}


/* Estilo para el icono de advertencia */
.fa-exclamation-triangle {
    color: #A31800 !important;
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


.fa-microscope{
    color: #A31800!important;
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

/* ========== CORRECCIÓN PARA BOTONES DE FORMULARIO ========== */
@media (max-width: 768px) {
    /* Hacer los botones más pequeños y manejables en móvil */
    .d-flex.justify-content-between.pt-3.border-top,
    .d-flex.justify-content-between.mt-4.pt-3.border-top {
        flex-direction: column !important;
        gap: 10px !important;
    }
    
    /* Botones ocupan todo el ancho pero con mejor tamaño */
    .d-flex.justify-content-between.pt-3.border-top .btn,
    .d-flex.justify-content-between.mt-4.pt-3.border-top .btn {
        width: 100% !important;
        padding: 12px 20px !important; /* Un poco más pequeños que antes */
        font-size: 1rem !important;
        margin: 0 !important;
    }
    
    /* Para formularios con btn-group */
    .btn-group {
        width: 100% !important;
        display: flex !important;
        gap: 8px !important;
    }
    
    .btn-group .btn {
        flex: 1 !important;
        padding: 12px 15px !important;
    }
    
    /* Ajustar espaciado del formulario */
    .card-body {
        padding: 1rem !important;
    }
    
    /* Ajustar inputs para mejor visualización */
    .form-control, .form-select {
        font-size: 16px !important; /* Evita zoom automático en iOS */
        padding: 12px !important;
    }
    
    /* Ajustar labels */
    .form-label {
        font-size: 0.95rem !important;
        margin-bottom: 0.25rem !important;
    }
    
    /* Ajustar textos pequeños */
    small.text-muted {
        font-size: 0.8rem !important;
    }
    
    /* Ajustar títulos de sección */
    h6.border-bottom {
        font-size: 1rem !important;
        padding-bottom: 0.5rem !important;
    }
    
    /* Ajustar input groups */
    .input-group {
        flex-wrap: nowrap !important;
    }
    
    .input-group .form-control {
        font-size: 16px !important;
    }
    
    .input-group-text {
        padding: 12px !important;
    }
}

/* Ajuste para tablets */
@media (min-width: 769px) and (max-width: 991px) {
    .btn {
        padding: 10px 20px !important;
    }
}
</style>
@endsection