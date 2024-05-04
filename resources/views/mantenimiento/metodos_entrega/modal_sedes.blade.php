<div class="modal inmodal" id="modal_sedes" role="dialog" aria-hidden="true">

    <div class="modal-dialog modal-lg">
        <div class="modal-content animated bounceInRight">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" @click.prevent="Cerrar">
                    <span aria-hidden="true">&times;</span>
                    <span class="sr-only">Close</span>
                </button>
                <i class="fa-solid fa-building modal-icon"></i>                
                <h4 class="modal-title">SEDES</h4>
                <small class="font-bold">Registrar</small>
            </div>
            <div class="modal-body content_cliente">
                @include('components.overlay_search')
                @include('components.overlay_save')
               
                <ul class="nav nav-tabs" id="myTab" role="tablist">
                    <li class="nav-item">
                        <a class="nav-link active" id="tab1-tab" data-toggle="tab" href="#tab1" role="tab" aria-controls="tab1" aria-selected="true">LISTADO DE SEDES</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" id="tab2-tab" data-toggle="tab" href="#tab2" role="tab" aria-controls="tab2" aria-selected="false">REGISTRAR SEDE</a>
                    </li>
                </ul>
                <div class="tab-content" id="myTabContent">
                    <div class="tab-pane fade show active" id="tab1" role="tabpanel" aria-labelledby="tab1-tab">
                        @include('mantenimiento.metodos_entrega.table-sedes')
                    </div>
                    <div class="tab-pane fade" id="tab2" role="tabpanel" aria-labelledby="tab2-tab">
                        <form id="formSede" action="" method="post">
                            <input name="agencia" type="text" hidden id="agencia_id">
                            <div class="row">
                                <div class="col-lg-6 col-md-6 mt-3">
                                    <label for="direccion" style="font-weight: bold;">
                                        DIRECCIÓN EMPRESA<span style="color: rgb(227, 160, 36);font-weight: bold;">*</span>
                                    </label>
                                    <input required id="direccion" name="direccion" class="form-control" placeholder="INGRESE UNA DIRECCIÓN"></input>
                                </div>
                                <div class="form-group col-lg-6 col-xs-12 mt-3">
                                    <label class="required" style="font-weight: bold;">Departamento</label>
                                    <select required id="departamento" name="departamento" class="select2_form form-control {{ $errors->has('departamento') ? ' is-invalid' : '' }}" style="width: 100%">
                                        <option></option>
                                        @foreach(departamentos() as $departamento)
                                            <option value="{{ $departamento->id }}" {{ (old('departamento') == $departamento->id ? "selected" : "") }} >{{ $departamento->nombre }}</option>
                                        @endforeach
                                    </select>
                                </div>
                                <div class="form-group col-lg-6 col-xs-12 mt-3">
                                    <label class="required" style="font-weight: bold;">Provincia</label>
                                    <select required id="provincia" name="provincia" class="select2_form form-control {{ $errors->has('provincia') ? ' is-invalid' : '' }}" style="width: 100%">
                                        <option></option>
                                    </select>
                                </div>
                                <div class="form-group col-lg-6 col-xs-12 mt-3">
                                    <label class="required" style="font-weight: bold;">Distrito</label>
                                    <select required id="distrito" name="distrito" class="select2_form form-control {{ $errors->has('distrito') ? ' is-invalid' : '' }}" style="width: 100%">
                                        <option></option>
                                    </select>
                                </div>
                            </div>
                            <div class="row">
                                <div class="col-12 d-flex justify-content-end">
                                    <button type="submit" class="btn btn-primary btn-sm" style="color:white;"><i
                                        class="fa fa-save"></i> Guardar</button>
                                </div>
                            </div>
                        </form>
                    </div>
                </div>       
            </div>

            <div class="modal-footer">
                <div class="col-md-6 text-left">
                    <i class="fa fa-exclamation-circle leyenda-required"></i> <small class="leyenda-required">Los
                        campos
                        marcados con asterisco (*) son obligatorios.</small>
                </div>
                <div class="col-md-6 text-right">
                    <button type="button" class="btn btn-danger btn-sm" data-dismiss="modal" @click.prevent="Cerrar"><i
                            class="fa fa-times"></i> Cancelar</button>
                </div>
            </div>
        </div>

    </div>
</div>


