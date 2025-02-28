<form action="" method="post" id="formRegistrarGuia">

    <div class="row mb-3">
        <div class="col-12">
            <i class="fas fa-file-invoice"></i> <label for="" style="font-weight: bold;">DATOS DEL COMPROBANTE</label>
            <hr style="margin:0px 0 10px 0;">
        </div>

        <div class="col-lg-3 col-md-3 col-sm-6 col-xs-12">
            <label for="traslado" style="font-weight:bold;" class="required">TRASLADO</label>
            <div class="input-group mb-3">
                <div class="input-group-prepend">
                  <span class="input-group-text" id="basic-addon1">
                    <i class="fas fa-truck-loading"></i>
                </span>
                </div>
                <input value="{{'TR-'.$traslado->id}}" readonly name="traslado" id="traslado" type="text" class="form-control" placeholder="Registrador" aria-label="Username" aria-describedby="basic-addon1">
            </div>
        </div>

        <div class="col-lg-3 col-md-4 col-sm-12 col-xs-12">
            <div class="form-group">
                <label style="font-weight: bold;" class="required" for="condicion_id">ALMACÉN</label>
                <select disabled required onchange="cambiarAlmacen(this.value)"  id="almacen" name="almacen" class="select2_form form-control" required>
                    <option></option>
                    @foreach ($almacenes as $almacen)
                        <option
                        @if ($almacen_traslado->id == $almacen->id)
                            selected
                        @endif
                        value="{{ $almacen->id }}">
                            {{ $almacen->descripcion }}
                        </option>
                    @endforeach
                </select>
            </div>
            <span style="font-weight: bold;color:red;" class="almacen_error msgError"></span>
        </div>

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

        {{-- <div class="col-lg-3 col-md-4 col-sm-12 col-xs-12">
            <div class="form-group">
                <label style="font-weight: bold;" class="required" for="condicion_id">ALMACÉN</label>
                <select required onchange="cambiarAlmacen(this.value)"  id="almacen" name="almacen" class="select2_form form-control" required>
                    <option></option>
                    @foreach ($almacenes as $almacen)
                        <option
                        @if ($almacen->sede_id == $sede_id)
                            selected
                        @endif
                        value="{{ $almacen->id }}">
                            {{ $almacen->descripcion }}
                        </option>
                    @endforeach
                </select>
            </div>
            <span style="font-weight: bold;color:red;" class="almacen_error msgError"></span>
        </div> --}}
    
        
        <div class="col-lg-4 col-md-6 col-sm-6 col-xs-12 mb-3">
            <label class="required" for="fecha_emision" style="font-weight: bold;">FECHA EMISIÓN</label>
            <?php
                $hoy = date('Y-m-d');
            ?>
            <input required id="fecha_emision" name="fecha_emision" type="date" class="form-control" min="<?= $hoy ?>" value="<?= $hoy ?>">        
            <span style="font-weight: bold;color:red;" class="fecha_emision_error msgError"></span>
        </div>
    </div>

    <div class="row mb-3">
        <div class="col-12">
            <div class="p-2 bg-success text-white rounded shadow-sm">
                <i class="fas fa-paper-plane"></i> <label for="" style="font-weight: bold;">DATOS ENVÍO</label>
            </div>
            <hr class="mt-2 mb-3">
        </div>

        <div class="col-lg-4 col-md-4 col-sm-6 col-xs-12">
            <label class="required" for="motivo_traslado" style="font-weight: bold;">MOTIVO TRASLADO</label>
            
            <select disabled onchange="cambiarMotivoTraslado(this.value)" required name="motivo_traslado" id="motivo_traslado" class="select2_form">
               
               @foreach ($motivos_traslado as $motivo_traslado)
                   <option 
                   @if ($motivo_traslado->simbolo === '04')
                       selected
                   @endif
                   value="{{$motivo_traslado->simbolo}}">{{$motivo_traslado->descripcion}}</option>
               @endforeach
                {{-- <option value="01">VENTA</option> --}}
               {{-- <option value="02">COMPRA</option> --}}
               {{-- <option selected value="04">TRASLADO ENTRE ESTABLECIMIENTOS DE LA MISMA EMPRESA</option> --}}
               {{-- <option value="08">IMPORTACIÓN</option>
               <option value="09">EXPORTACIÓN</option>
               <option value="13">OTROS</option>
               <option value="14">VENTA SUJETA A CONFIRMACIÓN DEL COMPRADOR</option>
               <option value="18">TRASLADO EMISOR ITINERANTE CP</option>
               <option value="19">TRASLADO A ZONA PRIMARIA</option> --}}
            </select>
            <span style="font-weight: bold;color:red;" class="motivo_traslado_error msgError"></span>
        </div>

        <div class="col-lg-4 col-md-4 col-sm-6 col-xs-12">
            <label class="required" for="modalidad_traslado" style="font-weight: bold;">MODALIDAD DE TRASLADO</label>
            <select required onchange="cambiarModalidadTraslado(this.value)" name="modalidad_traslado" id="modalidad_traslado" class="select2_form">
               <option value="01">TRANSPORTE PÚBLICO</option>
               <option value="02">TRANSPORTE PRIVADO</option>
            </select>
            <span style="font-weight: bold;color:red;" class="modalidad_traslado_error msgError"></span>
        </div>

        <div class="col-lg-4 col-md-6 col-sm-6 col-xs-12 mb-3">
            <label class="required" for="fecha_traslado" style="font-weight: bold;">FECHA TRASLADO</label>
            <?php
                $hoy = date('Y-m-d');
                $max_fecha_traslado = date('Y-m-d', strtotime('+5 days'));
            ?>
            <input required id="fecha_traslado" name="fecha_traslado" type="date" class="form-control" 
            min="<?= $hoy ?>" max="<?= $max_fecha_traslado ?>" value="<?= $hoy ?>">
            <span style="font-weight: bold;color:red;" class="fecha_traslado_error msgError"></span>
        </div>
        <div class="col-lg-4 col-md-4 col-sm-6 col-xs-12">
            <label for="peso" style="font-weight:bold;" class="required">PESO</label>
            <div class="input-group">
                <div class="input-group-prepend">
                    <span class="input-group-text" id="basic-addon1">
                        <i class="fas fa-weight-hanging"></i>
                    </span>
                </div>
                <input required value="0.1" readonly name="peso" id="peso" type="text" class="form-control" placeholder="Peso" aria-label="Username" aria-describedby="basic-addon1">
            </div>
            <span style="font-weight: bold;color:red;" class="peso_error msgError"></span>
        </div>
        <div class="col-lg-4 col-md-4 col-sm-6 col-xs-12 mb-3">
            <label class="required" for="unidad" style="font-weight: bold;">UNIDAD</label>
            <select required name="unidad" id="unidad" class="select2_form">
               <option value="KGM">KILOGRAMOS</option>
               <option value="TNE">TONELADAS</option>
            </select>
            <span style="font-weight: bold;color:red;" class="unidad_error msgError"></span>
        </div>

        <div class="col-12 mb-3" id="divCategoriaML">
            <div class="form-group form-check">
                <input id="tipo_vehiculo" name="categoria_M1L" type="checkbox" class="form-check-input chkTipoVehiculo" >
                <label class="form-check-label" for="tipo_vehiculo">VEHÍCULOS CATEGORÍA M1 O L</label>
            </div>
            <span style="font-weight: bold;color:red;" class="tipo_vehiculo_error msgError"></span>
        </div>

    </div>

    @include('almacenes.traslados.guia.subforms.sub_transporte')
    @include('almacenes.traslados.guia.subforms.sub_ubigeo')



</form>