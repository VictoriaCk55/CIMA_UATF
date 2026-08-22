
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">

    <style>
        @page{
            margin: 1cm;
        }

        body{
            font-family: "Times New Roman", Times, serif;
            font-size: 12px;
            color: #000;
            margin: 0;
            padding: 0;
        }

        /* ====================================================== */
        /* HEADER Y FOOTER */
        /* ====================================================== */

        header{
            padding:0.5cm;
            position: fixed;
            top: -1cm;
            left: 0;
            right:0;
            margin-bottom: 0.3cm;
        }

        footer{
            position: fixed;
            bottom: -1cm;
            left: 0;
            right: 0;
            margin-bottom: 0;
            padding: 0;
        }

        .header-table{
            width: 100%;
            border-collapse: collapse;
        }

        .footer-table{
            width: 100%;
            border-collapse: collapse;
        }

        .header-logo{
            width: 85px;
        }

        .header-title{
            text-align: center;
        }

        .footer-text{
            font-size: 8px;
            text-align: center;
            line-height: 1.2;
        }

        .pagenum:before{
            content: counter(page);
        }

        .pagecount:before{
            content: counter(pages);
        }

        .page-content{
            padding-bottom: 140px;
        }

        *{
            box-sizing: border-box;
        }

        table{
            width: 100%;
            border-collapse: collapse;
            page-break-inside: auto;
        }

        td, th{
            padding: 0;
            text-align: center;
            vertical-align: middle;
            overflow: hidden;
        }

        .encabezado-tabla td{
            font-size: 12px;
            padding: 2px 4px;
            line-height: 1.1;
        }

        .sin-borde, .sin-borde td, .sin-borde th{
            border: none !important;
            font-size: 11px;
        }

        .left{
            text-align: left;
        }

        .center{
            text-align: center;
            font-size: 16px;
        }

        .logo{
            width: 80px;
            height: auto;
        }

        .titulo{
            font-size: 16px;
            font-weight: bold;
        }

        .subtitulo{
            font-size: 12px;
            font-weight: bold;
        }

        .small{
            font-size: 10px;
            border: 1px solid #000;
            pading: 2px 3px;
        }

        .gris{
            background-color: #f0f0f0;
            font-weight: bold;
        }

        .firma{
            height: 70px;
            padding-top: 20px;
        }

        .vertical-text{
            writing-mode: vertical-rl;
            transform: rotate(180deg);
            white-space: nowrap;
            text-align: center;
        }

    </style>

