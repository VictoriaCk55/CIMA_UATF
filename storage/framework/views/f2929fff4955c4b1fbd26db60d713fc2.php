<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <?php
        $cfg = \App\Models\Documento::whereSlug('solicitud-ensayo')->first() ?? new \App\Models\Documento;
        $logo = $cfg->config('logo_path');
    ?>
    <title>REPORTE <?php echo e($reporte->codigoRuido()); ?></title>
    <style>
        @page { margin: 10mm 15mm 10mm 15mm; }
        body {
            font-family: "Times New Roman", Times, serif;
            font-size: 12pt;
            line-height: 1.4;
            color: #000;
            margin: 0;
            padding: 52pt 0 0 0;
            background: #fff;
        }
        .header-line1 { text-align: center; font-size: 10pt; font-weight: bold; margin: 0; line-height: 1.1; white-space: nowrap; }
        .header-line2 { text-align: center; font-size: 10pt; font-weight: bold; margin: 0; line-height: 1.1; }
        .header-line3 { text-align: center; font-size: 11pt; font-weight: bold; margin: 0; line-height: 1.1; }
        .header-table { width: 100%; margin-bottom: 6pt; }
        .header-table .logo { width: 65px; height: auto; max-height: 65px; }
        .header-table .logo-cell { width: 18%; text-align: center; vertical-align: middle; }
        .titulo-ppal { text-align: center; font-size: 12pt; font-weight: bold; text-decoration: underline; margin: 0; }
        .titulo-sec { text-align: center; font-size: 11pt; font-weight: bold; text-decoration: underline; margin: 0 0 3pt 0; }
        .info-grid { width: 100%; border-collapse: collapse; margin-bottom: 8pt; }
        .info-grid td { padding: 0.5pt 4pt; vertical-align: top; font-size: 10pt; line-height: 1.2; }
        .info-grid .label { font-weight: bold; width: 1.5in; white-space: nowrap; }
        .info-grid .value { width: auto; }
        .section-title { text-align: center; font-size: 10pt; font-weight: bold; margin: 8pt 0 3pt 0; }
        .tabla { width: 100%; border-collapse: collapse; margin-bottom: 8pt; }
        .tabla th, .tabla td { border: 1px solid #000; padding: 1.5pt 5pt; font-size: 8pt; text-align: center; vertical-align: middle; line-height: 1.1; }
        .tabla th { background-color: #B0E68E; color: #000; font-weight: bold; }
        .tabla td.num { font-weight: bold; }
        .tabla td.left { text-align: left; }
        .tabla-pm { width: 100%; border-collapse: collapse; margin-bottom: 8pt; }
        .tabla-pm th, .tabla-pm td { border: 1px solid #000; padding: 1.5pt 3pt; font-size: 8pt; vertical-align: middle; }
        .tabla-pm th { background-color: #B0E68E; color: #000; font-weight: bold; text-align: center; }
        .tabla-pm td.left { text-align: left; }
        .comentario-box { border: 1px solid #B0E68E; padding: 8pt 10pt; margin: 8pt 0 20pt 0; }
        .comentarios { text-align: justify; font-size: 10pt; line-height: 1.5; margin: 4pt 0 0 0; padding: 0; white-space: pre-wrap; }
        .firmas-section { width: 100%; margin-top: 30pt; }
        .firmas-section td { width: 45%; text-align: center; padding: 6pt; font-size: 10pt; vertical-align: bottom; }
        .firmas-section .sello-cell { width: 10%; text-align: center; vertical-align: middle; }
        .firma-line { border-top: 1px solid #B0E68E; padding-top: 4pt; margin-top: 40pt; display: inline-block; }
        .sello-img { width: 55px; opacity: 0.5; }
        .sello-ovalado { width: 50px; opacity: 0.5; }
    </style>
</head>
<body>
    <?php
        $p = $reporte->proforma;
        $c = $p->cliente;
        $rr = $reporte->resultados_ruido ?? [];
        if (is_string($rr)) { $rr = json_decode($rr, true) ?? []; }
        $pm = $reporte->puntos_medicion ?? [];
        if (is_string($pm)) { $pm = json_decode($pm, true) ?? []; }
        $pm = array_values(array_filter($pm, fn($pt) => (!isset($pt['categoria']) || $pt['categoria'] === 'RUIDO') && (!empty($pt['descripcion']) || !empty($pt['valor1']) || !empty($pt['valor2']))));
        $puntosCount = max(count($rr), count($pm));
        $info = $reporte->info('RUIDO');
        $tipoMedicion = $info['subtipo_ruido'] ?? 'Ruido Ambiental';
    ?>

    <!-- ENCABEZADO INSTITUCIONAL -->
    <div style="position: fixed; top: -12pt; left: 0; right: 0; background: #fff; z-index: 1000; padding-bottom: 3pt; border-bottom: 1.5pt solid #B0E68E;">
    <table style="border-collapse: collapse; width: auto; margin: 0 auto;">
        <tr>
            <td style="text-align: center; vertical-align: bottom; white-space: nowrap; padding: 0;">
                <?php if($logo && file_exists(storage_path('app/public/' . $logo))): ?>
                    <img src="<?php echo e(storage_path('app/public/' . $logo)); ?>" style="width: 65px; height: auto; max-height: 65px;" alt="Logo">
                <?php elseif(file_exists(public_path('images/logo-cima.jpg'))): ?>
                    <img src="<?php echo e(public_path('images/logo-cima.jpg')); ?>" style="width: 65px; height: auto; max-height: 65px;" alt="Logo CIMA">
                <?php elseif(file_exists(public_path('images/logo-cima.png'))): ?>
                    <img src="<?php echo e(public_path('images/logo-cima.png')); ?>" style="width: 65px; height: auto; max-height: 65px;" alt="Logo CIMA">
                <?php endif; ?>
            </td>
            <td style="text-align: center; vertical-align: bottom; padding: 0 3cm;">
                <div class="header-line1">CENTRO DE INVESTIGACIÓN MINERO AMBIENTAL</div>
                <div class="header-line2">UNIVERSIDAD AUTÓNOMA TOMÁS FRÍAS</div>
                <div class="header-line3">"CIMA - UATF"</div>
            </td>
            <td style="text-align: center; vertical-align: bottom; white-space: nowrap; padding: 0;">
                <?php $logoUatf = public_path('images/uatf.png'); ?>
                <?php if(file_exists($logoUatf)): ?>
                    <img src="<?php echo e($logoUatf); ?>" style="width: 55px; height: auto; max-height: 55px;" alt="Logo UATF">
                <?php endif; ?>
            </td>
        </tr>
    </table>
    </div>

    <!-- TÍTULO -->
    <div class="titulo-ppal">REPORTE</div>
    <div class="titulo-sec">MEDICIÓN DE NIVEL DE PRESIÓN SONORA (NPS)</div>

    <!-- INFORMACIÓN GENERAL -->
    <table class="info-grid">
        <tr><td class="label">NOMBRE CLIENTE:</td><td class="value" colspan="3"><?php echo e(strtoupper($c->razon_social)); ?></td></tr>
        <tr><td class="label">CÓDIGO REPORTE:</td><td class="value" colspan="3"><?php echo e($reporte->codigoRuido()); ?></td></tr>
        <tr><td class="label">FECHA EMISIÓN DE REPORTE:</td><td class="value" colspan="3"><?php echo e(!empty($info['fecha_emision']) ? \Carbon\Carbon::parse($info['fecha_emision'])->format('Y/m/d') : ''); ?></td></tr>
        <tr><td class="label">FECHA DE MEDICIÓN:</td><td class="value" colspan="3"><?php echo e(!empty($info['fecha_medicion']) ? \Carbon\Carbon::parse($info['fecha_medicion'])->format('Y/m/d') : ''); ?></td></tr>
        <tr><td class="label">TIPO DE MEDICIÓN:</td><td class="value" colspan="3"><?php echo e($tipoMedicion); ?></td></tr>
        <tr><td class="label">PERIODO DE MEDICIÓN:</td><td class="value" colspan="3"><?php echo e(strtolower($info['periodo_medicion'] ?? '')); ?></td></tr>
        <tr><td class="label">MEDICIÓN EFECTUADA POR:</td><td class="value" colspan="3"><?php echo e(strtolower($info['medicion_efectuada_por'] ?? '')); ?></td></tr>
        <tr><td class="label">EQUIPO USADO PARA MEDICIÓN:</td><td class="value" colspan="3"><?php echo e(strtolower($info['equipo_usado'] ?? '')); ?></td></tr>
    </table>

    <!-- TABLA DE RESULTADOS NPS -->
    <div class="section-title">RESULTADOS DE MEDICIÓN DEL NPS</div>
    <table class="tabla">
        <thead>
            <tr>
                <th rowspan="2" style="width: 10%;">CÓDIGO</th>
                <th colspan="2" style="width: 22%;">HORA DE MEDICIÓN</th>
                <th rowspan="2" style="width: 12%;">TIPO DE RUIDO</th>
                <th rowspan="2" style="width: 18%;">MÁXIMO (Lmáx.) dB(A)</th>
                <th rowspan="2" style="width: 18%;">MÍNIMO (Lmín.) dB(A)</th>
                <th rowspan="2" style="width: 20%;">EQUIVALENTE (Leq) dB(A)</th>
            </tr>
            <tr>
                <th>INICIAL</th>
                <th>FINAL</th>
            </tr>
        </thead>
        <tbody>
            <?php $__currentLoopData = $rr; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $r): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
            <tr>
                <td><?php echo e($r['codigo'] ?? ''); ?></td>
                <td><?php echo e($r['hora_inicial'] ?? ''); ?></td>
                <td><?php echo e($r['hora_final'] ?? ''); ?></td>
                <td><?php echo e($r['tipo_ruido'] ?? $subtipo); ?></td>
                <td class="num"><?php echo e($r['lmax'] ?? ''); ?></td>
                <td class="num"><?php echo e($r['lmin'] ?? ''); ?></td>
                <td class="num"><?php echo e($r['leq'] ?? ''); ?></td>
            </tr>
            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
        </tbody>
    </table>

    <!-- DESCRIPCIÓN DE PUNTOS DE MEDICIÓN -->
    <div class="section-title">DESCRIPCIÓN REFERENCIAL DE LOS PUNTOS DE MEDICIÓN DEL NPS</div>
    <table class="tabla-pm">
        <thead>
            <tr>
                <th style="width: 18%;">CÓDIGO</th>
                <th style="width: 42%;">DESCRIPCIÓN DEL PUNTO</th>
                <th colspan="4" style="width: 40%;">UBICACIÓN<br><span style="font-size: 8pt; font-weight: normal;">ZONA <?php echo e($pm[0]['zona'] ?? ''); ?></span></th>
            </tr>
        </thead>
        <tbody>
            <?php for($i = 0; $i < $puntosCount; $i++): ?>
            <?php $r = $rr[$i] ?? []; $pt = $pm[$i] ?? []; ?>
            <tr>
                <td style="text-align: center;"><?php echo e($r['codigo'] ?? $pt['codigo'] ?? ''); ?></td>
                <td class="left"><?php echo e($pt['descripcion'] ?? ''); ?></td>
                <td style="text-align: center;"><?php echo e($pt['direccion1'] ?? 'N'); ?></td>
                <td style="text-align: center;"><?php echo e($pt['valor1'] ?? $pt['norte'] ?? ''); ?></td>
                <td style="text-align: center;"><?php echo e($pt['direccion2'] ?? 'E'); ?></td>
                <td style="text-align: center;"><?php echo e($pt['valor2'] ?? $pt['este'] ?? ''); ?></td>
            </tr>
            <?php endfor; ?>
        </tbody>
    </table>

    <!-- COMENTARIOS -->
    <div class="comentario-box">
        <strong style="font-size: 10pt;">COMENTARIO:</strong>
        <?php if($reporte->observaciones_ruido): ?>
        <div class="comentarios"><?php echo e($reporte->observaciones_ruido); ?></div>
        <?php endif; ?>
        <div class="comentarios" style="margin-top: 8pt;">De acuerdo al Reglamento en Materia de Contaminación Atmosférica de la Ley 1333, en su Anexo 6 - Limites permisibles de emisión de ruido, el limite maximo permisible de emisión de ruido en fuentes fijas es de 68 dB(A) de las seis a las veintidós horas y de 65 dB(A) de las veintidós a las seis horas.</div>
        <div class="comentarios">Este documento pierde validez si no cuenta con las firmas y sellos autorizados.</div>
    </div>

    <!-- FIRMAS -->
    <table class="firmas-section">
        <tr>
            <td>
                <div class="firma-line">
                    <strong><?php echo e($reporte->responsable_uia ?? '_________________________'); ?></strong>
                </div>
                <div style="margin-top: 2pt;"><?php echo e($reporte->cargo_responsable ?? 'RESPONSABLE - UIA'); ?></div>
            </td>
            <td class="sello-cell">
                <?php if($cfg->config('sello')): ?>
                <img src="<?php echo e(public_path($cfg->config('sello'))); ?>" class="sello-img" alt="Sello">
                <?php endif; ?>
            </td>
            <td>
                <div class="firma-line" style="margin-top: 40pt;">
                    <strong><?php echo e($reporte->directora_cima ?? '_________________________'); ?></strong>
                </div>
                <div style="margin-top: 2pt;"><?php echo e($reporte->cargo_directora ?? 'DIRECTORA CIMA - UATF'); ?></div>
                <?php if($cfg->config('sello_ovalado')): ?>
                <div style="text-align: right; margin-top: 4pt;">
                    <img src="<?php echo e(public_path($cfg->config('sello_ovalado'))); ?>" class="sello-ovalado" alt="Sello Ovalado">
                </div>
                <?php endif; ?>
            </td>
        </tr>
    </table>

    <div style="text-align: center; margin-top: 30pt; font-size: 10pt; color: #666; white-space: nowrap;">
        ——— FIN DEL INFORME ———
    </div>

    <script type="text/php">
        if (isset($pdf)) {
            $fontBoldItalic = $fontMetrics->getFont("times", "bold_italic");
            $h = $pdf->get_height();
            $w = $pdf->get_width();
            $green = array(0.690, 0.902, 0.557);
            $black = array(0, 0, 0);

            $linea1 = "Av. Arce esq. Villazón s/n. Edificio Facultad de Ingeniería Minera bloque 1. Segundo piso: Teléfono/Fax: 62-29711";
            $linea2 = "E-MAIL: cima.uatf@uatf.edu.bo";

            $pdf->page_line(72, $h - 50, 540, $h - 50, $green, 1.5);
            $x1 = ($w - $fontMetrics->getTextWidth($linea1, $fontBoldItalic, 7)) / 2;
            $x2 = ($w - $fontMetrics->getTextWidth($linea2, $fontBoldItalic, 7)) / 2;
            $pdf->page_text($x1, $h - 44, $linea1, $fontBoldItalic, 7, $black);
            $pdf->page_text($x2, $h - 32, $linea2, $fontBoldItalic, 7, $black);
            $pdf->page_text(($w - $fontMetrics->getTextWidth("Página 99 de 99", $fontBoldItalic, 7)) / 2, $h - 20, "Página {PAGE_NUM} de {PAGE_COUNT}", $fontBoldItalic, 7, $black);
        }
    </script>
</body>
</html>
<?php /**PATH D:\CIMA_UATF-main\resources\views/reportes/ruido-pdf.blade.php ENDPATH**/ ?>