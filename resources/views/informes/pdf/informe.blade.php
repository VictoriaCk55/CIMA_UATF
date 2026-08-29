{{-- INFORME FINAL PDF CON HEADER Y FOOTER FIJOS --}}
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8"/>
    <title>INFORME {{ $informe->codigo }}</title>

    <style>
        /* ====================================================== */
        /* CONFIGURACIÓN DE PÁGINA A4 */
        /* ====================================================== */
        @page {
            size: A4;
            margin: 32mm 10mm 55mm 10mm;
        }

        body {
            font-family: "Times New Roman", Times, serif;
            font-size: 10px;
            line-height: 1.4;
            color: #000;
            margin: 0;
            padding: 0;
            background-color: white;
        }

        /* ====================================================== */
        /* NUMERACIÓN DE PÁGINAS */
        /* ====================================================== */
        .pagenum:before {
            content: counter(page);
        }

        /* ====================================================== */
        /* HEADER - Fijo en cada página */
        /* ====================================================== */
        header {
            position: fixed;
            top: -26mm;
            left: 0;
            right: 0;
            padding: 0;
            background: white;
            z-index: 1000;
        }

        .header-table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 3px;
        }

        .header-table td {
            vertical-align: middle;
            padding: 2px 0;
        }

        .logo-cell {
            width: 100px;
            text-align: center;
        }

        .logo-container {
            width: 70px;
            height: 70px;
            border: 1px solid #ccc;
            display: inline-block;
            background-color: #f9f9f9;
            overflow: hidden;
            text-align: center;
            padding: 2px;
        }

        .logo-container img {
            max-width: 65px;
            max-height: 65px;
        }

        .center-cell {
            text-align: center;
            padding: 0 10px;
        }

        .center-cell .titulo {
            font-size: 14px;
            font-weight: bold;
            text-transform: uppercase;
            color: #1c3d6e;
            margin: 0;
            letter-spacing: 1px;
        }

        .center-cell .subtitulo {
            font-size: 10px;
            font-weight: 600;
            color: #2c5282;
            margin: 2px 0;
        }

        .center-cell .institucion {
            font-size: 9px;
            color: #4a5568;
            font-style: italic;
            margin: 2px 0;
        }

        .center-cell .sigla {
            font-size: 9px;
            font-weight: bold;
            color: #1c3d6e;
            margin: 2px 0;
        }

        .codigo-cell {
            width: 120px;
            text-align: right;
        }

        .codigo-box {
            border: 1px solid #000;
            padding: 4px 6px;
            font-size: 8px;
            background-color: #f8f9fa;
            text-align: center;
            display: inline-block;
            line-height: 1.3;
        }

        .codigo-box .label {
            font-weight: bold;
        }

        .codigo-box .codigo-informe {
            margin-top: 3px;
            padding-top: 3px;
            border-top: 1px solid #ccc;
            font-weight: bold;
            font-size: 9px;
        }

        .separator {
            border-top: 2px solid #1c3d6e;
            margin: 3px 0 6px 0;
            width: 100%;
        }

        /* ====================================================== */
        /* CONTENIDO */
        /* ====================================================== */
        .page-content {
            padding-top: 5px;
        }

        .section-title {
            background-color: #2c5282;
            color: white;
            font-weight: bold;
            padding: 3px 8px;
            margin-bottom: 4px;
            font-size: 10px;
            border-radius: 2px;
        }

        .data-table {
            width: 100%;
            border-collapse: collapse;
            font-size: 9px;
            margin-bottom: 6px;
        }

        .data-table td {
            border: 1px solid #999;
            padding: 3px 5px;
            vertical-align: middle;
        }

        .data-table .label {
            background-color: #f0f4f8;
            font-weight: bold;
            width: 25%;
        }

        .params-table {
            width: 100%;
            border-collapse: collapse;
            font-size: 8px;
            margin-bottom: 8px;
        }

        .params-table thead th {
            background-color: #2c5282;
            color: white;
            text-align: center;
            padding: 3px;
            border: 1px solid #999;
            font-weight: bold;
            font-size: 8px;
        }

        .params-table tbody td {
            border: 1px solid #999;
            padding: 2px 3px;
            font-size: 8px;
        }

        .cronograma-table {
            width: 100%;
            border-collapse: collapse;
            font-size: 9px;
            margin-bottom: 6px;
        }

        .cronograma-table td {
            border: 1px solid #999;
            padding: 3px 6px;
            vertical-align: middle;
        }

        .cronograma-table .label {
            background-color: #f0f4f8;
            font-weight: bold;
            width: 40%;
        }

        .contenido-box {
            border: 1px solid #999;
            padding: 6px 8px;
            margin-bottom: 6px;
            background-color: #f9f9f9;
            border-radius: 3px;
            font-size: 9px;
            line-height: 1.5;
            white-space: pre-wrap;
            word-wrap: break-word;
        }

        .conclusion-box {
            border: 1px solid #2c5282;
            padding: 6px 8px;
            margin-bottom: 6px;
            background-color: #e8f0fe;
            border-radius: 3px;
            border-left: 4px solid #2c5282;
            font-size: 9px;
            line-height: 1.5;
        }

        .recomendacion-box {
            border: 1px solid #856404;
            padding: 6px 8px;
            margin-bottom: 6px;
            background-color: #fff3cd;
            border-radius: 3px;
            border-left: 4px solid #856404;
            font-size: 9px;
            line-height: 1.5;
        }

        .observacion-box {
            padding: 5px 6px;
            border: 1px solid #999;
            background-color: #fff9c4;
            border-radius: 3px;
            font-size: 9px;
        }

        .align-right { text-align: right; }
        .align-center { text-align: center; }
        .align-left { text-align: left; }
        .bold { font-weight: bold; }

        .estado-badge {
            display: inline-block;
            padding: 2px 8px;
            border-radius: 20px;
            font-size: 8px;
            font-weight: bold;
            text-transform: uppercase;
            color: white;
        }

        .estado-APROBADO { background-color: #28a745; }
        .estado-BORRADOR { background-color: #6c757d; }
        .estado-EN_PROCESO { background-color: #ffc107; color: #212529; }
        .estado-REVISADO { background-color: #17a2b8; }
        .estado-ENTREGADO { background-color: #007bff; }

        .prioridad-badge {
            display: inline-block;
            padding: 2px 8px;
            border-radius: 20px;
            font-size: 8px;
            font-weight: bold;
            text-transform: uppercase;
            color: white;
        }

        .prioridad-URGENTE { background-color: #dc3545; }
        .prioridad-ALTA { background-color: #fd7e14; }
        .prioridad-MEDIA { background-color: #ffc107; color: #212529; }
        .prioridad-BAJA { background-color: #28a745; }

        .nota-box {
            font-size: 8px;
            margin-top: 8px;
            padding: 5px 8px;
            background-color: #f8f9fa;
            border-radius: 3px;
            border-left: 3px solid #2c5282;
        }

        .nota-box p {
            margin: 2px 0;
        }

        .fin-informe {
            text-align: center;
            font-size: 10px;
            font-weight: bold;
            color: #333;
            margin-top: 20px;
            padding: 8px 0;
            letter-spacing: 2px;
        }

        .fin-informe span {
            padding: 0 15px;
        }

        /* ====================================================== */
        /* FOOTER - Fijo en cada página */
        /* ====================================================== */
        footer {
            position: fixed;
            bottom: -48mm;
            left: 0;
            right: 0;
            padding: 0;
            background: white;
            z-index: 1000;
        }

        /* FIRMAS - Antes de la línea divisora */
        .footer-firmas {
            width: 100%;
            border-collapse: collapse;
            margin: 0 0 8px 0;
        }

        .footer-firmas td {
            text-align: center;
            padding: 0 15px;
            vertical-align: bottom;
            width: 50%;
        }

        .firma-linea {
            border-top: 1px solid #000;
            width: 70%;
            margin: 0 auto;
            padding-top: 0;
        }

        .firma-texto {
            margin-top: 4px;
            font-size: 8px;
            line-height: 1.3;
        }

        .firma-texto .nombre {
            font-weight: bold;
            font-size: 9px;
        }

        /* LÍNEA DIVISORA */
        .footer-line {
            border-top: 2px solid #1c3d6e;
            margin: 6px 0 6px 0;
            width: 100%;
        }

        /* INFORMACIÓN INSTITUCIONAL - Times New Roman 7pt cursiva y negritas */
        .footer-info {
            width: 100%;
            border-collapse: collapse;
            margin-top: 3px;
        }

        .footer-info td {
            font-family: "Times New Roman", Times, serif;
            font-size: 7pt;
            font-weight: bold;
            font-style: italic;
            color: #333;
            text-align: center;
            padding: 1px 3px;
            border: none;
            vertical-align: middle;
            line-height: 1.4;
        }

        .footer-info .pagina {
            font-weight: bold;
            font-style: normal;
            font-size: 7pt;
        }

        .mb-10 { margin-bottom: 10px; }
        .mt-10 { margin-top: 10px; }
        .mt-20 { margin-top: 20px; }
    </style>
</head>
<body>

    @php
        $cfg = \App\Models\Documento::whereSlug('informe-final')->first() ?? new \App\Models\Documento;
        $global = \App\Models\Configuracion::obtener();
        $logoPath = $cfg->config('logo_path') ?? $global->logo_path;
    @endphp

    <!-- ====================================================== -->
    <!-- HEADER - Fijo en cada página -->
    <!-- ====================================================== -->
    <header>
        <table class="header-table">
            <tr>
                <!-- LOGO -->
                <td class="logo-cell">
                    <div class="logo-container">
                        @if($logoPath && file_exists(storage_path('app/public/' . $logoPath)))
                            <img src="{{ storage_path('app/public/' . $logoPath) }}" alt="Logo">
                        @elseif(file_exists(public_path('images/logo-cima.jpg')))
                            <img src="{{ public_path('images/logo-cima.jpg') }}" alt="Logo CIMA">
                        @elseif(file_exists(public_path('images/logo-cima.png')))
                            <img src="{{ public_path('images/logo-cima.png') }}" alt="Logo CIMA">
                        @else
                            <div style="width: 65px; height: 65px; background: #2c5282; color: white; display: flex; align-items: center; justify-content: center; font-size: 13px; font-weight: bold;">
                                {{ $cfg->config('institucion_sigla', 'CIMA') }}
                            </div>
                        @endif
                    </div>
                </td>

                <!-- TÍTULO CENTRADO -->
                <td class="center-cell">
                    <div class="titulo">INFORME TÉCNICO</div>
                    <div class="subtitulo">{{ strtoupper($cfg->config('laboratorio_nombre') ?? $global->laboratorio_nombre ?? 'CENTRO DE INVESTIGACIÓN MINERO AMBIENTAL') }}</div>
                    <div class="institucion">{{ $cfg->config('institucion_nombre') ?? $global->institucion_nombre ?? 'CIMA-UATF' }}</div>
                    <div class="sigla">{{ $cfg->config('institucion_sigla') ?? $global->institucion_sigla ?? 'CIMA-UATF' }}</div>
                    <div style="font-size: 8px; margin-top: 2px;">
                        <span style="display: inline-block; padding: 1px 6px; border: 1px solid #666; border-radius: 2px; background: #f8f9fa; font-weight: bold;">INFORME FINAL</span>
                    </div>
                </td>

                <!-- CÓDIGO -->
                <td class="codigo-cell">
                    <div class="codigo-box">
                        <div><span class="label">{{ $cfg->codigo_documento ?? 'PO03-FR04' }}</span></div>
                        <div><span class="label">VERSIÓN:</span> {{ $cfg->version ?? '01' }}</div>
                        <div><span class="label">FECHA:</span> {{ $cfg->fecha_documento ?? $informe->fecha_emision->format('Y-m-d') }}</div>
                        <div class="codigo-informe">
                            <span class="label">CÓDIGO:</span> {{ $informe->codigo }}
                        </div>
                    </div>
                </td>
            </tr>
        </table>
        <div class="separator"></div>
    </header>

    <!-- ====================================================== -->
    <!-- CONTENIDO -->
    <!-- ====================================================== -->
    <div class="page-content">

        <!-- ESTADO Y PRIORIDAD -->
        <div style="margin-bottom: 8px; padding: 4px 8px; background-color: #f8f9fa; border: 1px solid #dee2e6; border-radius: 4px; display: flex; justify-content: space-between; font-size: 9px;">
            <div>
                <span class="bold">ESTADO:</span>
                <span class="estado-badge estado-{{ $informe->estado }}">
                    {{ $informe->estado_texto ?? $informe->estado }}
                </span>
            </div>
            <div>
                <span class="bold">PRIORIDAD:</span>
                <span class="prioridad-badge prioridad-{{ $informe->prioridad }}">
                    {{ $informe->prioridad_texto ?? $informe->prioridad }}
                </span>
            </div>
        </div>

        <!-- SECCIÓN 1: DATOS DE LA PROFORMA ASOCIADA -->
        @if($informe->proforma)
        <div class="mb-10">
            <div class="section-title">1.- DATOS DE LA PROFORMA ASOCIADA</div>

            <table class="data-table">
                <tr>
                    <td class="label" style="width: 25%;">Código Proforma:</td>
                    <td style="width: 75%;">{{ $informe->proforma->codigo }}</td>
                </tr>
                <tr>
                    <td class="label">Cliente:</td>
                    <td>{{ $informe->proforma->cliente->razon_social ?? 'N/A' }}</td>
                </tr>
                <tr>
                    <td class="label">Persona de contacto:</td>
                    <td>{{ $informe->proforma->persona_contacto ?? $informe->proforma->cliente->persona_contacto ?? 'N/A' }}</td>
                </tr>
                <tr>
                    <td class="label">Teléfono:</td>
                    <td>{{ $informe->proforma->telefono_contacto ?? $informe->proforma->cliente->telefono ?? 'N/A' }}</td>
                </tr>
                <tr>
                    <td class="label">NIT:</td>
                    <td>{{ $informe->proforma->cliente->nit ?? 'N/A' }}</td>
                </tr>
                <tr>
                    <td class="label">Tipo de muestra:</td>
                    <td>{{ $informe->proforma->tipo_muestra }}</td>
                </tr>
                @if($informe->proforma->procedencia)
                <tr>
                    <td class="label">Procedencia:</td>
                    <td>{{ $informe->proforma->procedencia }}</td>
                </tr>
                @endif
                <tr>
                    <td class="label">Fecha recepción:</td>
                    <td>{{ $informe->proforma->fecha_recepcion->format('d/m/Y') }}</td>
                </tr>
                <tr>
                    <td class="label">Muestreado por:</td>
                    <td>{{ $informe->proforma->muestreado_por ?? 'N/A' }}</td>
                </tr>
            </table>
        </div>

        <!-- SECCIÓN 2: PARÁMETROS SOLICITADOS -->
        @if($informe->proforma->parametros && $informe->proforma->parametros->count() > 0)
        <div class="mb-10">
            <div class="section-title">2.- PARÁMETROS SOLICITADOS</div>

            <table class="params-table">
                <thead>
                    <tr>
                        <th style="width: 5%;">#</th>
                        <th style="width: 50%;">Parámetro</th>
                        <th style="width: 20%;">Método</th>
                        <th style="width: 10%;">N° Muestras</th>
                        <th style="width: 15%;">Precio Unit. (Bs)</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($informe->proforma->parametros as $index => $parametro)
                    <tr>
                        <td class="align-center">{{ $index + 1 }}</td>
                        <td class="align-left">{{ $parametro->nombre }}</td>
                        <td class="align-center">{{ $parametro->metodo ?? 'N/A' }}</td>
                        <td class="align-center">{{ $parametro->pivot->cantidad_muestras }}</td>
                        <td class="align-right">Bs. {{ number_format($parametro->pivot->precio_unitario, 2) }}</td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
        @endif
        @endif

        <!-- SECCIÓN 3: CRONOGRAMA DEL INFORME -->
        <div class="mb-10">
            <div class="section-title">3.- CRONOGRAMA DEL INFORME</div>

            <table class="cronograma-table">
                @if($informe->fecha_emision)
                <tr>
                    <td class="label">Fecha de Emisión:</td>
                    <td>{{ $informe->fecha_emision->format('d/m/Y') }}</td>
                </tr>
                @endif
                @if($informe->fecha_analisis)
                <tr>
                    <td class="label">Fecha de Análisis:</td>
                    <td>{{ \Carbon\Carbon::parse($informe->fecha_analisis)->format('d/m/Y') }}</td>
                </tr>
                @endif
                @if($informe->fecha_revision)
                <tr>
                    <td class="label">Fecha de Revisión:</td>
                    <td>{{ \Carbon\Carbon::parse($informe->fecha_revision)->format('d/m/Y') }}</td>
                </tr>
                @endif
                @if($informe->fecha_entrega)
                <tr>
                    <td class="label" style="background-color: #d4edda; font-weight: bold;">Fecha de Entrega:</td>
                    <td style="background-color: #d4edda; font-weight: bold;">{{ \Carbon\Carbon::parse($informe->fecha_entrega)->format('d/m/Y') }}</td>
                </tr>
                @endif
            </table>
        </div>

        <!-- SECCIÓN 4: RESULTADOS DEL ANÁLISIS -->
        @if($informe->resultado)
        <div class="mb-10">
            <div class="section-title">4.- RESULTADOS DEL ANÁLISIS</div>
            <div class="contenido-box">
                {!! nl2br(e($informe->resultado)) !!}
            </div>
        </div>
        @endif

        <!-- SECCIÓN 5: CONCLUSIONES -->
        @if($informe->conclusiones)
        <div class="mb-10">
            <div class="section-title">5.- CONCLUSIONES</div>
            <div class="conclusion-box">
                {!! nl2br(e($informe->conclusiones)) !!}
            </div>
        </div>
        @endif

        <!-- SECCIÓN 6: RECOMENDACIONES -->
        @if($informe->recomendaciones)
        <div class="mb-10">
            <div class="section-title">6.- RECOMENDACIONES</div>
            <div class="recomendacion-box">
                {!! nl2br(e($informe->recomendaciones)) !!}
            </div>
        </div>
        @endif

        <!-- SECCIÓN 7: OBSERVACIONES -->
        @if($informe->observaciones)
        <div class="mb-10">
            <div class="section-title">7.- OBSERVACIONES</div>
            <div class="observacion-box">
                {!! nl2br(e($informe->observaciones)) !!}
            </div>
        </div>
        @endif

        <!-- NOTAS -->
        <div class="nota-box">
            <p><strong>Nota 1:</strong> {{ $cfg->config('nota1', 'Este informe es válido únicamente con las firmas correspondientes.') }}</p>
            <p><strong>Nota 2:</strong> {{ $cfg->config('nota2', 'Los resultados reportados corresponden exclusivamente a las muestras analizadas.') }}</p>
            <p><strong>Nota 3:</strong> {{ $cfg->config('nota3', 'Prohibida la reproducción parcial de este informe sin autorización del CIMA.') }}</p>
        </div>

        <!-- FIN DEL INFORME -->
        <div class="fin-informe" id="finInforme">
            <span>-------------------- FIN DEL INFORME --------------------</span>
        </div>

    </div>

    <!-- ====================================================== -->
    <!-- FOOTER - Fijo en cada página -->
    <!-- ====================================================== -->
    <footer>
        <!-- FIRMAS - Antes de la línea divisora -->
        <table class="footer-firmas">
            <tr>
                <td>
                    <div class="firma-linea"></div>
                    <div class="firma-texto">
                        <div class="nombre">{{ $cfg->config('responsable_nombre') ?? $global->responsable_nombre ?? 'Lic. Mayra Anghela Calderón Rosas' }}</div>
                        <div>{{ $cfg->config('responsable_cargo') ?? $global->responsable_cargo ?? 'RESPONSABLE - UAQ' }}</div>
                    </div>
                </td>
                <td>
                    <div class="firma-linea"></div>
                    <div class="firma-texto">
                        <div class="nombre">{{ $cfg->config('director_nombre') ?? $global->director_nombre ?? 'M.Sc. Ing. Elva Fernández I.' }}</div>
                        <div>{{ $cfg->config('director_cargo') ?? $global->director_cargo ?? 'DIRECTOR(A) CIMA - UATF' }}</div>
                    </div>
                </td>
            </tr>
        </table>

        <!-- LÍNEA DIVISORA -->
        <div class="footer-line"></div>

        <!-- INFORMACIÓN INSTITUCIONAL CON NUMERACIÓN -->
        <table class="footer-info">
            <tr>
                <td colspan="3">
                    Av. Arce esq. Villazón s/n - Teléfono/Fax 62-29711 - Edificio facultad de Ingenieria Minera bloque 1, segundo piso
                </td>
            </tr>
            <tr>
                <td colspan="3">
                    Cel: 78570522 - cima-uatf@uatf.edu.bo
                </td>
            </tr>
            <tr>
                <td colspan="3" class="pagina">
                    Página <span class="pagenum"></span> de {{ $totalPaginas ?? '?' }}
                </td>
            </tr>
        </table>
    </footer>
</body>
</html>