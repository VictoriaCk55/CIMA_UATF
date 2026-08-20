<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    @php $cfg = \App\Models\Documento::whereSlug($proforma->tipo === 'AMBIENTAL' ? 'solicitud-ensayo-ambiental' : 'solicitud-ensayo')->first() ?? new \App\Models\Documento; @endphp
    <title>PROFORMA {{ $proforma->codigo }} - {{ $cfg->config('institucion_sigla', 'CIMA') }}</title>
    <style>
        /* CONFIGURACIÓN BASE */
        @page {
            margin: 10mm 10mm 25mm 10mm; /* Reduje el margen inferior para que las firmas queden más arriba */
        }

        @page horizontal {
            size: letter landscape;
        }

        .horizontal-page {
            page: horizontal;
        }

        body {
            font-family: "Times New Roman", Times, serif;
            font-size: 10px;
            line-height: 1.2;
            color: #000;
            margin: 0;
            padding: 0;
            background-color: white;
        }
        
        /* ENCABEZADO */
        .header-table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 8px;
        }
        
        .header-table td {
            vertical-align: top;
            padding: 0;
        }
        
        .logo-cell {
            width: 80px;
        }
        
        .logo-container {
            width: 70px;
            height: 70px;
            border: 1px solid #ccc;
            display: block;
            background-color: #f9f9f9;
            overflow: hidden;
            text-align: center;
        }
        
        .logo-container img {
            max-width: 65px;
            max-height: 65px;
            margin-top: 2px;
        }
        
        .center-cell {
            text-align: center;
            padding: 0 8px;
        }
        
        .center-cell h1 {
            font-size: 14px;
            font-weight: bold;
            margin: 0 0 2px 0;
            text-transform: uppercase;
            color: #1c3d6e;
        }
        
        .center-cell h2 {
            font-size: 11px;
            margin: 0 0 2px 0;
            color: #2c5282;
            font-weight: 600;
        }
        
        .center-cell h3 {
            font-size: 9px;
            margin: 0 0 3px 0;
            color: #4a5568;
            font-style: italic;
        }
        
        /* Unidad de la proforma (Verde si es AMBIENTAL) */
        .unidad-badge {
            padding: 2px 6px;
            border-radius: 15px;
            font-size: 8px;
            font-weight: bold;
            display: inline-block;
            margin-top: 3px;
        }
        .unidad-badge.ambiental {
            background-color: #B0E68E !important;
            color: #000000 !important;
        }
        .unidad-badge.agua {
            background-color: #2c5282 !important;
            color: white !important;
        }
        
        .document-subtitle {
            font-size: 8px;
            font-weight: bold;
            color: #333;
            margin: 2px 0;
        }
        
        .document-options {
            margin: 5px 0 0 0;
            font-size: 8px;
        }
        
        .doc-option {
            display: inline-block;
            margin: 0 2px;
            padding: 1px 4px;
            border: 1px solid #666;
            border-radius: 2px;
            background-color: #f8f9fa;
        }
        
        .doc-option.selected.proforma { background-color: #2c5282; color: white; border-color: #2c5282; font-weight: bold; }
        .doc-option.selected.cotizacion { background-color: #28a745; color: white; border-color: #28a745; font-weight: bold; }
        .doc-option.selected.contrato { background-color: #fd7e14; color: white; border-color: #fd7e14; font-weight: bold; }
        .doc-option.selected.contrato-modificado { background-color: #dc3545; color: white; border-color: #dc3545; font-weight: bold; }
        
        .codigo-cell {
            width: 100px;
            text-align: right;
        }
        
        .codigo-box {
            border: 1px solid #000;
            padding: 3px;
            font-size: 8px;
            background-color: #f8f9fa;
            text-align: center;
            display: inline-block;
        }
        
        /* LÍNEA SEPARADORA */
        .separator {
            border-top: 2px solid #1c3d6e;
            margin: 5px 0 8px 0;
            width: 100%;
        }
        
        /* SECCIONES - Verdes si es AMBIENTAL */
        .section-title {
            background-color: #2c5282;
            color: white;
            font-weight: bold;
            padding: 3px 6px;
            margin-bottom: 5px;
            font-size: 9px;
            border-radius: 3px;
        }
        .section-title.ambiental {
            background-color: #B0E68E !important;
            color: #000000 !important;
        }
        
        /* TABLAS DE DATOS */
        .data-table {
            width: 100%;
            border-collapse: collapse;
            font-size: 9px;
            margin-bottom: 5px;
        }
        
        .data-table th, .data-table td {
            border: 1px solid #999;
            padding: 2px 5px;
            vertical-align: top;
        }
        
        .data-table th {
            background-color: #e0e0e0;
            text-align: left;
            font-weight: bold;
        }
        
        /* TABLA DE PARÁMETROS */
        .params-table {
            width: 100%;
            border-collapse: collapse;
            font-size: 9px;
            margin-bottom: 8px;
        }
        
        .params-table thead th {
            background-color: #2c5282;
            color: white;
            text-align: center;
            padding: 3px;
            border: 1px solid #999;
            font-weight: bold;
        }
        .params-table.ambiental thead th {
            background-color: #B0E68E !important;
            color: #000000 !important;
        }
        
        .params-table tbody td {
            border: 1px solid #999;
            padding: 3px 4px;
            background-color: #ffffff;
        }
        
        .align-right { text-align: right; }
        .align-center { text-align: center; }
        .align-left { text-align: left; }
        .bold { font-weight: bold; }
        
        /* ALERTA DE MODIFICACIÓN */
        .alert-modification {
            background-color: #fff3cd;
            border: 1px solid #ffc107;
            border-left: 5px solid #ffc107;
            padding: 6px;
            margin: 8px 0;
            border-radius: 5px;
            font-size: 9px;
        }
        .alert-modification-title { font-weight: bold; color: #856404; margin-bottom: 3px; font-size: 10px; }
        .alert-modification-text { color: #856404; margin-bottom: 3px; }
        .alert-modification-detail { font-style: italic; margin-top: 3px; padding-top: 3px; border-top: 1px dashed #ffc107; color: #6c757d; }
        
        /* RESUMEN FINANCIERO */
        .financial-summary {
            border: 2px solid #2c5282;
            padding: 6px;
            margin: 10px 0;
            background-color: #f8f9fa;
            border-radius: 4px;
        }
        
        .summary-title {
            font-size: 10px;
            font-weight: bold;
            color: #2c5282;
            margin-bottom: 3px;
            text-align: center;
            text-transform: uppercase;
        }
        
        .summary-line { margin: 2px 0; font-size: 10px; display: flex; justify-content: space-between; }
        .summary-line.total { font-size: 11px; font-weight: bold; margin-top: 3px; padding-top: 3px; border-top: 2px solid #2c5282; }
        .summary-label { font-weight: bold; }
        .summary-value { font-weight: bold; }
        .summary-value.text-success { color: #28a745; }
        .summary-value.text-danger { color: #dc3545; }
        
        .total-in-words {
            font-style: italic;
            margin: 8px 0;
            text-align: center;
            font-size: 10px;
            padding: 6px;
            background-color: #f0f7ff;
            border: 1px solid #cce5ff;
            border-radius: 4px;
            color: #004085;
        }
        
        /* FIRMAS - CENTRADAS Y MÁS ARRIBA */
        .signatures-wrapper {
            width: 100%;
            position: fixed;
            bottom: 5mm; /* Subido para que quede más al centro de la hoja */
            left: 0;
            padding: 0; /* Sin padding lateral para que ocupe todo el ancho */
            page-break-inside: avoid;
        }

        .signatures-table {
            width: 100%;
            border-collapse: collapse;
        }

        .signature-text {
            margin-top: 3px;
            font-size: 9px;
            color: #333;
            line-height: 1.2;
            text-align: center;
        }
        
        /* NOTAS */
        .notes-section {
            font-size: 9px;
            margin-top: 8px;
            padding: 4px;
            background-color: #f8f9fa;
            border-radius: 3px;
            border-left: 3px solid #2c5282;
        }
        .notes-section.ambiental {
            border-left: 3px solid #B0E68E !important;
        }
        .notes-section p { margin: 1px 0; }
        
        /* FOOTER */
        .footer {
            margin-top: 10px;
            font-size: 8px;
            color: #666;
            text-align: center;
            padding-top: 3px;
        }
        
        .mb-8 { margin-bottom: 6px; }
        .mt-8 { margin-top: 6px; }
    </style>
</head>
<body>
    <!-- ENCABEZADO -->
    <table class="header-table">
         <tr>
            <td class="logo-cell">
                <div class="logo-container">
                    @php
                        $logo = $cfg->config('logo_path');
                    @endphp
                    @if($logo && file_exists(storage_path('app/public/' . $logo)))
                        <img src="{{ storage_path('app/public/' . $logo) }}" alt="Logo">
                    @elseif(file_exists(public_path('images/logo-cima.jpg')))
                        <img src="{{ public_path('images/logo-cima.jpg') }}" alt="Logo CIMA">
                    @elseif(file_exists(public_path('images/logo-cima.png')))
                        <img src="{{ public_path('images/logo-cima.png') }}" alt="Logo CIMA">
                    @endif
                </div>
             </td>
            
            <td class="center-cell">
                <h1>PROFORMA DE SERVICIOS</h1>
                <h2>{{ strtoupper($cfg->config('laboratorio_nombre')) }}</h2>
                <h3>
                    @if($proforma->unidad == 'UIA')
                        Unidad de Investigación Ambiental "UIA"
                    @elseif($proforma->unidad == 'UAQ')
                        Unidad de Análisis Químico "UAQ"
                    @else
                        Unidad de Análisis Químico "UAQ"
                    @endif
                </h3>
                
                <div class="document-subtitle">
                    @if($proforma->tipo === 'AGUA')
                        ANÁLISIS QUÍMICO - BACTERIOLÓGICO: AGUAS, SUELOS, SEDIMENTOS Y MINERALES
                    @elseif($proforma->tipo === 'AMBIENTAL')
                        MUESTREO DE MATERIAL PARTICULADO, RUIDO, GASES, AGUAS, SEDIMIENTOS Y VEGETACIÓN
                    @else
                        {{ $cfg->config('footer_texto') }}
                    @endif
                </div>
                
                <div class="document-options">
                    @php $td = $proforma->tipo_documento ?? []; @endphp
                    <span class="doc-option{{ in_array('PROFORMA', $td) ? ' selected proforma' : '' }}">PROFORMA</span>
                    <span class="doc-option{{ in_array('COTIZACION', $td) ? ' selected cotizacion' : '' }}">COTIZACIÓN</span>
                    <span class="doc-option{{ in_array('CONTRATO', $td) ? ' selected contrato' : '' }}">CONTRATO</span>
                    <span class="doc-option{{ in_array('CONTRATO MODIFICADO', $td) ? ' selected contrato-modificado' : '' }}">CONTRATO MODIFICADO</span>
                </div>
                
                @if($proforma->unidad)
                <div class="unidad-badge {{ $proforma->tipo === 'AMBIENTAL' ? 'ambiental' : 'agua' }}">
                    <i class="fas fa-building"></i> {{ $proforma->unidad == 'UIA' ? 'Unidad de Investigación Ambiental' : 'Unidad de Análisis Químico' }}
                </div>
                @endif
            </td>
            
            <td class="codigo-cell">
                <div class="codigo-box">
                    <div><strong>{{ $cfg->codigo_documento }}</strong></div>
                    <div>VERSIÓN: {{ $cfg->version }}</div>
                    <div>FECHA: {{ $cfg->fecha_documento ?? $proforma->fecha_emision->format('Y-m-d') }}</div>
                    <div style="margin-top: 5px; border-top: 1px solid #ccc; padding-top: 3px;">
                        <strong>CÓDIGO:</strong> {{ $proforma->codigo }}
                    </div>
                </div>
             </td>
         </tr>
    </table>
    
    <div class="separator"></div>

    <!-- DATOS DE RECEPCIÓN -->
    @php $numRecepcion = explode('-', $proforma->codigo)[2] ?? $proforma->numero_recepcion; @endphp
    <table class="data-table" style="margin-bottom: 3px;">
        <tr>
            <td style="width: 50%; text-align: left;"><strong>Fecha de recepción:</strong> {{ $proforma->fecha_recepcion->format('d/m/Y') }}</td>
            <td style="width: 50%; text-align: right;"><strong>Nro. Recepción:</strong> {{ $numRecepcion }}</td>
        </tr>
    </table>

    <!-- SECCIÓN 1: DATOS DEL CLIENTE -->
    <div class="mb-8">
        <div class="section-title {{ $proforma->tipo === 'AMBIENTAL' ? 'ambiental' : '' }}">1.- DATOS DEL CLIENTE</div>
        
        <table class="data-table">
               <tr>
                    <td style="width: 25%;"><strong>Nombre/Razón Social:</strong></td>
                    <td style="width: 75%;" colspan="3">{{ $proforma->cliente->razon_social }}</td>
                 </tr>
                 <tr>
                    <td><strong>Persona en contacto:</strong></td>
                    <td style="width: 25%;">{{ $proforma->persona_contacto ?? $proforma->cliente->persona_contacto }}</td>
                    <td style="width: 25%;"><strong>Teléfono/Celular:</strong></td>
                    <td style="width: 25%;">{{ $proforma->telefono_contacto ?? $proforma->cliente->telefono ?? 'N/A' }}</td>
                 </tr>
                 <tr>
                    <td><strong>NIT:</strong></td>
                    <td style="width: 25%;">{{ $proforma->cliente->nit ?? 'N/A' }}</td>
                    <td style="width: 25%;"><strong>Dirección:</strong></td>
                    <td style="width: 25%;">{{ $proforma->cliente->direccion ?? 'N/A' }}</td>
                 </tr>
          </table>
    </div>

    <!-- SECCIÓN 2: DATOS DE LA MUESTRA -->
    <div class="mb-8">
        <div class="section-title {{ $proforma->tipo === 'AMBIENTAL' ? 'ambiental' : '' }}">2.- DATOS DE LA MUESTRA</div>
        
        <table class="data-table">
              <tr>
                    <td style="width: 25%;"><strong>Tipo de muestra:</strong></td>
                    <td style="width: 25%;">{{ $proforma->tipo_muestra }}</td>
                    <td style="width: 25%;"><strong>Muestreado por:</strong></td>
                    <td style="width: 25%;">{{ $proforma->muestreado_por ?? 'N/A' }}</td>
                 </tr>
                 <tr>
                    <td><strong>Fecha de muestreo:</strong></td>
                    <td>{{ $proforma->fecha_emision->format('d/m/Y') }}</td>
                    <td><strong>Hora recepción:</strong></td>
                    <td>{{ $proforma->hora_recepcion ?? 'N/A' }}</td>
                 </tr>
                  <tr>
                    <td><strong>Procedencia:</strong></td>
                    <td style="width: 25%;">{{ $proforma->procedencia ?? 'N/A' }}</td>
                    <td style="width: 25%;"><strong>Coordenadas:</strong></td>
                    <td style="width: 25%;">
                        @php
                            $coords = [];
                            if ($proforma->punto_cardinal_1 && $proforma->valor_cardinal_1) {
                                $coords[] = '<b>' . $proforma->punto_cardinal_1 . ':</b> ' . $proforma->valor_cardinal_1;
                            }
                            if ($proforma->punto_cardinal_2 && $proforma->valor_cardinal_2) {
                                $coords[] = '<b>' . $proforma->punto_cardinal_2 . ':</b> ' . $proforma->valor_cardinal_2;
                            }
                        @endphp
                        {!! !empty($coords) ? implode('&nbsp;&nbsp;&nbsp;&nbsp;', $coords) : 'N/A' !!}
                    </td>
                 </tr>
                 <tr>
                    <td><strong>Código de Cliente:</strong></td>
                    <td colspan="3">{{ implode(', ', $proforma->codigo_cliente ?? []) ?: 'N/A' }}</td>
                 </tr>
          </table>
    </div>

    <!-- SECCIÓN 3: PARÁMETROS A ANALIZAR -->
    <div class="mb-8">
        <div class="section-title {{ $proforma->tipo === 'AMBIENTAL' ? 'ambiental' : '' }}">3.- PARÁMETROS A ANALIZAR - MUESTRAS DE {{ strtoupper($proforma->tipo_muestra) }}</div>
        
        <table class="params-table {{ $proforma->tipo === 'AMBIENTAL' ? 'ambiental' : '' }}">
            <thead>
                 <tr>
                    <th style="width: 5%;">#</th>
                    <th style="width: 30%;">Parámetro</th>
                    <th style="width: 25%;">Método</th>
                    <th style="width: 10%;">N° Muestras</th>
                    <th style="width: 15%;">Precio Unit. (Bs)</th>
                    <th style="width: 15%;">Total (Bs)</th>
                 </tr>
            </thead>
            <tbody>
                @if($proforma->parametros->count() > 0)
                    @foreach($proforma->parametros as $index => $parametro)
                    @php
                        $nombreParam = $parametro->nombre;
                        if ($parametro->categoria === 'RUIDO') {
                            $nombreParam = 'RUIDO';
                        } elseif ($parametro->categoria === 'GASES') {
                            $nombreParam = 'GASES';
                        }
                    @endphp
                    <tr>
                        <td class="align-center">{{ $index + 1 }}</td>
                        <td>{{ $nombreParam }}</td>
                        <td class="align-center">{{ $proforma->tipo === 'AMBIENTAL' && $parametro->categoria === 'GASES' ? ($parametro->pivot->metodo ?? '') : ($proforma->tipo === 'AMBIENTAL' ? ($parametro->metodo ?? '') : ($parametro->tecnica ?: ($parametro->codigo_poe ?? ''))) }}</td>
                        <td class="align-center">{{ $parametro->pivot->cantidad_muestras }}</td>
                        <td class="align-right">Bs. {{ number_format($parametro->pivot->precio_unitario, 2) }}</td>
                        <td class="align-right">Bs. {{ number_format($parametro->pivot->precio_unitario * $parametro->pivot->cantidad_muestras, 2) }}</td>
                    </tr>
                    @endforeach

                    @if($proforma->tipo === 'AMBIENTAL' && $proforma->logisticasMuestreo->count() > 0)
                    @php
                        $totalPuntos = $proforma->logisticasMuestreo->sum(fn($l) => $l->pivot->cantidad);
                        $totalCosto = $proforma->logisticasMuestreo->sum(fn($l) => $l->costo);
                        $descripcionUsuario = $proforma->logisticasMuestreo->first(fn($l) => !empty($l->pivot->descripcion))?->pivot->descripcion ?? 'Logística de muestreo';
                    @endphp
                    <tr>
                        <td class="align-center">{{ $proforma->parametros->count() + 1 }}</td>
                        <td>{{ $descripcionUsuario }}</td>
                        <td class="align-center" style="text-align: center; vertical-align: middle;">
                            <strong style="font-size: 7px;">NÚMERO DE PUNTOS TOTALES</strong><br>
                            <span style="font-size: 12px; font-weight: bold;">{{ $totalPuntos }}</span>
                        </td>
                        <td class="align-center">{{ $totalPuntos }}</td>
                        <td class="align-right">Bs. {{ number_format($totalCosto, 2) }}</td>
                        <td class="align-right">Bs. {{ number_format($totalCosto, 2) }}</td>
                    </tr>
                    @endif
                @else
                    <tr>
                        <td colspan="6" class="align-center">No hay parámetros asignados</td>
                    </tr>
                @endif
            </tbody>
         </table>
        
        <!-- ALERTA DE MODIFICACIÓN DE PARÁMETROS -->
        @if($proforma->parametros_modificados && $proforma->justificacion_modificacion)
        <div class="alert-modification">
            <div class="alert-modification-title">
                <strong>⚠️ MODIFICACIÓN DE PARÁMETROS BAJO CONTRATO</strong>
            </div>
            <div class="alert-modification-text">
                Esta proforma ha sido modificada en sus parámetros de análisis.
            </div>
            <div class="alert-modification-text">
                <strong>Justificación:</strong> {{ $proforma->justificacion_modificacion }}
            </div>
            @if($proforma->usuarioModificacion)
            <div class="alert-modification-detail">
                Modificado por: {{ $proforma->usuarioModificacion->name }} | 
                Fecha: {{ $proforma->updated_at->format('d/m/Y H:i:s') }}
            </div>
            @endif
        </div>
        @endif
        
        <!-- TOTAL EN LETRAS -->
        <div class="total-in-words">
            <strong>{{ $totalEnLetras }}</strong>
        </div>

    </div>

    <!-- RESUMEN FINANCIERO -->
    @php
        $subtotalCalculado = $proforma->parametros->sum(fn($p) => $p->pivot->cantidad_muestras * $p->pivot->precio_unitario);
        if ($proforma->tipo === 'AMBIENTAL') {
            $subtotalCalculado += $proforma->logisticasMuestreo->sum(fn($l) => $l->costo);
        }
    @endphp
    <div class="financial-summary">
        <div class="summary-title">RESUMEN FINANCIERO</div>
        
        <div class="summary-line">
            <span class="summary-label">Subtotal:</span>
            <span class="summary-value">Bs. {{ number_format($subtotalCalculado, 2) }}</span>
        </div>
        
        @if($proforma->descuento > 0)
        <div class="summary-line">
            <span class="summary-label">Descuento Institucional (20%):</span>
            <span class="summary-value text-danger">- Bs. {{ number_format($proforma->descuento, 2) }}</span>
        </div>
        @endif
        
        <div class="summary-line total">
            <span class="summary-label">TOTAL:</span>
            <span class="summary-value text-success">Bs. {{ number_format($proforma->total, 2) }}</span>
        </div>
        
        @if($proforma->adelanto > 0)
        <div class="summary-line" style="margin-top: 4px;">
            <span class="summary-label">Adelanto recibido:</span>
            <span class="summary-value">Bs. {{ number_format($proforma->adelanto, 2) }}</span>
        </div>
        
        <div class="summary-line" style="border-top: 1px dashed #999; padding-top: 3px;">
            <span class="summary-label">Saldo pendiente:</span>
            <span class="summary-value {{ $proforma->saldo > 0 ? 'text-danger' : 'text-success' }}">
                Bs. {{ number_format($proforma->saldo, 2) }}
            </span>
        </div>
        @endif
    </div>

    <!-- OBSERVACIONES -->
    @if($proforma->observaciones)
    <div class="mb-8">
        <div class="section-title {{ $proforma->tipo === 'AMBIENTAL' ? 'ambiental' : '' }}">OBSERVACIONES</div>
        <div style="padding: 4px; border: 1px solid #e2e8f0; border-radius: 3px; font-size: 9px; background-color: #fffde7; line-height: 1.2;">
            {!! nl2br(e($proforma->observaciones)) !!}
        </div>
    </div>
    @endif

    <!-- NOTAS -->
    <div class="notes-section {{ $proforma->tipo === 'AMBIENTAL' ? 'ambiental' : '' }}">
        <p><strong>Nota 1:</strong> {{ $cfg->config('nota1', 'Para realizar el análisis se debe dejar cancelado el 100% del monto total.') }}</p>
        <p><strong>Nota 2:</strong> {{ $cfg->config('nota2', 'El laboratorio no realiza declaraciones de conformidad sobre los resultados que se reportan.') }}</p>
        <p><strong>Nota 3:</strong> {{ $cfg->config('nota3', 'Los resultados estarán disponibles dentro de los plazos establecidos según el tipo de análisis.') }}</p>
    </div>
    
    <!-- FIRMAS CENTRADAS Y SUBIDAS -->
    <div class="signatures-wrapper">
        <table class="signatures-table">
             <tr>
                 <td style="width: 50%; text-align: center; vertical-align: top; padding: 0 10px;">
                    <div style="border-top: 1px solid #000; width: 85%; margin: 30px auto 0 auto; display: block;"></div>
                    <div class="signature-text">
                        <strong>{{ $cfg->config('responsable_nombre') }}</strong><br>
                        {{ $cfg->config('responsable_cargo') }}<br>
                        {{ $cfg->config('institucion_nombre') }}
                    </div>
                 </td>
                 
                 <td style="width: 50%; text-align: center; vertical-align: top; padding: 0 10px;">
                    <div style="border-top: 1px solid #000; width: 85%; margin: 30px auto 0 auto; display: block;"></div>
                    <div class="signature-text">
                        <strong>{{ $cfg->config('director_nombre') }}</strong><br>
                        {{ $cfg->config('director_cargo') }}<br>
                        {{ $proforma->cliente->razon_social }}
                    </div>
                 </td>
             </tr>
         </table>
     </div>

    <!-- FOOTER -->
    <div class="footer">
        <p><strong>{{ $cfg->config('institucion_nombre') }}</strong></p>
        <p>{{ $cfg->config('footer_direccion') }}</p>
        <p>{{ $cfg->config('footer_telefono') }} | {{ $cfg->config('footer_email') }}</p>
        <p><em>{{ $cfg->config('footer_texto') }}</em></p>
    </div>

    <script type="text/php">
        if (isset($pdf)) {
            $font = $fontMetrics->getFont("times", "normal");
            $pdf->getCanvas()->page_text(260, 50, "Página {PAGE_NUM} de {PAGE_COUNT}", $font, 9, array(100,100,100));
        }
    </script>
</body>
</html>