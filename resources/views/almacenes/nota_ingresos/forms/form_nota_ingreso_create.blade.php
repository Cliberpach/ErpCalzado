<form action="{{route('almacenes.nota_ingreso.store')}}" method="POST" id="enviar_ingresos">
    {{csrf_field()}}
    <input type="hidden" name="generarAdhesivos" id="generarAdhesivos">
    <div class="col-sm-12">
        <h4 class=""><b>Nota de Ingreso</b></h4>
        <div class="row">
            <div class="col-md-12">
                <p>Información general</p>
            </div>
        </div>
        <div class="form-group row">

            <div class="col-12 col-lg-3 col-md-3 mb-3">
                <label for="registrador" style="font-weight: bold;">REGISTRADOR</label>
                <div class="input-group">
                    <div class="input-group-prepend">
                      <span class="input-group-text" id="basic-addon1">
                        <i class="fas fa-user-shield"></i>
                      </span>
                    </div>
                    <input value="{{$registrador->usuario}}" readonly name="registrador" id="registrador" type="text" class="form-control" placeholder="Username" aria-label="Username" aria-describedby="basic-addon1">
                  </div>
            </div>

            <div class="col-12 col-lg-3 col-md-3 mb-3">
                <label for="fecha_registro" style="font-weight: bold;">FECHA REGISTRO</label>
                <div class="input-group">
                    <div class="input-group-prepend">
                      <span class="input-group-text" id="basic-addon1">
                        <i class="fas fa-calendar-alt"></i>
                      </span>
                    </div>
                    <input value="<?= date('Y-m-d'); ?>" readonly name="registrador" id="fecha_registro" type="date" class="form-control">
                </div>
            </div>

            <div class="col-12 col-lg-3 col-md-3">
                <label style="font-weight: bold;" class="required">Almacén Destino</label>
                <select onchange="cambiarAlmacenDestino();"  required name="almacen_destino" id="almacen_destino" class="select2_form form-control {{ $errors->has('almacen_destino') ? ' is-invalid' : '' }}">
                    <option value="">Seleccionar Destino</option>
                    @foreach ($almacenes_destino as $almacen_destino)
                        @if ($almacen_destino->tipo_almacen == 'PRINCIPAL')
                            <option value="{{$almacen_destino->id}}">{{$almacen_destino->descripcion}}</option>
                        @endif
                    @endforeach
                </select>
                @if ($errors->has('destino'))
                <span class="invalid-feedback" role="alert">
                    <strong>{{ $errors->first('almacen_destino') }}</strong>
                </span>
                @endif
            </div>
            <div class="col-12 col-lg-3 col-md-3">
                <label for="observacion">OBSERVACIÓN</label>
                <textarea maxlength="200" class="form-control" name="observacion" id="observacion" cols="30" rows="3"></textarea>
            </div>
        </div>
    </div>
</form>
