<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8">

    <style>

        @page{
            size: A4 landscape;
            margin: 10mm;
        }

        body{
            font-family: "Times New Roman", Times, serif;
            font-size: 11px;
            color: #000;
        }

        *{
            box-sizing: border-box;
        }

        /* =====================================================
           TABLAS
        ====================================================== */

        table{
            width: 100%;
            border-collapse: collapse;
        }

        td,
        th{
            border: 1px solid #000;
            text-align: center;
            vertical-align: middle;
            padding: 0.5px;
            overflow: hidden;
        }

        .custodia-table{
            width: 100%;
            table-layout: fixed;
            border-collapse: collapse;
        }
        .text-center{
            text-align: center;
            font-size: 12pt;
        }

        /* =====================================================
           ENCABEZADO
        ====================================================== */

        .logo-container{
            width: 60px;
            height: 60px;
            border: 1px solid #aaa;
            margin: auto;
            text-align: center;
            background: #f5f5f5;
        }

        .logo-container img{
            max-width: 55px;
            max-height: 55px;
            margin-top: 3px;
        }

        .titulo-principal{
            font-size: 14pt;
            font-weight: bold;
            letter-spacing: 1px;
            color: #1c3d6e;
            margin: 0;
            font-family: 'Times New Roman', Times, serif;
        }

        .codigo-cell{
            font-size: 8pt;
            font-weight: bold;
            padding: 3px;
            font-family: 'Times New Roman', Times, serif;
        }

        /* =====================================================
           CHECKBOX
        ====================================================== */

        .checkbox-row{
            text-align: center;
            margin: 6px 0;
        }

        .checkbox-option{
            display: inline-block;
            margin: 0 15px;
            font-size: 11px;
        }

        .checkbox-square{
            display: inline-block;
            width: 12px;
            height: 12px;
            border: 1px solid #000;
            text-align: center;
            line-height: 12px;
            font-size: 9px;
            font-weight: bold;
            vertical-align: middle;
            margin-left: 3px;
        }

        /* =====================================================
           ALTURAS
        ====================================================== */

        .fila-superior td{
            font-size: 9pt;
            font-family: 'Times New Roman', Times, serif;
        }

        .fila-superior{
            height: 7mm;
        }

        .fila-sub{
            height: 30mm;
        }

        .fila-datos{
            height: 7mm;
        }

        .fila-datos td{
            height: 5mm;
            vertical-align: middle;
        }

        .h-large{
            height: 30mm;
            vertical-align: middle;
        }

        /* =====================================================
           COLUMNAS PRINCIPALES
        ====================================================== */

        .col-identificacion,
        .col-codigo,
        .col-matriz-principal{
            font-size: 10pt;
            font-weight: bold;
            padding: 1px;
            line-height: 1.1;
        }

        .col-identificacion{
            width: 10mm;
            text-align: center;
        }

        .col-codigo{
            width: 10mm;
            text-align: center;
        }

        /* =====================================================
           MATRIZ
        ====================================================== */

        .matriz-sub{
            width: 5mm;
            min-width: 5mm;
            max-width: 5mm;
            padding: 0;
            height: 30mm;
            vertical-align: middle;
        }

        /* =====================================================
           COLUMNAS ANALISIS
        ====================================================== */

        .w-small{
            width: 4mm;
            min-width: 4mm;
            max-width: 4mm;
            padding: 0;
            vertical-align: middle;
        }

        /* =====================================================
           TEXTO VERTICAL
        ====================================================== */

        .vertical-text{
            display: inline-block;
            transform: rotate(-90deg);
            white-space: nowrap;
            font-size: 9px;
        }

        /* =====================================================
           DOMPDF
        ====================================================== */

        tr,
        td,
        th{
            page-break-inside: avoid;
        }

    </style>

</head>

