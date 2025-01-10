<form action="{{route('almacenes.nota_ingreso.store')}}" method="POST" id="enviar_ingresos">
    {{csrf_field()}}
    <input type="hidden" name="generarAdhesivos" id="generarAdhesivos">
    <div class="col-sm-12">
        <h4 class=""><b>Nota de Ingreso</b></h4>
        <div class="row">
            <div class="col-md-12">
                <p>Registrar datos de la Nota de Ingreso :</p>
            </div>
        </div>
        <div class="form-group row">

            <input type="hidden" id="numero"  name="numero" class="form-control" value="{{$ngenerado}}" >
            <input type="hidden" id="sede_id"  name="sede_id" class="form-control" value="{{$sede_id}}" >


            <div class="col-12 col-lg-3 col-md-3"  id="fecha">
                <label>Fecha</label>
                <div class="input-group date">
                    <span class="input-group-addon">
                        <i class="fa fa-calendar"></i>
                    </span>
                    <input type="date" id="fecha" name="fecha"
                        class="form-control {{ $errors->has('fecha') ? ' is-invalid' : '' }}"
                        value="{{old('fecha',$fecha_hoy)}}"
                        autocomplete="off" readonly required>
                    @if ($errors->has('fecha'))
                    <span class="invalid-feedback" role="alert">
                        <strong>{{ $errors->first('fecha') }}</strong>
                    </span>
                    @endif
                </div>
            </div>
            <div class="col-12 col-md-3 d-none">

                <label class="required">Moneda</label>
                <select
                    class="select2_form form-control {{ $errors->has('moneda') ? ' is-invalid' : '' }}"
                    style="text-transform: uppercase; width:100%" value="{{old('moneda')}}"
                    name="moneda" id="moneda" required disabled>
                    {{-- onchange="cambioMoneda(this)" --}}
                        <option></option>
                    @foreach ($monedas as $moneda)
                    <option value="{{$moneda->descripcion}}" @if(old('moneda') == $moneda->descripcion || $moneda->descripcion == 'SOLES') {{'selected'}} @endif
                        >{{$moneda->simbolo.' - '.$moneda->descripcion}}</option>
                    @endforeach
                    @if ($errors->has('moneda'))
                    <span class="invalid-feedback" role="alert">
                        <strong>{{ $errors->first('moneda') }}</strong>
                    </span>
                    @endif

                </select>

                <input type="hidden" id="moneda" name="moneda" value="SOLES">

            </div>
            <div class="col-12 col-lg-3 col-md-3">
                <label class="required">Origen</label>
                <select name="origen" id="origen" class="select2_form form-control {{ $errors->has('origen') ? ' is-invalid' : '' }}" required>
                    <option value="">Seleccionar Origen</option>
                    @foreach ($origenes as  $tabla)
                        <option {{ old('origen') == $tabla->id ? 'selected' : '' }} value="{{$tabla->id}}">{{$tabla->descripcion}}</option>
                    @endforeach
                </select>
                @if ($errors->has('origen'))
                <span class="invalid-feedback" role="alert">
                    <strong>{{ $errors->first('origen') }}</strong>
                </span>
                @endif
            </div>
            <div class="col-12 col-lg-3 col-md-3">
                <label style="font-weight: bold;" class="required">Almacén Destino</label>
                <select onchange="cambiarAlmacenDestino();"  required name="almacen_destino" id="almacen_destino" class="select2_form form-control {{ $errors->has('almacen_destino') ? ' is-invalid' : '' }}">
                    <option value="">Seleccionar Destino</option>
                    @foreach ($almacenes_destino as $almacen_destino)
                        <option value="{{$almacen_destino->id}}">{{$almacen_destino->descripcion}}</option>
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
                <textarea class="form-control" name="observacion" id="observacion" cols="30" rows="3"></textarea>
            </div>
        </div>
    </div>
    <input type="hidden" id="notadetalle_tabla" name="notadetalle_tabla[]">
</form>