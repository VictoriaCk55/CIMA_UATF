<!DOCTYPE html>
<html lang="es">
<head>
<meta charset="UTF-8">

<style>

    @page {
        margin: 15px;
    }

    body{
        font-family: "Times New Roman", Times, serif;
        font-size: 14pt;
        color:#0070C0;
    }

    table{
        width: auto;
        border-collapse: collapse;
    }

    .wrapper{
        text-align: center;
    }

    .table-content{
        display: inline-block;
        text-align: left;
    }

    .table-content table{
        width: 100%;
    }

    td, th{
        border:1px solid #000;
        padding:4px;
        text-align:center;
        vertical-align: middle;
        font-weight:bold;
        font-family: "Times New Roman", Times, serif;
        font-size:14pt;
    }

    .logo{
        max-height:1.5cm;
        width:110px;
    }

    .titulo{
        font-size:22px;
        font-weight:bold;
    }

    .encabezado td{
        height:0.5cm;
    }

    .left{
        text-align:left;
    }

    .small-doc{
        font-size:12pt;
    }

    .codigo-lab{
        width:20px;
        font-weight:bold;
        padding:2px;
        writing-mode:tb-rl;
        -webkit-writing-mode:vertical-rl;
        -webkit-text-orientation:mixed;
        text-orientation:mixed;
    }

    .codigo-cell{
        font-size:7px;
        text-align:center;
        overflow:hidden;
    }

</style>

</head>
<body>
<?php $parametrosReversed = $proforma->parametros->reverse(); ?>
<div class="wrapper">
<div class="table-content">

<table class="encabezado">

    <tr>

        <td rowspan="3" width="4.5cm">

            <?php
                $cfg = \App\Models\Documento::whereSlug('resultados-ensayo')->first() ?? new \App\Models\Documento;
                $logo = $cfg->config('logo_path');
            ?>
            <?php if($logo && file_exists(storage_path('app/public/' . $logo))): ?>
                <img src="<?php echo e(storage_path('app/public/' . $logo)); ?>" class="logo">
            <?php else: ?>
                <img src="<?php echo e(public_path('images/logo-cima.jpg')); ?>" class="logo">
            <?php endif; ?>

        </td>

        <td rowspan="3" class="titulo">

            RESULTADOS DE ENSAYO

        </td>

        <td class="left small-doc" width="4.5cm">
            <?php echo e($cfg->codigo_documento); ?>

        </td>

    </tr>

    <tr>

        <td class="left small-doc">
            VERSION: <?php echo e($cfg->version); ?>

        </td>

    </tr>

    <tr>

        <td class="left small-doc">
            FECHA: <?php echo e($cfg->fecha_documento); ?>

        </td>

    </tr>

</table>

<div style="height:1mm;"></div>

<table>

    <tr>

        <th colspan="2" width="170">
            Parámetros
        </th>

        <?php $__currentLoopData = $parametrosReversed; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $p): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>

            <th>
                <?php echo e($p->nombre); ?>

            </th>

        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>

    </tr>

    <tr>

        <td colspan="2" width="170">
            <strong>
                Límites de cuantificación
            </strong>
        </td>

        <?php $__currentLoopData = $parametrosReversed; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $p): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>

            <td>
                <?php echo e(($limites[$p->id] ?? $p->limite_cuantificacion) ?: '---'); ?>

            </td>

        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>

    </tr>

    <tr>

        <td colspan="2" width="170">
            <strong>
                Unidad
            </strong>
        </td>

        <?php $__currentLoopData = $parametrosReversed; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $p): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>

            <td>
                <?php echo e(($unidades[$p->id] ?? $p->unidad) ?: '---'); ?>

            </td>

        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>

    </tr>

    <tr>

        <td colspan="2" width="170">
            <strong>
                Método ó técnica de ensayo
            </strong>
        </td>

        <?php $__currentLoopData = $parametrosReversed; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $p): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>

            <td>
                <?php echo e($p->codigo_poe ?? '---'); ?>

            </td>

        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>

    </tr>

    <tr>

        <td colspan="2" width="170">
            <strong>
                Responsable de ensayo
            </strong>
        </td>

        <?php $__currentLoopData = $parametrosReversed; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $p): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>

            <td>
                <?php echo e($responsables[$p->id] ?? '---'); ?>

            </td>

        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>

    </tr>

    <tr>

        <td colspan="2" width="170">
            <strong>
                Fecha de ensayo
            </strong>
        </td>

        <?php $__currentLoopData = $parametrosReversed; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $p): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>

            <td>
                <?php echo e($fechas[$p->id] ?? '---'); ?>

            </td>

        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>

    </tr>

    <?php $firstCodLab = true; $totalCodLab = count($resultados); ?>
    <?php $__currentLoopData = $resultados; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $muestra => $datos): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>

    <tr>

        <?php if($firstCodLab): ?>
        <td rowspan="<?php echo e($totalCodLab); ?>" class="codigo-lab">
            <strong>Cod. Lab.</strong>
        </td>
        <?php $firstCodLab = false; ?>
        <?php endif; ?>

        <td>
            <?php
                $numeroProforma = last(explode('-', $proforma->codigo));
                $partesCodigoLab = explode('-', $proforma->generarCodigoLaboratorio($muestra));
                $partesCodigoLab[2] = $numeroProforma;
            ?>
            <?php echo e(implode('-', $partesCodigoLab)); ?>

        </td>

        <?php $__currentLoopData = $parametrosReversed; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $p): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>

            <td>
                <?php echo e($datos[$p->id] ?? '---'); ?>

            </td>

        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>

    </tr>

    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>

    <tr>

        <td colspan="2" width="170">
            <strong>V°B°</strong>
        </td>

        <td>

            <?php
                $vbsList = array_unique(array_filter(array_values($vbs ?? [])));
            ?>
            <?php echo e(!empty($vbsList) ? implode(' / ', $vbsList) : '---'); ?>


        </td>

        <td style="text-align:right;">
            Pag 1 de 1
        </td>

    </tr>

</table>

</div>
</div>
</body>
</html>
<?php /**PATH C:\Users\CORE I7\OneDrive\Escritorio\CIMA_v3_Local\resources\views/proformas/resultados-pdf.blade.php ENDPATH**/ ?>