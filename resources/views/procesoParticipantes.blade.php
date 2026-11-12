@php use Carbon\Carbon; 
use App\Models\Bien;
@endphp
<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Procesos</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <style>
        body, html {
            font-family: Arial, sans-serif;
            margin: 10px 10px;
            padding: 0 10px;
            height: 99%;
            width: 98%;
            justify-content: center;
            align-items: center;
        }
        @page{
            size: A4 landscape;
        }

        .headerBlock {
            background-color: black;
            height: 75px;
            width: 40px;
            margin-right: 20px;
        }

        .headerImage {
            width: auto;
            height: 60px;
            padding-left: 20px;
            padding-right: 20px;
        }

        .footerImage {
            width: auto;
            height: 75px;
            opacity: 0.5;
        }


        /*    WIDTH*/
        .w100 {
            width: 100%;
        }

        .h100 {
            height: 100%;
        }

        /*    MARGIN*/

        /*    STYLES*/
        .wContent {
            width: 60px;
            vertical-align: baseline;
        }

        .text-center {
            text-align: center;
        }

        .text-start {
            text-align: left;
        }

        .text-end {
            text-align: right;
        }

        .interletter {
            color: #4e413e;
            letter-spacing: 2px;
        }

        /*    FOOTER*/
        .footer {
            color: white;
            position: absolute;
            bottom: 0;
            left: 0;
            width: 100%;
            padding-top: 16px;
            padding-bottom: 20px;
            height: 120px;
            font-size: 14px;
        }

        .tableFooter {
            border-top: 2px solid #040780;
            border-bottom: 2px solid #040780;
        }

        .tableFooter td {
            padding-top: 15px;
            font-size: 10px;
        }

        .black {
            background-color: black;
        }

        .p64 {
            padding: 64px;
        }

        .pe64 {
            padding-right: 64px;
        }

        .mb40 {
            margin-bottom: 40px;
        }

        .mb20 {
            margin-bottom: 20px;
        }

        .mb10 {
            margin-bottom: 10px;
        }

        .p32 {
            padding: 32px 32px 24px;
        }

        .px64 {
            padding-left: 64px;
            padding-right: 64px;
        }

        .w50 {
            width: 50%;
        }

        .m0 {
            margin: 0;
        }

        .font30 {
            font-size: 30px;
        }

        .mx64 {
            margin-left: 64px;
            margin-right: 64px;
        }

        .mx56 {
            margin-left: 56px;
            margin-right: 56px;
        }

        .tableData th {
            padding: 4px;
            color: white;
            background-color: #040780;
            border-top: 1px solid #040780;
            border-bottom: 2px solid #040780;
        }

        .tableData td {
            padding: 4px;
            border-bottom: 1px solid rgba(30, 41, 59, 0.5);
        }

        .bold {
            font-weight: bold;
        }

        .gray {
            color: #4d4d4d;
        }

        .font12 {
            font-size: 12px;
        }

        .font14 {
            font-size: 14px;
        }

        .font20 {
            font-size: 20px;
        }

        .font10 {
            font-size: 10px;
        }


    </style>
</head>


<body>

<table class="w100">
    <tr class="w100">
        <td class="text-center w100">
            <h2 class="m0 text-center font20">LISTADO DE PROCESOS, BIENES E INSCRIPCIONES</h2>
        </td>
    </tr>
    <tr>
        <td class="text-center interletter bold">
            {{ Carbon::now()->locale('es')->isoFormat('D [de] MMMM [de] YYYY') }}
        </td>
    </tr>
</table>

    <table class="w100 tableData">
        <tr class="text-center font12">
            <th style="width: 20%">PROCESO</th>            
            <th style="width: 10%">FECHA INICIO</th>
            <th style="width: 10%">FECHA FIN</th>
            <th style="width: 10%">TIPO</th>
            <th style="width: 10%">MODALIDAD</th>
            <th style="width: 10%">EMPRESA</th>
            <th style="width: 10%">BIEN</th>
            <th style="width: 10%">VALOR REF.</th>
            <th style="width: 10%">PARTICIPANTE</th>
            <th style="width: 10%">SITUACION</th>
        </tr>


        @foreach ($lista as $index => $proceso)
        @php $bienes = Bien::find($proceso->bien_id);
        if($proceso->situacion_inscripcion=="P"){
            $situacion="Pendiente";
        }elseif($proceso->situacion_inscripcion=="A"){
            $situacion="Aprobado";
        }else{
            $situacion="Rechazado";
        }

        @endphp
            <tr class="font12">
                <td>{{ $proceso->nombre }}</td>
                <td>{{ date("d/m/Y H:i:s",strtotime($proceso->fechainicio)) }}</td>
                <td>{{ date("d/m/Y H:i:s",strtotime($proceso->fechafin)) }}</td>
                <td>{{ $proceso->tipo }}</td>
                <td>{{ $proceso->modalidad }}</td>
                <td>{{ $proceso->empresa->razonsocial }}</td>
                <td>{{ $bienes->producto->nombre }}</td>
                <td class="text-center">{{ ($bienes->producto->moneda=="SOLES"?"S/ ":"$ ").$bienes->valorreferencia }}</td>
                <td>{{ $proceso->apellidopaterno.' '.$proceso->apellidomaterno.' '.$proceso->nombres }}</td>
                <td>{{ $situacion }}</td>
            </tr>
        @endforeach
    </table>

</body>

</html>