</head>
<body>

    <?php $cfg = \App\Models\Documento::whereSlug('informe-resultados')->first() ?? new \App\Models\Documento; ?>

    <!-- ====================================================== -->
    <!-- HEADER -->
    <!-- ====================================================== -->

    <header>
        <table class="header-table">
            <tr>
                <!-- LOGO -->
                <td width="18%" style="border: none;">
                    <?php
                        $logo = $cfg->config('logo_path');
                    ?>
                    <?php if($logo && file_exists(storage_path('app/public/' . $logo))): ?>
                        <img src="<?php echo e(storage_path('app/public/' . $logo)); ?>" class="header-logo">
                    <?php else: ?>
                        <img src="<?php echo e(public_path('images/logo-cima.jpg')); ?>" class="header-logo">
                    <?php endif; ?>
                </td>

                <!-- TITULO -->
                <td width="64%" class="header-title" style="border: none; color: #0070C0;">
                    <div style="font-size: 12pt; font-weight: bold; line-height: 1.2; font-family: 'Times New Roman', Times, serif;">
                        <?php echo e(strtoupper($cfg->config('laboratorio_nombre'))); ?>

                    </div>

                    <div style="font-size: 12pt; font-weight: bold; line-height: 1.2; font-family: 'Times New Roman', Times, serif;">
                        <?php echo e(strtoupper($cfg->config('universidad_nombre'))); ?>

                    </div>

                    <div style="font-size: 14pt; font-weight: bold; margin-top: 2px; font-family: 'Times New Roman', Times, serif; letter-spacing: 2px;">
                        "<?php echo e(strtoupper($cfg->config('institucion_sigla'))); ?>"
                    </div>

                    <div style="display: inline-block; padding: 2px 6px; margin-top: 2px; font-size: 7pt; font-family: 'Times New Roman', Times, serif; font-weight: bold; font-style: italic;">
                        <?php echo e($cfg->codigo_documento); ?>/Ver. <?php echo e($cfg->version); ?>/<?php echo e($cfg->fecha_documento); ?>

                    </div>
                </td>

                <!-- VACIO -->
                <td width="18%" style="border: none;"></td>

            </tr>

        </table>
        <!-- LINEA -->
        <div style="border-top: 2px solid #2d5ea8; margin-top: 4px;"></div>

    </header>

    <?php
        $maxMuestras = 0;
        foreach($proforma->parametros as $p) {
            $cant = $p->pivot->cantidad_muestras ?? 1;
            if($cant > $maxMuestras) $maxMuestras = $cant;
        }
        if($maxMuestras == 0) $maxMuestras = 1;
        $totalBloques = (int)ceil($maxMuestras / 2);
    ?>

    <?php for($bloque = 0; $bloque < $totalBloques; $bloque++): ?>
    <?php
        $m1 = $bloque * 2 + 1;
        $m2 = $bloque * 2 + 2;
        $tieneM2 = $m2 <= $maxMuestras;
    ?>
    <div style="margin-top: 2.3cm;<?php if($bloque > 0): ?> page-break-before: always; <?php endif; ?>">

    <!-- TÍTULO -->
    <table>
        <tr>
            <td class="titulo center" style="font-size: 18px;">
                INFORME DE ENSAYO
            </td>
        </tr>
        <tr>
            <td style="text-align: right; color: #ef1111; font-size: 16pt; font-family: 'Times New Roman', Times, serif;">
                <strong>Nº:</strong> <?php echo e($proforma->numero_recepcion ?? $proforma->codigo); ?>

            </td>
        </tr>
    </table>

    <!-- ===================================================== -->
    <!-- DATOS DEL CLIENTE Y MUESTRA -->
    <!-- ===================================================== -->

    <br>

    <table style="width: 100%; border-collapse: collapse; color: #0070C0; font-size: 10pt; font-family: "Times New Roman", Times, serif;">

    <!-- RAZÓN SOCIAL -->
    <tr style="height: 0.4cm;">
        <td colspan="4" style="padding: 1px 3px; font-weight: bold; text-align: left; font-style: italic; line-height: 1;">
            RAZÓN SOCIAL/CLIENTE:
            <span style="font-weight: normal; text-transform: uppercase;">
                <?php echo e(strtoupper($proforma->cliente->razon_social ?? '---')); ?>

            </span>
        </td>
    </tr>

    <!-- FILA 1 -->
    <tr style="height: 0.4cm;">
        <td style="width: 5.3cm; font-weight: bold; padding: 1px 3px; text-align: left; font-style: italic; line-height: 1;">CONTACTO CLIENTE:</td>
        <td style="padding: 1px 3px; text-align: left; text-transform: uppercase; font-style: italic; line-height: 1;"><?php echo e(strtoupper($proforma->cliente->persona_contacto ?? '---')); ?></td>
        <td style="width: 5.3cm; font-weight: bold; padding: 1px 3px; text-align: left; font-style: italic; line-height: 1;">FECHA RECEPCIÓN:</td>
        <td style="width: 2.2cm; padding: 1px 3px; text-align: left; font-style: italic; line-height: 1;"><?php echo e(optional($proforma->fecha_recepcion)->format('Y-m-d') ?? '---'); ?></td>
    </tr>

    <!-- FILA 2 -->
    <tr style="height: 0.4cm;">
        <td style="font-weight: bold; padding: 1px 3px; text-align: left; font-style: italic; line-height: 1;">TIPO DE MUESTRA:</td>
        <td style="padding: 1px 3px; text-align: left; text-transform: uppercase; font-style: italic; line-height: 1;"><?php echo e(strtoupper($proforma->tipo_muestra ?? '---')); ?></td>
        <td style="font-weight: bold; padding: 1px 3px; text-align: left; font-style: italic; line-height: 1;">INICIO ENSAYO:</td>
        <td style="width: 2.2cm; padding: 1px 3px; text-align: left; font-style: italic; line-height: 1;"><?php echo e(optional($proforma->fecha_recepcion)->format('Y-m-d')); ?></td>
    </tr>

    <!-- FILA 3 -->
    <tr style="height: 0.4cm;">
        <td style="font-weight: bold; padding: 1px 3px; text-align: left; font-style: italic; line-height: 1;">MUESTREADO POR:</td>
        <td style="padding: 1px 3px; text-align: left; text-transform: uppercase; font-style: italic; line-height: 1;"><?php echo e(strtoupper($proforma->muestreado_por ?? 'CLIENTE')); ?></td>
        <td style="font-weight: bold; padding: 1px 3px; text-align: left; font-style: italic; line-height: 1;">CONCLUSIÓN ENSAYO:</td>
        <td style="width: 2.2cm; padding: 1px 3px; text-align: left; font-style: italic; line-height: 1;"><?php echo e(optional($proforma->fecha_conclusion_ensayo)->format('Y-m-d') ?? '---'); ?></td>
    </tr>

    <!-- FILA 4 -->
    <tr style="height: 0.4cm;">
        <td style="font-weight: bold; padding: 1px 3px; text-align: left; font-style: italic; line-height: 1;">PROCEDENCIA:</td>
        <td style="padding: 1px 3px; text-align: left; text-transform: uppercase; font-style: italic; line-height: 1;"><?php echo e(strtoupper($proforma->procedencia ?? '---')); ?></td>
        <td style="font-weight: bold; padding: 1px 3px; text-align: left; font-style: italic; line-height: 1;">FECHA EMISIÓN:</td>
        <td style="width: 2.2cm; padding: 1px 3px; text-align: left; font-style: italic; line-height: 1;"><?php echo e(now()->format('Y-m-d')); ?></td>
    </tr>

