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
<<<<<<< HEAD
        font-size: 11px;
        color:#000;
=======
        font-size: 14pt;
        color:#0070C0;
>>>>>>> origin/main
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
<<<<<<< HEAD
    }

    .logo{
        width:90px;
=======
        font-weight:bold;
        font-family: "Times New Roman", Times, serif;
        font-size:14pt;
    }

    .logo{
        max-height:1.5cm;
        width:110px;
>>>>>>> origin/main
    }

    .titulo{
        font-size:22px;
        font-weight:bold;
    }

    .encabezado td{
<<<<<<< HEAD
        height:40px;
=======
        height:0.5cm;
>>>>>>> origin/main
    }

    .left{
        text-align:left;
    }

<<<<<<< HEAD
    .small{
        font-size:9px;
    }

    .codigo-lab{
        writing-mode: vertical-rl;
        transform: rotate(180deg);
        width:20px;
        font-weight:bold;
=======
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
>>>>>>> origin/main
    }

</style>

</head>
<body>
@php $parametrosReversed = $proforma->parametros->reverse(); @endphp
<div class="wrapper">
<div class="table-content">

<table class="encabezado">

    <tr>

<<<<<<< HEAD
        <td rowspan="3" width="120">
=======
        <td rowspan="3" width="4.5cm">
>>>>>>> origin/main

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

<<<<<<< HEAD
        <td class="left" width="180">
=======
        <td class="left small-doc" width="4.5cm">
>>>>>>> origin/main
            {{ $cfg->codigo_documento }}
        </td>

    </tr>

    <tr>

<<<<<<< HEAD
        <td class="left">
=======
        <td class="left small-doc">
>>>>>>> origin/main
            VERSION: {{ $cfg->version }}
        </td>

    </tr>

    <tr>

<<<<<<< HEAD
        <td class="left">
=======
        <td class="left small-doc">
>>>>>>> origin/main
            FECHA: {{ $cfg->fecha_documento }}
        </td>

    </tr>

</table>

<<<<<<< HEAD
<br>
=======
<div style="height:1mm;"></div>
>>>>>>> origin/main

<table>

    <tr>

<<<<<<< HEAD
        <th colspan="2">
=======
        <th colspan="2" width="170">
>>>>>>> origin/main
            Parámetros
        </th>

        @foreach($parametrosReversed as $p)

            <th>
                {{ $p->nombre }}
            </th>

        @endforeach

    </tr>

    <tr>

<<<<<<< HEAD
        <td colspan="2">
=======
        <td colspan="2" width="170">
>>>>>>> origin/main
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

<<<<<<< HEAD
        <td colspan="2">
=======
        <td colspan="2" width="170">
>>>>>>> origin/main
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

<<<<<<< HEAD
        <td colspan="2">
=======
        <td colspan="2" width="170">
>>>>>>> origin/main
            <strong>
                Método ó técnica de ensayo
            </strong>
        </td>

        @foreach($parametrosReversed as $p)

<<<<<<< HEAD
            <td class="small">
=======
            <td>
>>>>>>> origin/main
                {{ $p->codigo_poe ?? '---' }}
            </td>

        @endforeach

    </tr>

    <tr>

<<<<<<< HEAD
        <td colspan="2">
=======
        <td colspan="2" width="170">
>>>>>>> origin/main
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

<<<<<<< HEAD
        <td colspan="2">
=======
        <td colspan="2" width="170">
>>>>>>> origin/main
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
<<<<<<< HEAD
        <td rowspan="{{ $totalCodLab }}">
=======
        <td rowspan="{{ $totalCodLab }}" class="codigo-lab">
>>>>>>> origin/main
            <strong>Cod. Lab.</strong>
        </td>
        @php $firstCodLab = false; @endphp
        @endif

        <td>
<<<<<<< HEAD

            {{ $proforma->generarCodigoLaboratorio($muestra) }}

=======
            @php
                $numeroProforma = last(explode('-', $proforma->codigo));
                $partesCodigoLab = explode('-', $proforma->generarCodigoLaboratorio($muestra));
                $partesCodigoLab[2] = $numeroProforma;
            @endphp
            {{ implode('-', $partesCodigoLab) }}
>>>>>>> origin/main
        </td>

        @foreach($parametrosReversed as $p)

            <td>
<<<<<<< HEAD

                {{ $datos[$p->id] ?? '---' }}

=======
                {{ $datos[$p->id] ?? '---' }}
>>>>>>> origin/main
            </td>

        @endforeach

    </tr>

    @endforeach

    <tr>

<<<<<<< HEAD
        <td>
            <strong>V°B°</strong>
        </td>

        <td colspan="{{ count($parametrosReversed) + 1 }}">
=======
        <td colspan="2" width="170">
            <strong>V°B°</strong>
        </td>

        <td>
>>>>>>> origin/main

            @php
                $vbsList = array_unique(array_filter(array_values($vbs ?? [])));
            @endphp
            {{ !empty($vbsList) ? implode(' / ', $vbsList) : '---' }}

        </td>

<<<<<<< HEAD
    </tr>

</table>

<br>

<table>

    <tr>

        <td style="border:none; text-align:right;">

            Pag 1 de 1

=======
        <td style="text-align:right;">
            Pag 1 de 1
>>>>>>> origin/main
        </td>

    </tr>

</table>

</div>
</div>
</body>
<<<<<<< HEAD
</html>
=======
</html>
>>>>>>> origin/main
