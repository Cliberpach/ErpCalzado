<div class="modal inmodal" id="modal_editar_detalle" tabindex="-1" role="dialog" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content animated bounceInRight">
            <div class="modal-header">
                <button type="button"  class="close" data-dismiss="modal">
                    <span aria-hidden="true">&times;</span>
                    <span class="sr-only">Close</span>
                </button>
                <i class="fa fa-cogs modal-icon"></i>
                <h4 class="modal-title">Detalle de la nota de ingreo</h4>
                <small class="font-bold">Editar detalle</small>
            </div>
            <div class="modal-body">
                <form id="frm_editar_detalle">
                    <input type="hidden" id="indice" name="indice">
                    <div class="row">
                        <div class="col-12">
                            <div class="form-group">
                                <label class="">Productos</label>
                                <select name="producto" id="producto" class="form-control select2_form" disabled>
                                    <option value=""></option>
                                    @foreach ($productos as $producto)
                                        <option  value="{{$producto->id}}" id="{{$producto->id}}">{{$producto->nombre}}</option>
                                    @endforeach
                                </select>
                                <input type="hidden" id="producto_anterior">
                            </div>
                        </div>
                        <div class="col-lg-6 col-xs-12">
                            <div class="form-group">
                                <label class="required">Cantidad</label>
                                <input type="text" id="cantidad" name="cantidad" class="form-control" onkeypress="return isNumber(event)"  required>
                            </div>
                        </div>
                        <div class="col-lg-6 col-xs-12">
                            <div class="form-group">
                                <label class="required">Costo U</label>
                                <input type="text" id="costo" name="costo" class="form-control" onkeypress="return filterFloat(event, this, true);"  required>
                            </div>
                        </div>
                    </div>
                    <div class="form-group row d-none">
                        <div class="col-lg-6 col-xs-12">
                            <label class="">Lote</label>
                            <input type="text" name="lote" id="lote" class="form-control">
                            <input type="hidden" name="detalle_id" id="detalle_id" class="form-control">
                        </div>
                    </div>
                    <input type="hidden" name="pro_id" id="prod_id">
                    <div class="form-group row d-none">
                        <div class="col-lg-6 col-xs-12">
                            <label class="col-form-label">Fecha Vencimiento</label>
                            <div class="input-group date">
                                <span class="input-group-addon">
                                    <i class="fa fa-calendar"></i>
                                </span>
                                <input type="date" id="fechavencimiento" name="fechavencimiento"
                                    class="form-control"
                                    required>
                            </div>
                        </div>
                    </div>
                </form>
            </div>
            <div class="modal-footer">
                <div class="col-md-6 text-left">
                    <i class="fa fa-exclamation-circle leyenda-required"></i> <small class="leyenda-required">Los campos marcados con asterisco (<label class="required"></label>) son obligatorios.</small>
                </div>
                <div class="col-md-6 text-right">
                    <button type="submit" id="btn_editar" class="btn btn-primary btn-sm" form="frm_editar_detalle"><i class="fa fa-save"></i> Guardar</button>
                    <button type="button"  class="btn btn-danger btn-sm" data-dismiss="modal"><i class="fa fa-times"></i> Cancelar</button>
                </div>
            </div>
        </div>
    </div>
</div>
@push('scripts')
<script>
    function get_producto_detalle(e)
    {

        var lote_id=$("#modal_editar_detalle #lote").val();
        $.ajax({
              dataType : 'json',
              type : 'POST',
              async:false,
              url : '{{ route('almacenes.nota_ingreso.productos') }}',
              data : {
                  '_token' : $('input[name=_token]').val(),
                  'lote_id' : lote_id,
              }
          }).done(function (result){
                var html="";
             if(result.length==0)
             {
                html="<option value=''>No hay datos</option>";
             }
             else{

              for(var i=0;i<result.length;i++)
              {
                  html=html+"<option value='"+result[i].producto_id+"'>"+result[i].descripcion_producto+"</option>";
              }

             }

             var id=$("#prod_id").val();
              $("#modal_editar_detalle #producto").html(html);
              $("#modal_editar_detalle #producto").val(id).trigger('change');
        });
    }
//Validacion al ingresar tablas
$('#frm_editar_detalle').submit(function(e) {
    e.preventDefault();
    var enviar = true;
    if ($('#modal_editar_detalle #producto').val() == '') {
        toastr.error('Seleccione Producto.', 'Error');
        enviar = false;
    } else {
        if($('#modal_editar_detalle #producto').val() != $('#modal_editar_detalle #prod_id').val())
        {
            var existe = buscarproducto($('#modal_editar_detalle #producto').val())
            if (existe == true) {
                toastr.error('Producto con el mismo lote ya se encuentra ingresado.', 'Error');
                enviar = false;
            }
        }
    }

    if ($('#modal_editar_detalle #cantidad').val() == '' || $('#modal_editar_detalle #costo').val() == '' || $('#modal_editar_detalle #lote').val() ===null  || $('#modal_editar_detalle #fechavencimiento').val() == '') {
        toastr.error('Ingrese los valores.', 'Error');
        enviar = false;
    }
    if (enviar) {
        const swalWithBootstrapButtons = Swal.mixin({
            customClass: {
                confirmButton: 'btn btn-success',
                cancelButton: 'btn btn-danger',
                container: 'my-swal',
            },
            buttonsStyling: false
        })
        Swal.fire({
            customClass: {
                container: 'my-swal'
            },
            title: 'Opción Modificar',
            text: "¿Seguro que desea Modificar Dispositivo?",
            icon: 'question',
            showCancelButton: true,
            confirmButtonColor: "#1ab394",
            confirmButtonText: 'Si, Confirmar',
            cancelButtonText: "No, Cancelar",
        }).then((result) => {
            if (result.isConfirmed) {
                actualizarTabla($('#modal_editar_detalle #indice').val());
            } else if (
                //Read more about handling dismissals below
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
})
function actualizarTabla(i) {
    var table = $('.dataTables-ingreso').DataTable();
    table.row(i).remove().draw();
    var detalle = {
        cantidad:   convertFloat($('#modal_editar_detalle #cantidad').val()).toFixed(2),
        lote: $('#modal_editar_detalle #lote').val(),
        producto: $( "#modal_editar_detalle #producto option:selected" ).text(),
        fechavencimiento: $('#modal_editar_detalle #fechavencimiento').val(),
        producto_id:$( "#modal_editar_detalle #producto" ).val(),
        costo:convertFloat($( "#modal_editar_detalle #costo" ).val()).toFixed(4),
        id:$( "#modal_editar_detalle #detalle_id" ).val()
    }
    agregarTabla(detalle);
    $('#modal_editar_detalle').modal('hide');
}
</script>
@endpush