<script>
    
        function eventsSedes(){  
            $("#departamento").on("change", function (e) {
                var departamento_id = this.value;
                if (departamento_id !== "" || departamento_id.length > 0) {
                    $.ajax({
                        type: 'post',
                        dataType: 'json',
                        data: {
                            _token: $('input[name=_token]').val(),
                            departamento_id: departamento_id
                        },
                        url: "{{ route('mantenimiento.ubigeo.provincias') }}",
                        success: function (data) {
                            // Limpiamos data
                            $("#provincia").empty();
                            $("#distrito").empty();

                            if (!data.error) {
                                // Mostramos la información
                                if (data.provincias != null) {
                                    $("#provincia").select2({
                                        data: data.provincias
                                    }).val($('#provincia').find(':selected').val()).trigger('change');
                                }
                            } else {
                                toastr.error(data.message, 'Mensaje de Error', {
                                    "closeButton": true,
                                    positionClass: 'toast-bottom-right'
                                });
                            }
                        }
                    });
                }
            });

            $('#provincia').on("change", function (e) {
                var provincia_id = this.value;
                if (provincia_id !== "" || provincia_id.length > 0) {
                    $.ajax({
                        type: 'post',
                        dataType: 'json',
                        data: {
                            _token: $('input[name=_token]').val(),
                            provincia_id: provincia_id
                        },
                        url: "{{ route('mantenimiento.ubigeo.distritos') }}",
                        success: function (data) {
                            // Limpiamos data
                            $("#distrito").empty();

                            if (!data.error) {
                                // Mostramos la información
                                if (data.distritos != null) {
                                    $("#distrito").select2({
                                        data: data.distritos
                                    });
                                }
                            } else {
                                toastr.error(data.message, 'Mensaje de Error', {
                                    "closeButton": true,
                                    positionClass: 'toast-bottom-right'
                                });
                            }
                        }
                    });
                }
            });

            document.querySelector('#formSede').addEventListener('submit',async (e)=>{
                e.preventDefault();
                try {
                    const formData  =   new FormData(e.target);
                    const res       =   await   axios.post(route('mantenimiento.metodo_entrega.createSede'),formData);
                    console.log(res);
                    if(res.data.success){
                        //======= LIMPIANDO FORMULARIO =====
                        //e.target.reset();
                        //======== DIBUJANDO LA NUEVA SEDE =======
                        const nueva_sede    =   res.data.nueva_sede;
                        sedes_data_table.row.add([
                            nueva_sede.id,
                            `${nueva_sede.direccion} - ${nueva_sede.departamento}-${nueva_sede.provincia}-${nueva_sede.distrito}`,
                            'ACCIONES'
                        ]).draw();
                        //====== REDIRIGIENDO A LISTADO DE SEDES =====
                        document.querySelector('#tab1-tab').click();
                        //====== MOSTRANDO ALERTA =======
                        toastr.success(res.data.message,'OPERACIÓN COMPLETADA');
                    }else{
                        toastr.error(`${res.data.message} - ${res.data.exception}`,'ERROR');
                    }
                } catch (error) {
                    
                }
            })

        }

        function dataTableSedes(){
            return new DataTable('#table-sedes',{
                "order": [
                            [0, 'desc']
                ],
                buttons: [
                        {
                            extend: 'excelHtml5',
                            className: 'custom-button btn-check', 
                            text: '<i class="fa fa-file-excel-o" style="font-size:15px;"></i> Excel',
                            title: 'DETALLES DEL PEDIDO',
                        },
                        {
                            extend: 'print',
                            className: 'custom-button btn-check', 
                            text: '<i class="fa fa-print"></i> Imprimir',
                            title: 'DETALLES DEL PEDIDO'
                        },
                    ], 
                dom: '<"buttons-container"B><"search-length-container"lf>t',
                bProcessing: true,
                language: {
                        processing:     "Procesando datos...",
                        search:         "BUSCAR: ",
                        lengthMenu:    "MOSTRAR _MENU_ ITEMS",
                        info:           "MOSTRANDO _START_ A _END_ DE _TOTAL_ ITEMS",
                        infoEmpty:      "MOSTRANDO 0 ITEMS",
                        infoFiltered:   "(FILTRADO de _MAX_ ITEMS)",
                        infoPostFix:    "",
                        loadingRecords: "CARGA EN CURSO",
                        zeroRecords:    "Aucun &eacute;l&eacute;ment &agrave; afficher",
                        emptyTable:     "NO HAY ITEMS DISPONIBLES",
                        paginate: {
                            first:      "PRIMERO",
                            previous:   "ANTERIOR",
                            next:       "SIGUIENTE",
                            last:       "ÚLTIMO"
                        },
                        aria: {
                            sortAscending:  ": activer pour trier la colonne par ordre croissant",
                            sortDescending: ": activer pour trier la colonne par ordre décroissant"
                        }
                }
            });
        }
    
        function pintarSedes(sedes){
            sedes_data_table.clear().draw();
                
            sedes.forEach((s)=>{
                sedes_data_table.row.add([
                    s.id,
                    `${s.direccion} - ${s.departamento}-${s.provincia}-${s.distrito}`,
                    'ACCIONES'
                ]).draw();
            })
        }


        
</script> 

