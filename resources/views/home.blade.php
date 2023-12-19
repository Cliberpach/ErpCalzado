@extends('layout') 
@section('content')
<div class="wrapper wrapper-content">
    <div class="row">
        <div class="col-lg-3">
            <div class="ibox ">
                <div class="ibox-title">
                    <span class="label label-success float-right">{{ mes() }}</span>
                    <h5>Ventas</h5>
                </div>
                <div class="ibox-content">
                    <h1 class="no-margins">{{ number_format(ventas_mensual(), 2) }}</h1>
                    <div class="stat-percent font-bold text-success d-none">98% <i class="fa fa-bolt"></i></div>
                    <small>Total de ventas:</small>
                </div>
            </div>
        </div>
        <div class="col-lg-3">
            <div class="ibox ">
                <div class="ibox-title">
                    <span class="label label-info float-right">{{ mes() }}</span>
                    <h5>Compras</h5>
                </div>
                <div class="ibox-content">
                    <h1 class="no-margins">{{ number_format(compras_mensual(),2) }}</h1>
                    <div class="stat-percent font-bold text-info d-none">20% <i class="fa fa-level-up"></i></div>
                    <small>Total de Compras:</small>
                </div>
            </div>
        </div>
        <div class="col-lg-3">
            <div class="ibox ">
                <div class="ibox-title">
                    <span class="label label-primary float-right">{{ mes() }}</span>
                    <h5>Cuentas Cobrar</h5>
                </div>
                <div class="ibox-content">
                    <h1 class="no-margins">{{ number_format(cuentas_cobrar(),2) }}</h1>
                    <div class="stat-percent font-bold text-navy d-none">44% <i class="fa fa-level-up"></i></div>
                    <small>Cuentas Cobrar total:</small>
                </div>
            </div>
        </div>
        <div class="col-lg-3">
                <div class="ibox ">
                    <div class="ibox-title">
                        <span class="label label-danger float-right">{{ mes() }}</span>
                        <h5>Cuentas Pagar</h5>
                    </div>
                    <div class="ibox-content">
                        <h1 class="no-margins">{{ number_format(cuentas_pagar(),2) }}</h1>
                        <div class="stat-percent font-bold text-danger d-none">38% <i class="fa fa-level-down"></i></div>
                        <small>Cuentas Pagar total:</small>
                    </div>
                </div>
    </div>
</div>

<div class="row">
    <div class="col-lg-12">

        <div class="ibox ">
            <div class="ibox-title">
                <h5>Ventas</h5>
                <div class="ibox-tools">
                    <a class="collapse-link">
                        <i class="fa fa-chevron-up"></i>
                    </a>

                </div>
            </div>

            <div class="ibox-content">
                <div class="row">
                <div class="col-12 text-center">

                    <h2><b>Ventas</b></h2>

                    <div class="flot-chart">
                        <div class="flot-chart-content" id="ventas_morris"></div>
                    </div>


                </div>
            </div>

        </div>


    </div>

    <div class="col-lg-12">
        <div class="ibox ">

            <div class="ibox-title">
                <h5>Compras</h5>
                <div class="ibox-tools">
                    <a class="collapse-link">
                        <i class="fa fa-chevron-up"></i>
                    </a>

                </div>
            </div>

            <div class="ibox-content">
                <div class="row">
                    <div class="col-12 text-center">
                        <h2><b>Compras</b></h2>
                        <div class="flot-chart">
                            <div class="flot-chart-content" id="compras_morris"></div>
                        </div>
                    </div>
                </div>
            </div>

        </div>



    </div>

    <div class="col-lg-12">
        <div class="ibox ">
            <div class="ibox-title">
                <h5>Cuentas por Cobrar</h5>
                <div class="ibox-tools">
                    <a class="collapse-link">
                        <i class="fa fa-chevron-up"></i>
                    </a>

                </div>
            </div>

            <div class="ibox-content">
                <div class="row">

                    <div class="col-12">
                        <h2><b>Cuentas por Cobrar</b></h2>
                        <div class="flot-chart">
                            <div class="flot-chart-content" id="cobrar_morris"></div>
                        </div>
                    </div>

                </div>

            </div>

        </div>

    </div>

    <div class="col-lg-12">
        <div class="ibox ">
            <div class="ibox-title">
                <h5>Cuentas por Pagar</h5>
                <div class="ibox-tools">
                    <a class="collapse-link">
                        <i class="fa fa-chevron-up"></i>
                    </a>

                </div>

            </div>
            <div class="ibox-content">
                <div class="row">
                    <div class="col-12 text-center">
                        <h2><b>Cuentas por Pagar</b></h2>
                        <div class="flot-chart">
                            <div class="flot-chart-content" id="pagar_morris"></div>
                        </div>
                    </div>
                </div>
            </div>

        </div>
    </div>