<body>

    <!-- =====================================================
         ENCABEZADO
    ====================================================== -->

    <table>

        <tr>

            <!-- LOGO -->
            <td rowspan="3" style="width:28mm;">

                <div class="logo-container">

                    <?php
                        $cfg = \App\Models\Documento::whereSlug('cadena-custodia')->first() ?? new \App\Models\Documento;
                        $logo = $cfg->config('logo_path');
                    ?>
                    <?php if($logo && file_exists(storage_path('app/public/' . $logo))): ?>
                        <img src="<?php echo e(storage_path('app/public/' . $logo)); ?>" alt="Logo">
                    <?php elseif(file_exists(public_path('images/logo-cima.jpg'))): ?>
                        <img src="<?php echo e(public_path('images/logo-cima.jpg')); ?>" alt="Logo">
                    <?php elseif(file_exists(public_path('images/logo-cima.png'))): ?>
                        <img src="<?php echo e(public_path('images/logo-cima.png')); ?>" alt="Logo">
                    <?php endif; ?>

                </div>

            </td>

            <!-- TITULO -->
            <td rowspan="3">

                <div class="titulo-principal">
                    CADENA DE CUSTODIA
                </div>

            </td>

            <!-- CODIGO -->
            <td class="codigo-cell" style="width:28mm;">
                <strong><?php echo e($cfg->codigo_documento ?? 'PO01-FR02'); ?></strong>
            </td>

        </tr>

        <tr>

            <td class="codigo-cell">
                VERSIÓN: <?php echo e($cfg->version ?? '06'); ?>

            </td>

        </tr>

        <tr>

            <td class="codigo-cell">
                FECHA: <?php echo e($cfg->fecha_documento ?? date('Y-m-d')); ?>

            </td>

        </tr>

    </table>

    <!-- =====================================================
         CHECKBOX
    ====================================================== -->

    <div class="checkbox-row">

        <span class="checkbox-option">

            <strong>PROFORMA</strong>

            <span class="checkbox-square">
                <?php echo e(in_array('PROFORMA', $proforma->tipo_documento ?? []) ? '✔' : ''); ?>

            </span>

        </span>

        <span class="checkbox-option">

            <strong>COTIZACION</strong>

            <span class="checkbox-square">
                <?php echo e(in_array('COTIZACION', $proforma->tipo_documento ?? []) ? '✔' : ''); ?>

            </span>

        </span>

        <span class="checkbox-option">

            <strong>CONTRATO</strong>

            <span class="checkbox-square">
                <?php echo e(in_array('CONTRATO', $proforma->tipo_documento ?? []) ? '✔' : ''); ?>

            </span>

        </span>

        <span class="checkbox-option">

            <strong>CONTRATO MODIFICADO</strong>

            <span class="checkbox-square">
                <?php echo e(in_array('CONTRATO MODIFICADO', $proforma->tipo_documento ?? []) ? '✔' : ''); ?>

            </span>

        </span>

    </div>

    <!-- =====================================================
         TABLA PRINCIPAL
    ====================================================== -->

    <table>

        <!-- =================================================
             FILA 1
        ================================================== -->

        <tr class="fila-superior">

            <!-- TIPO MUESTRA -->
            <td colspan="1" style="text-align: center;">

                <strong>TIPO DE MUESTRA</strong>

            </td>

            <!-- ESPACIO GRANDE -->
            <td colspan="2"><?php echo e($proforma->tipo_muestra); ?></td>

            <!-- REQUERIMIENTO -->
            <td colspan="31" rowspan="3" class="text-center" style="font-size: 12pt; font-family: 'Times New Roman', Times, serif;">

                <strong>REQUERIMIENTO DE ANALISIS</strong>

            </td>

        </tr>

        <!-- =================================================
             FILA 2
        ================================================== -->

        <tr class="fila-superior">

            <!-- FECHA MUESTREO -->
            <td colspan="1" style="text-align: center;">

                <strong>FECHA DE MUESTREO</strong>

            </td>

            <!-- ESPACIO GRANDE -->
            <td colspan="2"><?php echo e($proforma->fecha_muestreo ? $proforma->fecha_muestreo->format('Y-m-d') : 'N/A'); ?></td>

        </tr>

        <!-- =================================================
             FILA 3
        ================================================== -->

        <tr class="fila-superior">

            <!-- FECHA RECEPCION -->
            <td colspan="1" style="text-align: center;">

                <strong>FECHA DE RECEPCION</strong>

            </td>

            <!-- ESPACIO GRANDE -->
            <td colspan="2"><?php echo e($proforma->fecha_recepcion ? $proforma->fecha_recepcion->format('Y-m-d') : 'N/A'); ?></td>

        </tr>

        <!-- =================================================
             ENCABEZADOS PRINCIPALES
        ================================================== -->

        <tr>

            <!-- IDENTIFICACION -->
            <td rowspan="2" colspan="1" class="h-large col-identificacion" style="overflow: hidden; word-wrap: break-word; text-align: center;">

                IDENTIFICACION
                <br>
                DE CAMPO O
                <br>
                CLIENTE

            </td>

            <!-- CODIGO -->
            <td rowspan="2" colspan="1" class="h-large col-codigo" style="overflow: hidden; word-wrap: break-word; text-align: center;">

                CODIGO
                <br>
                LABORATORIO

            </td>

            <!-- MATRIZ -->
            <td colspan="4" class="h-large col-matriz-principal" style="text-align: center;">

                MATRIZ

            </td>

            <!-- =================================================
                 28 COLUMNAS ANALISIS
            ================================================== -->

            <?php for($i = 0; $i < 28; $i++): ?>

                <?php
                    $parametroMostrar = $proforma->parametros[$i] ?? null;
                    $nombreParam = $parametroMostrar ? $parametroMostrar->nombre : '';
                    if ($parametroMostrar && $parametroMostrar->categoria === 'RUIDO') {
                        $nombreParam = 'RUIDO';
                    } elseif ($parametroMostrar && $parametroMostrar->categoria === 'GASES') {
                        $nombreParam = 'GASES';
                    }
                ?>

                <td class="w-small vertical">

                    <span class="vertical-text">
                        <?php echo e($nombreParam); ?>

                    </span>

                </td>

            <?php endfor; ?>

        </tr>

        <!-- =================================================
             SUBDIVISION MATRIZ
        ================================================== -->

        <tr class="fila-sub">

            <td class="matriz-sub">

                <div class="vertical-text">

                    AGUA SUBTERRANEA

                </div>

            </td>

            <td class="matriz-sub">

                <div class="vertical-text">

                    AGUA SUPERFICIAL

                </div>

            </td>

            <td class="matriz-sub">

                <div class="vertical-text">

                    SUELO

                </div>

            </td>

            <td class="matriz-sub">

                <div class="vertical-text">

                    OTROS

                </div>

            </td>

            <!-- =================================================
                 28 COLUMNAS METODOS
            ================================================== -->

            <?php for($i = 0; $i < 28; $i++): ?>

                <?php
                    $paramSub = $proforma->parametros[$i] ?? null;
                    if ($paramSub && $paramSub->categoria === 'GASES') {
                        $tecnicaSub = $paramSub->pivot->metodo ?? '';
                    } else {
                        $tecnicaSub = $paramSub ? ($paramSub->metodo ?? '') : '';
                    }
                ?>

                <td class="w-small vertical">

                    <span class="vertical-text">
                        <?php echo e($tecnicaSub); ?>

                    </span>

                </td>

            <?php endfor; ?>

        </tr>

        <!-- =================================================
             FILAS DE MUESTRAS POR PARAMETRO
        ================================================== -->
        
        <?php
            // Encontrar el número máximo de muestras entre todos los parámetros
            $maxMuestras = 0;
            foreach($proforma->parametros as $parametro) {
                $cantidad = $parametro->pivot->cantidad_muestras ?? 1;
                if($cantidad > $maxMuestras) {
                    $maxMuestras = $cantidad;
                }
            }
            
            // Si no hay parámetros, al menos mostrar 1 fila
            if($maxMuestras == 0) $maxMuestras = 1;
        ?>

        <?php for($fila = 0; $fila < $maxMuestras; $fila++): ?>
        
        <?php
            // Número de muestra actual (1-indexed)
            $numMuestra = $fila + 1;
        ?>

        <tr class="fila-datos">

            <!-- IDENTIFICACION -->
            <td colspan="1" style="width: 10mm; font-size: 7px; text-align: center; overflow: hidden;">
                <?php
                    $codigos = $proforma->codigo_cliente ?? [];
                    $codigoIdx = $fila < count($codigos) ? $codigos[$fila] : (count($codigos) > 0 ? $codigos[count($codigos)-1] : '---');
                ?>
                <?php echo e($codigoIdx); ?>

            </td>

            <!-- CODIGO -->
            <td colspan="1" style="width: 10mm; font-size: 9pt; font-family: 'Times New Roman', Times, serif; text-align: center; overflow: hidden;">
                <?php
                    $numeroProforma = last(explode('-', $proforma->codigo));
                    $partesCodigoLab = explode('-', $proforma->generarCodigoLaboratorio($numMuestra));
                    $partesCodigoLab[2] = $numeroProforma;
                ?>
                <?php echo e(implode('-', $partesCodigoLab)); ?>

            </td>

            <!-- MATRIZ -->
             <?php
                $tipo = strtoupper($proforma->tipo_muestra);
            ?>

            <!-- AGUA SUBTERRANEA -->
            <td style="width: 5mm;">
                <?php if(str_contains($tipo, 'SUBTERRANEA')): ?>
                    X
                <?php endif; ?>
            </td>

            <!-- AGUA SUPERFICIAL -->
            <td style="width: 5mm;">
                <?php if(str_contains($tipo, 'SUPERFICIAL')): ?>
                    X
                <?php endif; ?>
            </td>

            <!-- SUELO -->
            <td style="width: 5mm;">
                <?php if(str_contains($tipo, 'SUELO')): ?>
                    X
                <?php endif; ?>
            </td>

            <!-- OTROS -->
            <td style="width: 5mm;">
                <?php if(
                    !str_contains($tipo, 'SUBTERRANEA') &&
                    !str_contains($tipo, 'SUPERFICIAL') &&
                    !str_contains($tipo, 'SUELO')
                ): ?>
                    X
                <?php endif; ?>
            </td>

            <!-- =================================================
                 DINAMICA DE COLOR DE CANTIDAD DE MUESTRAS POR PARAMETRO
            ================================================== -->
            <?php for($i = 0; $i < 28; $i++): ?>
                
                <?php
                    $parametro = $proforma->parametros[$i] ?? null;
                ?>
                
                <?php if(!$parametro): ?>
                    <!-- Si NO existe el parámetro: fondo AZUL -->
                    <td class="w-small" style="background-color: #aed0ef;">
                    </td>
                <?php else: ?>
                    <?php
                        $cantidadMuestrasParametro = $parametro->pivot->cantidad_muestras ?? 1;
                        $debePintarse = $numMuestra <= $cantidadMuestrasParametro;
                    ?>
                    
                    <?php if($debePintarse): ?>
                        <!-- Si APPLICA para esta muestra: fondo blanco -->
                        <td class="w-small" style="background-color: #ffff; text-align: center; vertical-align: middle;">
                        </td>
                    <?php else: ?>
                        <!-- Si NO APPLICA para esta muestra: fondo AZUL -->
                        <td class="w-small" style="background-color: #aed0ef;">
                        </td>
                    <?php endif; ?>
                <?php endif; ?>
                
            <?php endfor; ?>

        </tr>

        <?php endfor; ?>
        

    </table>
        <table style="width:100%; border-collapse: collapse; margin-top: 10px;">
            <tr>
                <td style="border:1px solid black; padding:5px; font-size: 11pt; background-color: rgb(100, 170, 220); color: #000">
                    <strong>OBSERVACIONES</strong>
                </td>
            </tr>

            <tr>
                <td style="border:1px solid black; height:100px;">
                    <?php echo e($observacion ?? ''); ?>

                </td>
            </tr>
        </table>

</body>

</html><?php /**PATH C:\Users\CORE I7\OneDrive\Escritorio\CIMA_v3_Local\resources\views/proformas/cadena_custodia.blade.php ENDPATH**/ ?>