<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8"/>
    <title>PROFORMA <?php echo e($proforma->codigo); ?></title>

    <style>
        /* ====================================================== */
        /* CONFIGURACIÓN DE PÁGINA A4 */
        /* ====================================================== */
        @page {
            size: A4;
            margin: 32mm 10mm 50mm 10mm;
        }

        body {
            font-family: "Times New Roman", Times, serif;
            font-size: 10px;
            line-height: 1.3;
            color: #000;
            margin: 0;
            padding: 0;
            background-color: white;
        }

        /* ====================================================== */
        /* NUMERACIÓN DE PÁGINA ACTUAL (esta sí funciona en dompdf) */
        /* El TOTAL de páginas ya NO se calcula con counter(pages) */
        /* (bug conocido de dompdf que siempre devuelve 0), sino   */
        /* que se pasa desde el controlador como $totalPaginas.    */
        /* ====================================================== */
        .pagenum:before {
            content: counter(page);
        }

        /* ====================================================== */
        /* HEADER */
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
            margin-top: 2px;
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
            vertical-align: middle;
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

        .codigo-box .codigo-proforma {
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

        /* ====================================================== */
        /* COLORES POR TIPO DE PROFORMA */
        /* ====================================================== */
        <?php if($proforma->tipo === 'AMBIENTAL'): ?>
            .section-title {
                background-color: #B0E68E;
                color: #1a3a1a;
                font-weight: bold;
                padding: 3px 8px;
                margin-bottom: 4px;
                font-size: 10px;
                border-radius: 2px;
            }

            .params-table thead th {
                background-color: #B0E68E;
                color: #1a3a1a;
                text-align: center;
                padding: 3px;
                border: 1px solid #999;
                font-weight: bold;
                font-size: 8px;
            }

            .logistica-table thead th {
                background-color: #B0E68E;
                color: #1a3a1a;
                text-align: center;
                padding: 3px;
                border: 1px solid #999;
                font-weight: bold;
                font-size: 8px;
            }

            .logistica-table tfoot {
                background-color: #e8f5e0;
                font-weight: bold;
            }
        <?php else: ?>
            .section-title {
                background-color: #2c5282;
                color: white;
                font-weight: bold;
                padding: 3px 8px;
                margin-bottom: 4px;
                font-size: 10px;
                border-radius: 2px;
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

            .logistica-table thead th {
                background-color: #2c5282;
                color: white;
                text-align: center;
                padding: 3px;
                border: 1px solid #999;
                font-weight: bold;
                font-size: 8px;
            }

            .logistica-table tfoot {
                background-color: #f0f4f8;
                font-weight: bold;
            }
        <?php endif; ?>

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

        .logistica-table {
            width: 100%;
            border-collapse: collapse;
            font-size: 8px;
            margin-bottom: 8px;
        }

        .logistica-table thead th {
            text-align: center;
            padding: 3px;
            border: 1px solid #999;
            font-weight: bold;
            font-size: 8px;
        }

        .logistica-table tbody td {
            border: 1px solid #999;
            padding: 2px 3px;
            font-size: 8px;
        }

        .logistica-table tfoot td {
            border: 1px solid #999;
            padding: 2px 3px;
            font-size: 8px;
        }

        .align-right { text-align: right; }
        .align-center { text-align: center; }
        .align-left { text-align: left; }
        .bold { font-weight: bold; }

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

        .financial-summary {
            border: 2px solid #2c5282;
            padding: 8px 12px;
            margin: 10px 0;
            background-color: #f8f9fa;
            border-radius: 4px;
        }

        .summary-title {
            font-size: 11px;
            font-weight: bold;
            color: #2c5282;
            margin-bottom: 6px;
            text-align: center;
            text-transform: uppercase;
        }

        .summary-line {
            margin: 3px 0;
            font-size: 10px;
            display: flex;
            justify-content: space-between;
        }

        .summary-line.total {
            font-size: 12px;
            font-weight: bold;
            margin-top: 6px;
            padding-top: 5px;
            border-top: 2px solid #2c5282;
        }

        .text-success { color: #28a745; }
        .text-danger { color: #dc3545; }

        /* ====================================================== */
        /* ALERTA */
        /* ====================================================== */
        .alert-modification {
            background-color: #fff3cd;
            border: 1px solid #ffc107;
            border-left: 4px solid #ffc107;
            padding: 6px 8px;
            margin: 8px 0;
            border-radius: 4px;
            font-size: 8px;
        }

        .alert-modification-title {
            font-weight: bold;
            color: #856404;
            margin-bottom: 2px;
            font-size: 9px;
        }

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

        .observacion-box {
            padding: 5px 6px;
            border: 1px solid #e2e8f0;
            border-radius: 3px;
            font-size: 8px;
            background-color: #fffde7;
            line-height: 1.4;
        }

        /* ====================================================== */
        /* FIN DEL INFORME */
        /* ====================================================== */
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
        /* FOOTER */
        /* ====================================================== */
        footer {
            position: fixed;
            bottom: -42mm;
            left: 0;
            right: 0;
            padding: 0;
            background: white;
            z-index: 1000;
        }

        /* FIRMAS */
        .footer-firmas {
            width: 100%;
            border-collapse: collapse;
            margin: 0 0 5px 0;
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
            margin-top: 3px;
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
            margin: 6px 0 4px 0;
            width: 100%;
        }

        /* INFORMACIÓN INSTITUCIONAL */
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

    <?php
        $docSlug = match($proforma->tipo) {
            'AMBIENTAL' => 'solicitud-ensayo-ambiental',
            'ANALISIS_QUIMICO', 'INVESTIGACION' => 'solicitud-ensayo',
            default => 'solicitud-ensayo'
        };
        $cfg = \App\Models\Documento::whereSlug($docSlug)->first() ?? new \App\Models\Documento;
        $global = \App\Models\Configuracion::obtener();
        $logoPath = $cfg->config('logo_path') ?? $global->logo_path;
    ?>

    <!-- ====================================================== -->
    <!-- HEADER -->
    <!-- ====================================================== -->
    <header>
        <table class="header-table">
            <tr>
                <td class="logo-cell">
                    <div class="logo-container">
                        <?php if($logoPath && file_exists(storage_path('app/public/' . $logoPath))): ?>
                            <img src="<?php echo e(storage_path('app/public/' . $logoPath)); ?>" alt="Logo">
                        <?php elseif(file_exists(public_path('images/logo-cima.jpg'))): ?>
                            <img src="<?php echo e(public_path('images/logo-cima.jpg')); ?>" alt="Logo CIMA">
                        <?php elseif(file_exists(public_path('images/logo-cima.png'))): ?>
                            <img src="<?php echo e(public_path('images/logo-cima.png')); ?>" alt="Logo CIMA">
                        <?php else: ?>
                            <div style="width: 65px; height: 65px; background: #2c5282; color: white; display: flex; align-items: center; justify-content: center; font-size: 13px; font-weight: bold;">
                                <?php echo e($cfg->config('institucion_sigla', 'CIMA')); ?>

                            </div>
                        <?php endif; ?>
                    </div>
                </td>
                <td class="center-cell">
                    <div class="titulo">PROFORMA DE SERVICIOS</div>
                    <div class="subtitulo"><?php echo e(strtoupper($cfg->config('laboratorio_nombre') ?? $global->laboratorio_nombre ?? 'CENTRO DE INVESTIGACIÓN MINERO AMBIENTAL')); ?></div>
                    <div class="institucion">
                        <?php if($proforma->unidad == 'UIA'): ?>
                            Unidad de Investigación Ambiental "UIA"
                        <?php elseif($proforma->unidad == 'UAQ'): ?>
                            Unidad de Análisis Químico "UAQ"
                        <?php else: ?>
                            <?php echo e($cfg->config('footer_texto')); ?>

                        <?php endif; ?>
                    </div>
                    <div class="sigla">
                        <?php echo e($cfg->config('institucion_sigla') ?? $global->institucion_sigla ?? 'CIMA-UATF'); ?>

                    </div>
                    <?php if($proforma->unidad): ?>
                        <div style="font-size: 8px; font-weight: bold; color: #2c5282; margin-top: 2px;">
                            <?php echo e($proforma->unidad == 'UIA' ? 'Unidad de Investigación Ambiental' : 'Unidad de Análisis Químico'); ?>

                        </div>
                    <?php endif; ?>
                </td>
                <td class="codigo-cell">
                    <div class="codigo-box">
                        <div><span class="label"><?php echo e($cfg->codigo_documento ?? 'PO01-FR02'); ?></span></div>
                        <div><span class="label">VERSIÓN:</span> <?php echo e($cfg->version ?? '06'); ?></div>
                        <div><span class="label">FECHA:</span> <?php echo e($cfg->fecha_documento ?? $proforma->fecha_emision->format('Y-m-d')); ?></div>
                        <div class="codigo-proforma">
                            <span class="label">CÓDIGO:</span> <?php echo e($proforma->codigo); ?>

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

        <!-- DATOS DE RECEPCIÓN -->
        <?php $numRecepcion = explode('-', $proforma->codigo)[2] ?? $proforma->numero_recepcion; ?>
        <table class="data-table" style="margin-bottom: 4px;">
            <tr>
                <td style="width: 50%; text-align: left; border: 1px solid #999; padding: 3px 5px;">
                    <strong>Fecha de recepción:</strong> <?php echo e($proforma->fecha_recepcion->format('d/m/Y')); ?>

                </td>
                <td style="width: 50%; text-align: right; border: 1px solid #999; padding: 3px 5px;">
                    <strong>Nro. Recepción:</strong> <?php echo e($numRecepcion); ?>

                </td>
            </tr>
        </table>

        <!-- ====================================================== -->
        <!-- SECCIÓN 1: DATOS DEL CLIENTE -->
        <!-- ====================================================== -->
        <div class="mb-10">
            <div class="section-title">1.- DATOS DEL CLIENTE</div>
            <table class="data-table">
                <tr>
                    <td class="label" style="width: 25%;">Nombre/Razón Social:</td>
                    <td style="width: 75%;" colspan="3"><?php echo e($proforma->cliente->razon_social); ?></td>
                </tr>
                <tr>
                    <td class="label">Persona en contacto:</td>
                    <td style="width: 25%;"><?php echo e($proforma->persona_contacto ?? $proforma->cliente->persona_contacto); ?></td>
                    <td class="label" style="width: 20%;">Teléfono/Celular:</td>
                    <td style="width: 30%;"><?php echo e($proforma->telefono_contacto ?? $proforma->cliente->telefono ?? 'N/A'); ?></td>
                </tr>
                <tr>
                    <td class="label">NIT:</td>
                    <td><?php echo e($proforma->cliente->nit ?? 'N/A'); ?></td>
                    <td class="label">Dirección:</td>
                    <td><?php echo e($proforma->cliente->direccion ?? 'N/A'); ?></td>
                </tr>
            </table>
        </div>

        <!-- ====================================================== -->
        <!-- SECCIÓN 2: DATOS DE LA MUESTRA -->
        <!-- ====================================================== -->
        <div class="mb-10">
            <div class="section-title">2.- DATOS DE LA MUESTRA</div>
            <table class="data-table">
                <tr>
                    <td class="label" style="width: 25%;">Tipo de muestra:</td>
                    <td style="width: 25%;"><?php echo e($proforma->tipo_muestra); ?></td>
                    <td class="label" style="width: 25%;">Muestreado por:</td>
                    <td style="width: 25%;"><?php echo e($proforma->muestreado_por ?? 'N/A'); ?></td>
                </tr>
                <tr>
                    <td class="label">Fecha de muestreo:</td>
                    <td><?php echo e($proforma->fecha_emision->format('d/m/Y')); ?></td>
                    <td class="label">Hora recepción:</td>
                    <td><?php echo e($proforma->hora_recepcion ?? 'N/A'); ?></td>
                </tr>
                <tr>
                    <td class="label">Procedencia:</td>
                    <td><?php echo e($proforma->procedencia ?? 'N/A'); ?></td>
                    <td class="label">Coordenadas:</td>
                    <td>
                        <?php
                            $coords = [];
                            if ($proforma->punto_cardinal_1 && $proforma->valor_cardinal_1) {
                                $coords[] = $proforma->punto_cardinal_1 . ': ' . $proforma->valor_cardinal_1;
                            }
                            if ($proforma->punto_cardinal_2 && $proforma->valor_cardinal_2) {
                                $coords[] = $proforma->punto_cardinal_2 . ': ' . $proforma->valor_cardinal_2;
                            }
                        ?>
                        <?php echo e(!empty($coords) ? implode(' | ', $coords) : 'N/A'); ?>

                    </td>
                </tr>
                <tr>
                    <td class="label">Código de Cliente:</td>
                    <td colspan="3"><?php echo e(implode(', ', $proforma->codigo_cliente ?? []) ?: 'N/A'); ?></td>
                </tr>
            </table>
        </div>

        <!-- ====================================================== -->
        <!-- SECCIÓN 3: PARÁMETROS A ANALIZAR (SIEMPRE VISIBLE)    -->
        <!-- ====================================================== -->
        <div class="mb-10">
            <div class="section-title">3.- PARÁMETROS A ANALIZAR - MUESTRAS DE <?php echo e(strtoupper($proforma->tipo_muestra)); ?></div>
            <table class="params-table">
                <thead>
                    <tr>
                        <th style="width: 5%;">#</th>
                        <th style="width: 35%;">Parámetro</th>
                        <th style="width: 25%;">Método</th>
                        <th style="width: 10%;">N° Muestras</th>
                        <th style="width: 15%;">Precio Unit. (Bs)</th>
                        <th style="width: 10%;">Total (Bs)</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if($proforma->parametros->count() > 0): ?>
                        <?php $__currentLoopData = $proforma->parametros; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $index => $parametro): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                        <?php
                            $nombreParam = $parametro->nombre;
                            if ($parametro->categoria === 'RUIDO') {
                                $nombreParam = 'RUIDO';
                            } elseif ($parametro->categoria === 'GASES') {
                                $nombreParam = 'GASES';
                            }
                            $metodo = $proforma->tipo === 'AMBIENTAL' && $parametro->categoria === 'GASES'
                                ? ($parametro->pivot->metodo ?? '')
                                : ($proforma->tipo === 'AMBIENTAL' ? ($parametro->metodo ?? '') : ($parametro->tecnica ?: ($parametro->codigo_poe ?? '')));
                        ?>
                        <tr>
                            <td class="align-center"><?php echo e($index + 1); ?></td>
                            <td class="align-left"><?php echo e($nombreParam); ?></td>
                            <td class="align-center"><?php echo e($metodo ?: '---'); ?></td>
                            <td class="align-center"><?php echo e($parametro->pivot->cantidad_muestras); ?></td>
                            <td class="align-right">Bs. <?php echo e(number_format($parametro->pivot->precio_unitario, 2)); ?></td>
                            <td class="align-right">Bs. <?php echo e(number_format($parametro->pivot->precio_unitario * $parametro->pivot->cantidad_muestras, 2)); ?></td>
                        </tr>
                        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                    <?php else: ?>
                        <tr>
                            <td colspan="6" class="align-center">No hay parámetros asignados</td>
                        </tr>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>

        <!-- ====================================================== -->
        <!-- SECCIÓN 4: LOGÍSTICA DE MUESTREO (SOLO AMBIENTAL)     -->
        <!-- ====================================================== -->
        <?php if($proforma->tipo === 'AMBIENTAL' && $proforma->logisticasMuestreo->count() > 0): ?>
        <div class="mb-10">
            <div class="section-title">4.- LOGÍSTICA DE MUESTREO</div>
            <table class="logistica-table">
                <thead>
                    <tr>
                        <th style="width: 5%;">#</th>
                        <th style="width: 30%;">Concepto</th>
                        <th style="width: 30%;">Descripción</th>
                        <th style="width: 10%;">Cantidad</th>
                        <th style="width: 15%;">Precio Unit. (Bs)</th>
                        <th style="width: 10%;">Subtotal (Bs)</th>
                    </tr>
                </thead>
                <tbody>
                    <?php $logIndex = 0; ?>
                    <?php $__currentLoopData = $proforma->logisticasMuestreo; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $logistica): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                    <?php
                        $logIndex++;
                        $precioUnitario = $logistica->pivot->precio_unitario ?? $logistica->costo ?? 0;
                        $cantidad = $logistica->pivot->cantidad ?? 1;
                        $subtotal = $precioUnitario * $cantidad;
                        $descripcion = $logistica->pivot->descripcion ?? $logistica->descripcion ?? '';
                        $categoria = $logistica->categoria ?? '';
                    ?>
                    <tr>
                        <td class="align-center"><?php echo e($logIndex); ?></td>
                        <td class="align-left"><?php echo e($categoria); ?></td>
                        <td class="align-left"><?php echo e($descripcion); ?></td>
                        <td class="align-center"><?php echo e($cantidad); ?></td>
                        <td class="align-right">Bs. <?php echo e(number_format($precioUnitario, 2)); ?></td>
                        <td class="align-right">Bs. <?php echo e(number_format($subtotal, 2)); ?></td>
                    </tr>
                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                </tbody>
                <tfoot>
                    <tr>
                        <td colspan="5" class="align-right">TOTAL LOGÍSTICA:</td>
                        <td class="align-right">
                            Bs. <?php echo e(number_format($proforma->logisticasMuestreo->sum(fn($l) => ($l->pivot->precio_unitario ?? $l->costo ?? 0) * ($l->pivot->cantidad ?? 1)), 2)); ?>

                        </td>
                    </tr>
                </tfoot>
            </table>
        </div>
        <?php endif; ?>

        <!-- ALERTA DE MODIFICACIÓN DE PARÁMETROS -->
        <?php if($proforma->parametros_modificados && $proforma->justificacion_modificacion): ?>
        <div class="alert-modification">
            <div class="alert-modification-title">
                <strong>!! MODIFICACIÓN DE PARÁMETROS BAJO CONTRATO ¡¡</strong>
            </div>
            <div style="font-size: 8px; color: #856404;">
                <strong>Justificación:</strong> <?php echo e($proforma->justificacion_modificacion); ?>

            </div>
            <?php if($proforma->usuarioModificacion): ?>
            <div style="font-size: 7px; color: #856404; margin-top: 2px; border-top: 1px dashed #ffc107; padding-top: 2px;">
                Modificado por: <?php echo e($proforma->usuarioModificacion->name); ?> |
                Fecha: <?php echo e($proforma->updated_at->format('d/m/Y H:i:s')); ?>

            </div>
            <?php endif; ?>
        </div>
        <?php endif; ?>

        <!-- TOTAL EN LETRAS -->
        <div class="total-in-words">
            <strong><?php echo e($totalEnLetras); ?></strong>
        </div>

        <!-- ====================================================== -->
        <!-- RESUMEN FINANCIERO -->
        <!-- ====================================================== -->
        <?php
            $subtotalCalculado = $proforma->parametros->sum(fn($p) => $p->pivot->cantidad_muestras * $p->pivot->precio_unitario);
            if ($proforma->tipo === 'AMBIENTAL') {
                $subtotalCalculado += $proforma->logisticasMuestreo->sum(fn($l) => ($l->pivot->precio_unitario ?? $l->costo ?? 0) * ($l->pivot->cantidad ?? 1));
            }
        ?>
        <div class="financial-summary">
            <div class="summary-title">RESUMEN FINANCIERO</div>
            <div class="summary-line">
                <span class="bold">Subtotal:</span>
                <span>Bs. <?php echo e(number_format($subtotalCalculado, 2)); ?></span>
            </div>
            <?php if($proforma->descuento > 0): ?>
            <div class="summary-line">
                <span class="bold">Descuento Institucional (20%):</span>
                <span class="text-danger">- Bs. <?php echo e(number_format($proforma->descuento, 2)); ?></span>
            </div>
            <?php endif; ?>
            <div class="summary-line total">
                <span class="bold">TOTAL:</span>
                <span class="text-success">Bs. <?php echo e(number_format($proforma->total, 2)); ?></span>
            </div>
            <?php if($proforma->adelanto > 0): ?>
            <div class="summary-line" style="margin-top: 4px;">
                <span class="bold">Adelanto recibido:</span>
                <span>Bs. <?php echo e(number_format($proforma->adelanto, 2)); ?></span>
            </div>
            <div class="summary-line" style="border-top: 1px dashed #999; padding-top: 3px;">
                <span class="bold">Saldo pendiente:</span>
                <span class="<?php echo e($proforma->saldo > 0 ? 'text-danger' : 'text-success'); ?>">
                    Bs. <?php echo e(number_format($proforma->saldo, 2)); ?>

                </span>
            </div>
            <?php endif; ?>
        </div>

        <!-- OBSERVACIONES -->
        <?php if($proforma->observaciones): ?>
        <div class="mb-10">
            <div class="section-title">OBSERVACIONES</div>
            <div class="observacion-box">
                <?php echo nl2br(e($proforma->observaciones)); ?>

            </div>
        </div>
        <?php endif; ?>

        <!-- NOTAS -->
        <div class="nota-box">
            <p><strong>Nota 1:</strong> <?php echo e($cfg->config('nota1', 'Para realizar el análisis se debe dejar cancelado el 100% del monto total.')); ?></p>
            <p><strong>Nota 2:</strong> <?php echo e($cfg->config('nota2', 'El laboratorio no realiza declaraciones de conformidad sobre los resultados que se reportan.')); ?></p>
            <p><strong>Nota 3:</strong> <?php echo e($cfg->config('nota3', 'Los resultados estarán disponibles dentro de los plazos establecidos según el tipo de análisis.')); ?></p>
        </div>

        <!-- FIN DEL INFORME -->
        <div class="fin-informe" id="finInforme">
            <span>-------------------- FIN DEL INFORME --------------------</span>
        </div>

    </div>

    <!-- ====================================================== -->
    <!-- FOOTER -->
    <!-- ====================================================== -->
    <footer>
        <!-- FIRMAS -->
        <table class="footer-firmas">
            <tr>
                <td>
                    <div class="firma-linea"></div>
                    <div class="firma-texto">
                        <div class="nombre"><?php echo e($cfg->config('responsable_nombre') ?? $global->responsable_nombre ?? 'Lic. Mayra Anghela Calderón Rosas'); ?></div>
                        <div><?php echo e($cfg->config('responsable_cargo') ?? $global->responsable_cargo ?? 'RESPONSABLE - UAQ'); ?></div>
                    </div>
                </td>
                <td>
                    <div class="firma-linea"></div>
                    <div class="firma-texto">
                        <div class="nombre"><?php echo e($cfg->config('director_nombre') ?? $global->director_nombre ?? 'M.Sc. Ing. Elva Fernández I.'); ?></div>
                        <div><?php echo e($cfg->config('director_cargo') ?? $global->director_cargo ?? 'DIRECTOR(A) CIMA - UATF'); ?></div>
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
                    
                    
                    
                    Página <span class="pagenum"></span> de <?php echo e($totalPaginas ?? '?'); ?>

                </td>
            </tr>
        </table>
    </footer>
</body>
</html><?php /**PATH C:\Users\CORE I7\OneDrive\Escritorio\CIMA_v3_Local\resources\views/proformas/pdf.blade.php ENDPATH**/ ?>