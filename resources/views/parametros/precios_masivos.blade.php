@extends('layouts.app')

@section('title', 'Actualización Masiva de Precios')

@section('content')
<div class="container-main">
    <div class="page-header">
        <div class="d-flex justify-content-between align-items-center">
            <div>
                <h1>
                    <i class="fas fa-dollar-sign me-2" style="color: #A31800;"></i>
                    Actualización Masiva de Precios
                </h1>
                <p class="page-subtitle">
                    Modifique los precios de todos los parámetros en una sola pantalla
                </p>
            </div>
            
            <a href="{{ route('parametros.index') }}" 
               class="btn btn-outline-secondary btn-volver" 
               style="border-radius: 30px; padding: 8px 20px; color: #000000 !important; border: 2px solid #ffffff !important; background-color: #ffffff !important; transition: all 0.3s ease !important; font-weight: 500 !important; box-shadow: 0 2px 4px rgba(0,0,0,0.05) !important;"
               onmouseover="this.style.transform='translateY(-2px)'; this.style.boxShadow='0 8px 20px rgba(128, 128, 128, 0.3)';"
               onmouseout="this.style.transform='translateY(0px)'; this.style.boxShadow='0 2px 4px rgba(0,0,0,0.05)';">
                <i class="fas fa-arrow-left me-2"></i>
                Volver al listado
            </a>
        </div>
    </div>

    @if(session('success'))
        <div class="alert alert-success alert-dismissible fade show" role="alert">
            <i class="fas fa-check-circle me-2"></i>
            {{ session('success') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    @endif

    @if(session('error'))
        <div class="alert alert-danger alert-dismissible fade show" role="alert">
            <i class="fas fa-exclamation-triangle me-2"></i>
            {{ session('error') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    @endif

    <div class="card">
        <div class="card-header" style="background-color: #A31800; color: white; border-radius: 12px 12px 0 0;">
            <div class="d-flex justify-content-between align-items-center flex-wrap gap-2">
                <h5 class="mb-0 text-white">
                    <i class="fas fa-edit me-2" style="color: #A31800;"></i>
                    Editar Precios
                </h5>
                <span class="badge bg-light text-dark fs-6">
                    <i class="fas fa-database me-1"></i>
                    Total: {{ $parametros->count() }} parámetros
                </span>
            </div>
        </div>
        <div class="card-body">
            <form action="{{ route('parametros.precios.actualizar') }}" method="POST" id="preciosForm">
                @csrf
                
                <div class="table-responsive">
                    <table class="table table-hover table-sm sortable" id="preciosTable">
                        <thead class="table-light">
                            <tr>
                                <th width="50" data-sort="numeric">#</th>
                                <th data-sort="string">Parámetro</th>
                                <th data-sort="string">Categoría</th>
                                <th data-sort="string">Tipo de Análisis</th>
                                <th width="200" class="text-end" data-sort="numeric">Precio Actual (Bs.)</th>
                                <th width="200" class="text-end">Nuevo Precio (Bs.)</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($parametros as $index => $parametro)
                            <tr style="transition: all 0.2s ease;" 
                                onmouseover="this.style.backgroundColor='#f8f9fa'; this.style.transform='scale(1.01)';"
                                onmouseout="this.style.backgroundColor='transparent'; this.style.transform='scale(1)';">
                                <td>{{ $index + 1 }}</td>
                                <td>
                                    <strong>{{ $parametro->nombre }}</strong>
                                    @if($parametro->nombre_completo)
                                        <br><small class="text-muted">{{ $parametro->nombre_completo }}</small>
                                    @endif
                                </td>
                                <td>
                                    <span class="badge 
                                        @if($parametro->tipo == 'AMBIENTAL') bg-warning text-dark
                                        @elseif($parametro->tipo == 'AGUA') bg-info
                                        @elseif($parametro->tipo == 'INVESTIGACION') bg-secondary
                                        @else bg-secondary
                                        @endif">
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
                                        {{ $parametro->categoria ?? 'N/A' }}
                                    </span>
                                </td>
                                <td class="text-end fw-bold text-success">
                                    {{ number_format($parametro->precio_unitario, 2) }}
                                </td>
                                <td>
                                    <div class="input-group input-group-sm" style="border-radius: 30px; overflow: hidden;">
                                        <span class="input-group-text" style="border-radius: 30px 0 0 30px; background-color: #f8f9fa;">Bs.</span>
                                        <input type="number" 
                                               class="form-control text-end precio-input" 
                                               name="precios[{{ $parametro->id }}]" 
                                               value="{{ old('precios.' . $parametro->id, $parametro->precio_unitario) }}"
                                               step="0.01" 
                                               min="0"
                                               data-original="{{ $parametro->precio_unitario }}"
                                               style="border-radius: 0 30px 30px 0; border-left: none;">
                                    </div>
                                </td>
                            </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>

                <div class="d-flex justify-content-between mt-4 pt-3 border-top flex-wrap gap-2">
                    <div class="d-flex gap-2 flex-wrap">
                        <button type="button" class="btn btn-outline-secondary" id="resetPrecios" onclick="resetPrecios()" 
                                style="border-radius: 30px; padding: 6px 20px; transition: all 0.3s ease;"
                                onmouseover="this.style.transform='translateY(-2px)'; this.style.boxShadow='0 4px 10px rgba(0,0,0,0.1)';"
                                onmouseout="this.style.transform='translateY(0)'; this.style.boxShadow='none';">
                            <i class="fas fa-undo me-1"></i>
                            Restaurar originales
                        </button>
                        <button type="button" class="btn btn-outline-info" id="buscarParametro"
                                style="border-radius: 30px; padding: 6px 20px; transition: all 0.3s ease;"
                                onmouseover="this.style.transform='translateY(-2px)'; this.style.boxShadow='0 4px 10px rgba(23, 162, 184, 0.3)';"
                                onmouseout="this.style.transform='translateY(0)'; this.style.boxShadow='none';">
                            <i class="fas fa-search me-1"></i>
                            Buscar
                        </button>
                    </div>
                    <button type="submit" class="btn" 
                            style="background-color: #A31800; color: white; border: none; border-radius: 30px; padding: 10px 30px; transition: all 0.3s ease;"
                            onmouseover="this.style.backgroundColor='#7a1200'; this.style.transform='translateY(-2px)'; this.style.boxShadow='0 5px 15px rgba(163, 24, 0, 0.3)';"
                            onmouseout="this.style.backgroundColor='#A31800'; this.style.transform='translateY(0)'; this.style.boxShadow='none';">
                        <i class="fas fa-save me-2"></i>
                        Guardar Todos los Precios
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

@push('scripts')
<script src="https://www.kryogenix.org/code/browser/sorttable/sorttable.js"></script>

<script>
    function resetPrecios() {
        if (confirm('¿Está seguro de que desea restaurar todos los precios a sus valores originales?')) {
            document.querySelectorAll('.precio-input').forEach(input => {
                const original = input.dataset.original;
                input.value = original;
                input.classList.remove('is-changed');
            });
        }
    }

    document.getElementById('buscarParametro').addEventListener('click', function() {
        const searchTerm = prompt('Ingrese el nombre del parámetro a buscar:');
        if (searchTerm) {
            const table = document.getElementById('preciosTable');
            const rows = table.querySelectorAll('tbody tr');
            let found = false;
            
            rows.forEach(row => {
                const texto = row.innerText.toLowerCase();
                if (texto.includes(searchTerm.toLowerCase())) {
                    row.style.display = '';
                    found = true;
                } else {
                    row.style.display = 'none';
                }
            });

            if (!found) {
                alert('No se encontraron parámetros con el término "' + searchTerm + '"');
            }
        }
    });

    document.querySelectorAll('.precio-input').forEach(input => {
        input.addEventListener('input', function() {
            const original = parseFloat(this.dataset.original);
            const current = parseFloat(this.value) || 0;
            if (current !== original) {
                this.classList.add('border-warning', 'bg-warning-light');
            } else {
                this.classList.remove('border-warning', 'bg-warning-light');
            }
        });
    });
</script>

<style>
.bg-warning-light {
    background-color: #fff3cd !important;
}
.precio-input:focus {
    border-color: #A31800 !important;
    box-shadow: 0 0 0 3px rgba(163, 24, 0, 0.15) !important;
}
.btn {
    transition: all 0.3s ease !important;
}
.sortable th {
    cursor: pointer;
}
.sortable th:hover {
    background-color: #e9ecef !important;
}
</style>
@endpush
@endsection