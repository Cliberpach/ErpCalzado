@extends('layout')

@section('mantenimiento-active', 'active')
@section('metodo_entrega-active', 'active')

@section('bread-module', 'Mantenimiento')
@section('bread-submodule', 'Métodos Entrega')
@section('hero-title', 'Lista de Métodos Entrega')
@section('hero-subtitle', 'Métodos Entrega')

@section('btn-add')
    <a class="main-btn-add" href="#" onclick="openMdlSedeCreate();">
        <i class="fas fa-plus-circle"></i> Nuevo
    </a>
@endsection

@section('content')

    @include('mantenimiento.metodos_entrega.modal_create')
    @include('mantenimiento.metodos_entrega.modal_edit')
    @include('mantenimiento.metodos_entrega.modal_sedes')

    <div class="wrapper wrapper-content animated fadeInRight">
        <div class="row">
            <div class="col-lg-12">
                <div class="ibox ">
                    <div class="ibox-content">

                        <div class="row">
                            <div class="col-12">
                                <a class="btn btn-success" href="{{ route('mantenimiento.metodo_entrega.tarifarios.index') }}"
                                    style="margin-right:8px;">
                                    <i class="fas fa-list-ol"></i> Tarifarios Envío
                                </a>
                            </div>
                            <div class="col-12">
                                <div class="table-responsive">
                                    @include('mantenimiento.metodos_entrega.tables.tbl_empresas_envio')
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection

@push('styles')
    <style>
        .search-length-container {
            display: flex;
            align-items: center;
            justify-content: space-between;
        }

        .buttons-container {
            display: flex;
            justify-content: end;
        }
    </style>
@endpush

@push('scripts')
    <script src="{{ mix('js/tomselect.js') }}"></script>
    <script>
        let sedes_data_table = null;
        let dtEmpresasEnvio = null;

        document.addEventListener('DOMContentLoaded', () => {
            sedes_data_table = dataTableSedes();
            loadDtEmpresasEnvio();
            events();
        })

        function events() {
            eventsSedes();
            eventsCreate();
            eventsUpdate();
        }

        function loadDtEmpresasEnvio() {
            dtEmpresasEnvio = new DataTable('.dataTables-metodos_entrega', {
                "bPaginate": true,
                "bLengthChange": true,
                "bFilter": true,
                "bInfo": true,
                "bAutoWidth": false,
                "serverSide": true,
                "processing": true,
                "ajax": "{{ route('mantenimiento.metodo_entrega.getTable') }}",
                "columns": [{
                        data: 'empresa',
                        className: "text-center"
                    },
                    {
                        data: 'tipo_envio',
                        className: "text-center"
                    },
                    {
                        data: 'fecha',
                        className: "text-center"
                    },
                    {
                        data: null,
                        className: "text-center",
                        searchable: false,
                        orderable: false,
                        render: function(data) {

                            var accionesHtml = `
                                <div class='btn-group'>
                                   <button type='button'
                                        class='btn btn-primary btn-sm dropdown-toggle'
                                        data-toggle='dropdown'
                                        aria-haspopup='true'
                                        aria-expanded='false'>

                                        <i class="fas fa-cog text-white"></i>

                                    </button>
                                    <div class='dropdown-menu'>

                                        <a class='dropdown-item modificarDetalle' onclick='editarMetodoEntrega(${data.id})' href='#' title='Modificar'>
                                            <i class='fa fa-edit'></i> Modificar
                                        </a>
                                        <div class='dropdown-divider'></div>
                                        ${["AGENCIA", "RECOJO EN TIENDA", "RECOJO EN ALMACEN"].includes(data.tipo_envio) ? `
                                                                        <a class='dropdown-item' href='#' onclick='sedes(${data.id})' title='Sedes'>
                                                                            <i class='fa fa-building'></i> Sedes
                                                                        </a>
                                                                        <div class='dropdown-divider'></div>
                                                                    ` : ""}
                                        <a class='dropdown-item' href='#' onclick='eliminar(${data.id})' title='Eliminar'>
                                            <i class='fa fa-trash'></i> Eliminar
                                        </a>
                                    </div>
                                </div>
                            `;

                            return accionesHtml;
                        }
                    }

                ],
                "language": {
                    "url": "{{ asset('Spanish.json') }}"
                },
                "order": [
                    [0, "desc"]
                ],
            });
        }

        async function sedes(id) {
            //===== OBTENEMOS LAS SEDES =====
            try {
                const res = await axios.get(route('mantenimiento.metodo_entrega.getSedes', id));
                console.log(res);
                if (res.data.success) {
                    pintarSedes(res.data.sedes);
                    document.querySelector('#agencia_id').value = id;
                    $('#modal_sedes').modal('show');
                } else {
                    toastr.error(`${res.data.message} - ${res.data.exception}`, 'ERROR AL OBTENER LAS SEDES');
                }
            } catch (error) {

            }
        }

        async function editarMetodoEntrega(id) {
            try {
                const res = await axios.get(route('mantenimiento.metodo_entrega.getMetodoEntrega', id));
                console.log(res);
                if (res.data.success) {
                    document.querySelector('#empresa_envio_id').value = id;
                    setFormEdit(res.data.metodo_entrega);
                    $('#modal_edit_metodo_entrega').modal('show');
                }
            } catch (error) {

            }
        }

        function setFormEdit(metodo_entrega) {
            document.querySelector('#empresa_edit').value = metodo_entrega.empresa;

            // Buscar la opción cuyo texto coincida con el tipo_envio guardado
            const select = document.querySelector('#tipo_envio_edit');
            const option = Array.from(select.options).find(o => o.text === metodo_entrega.tipo_envio);
            if (option && window.tsTipoEnvioEdit) {
                window.tsTipoEnvioEdit.setValue(option.value);
            }
        }

        function eliminar(id) {
            Swal.fire({
                title: 'Opción Eliminar',
                text: "¿Seguro que desea guardar cambios?",
                icon: 'question',
                showCancelButton: true,
                confirmButtonColor: "#1ab394",
                confirmButtonText: 'Si, Confirmar',
                cancelButtonText: "No, Cancelar",
            }).then((result) => {
                if (result.isConfirmed) {
                    //Ruta Eliminar
                    var url_eliminar = '{{ route('mantenimiento.metodo_entrega.destroy', ':id') }}';
                    url_eliminar = url_eliminar.replace(':id', id);
                    $(location).attr('href', url_eliminar);

                } else if (
                    /* Read more about handling dismissals below */
                    result.dismiss === Swal.DismissReason.cancel
                ) {
                    Swal.fire(
                        'Cancelado',
                        'La Solicitud se ha cancelado.',
                        'error'
                    )
                }
            })
        }
    </script>
@endpush
