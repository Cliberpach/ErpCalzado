<div class="modal inmodal" id="modal_mostrar_detalles" tabindex="-1" role="dialog" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content animated bounceInRight">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal">
                    <span aria-hidden="true">&times;</span>
                    <span class="sr-only">Close</span>
                </button>
                <i class="fa fa-cogs modal-icon"></i>
                <h4 class="modal-title">Detalles de Apertura</h4>
                <small class="font-bold">Detalles</small>
            </div>
            <div class="modal-body"> 
                <div class="table-responsive">
                    <h3 class="text-center"> Usuarios presentes </h3>
                    <table class="table table-sm table-striped table-bordered table-hover" style="text-transform:uppercase"
                        id="usuarios_venta">
                        <thead>
                            <tr>
    
                                <th class="text-center">
                                    Dejar Salir
                                </th>
                                <th class="text-center">USUARIO</th>
    
                                <th class="text-center">Fecha entrada</th>
    
    
                            </tr>
                        </thead>
                        <tbody>
                         
    
                        </tbody>
                
                    </table>
                </div>
            </div>
            </div>
           
            <div class="modal-footer">
                <div class="col-md-6 text-left" style="color:#fcbc6c">
                    <i class="fa fa-exclamation-circle"></i> <small>Los campos marcados con asterisco (<label
                            class="required"></label>) son obligatorios.</small>
                </div>
                <div class="col-md-6 text-right">
                    <button type="submit" class="btn btn-primary btn-sm" id="btnEnviarAperturaCaja"><i class="fa fa-save"></i> Guardar</button>
                    <button type="button" class="btn btn-danger btn-sm" data-dismiss="modal"><i
                            class="fa fa-times"></i> Cancelar</button>
                </div>
            </div>


            </form>
        </div>
    </div>
</div>
@push('styles')
    <link href="{{ asset('Inspinia/css/plugins/select2/select2.min.css') }}" rel="stylesheet">
@endpush
@push('scripts')
    <!-- Select2 -->
    <script src="{{ asset('Inspinia/js/plugins/select2/select2.full.min.js') }}"></script>
    <script>
        $btnEnviar=document.getElementById('btnActualizarDetalle');
        function verificarSeleccion(id) {
            let verificar = document.getElementById(`checkBox${id}`);
            if (verificar.checked) {
                // Se agregara el atributo name para que  se guarde ese dato
                document.getElementById(`idUsuario${id}`).setAttribute('name', 'usuarioVentas[]');

            } else {
                // Se quitara el atributo name para que no se guarde ese dato
                document.getElementById(`idUsuario${id}`).removeAttribute('name');
            }


        }
        //Select2
        $(".select2_form").select2({
            placeholder: "SELECCIONAR",
            allowClear: true,
            height: '200px',
            width: '100%',
        });
    </script>
@endpush
