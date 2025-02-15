<div class="modal inmodal" id="modal_crear_caja" tabindex="-1" role="dialog" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content animated bounceInRight">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal">
                    <span aria-hidden="true">&times;</span>
                    <span class="sr-only">Close</span>
                </button>
                <i class="fa fa-cogs modal-icon"></i>
                <h4 class="modal-title">Caja</h4>
                <small class="font-bold">Apertura de Caja</small>
            </div>
            <div class="modal-body">

                @include('pos.MovimientoCaja.forms.form_abrir_caja')
                
            </div>
            <div class="modal-footer">
                <div class="col-md-6 text-left" style="color:#fcbc6c">
                    <i class="fa fa-exclamation-circle"></i> <small>Los campos marcados con asterisco (<label
                            class="required"></label>) son obligatorios.</small>
                </div>
                <div class="col-md-6 text-right">
                    <button type="submit" class="btn btn-primary btn-sm" id="btnEnviarAperturaCaja" form="formAbrirCaja"  ><i
                            class="fa fa-save" ></i> Guardar</button>
                    <button type="button" class="btn btn-danger btn-sm" data-dismiss="modal"><i
                            class="fa fa-times"></i> Cancelar</button>
                </div>
            </div>
            
        </div>
    </div>
</div>
@push('styles')
    <link href="{{ asset('Inspinia/css/plugins/select2/select2.min.css') }}" rel="stylesheet">
    <style>
        .swal2-container {
            z-index: 9999 !important;
        }
    </style>
@endpush
@push('scripts')

    <!-- Select2 -->
    <script src="{{ asset('Inspinia/js/plugins/select2/select2.full.min.js') }}"></script>
    <script>

        function eventsMdlAbrirCaja(){
            document.querySelector('#formAbrirCaja').addEventListener('submit',(e)=>{
                e.preventDefault();
                abrirCaja(e.target);
            })

            $('#modal_crear_caja').on('hidden.bs.modal', function (e) {
                limpiarFormAbrirCaja();
            });

        }   

      

        async function openMdlAbrirCaja(){
            await getDatosAperturaCaja();
        }

        function openCaja(btnOpenCaja){
            btnOpenCaja.disabled = true;
            document.querySelector('#crear_caja_movimiento').submit();
            btnOpenCaja.innerHTML   =   `<i class="fa fa-save fa-spin" ></i> Guardando`;
        }

        function abrirCaja(formAperturarCaja){
            const swalWithBootstrapButtons = Swal.mixin({
            customClass: {
                confirmButton: "btn btn-success",
                cancelButton: "btn btn-danger"
            },
            buttonsStyling: false
            });
            swalWithBootstrapButtons.fire({
            title: "Desea aperturar la caja?",
            text: "Operación no reversible!",
            icon: "warning",
            showCancelButton: true,
            confirmButtonText: "Sí!",
            cancelButtonText: "No, cancelar!",
            reverseButtons: true
            }).then(async (result) => {
            if (result.isConfirmed) {
               
                toastr.clear();
                limpiarErroresValidacion('msgError');

                Swal.fire({
                    title: "Abriendo caja...",
                    text: "Por favor, espera",
                    allowOutsideClick: false,
                    didOpen: () => {
                        Swal.showLoading();
                    }
                });


                try {
                    const formData  =   new FormData(formAperturarCaja);
                    formData.append('sede_id',@json($sede_id));
                    const res       =   await axios.post(route('Caja.apertura'),formData);
                    if(res.data.success){
                        dtMovimientoCajas.ajax.reload();
                        $('#modal_crear_caja').modal('hide');
                        toastr.success(res.data.message,'OPERACIÓN COMPLETADA');
                    }else{
                        toastr.error(res.data.message,'ERROR EN EL SERVIDOR');
                    }
                } catch (error) {
                    
                    if (error.response) {
                        if (error.response.status === 422) {
                            const errors = error.response.data.errors;
                            pintarErroresValidacion(errors, 'error');
                            toastr.error('Errores de validación encontrados.', 'ERROR DE VALIDACIÓN');
                        } else {
                            toastr.error(error.response.data.message, 'ERROR EN EL SERVIDOR');
                        }
                    } else if (error.request) {
                        toastr.error('No se pudo contactar al servidor. Revisa tu conexión a internet.', 'ERROR DE CONEXIÓN');
                    } else {
                        toastr.error(error.message, 'ERROR DESCONOCIDO');
                    }       
                }finally{
                    Swal.close();
                }

            } else if (result.dismiss === Swal.DismissReason.cancel) {
                swalWithBootstrapButtons.fire({
                title: "Operación cancelada",
                text: "No se realizaron acciones",
                icon: "error"
                });
            }
            });
        }

      

        async function getDatosAperturaCaja(){
            try {
                mostrarAnimacion();
                const res   =   await axios.get(route('Caja.getDatosAperturaCaja',{sede_id:@json($sede_id)}));
                
                if(res.data.success){
                    setDatosMdlAperturaCaja(res.data);
                    $('#modal_crear_caja').modal('show');
                }else{
                    toastr.error(res.data.messaage,'ERROR EN EL SERVIDOR');
                }

            } catch (error) {
                toastr.error(error,'ERROR EN LA PETICIÓN OBTENER DATOS DE APERTURA');
            }finally{
                ocultarAnimacion();
            }
        }

        function setDatosMdlAperturaCaja(datos){
            const cajas_desocupadas     =   datos.cajas_desocupadas;
            const cajeros_desocupados   =   datos.cajeros_desocupados;

            $('#caja').empty().append('<option value="" disabled selected>Seleccionar</option>');  
            cajas_desocupadas.forEach((cd)=>{
                $('#caja').append(new Option(cd.nombre, cd.id)); 
            })

            $('#cajero_id').empty().append('<option value="" disabled selected>Seleccionar</option>');  
            cajeros_desocupados.forEach((cd)=>{
                $('#cajero_id').append(new Option(cd.nombre, cd.id)); 
            })

        }

        function limpiarFormAbrirCaja(){

            $('#caja').val(null).trigger('change'); 
            $('#caja').empty().trigger('change');  

            $('#cajero_id').val(null).trigger('change'); 
            $('#cajero_id').empty().trigger('change');  

            $('#turno').val(null).trigger('change'); 

            document.querySelector('#saldo_inicial').value  =   0;

        }

    </script>
@endpush