</table>

    <br>
    <div style="border: 1px solid #000; padding: 0; width: 90%; margin: 0 auto;">
    <table style="width: 100%; border-collapse: collapse; table-layout: fixed; font-size: 10px;">
        <!-- FILA SUPERIOR -->
        <tr>
            <td rowspan="5" style="border: 1px solid #000; text-align: center; vertical-align: middle; font-weight: bold; background: #f5f5f5; font-size: 9pt; font-family: 'Times New Roman', Times, serif; font-style: italic; overflow: hidden;">
                DATOS DE<br>LA MUESTRA
            </td>

            <td colspan="3" style="border: 1px solid #000; background: #9bd9e6; font-weight: bold; height: 26px; font-size: 9pt; font-family: 'Times New Roman', Times, serif; font-style: italic; text-align: center; vertical-align: middle; overflow: hidden;">
                CÓDIGO DE LABORATORIO:
            </td>

            <?php for($n = $m1; $n <= ($tieneM2 ? $m2 : $m1); $n++): ?>
            <td style="border: 1px solid #000; background: #9bd9e6; text-align: center; vertical-align: middle; font-size: 11pt; font-family: 'Times New Roman', Times, serif; overflow: hidden;">
                <strong><?php echo e($proforma->generarCodigoLaboratorio($n) ?? '---'); ?></strong>
            </td>
            <?php endfor; ?>
            <td style="border: 1px solid #000; background: #9bd9e6; overflow: hidden;">&nbsp;</td>

        </tr>

        <!-- FILA -->
        <tr>

            <td colspan="3" style="border: 1px solid #000; background: #9bd9e6; font-weight: bold; height: 18px; font-size: 9pt; font-family: 'Times New Roman', Times, serif; font-style: italic; text-align: center; vertical-align: middle; overflow: hidden;">
                CÓDIGO CLIENTE:
            </td>

            <?php for($n = $m1; $n <= ($tieneM2 ? $m2 : $m1); $n++): ?>
            <td style="border: 1px solid #000; background: #9bd9e6; text-align: center; vertical-align: middle; font-size: 11pt; font-family: 'Times New Roman', Times, serif; overflow: hidden;">
                <?php echo e($proforma->codigo_cliente[$n - 1] ?? '---'); ?>

            </td>
            <?php endfor; ?>
            <td style="border: 1px solid #000; background: #9bd9e6; overflow: hidden;">&nbsp;</td>

        </tr>

        <!-- FILA -->
        <tr>

            <td colspan="3" style="border: 1px solid #000; background: #9bd9e6; font-weight: bold; height: 18px; font-size: 9pt; font-family: 'Times New Roman', Times, serif; font-style: italic; text-align: center; vertical-align: middle; overflow: hidden;">
                FECHA DE MUESTREO:
            </td>

            <?php for($n = $m1; $n <= ($tieneM2 ? $m2 : $m1); $n++): ?>
            <td style="border: 1px solid #000; background: #9bd9e6; text-align: center; vertical-align: middle; font-size: 11pt; font-family: 'Times New Roman', Times, serif; overflow: hidden;">
                <?php echo e($proforma->fecha_emision->format('Y-m-d')); ?>

            </td>
            <?php endfor; ?>
            <td style="border: 1px solid #000; background: #9bd9e6; overflow: hidden;">&nbsp;</td>

        </tr>

        <!-- COORDENADAS -->
        <tr>

            <td rowspan="2" colspan="2" style="border: 1px solid #000; background: #9bd9e6; font-weight: bold; height: 34px; font-size: 9pt; font-family: 'Times New Roman', Times, serif; font-style: italic; text-align: center; vertical-align: middle; overflow: hidden;">
                COORDENADAS DE PUNTO DE MUESTREO: <?php echo e($muestreo->zona_utm ?: ''); ?>

            </td>

            <td style="border: 1px solid #000; background: #9bd9e6; font-weight: bold; height: 18px; font-size: 9pt; font-family: 'Times New Roman', Times, serif; font-style: italic; text-align: center; vertical-align: middle; overflow: hidden;">
                <?php echo e($muestreo->punto_cardinal_1 ?? 'E'); ?>

            </td>

            <?php for($n = $m1; $n <= ($tieneM2 ? $m2 : $m1); $n++): ?>
            <td style="border: 1px solid #000; background: #9bd9e6; text-align: center; vertical-align: middle; font-size: 11pt; font-family: 'Times New Roman', Times, serif; overflow: hidden;">
                <?php echo e($muestreo->valor_cardinal_1 ?? '---'); ?>

            </td>
            <?php endfor; ?>
            <td style="border: 1px solid #000; background: #9bd9e6; overflow: hidden;">&nbsp;</td>

        </tr>

        <!-- N -->
        <tr>

            <td style="border: 1px solid #000; background: #9bd9e6; font-weight: bold; height: 18px; font-size: 9pt; font-family: 'Times New Roman', Times, serif; font-style: italic; text-align: center; vertical-align: middle; overflow: hidden;">
                <?php echo e($muestreo->punto_cardinal_2 ?? 'N'); ?>

            </td>

            <?php for($n = $m1; $n <= ($tieneM2 ? $m2 : $m1); $n++): ?>
            <td style="border: 1px solid #000; background: #9bd9e6; text-align: center; vertical-align: middle; font-size: 11pt; font-family: 'Times New Roman', Times, serif; overflow: hidden;">
                <?php echo e($muestreo->valor_cardinal_2 ?? '---'); ?>

            </td>
            <?php endfor; ?>
            <td style="border: 1px solid #000; background: #9bd9e6; overflow: hidden;">&nbsp;</td>

        </tr>

        <!-- CABECERA – FILA AGRUPADA -->
        <tr>
            <td rowspan="2" style="border: 1px solid #000; background: #9bd9e6; font-weight: bold; height: 32px; text-align: center; vertical-align: middle; font-size: 9pt; font-family: 'Times New Roman', Times, serif; font-style: italic; overflow: hidden;">
                PARÁMETRO
            </td>

            <td rowspan="2" style="border: 1px solid #000; background: #9bd9e6; font-weight: bold; text-align: center; vertical-align: middle; font-size: 9pt; font-family: 'Times New Roman', Times, serif; font-style: italic; overflow: hidden;">
                MÉTODO DE ENSAYO
            </td>

            <td rowspan="2" style="border: 1px solid #000; background: #9bd9e6; font-weight: bold; text-align: center; vertical-align: middle; font-size: 9pt; font-family: 'Times New Roman', Times, serif; font-style: italic; overflow: hidden;">
                LÍMITE DE CUANTIFICACIÓN
            </td>

            <td rowspan="2" style="border: 1px solid #000; background: #9bd9e6; font-weight: bold; text-align: center; vertical-align: middle; font-size: 9pt; font-family: 'Times New Roman', Times, serif; font-style: italic; overflow: hidden;">
                UNIDAD
            </td>

            <?php for($n = $m1; $n <= ($tieneM2 ? $m2 : $m1); $n++): ?>
            <td rowspan="2" style="border: 1px solid #000; background: #9bd9e6; font-weight: bold; text-align: center; vertical-align: middle; font-size: 9pt; font-family: 'Times New Roman', Times, serif; font-style: italic; overflow: hidden;">
                RESULTADOS DE ENSAYO
            </td>
            <?php endfor; ?>
            <td rowspan="2" style="border: 1px solid #000; background: #9bd9e6; font-weight: bold; text-align: center; vertical-align: middle; font-size: 8pt; font-family: 'Times New Roman', Times, serif; font-style: italic; overflow: hidden;">
                LÍMITES<br>PERMISIBLES<br>SEGÚN <?php echo e($tipo === 'ANEXO_A-2' ? 'ANEXO A-2' : 'NB-512'); ?>

            </td>

        </tr>

        <!-- CABECERA – SUB-FILA -->
        <tr>

        </tr>

        <!-- FILAS DINAMICAS -->
        <?php $__currentLoopData = $proforma->parametros; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $p): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>

        <tr>
            <td style="border: 1px solid #000; height: 28px; text-align: center; vertical-align: middle; font-size: 10pt; font-family: 'Times New Roman', Times, serif; overflow: hidden;">
                <?php echo e($p->nombre); ?>

            </td>

            <td style="border: 1px solid #000; text-align: center; vertical-align: middle; font-size: 8pt; font-family: 'Times New Roman', Times, serif; overflow: hidden;">
                <?php echo e($p->codigo_poe ?? '---'); ?><br><?php echo e(ucwords(strtolower($p->tecnica ?? '---'))); ?>

            </td>

            <td style="border: 1px solid #000; text-align: center; vertical-align: middle; font-size: 10pt; font-family: 'Times New Roman', Times, serif; overflow: hidden;">
                <?php echo e($p->limite_cuantificacion ?? '---'); ?>

            </td>

            <td style="border: 1px solid #000; text-align: center; vertical-align: middle; font-size: 10pt; font-family: 'Times New Roman', Times, serif; overflow: hidden;">
                <?php echo e($p->unidad ?? '---'); ?>

            </td>

            <?php for($n = $m1; $n <= ($tieneM2 ? $m2 : $m1); $n++): ?>
            <td style="border: 1px solid #000; text-align: center; vertical-align: middle; font-size: 10pt; font-family: 'Times New Roman', Times, serif; overflow: hidden;">
                <?php echo e($resultados[$n][$p->id] ?? '---'); ?>

            </td>
            <?php endfor; ?>
            <td style="border: 1px solid #000; text-align: center; vertical-align: middle; font-size: 10pt; font-family: 'Times New Roman', Times, serif; overflow: hidden;">
                <?php $lp = $limitesMap[$p->nombre] ?? null; ?>
                <?php if($lp): ?>
                    <?php if($tipo === 'ANEXO_A-2'): ?>
                        D: <?php echo e($lp->limite_diario ?? '—'); ?><br>
                        M: <?php echo e($lp->limite_mes ?? '—'); ?>

                    <?php else: ?>
                        <?php echo e($lp->limite_permisible); ?>

                    <?php endif; ?>
                <?php else: ?>
                    ---
                <?php endif; ?>
            </td>

        </tr>

        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>

    </table>
    </div>
    <br>
    <table>
        <tr>
            <td>
                <b>POE:</b> Procedimiento Operativo de Ensayo, describo con detalle en la pista general de ensayos ofertados.<br>
            </td>
        </tr>
    </table>
    <div style="font-size: 8pt; margin-top: 8px; padding: 4px 8px; background-color: #f8f9fa; border-radius: 3px; border-left: 3px solid #2c5282; font-family: 'Times New Roman', Times, serif;">
        <p style="text-align: left; margin: 0; padding: 0; line-height: 1.2;"> <?php echo e($cfg->config('nota1', 'La información del presente informe corresponde a los resultados de ensayos en la muestra recepcionada.')); ?></p>
        <p style="text-align: left; margin: 0; padding: 0; line-height: 1.2;"> <?php echo e($cfg->config('nota2', '"CIMA-UATF", NO asume ninguna responsabilidad sobre la información proporcionada por el cliente, que pueda afectar la validez de los resultados.')); ?></p>
        <p style="text-align: left; margin: 0; padding: 0; line-height: 1.2;"> <?php echo e($cfg->config('nota3', '"CIMA-UATF", solo reconoce como válidos, informes de ensayo emitidos en soporte físico, con las firmas y sellos autorizados.')); ?></p>
    </div>
    </div>
    <?php endfor; ?>

    <footer>
        <table class="sin-borde">
            <tr class="sin-borde">
                <td class="sin-borde center">
                    <br><br><br><br>
                    _________________________
                    <br>
                    <?php echo e($cfg->config('responsable_nombre')); ?>

                    <br>
                    <strong><?php echo e($cfg->config('responsable_cargo')); ?></strong>
                </td>
                <td class="sin-borde center">
                    <br><br><br><br>
                    _________________________
                    <br>
                    <?php echo e($cfg->config('director_nombre')); ?>

                    <br>
                    <strong><?php echo e($cfg->config('director_cargo')); ?></strong>
                </td>
            </tr>
        </table>
        <br>
        <br>
        <br>

        <table style="width: 100%; border-collapse: collapse; margin-bottom: 20px;">
            <tr>
                <td style="text-align: right; font-size: 8pt; font-family: 'Times New Roman', Times, serif; font-style: italic; padding: 0; border: none; white-space: nowrap;">--------------------</td>
                <td style="text-align: center; font-size: 8pt; font-family: 'Times New Roman', Times, serif; font-style: italic; padding: 0; border: none; white-space: nowrap;">FIN DEL INFORME</td>
                <td style="text-align: left; font-size: 8pt; font-family: 'Times New Roman', Times, serif; font-style: italic; padding: 0; border: none; white-space: nowrap;">--------------------</td>
            </tr>
        </table>

        <div style="border-top: 2px solid #2d5ea8; margin-bottom: 4px;">
        </div>

        <table class="footer-table">

            <tr>

                <td class="footer-text" style="border: none; text-align: center; font-size: 7pt; font-style: italic; font-weight: bold; font-family: 'Times New Roman', Times, serif; line-height: 1.1;">
                    <?php echo e($cfg->config('footer_direccion')); ?> Edificio facultad de Ingenieria Minera bloque 1. segundo piso; Telefono/Fax:62-29711

                    <br>

                    <?php echo e($cfg->config('footer_telefono')); ?>; <?php echo e($cfg->config('footer_email')); ?>


                    <br>

                        Página
                        <span class="pagenum"></span>
                        de
                        <span class="pagenum"></span>
                    
                    <br><br>

                </td>

            </tr>

        </table>

    </footer>
    <br><br>
</body>
</html>
<?php /**PATH D:\CIMA_UATF-main\resources\views/proformas/informe-resultados-permisibles-pdf.blade.php ENDPATH**/ ?>