</div>

@stop
@push('styles')
<link href="{{asset('Inspinia/css/plugins/morris/morris-0.4.3.min.css')}}" rel="stylesheet">
@endpush
@push('scripts')

<script src="{{asset('Inspinia/js/plugins/morris/raphael-2.1.0.min.js')}}"></script>
<script src="{{asset('Inspinia/js/plugins/morris/morris.js')}}"></script>

<script>
    $(document).ready(function() {
        var ventas = [];
        var compras = [];
        var cobrar = [];
        var pagar = [];
        detalles();
    });
</script>

<script>

    function detalles()
    {
        $.ajax({
            url: '{{route("home.dashboard")}}',
            type: 'GET',
            data: { '_token': $('input[name=_token]').val()},
            success: function(response) {
                if (response.success) {
                    if(response.ventas.length > 0)
                    {
                        ventas = [];
                        ventas = response.ventas;
                    }
                    else{
                        ventas = [{'nombre':'NO HAY DATOS','total':'0'}];
                    }
                    //-------------------------------
                    if(response.compras.length > 0)
                    {
                        compras = [];
                        compras = response.compras;
                    }
                    else{
                        compras = [{'nombre':'NO HAY DATOS','total':'0'}];
                    }
                    //-------------------------------
                    if(response.cobrar.length > 0)
                    {
                        cobrar = [];
                        cobrar = response.cobrar;
                    }
                    else{
                        cobrar = [{'nombre':'NO HAY DATOS','total':'0'}];
                    }
                    //-------------------------------
                    if(response.pagar.length > 0)
                    {
                        pagar = [];
                        pagar = response.pagar;
                    }
                    else{
                        pagar = [{'nombre':'NO HAY DATOS','total':'0'}];
                    }
                    //-------------------------------
                    llenar();
                } else {
                    ventas = [{'nombre':'NO HAY DATOS','total':'0'}];
                    compras = [{'nombre':'NO HAY DATOS','total':'0'}];
                    cobrar = [{'nombre':'NO HAY DATOS','total':'0'}];
                    pagar = [{'nombre':'NO HAY DATOS','total':'0'}];
                }
            }
        });
    }

    function llenar()
    {
        new Morris.Bar({

            element: 'ventas_morris',

            data: ventas,
            xkey: "nombre",

            ykeys: ['total'],

            labels: ["Total S/"],
            hideHover: 'auto',
            resize: true,
            barColors: ['#1ab394'],
            });
        //-----------------------
        new Morris.Bar({

            element: 'compras_morris',

            data: compras,
            xkey: "nombre",

            ykeys: ['total'],

            labels: ["Total S/"],
            hideHover: 'auto',
            resize: true,
            barColors: ['#1ab394'],
            });
        //-----------------------
        new Morris.Bar({

            element: 'cobrar_morris',

            data: cobrar,
            xkey: "nombre",

            ykeys: ['total'],

            labels: ["Total S/"],
            hideHover: 'auto',
            resize: true,
            barColors: ['#1ab394'],
            });
        //-----------------------
        new Morris.Bar({

            element: 'pagar_morris',

            data: pagar,
            xkey: "nombre",

            ykeys: ['total'],

            labels: ["Total S/"],
            hideHover: 'auto',
            resize: true,
            barColors: ['#1ab394'],
            });
        //-----------------------
    }

    CalientaAgua = function(litrosAgua,gramosSal){alert(
        alert($('select option:selected').val())
    )};
</script>
@endpush
