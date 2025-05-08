@extends('layout') 
@section('content')

@section('ventas-active', 'active')
@section('ventas-caja-active', 'active')
<div id="app">
    <ventas-component :modospago="{{ modos_pago() }}"></ventas-component>
</div>
@stop
@section('vue-css')
<link rel="stylesheet" href="{{ asset('css/app.css') }}">

<style>
    .imagen {
        width: 200px;
        height: 200px;
        border-radius: 10%;
    }

    .imagen_update {
        width: 200px;
        height: 200px;
        border-radius: 10%;
    }
</style>
@stop
@section('vue-js')
<script src="{{ asset('js/app.js?v='.rand()) }}"></script>
<script>
    function comprobanteElectronico(id) {
        const url = route("ventas.documento.comprobante", { id: id,size:100});
        window.open(url, "Comprobante SISCOM", "width=900, height=600")
    }

    function comprobanteElectronicoTicket(id) {
        const url = route("ventas.documento.comprobante", { id: id,size:80});
        window.open(url, "Comprobante SISCOM", "width=900, height=600");
    }

    function xmlElectronico(id) {
        const swalWithBootstrapButtons = Swal.mixin({
            customClass: {
                confirmButton: 'btn btn-success',
                cancelButton: 'btn btn-danger',
            },
            buttonsStyling: false
        });

        Swal.fire({
            title: "Opción XML",
            text: "¿Seguro que desea obtener el documento de venta en xml?",
            showCancelButton: true,
            icon: 'info',
            confirmButtonColor: "#1ab394",
            confirmButtonText: 'Si, Confirmar',
            cancelButtonText: "No, Cancelar",
            // showLoaderOnConfirm: true,
        }).then((result) => {
            if (result.value) {

                var url = '{{ route("ventas.documento.xml", ":id")}}';
                url = url.replace(':id',id);

                window.location.href = url

                // Swal.fire({
                //     title: '¡Cargando!',
                //     type: 'info',
                //     text: 'Generando XML',
                //     showConfirmButton: false,
                //     onBeforeOpen: () => {
                //         Swal.showLoading()
                //     }
                // })

            } else if (
                /* Read more about handling dismissals below */
                result.dismiss === Swal.DismissReason.cancel
            ) {
                swalWithBootstrapButtons.fire(
                    'Cancelado',
                    'La Solicitud se ha cancelado.',
                    'error'
                )
            }
        })

    }

</script>
@stop
