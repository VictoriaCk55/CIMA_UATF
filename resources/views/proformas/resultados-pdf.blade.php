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
@php $parametrosReversed = $proforma->parametros->reverse(); @endphp
<div class="wrapper">
<div class="table-content">

<table class="encabezado">

    <tr>

        <td rowspan="3" width="4.5cm">

            @php
                $cfg = \App\Models\Documento::whereSlug('resultados-ensayo')->first() ?? new \App\Models\Documento;
                $logo = $cfg->config('logo_path');
            @endphp
            @if($logo && file_exists(storage_path('app/public/' . $logo)))
                <img src="{{ storage_path('app/public/' . $logo) }}" class="logo">
            @else
                <img src="{{ public_path('images/logo-cima.jpg') }}" class="logo">
            @endif

        </td>

        <td rowspan="3" class="titulo">

            RESULTADOS DE ENSAYO

        </td>

        <td class="left small-doc" width="4.5cm">
            {{ $cfg->codigo_documento }}
        </td>

    </tr>

    <tr>

        <td class="left small-doc">
            VERSION: {{ $cfg->version }}
        </td>

    </tr>

    <tr>

        <td class="left small-doc">
            FECHA: {{ $cfg->fecha_documento }}
        </td>

    </tr>

</table>

<div style="height:1mm;"></div>

<table>

    <tr>

        <th colspan="2" width="170">
            Parámetros
        </th>

        @foreach($parametrosReversed as $p)

            <th>
                {{ $p->nombre }}
            </th>

        @endforeach

    </tr>

    <tr>

        <td colspan="2" width="170">
            <strong>
                Límites de cuantificación
            </strong>
        </td>

        @foreach($parametrosReversed as $p)

            <td>
                {{ ($limites[$p->id] ?? $p->limite_cuantificacion) ?: '---' }}
            </td>

        @endforeach

    </tr>

    <tr>

        <td colspan="2" width="170">
            <strong>
                Unidad
            </strong>
        </td>

        @foreach($parametrosReversed as $p)

            <td>
                {{ ($unidades[$p->id] ?? $p->unidad) ?: '---' }}
            </td>

        @endforeach

    </tr>

    <tr>

        <td colspan="2" width="170">
            <strong>
                Método ó técnica de ensayo
            </strong>
        </td>

        @foreach($parametrosReversed as $p)

            <td>
                {{ $p->codigo_poe ?? '---' }}
            </td>

        @endforeach

    </tr>

    <tr>

        <td colspan="2" width="170">
            <strong>
                Responsable de ensayo
            </strong>
        </td>

        @foreach($parametrosReversed as $p)

            <td>
                {{ $responsables[$p->id] ?? '---' }}
            </td>

        @endforeach

    </tr>

    <tr>

        <td colspan="2" width="170">
            <strong>
                Fecha de ensayo
            </strong>
        </td>

        @foreach($parametrosReversed as $p)

            <td>
                {{ $fechas[$p->id] ?? '---' }}
            </td>

        @endforeach

    </tr>

    @php $firstCodLab = true; $totalCodLab = count($resultados); @endphp
    @foreach($resultados as $muestra => $datos)

    <tr>

        @if($firstCodLab)
        <td rowspan="{{ $totalCodLab }}" class="codigo-lab">
            <strong>Cod. Lab.</strong>
        </td>
        @php $firstCodLab = false; @endphp
        @endif

        <td>
            @php
                $numeroProforma = last(explode('-', $proforma->codigo));
                $partesCodigoLab = explode('-', $proforma->generarCodigoLaboratorio($muestra));
                $partesCodigoLab[2] = $numeroProforma;
            @endphp
            {{ implode('-', $partesCodigoLab) }}
        </td>

        @foreach($parametrosReversed as $p)

            <td>
                {{ $datos[$p->id] ?? '---' }}
            </td>

        @endforeach

    </tr>

    @endforeach

    <tr>

        <td colspan="2" width="170">
            <strong>V°B°</strong>
        </td>

        <td>

            @php
                $vbsList = array_unique(array_filter(array_values($vbs ?? [])));
            @endphp
            {{ !empty($vbsList) ? implode(' / ', $vbsList) : '---' }}

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
