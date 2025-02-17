<form action="" method="post">

    <div class="row mb-3">
        <div class="col-12">
            <i class="fas fa-file-invoice"></i> <label for="" style="font-weight: bold;">DATOS DEL COMPROBANTE</label>
            <hr style="margin:0px 0 10px 0;">
        </div>
        
        <div class="col-lg-4 col-md-6 col-sm-6 col-xs-12 mb-3">
            <label class="required" for="fecha_emision" style="font-weight: bold;">FECHA EMISIÓN</label>
            <?php
                $hoy = date('Y-m-d');
            ?>
            <input id="fecha_emision" name="fecha_emision" type="date" class="form-control" min="<?= $hoy ?>" value="<?= $hoy ?>">        
        </div>
        <div class="col-lg-12 col-md-12 col-sm-12 col-xs-12">
            <label class="required" for="cliente" style="font-weight: bold;">CLIENTE</label>
            <select name="cliente" id="cliente" class="select2_form">
                @foreach ($clientes as $cliente)
                    <option value="{{$cliente->id}}">{{$cliente->tipo_documento.':'.$cliente->documento.'-'.$cliente->nombre}}</option>
                @endforeach
            </select>
        </div>
    </div>

    <div class="row mb-3">
        <div class="col-12">
            <i class="fas fa-paper-plane"></i> <label for="" style="font-weight: bold;">DATOS ENVÍO</label>
            <hr style="margin:0px 0 10px 0;">
        </div>
        <div class="col-lg-4 col-md-4 col-sm-6 col-xs-12">
            <label class="required" for="motivo_traslado" style="font-weight: bold;">MOTIVO TRASLADO</label>
            <select name="motivo_traslado" id="motivo_traslado" class="select2_form">
               <option value="01">VENTA</option>
               <option value="02">COMPRA</option>
               <option value="04">TRASLADO ENTRE ESTABLECIMIENTOS DE LA MISMA EMPRESA</option>
               <option value="08">IMPORTACIÓN</option>
               <option value="09">EXPORTACIÓN</option>
               <option value="13">OTROS</option>
               <option value="14">VENTA SUJETA A CONFIRMACIÓN DEL COMPRADOR</option>
               <option value="18">TRASLADO EMISOR ITINERANTE CP</option>
               <option value="19">TRASLADO A ZONA PRIMARIA</option>
            </select>
        </div>

        <div class="col-lg-4 col-md-4 col-sm-6 col-xs-12">
            <label class="required" for="modalidad_traslado" style="font-weight: bold;">MODALIDAD DE TRASLADO</label>
            <select name="modalidad_traslado" id="modalidad_traslado" class="select2_form">
               <option value="01">TRANSPORTE PÚBLICO</option>
               <option value="02">TRANSPORTE PRIVADO</option>
            </select>
        </div>

        <div class="col-lg-4 col-md-6 col-sm-6 col-xs-12 mb-3">
            <label class="required" for="fecha_traslado" style="font-weight: bold;">FECHA TRASLADO</label>
            <?php
                $hoy = date('Y-m-d');
                $max_fecha_traslado = date('Y-m-d', strtotime('+5 days'));
            ?>
                <input id="fecha_traslado" name="fecha_traslado" type="date" class="form-control" 
                min="<?= $hoy ?>" max="<?= $max_fecha_traslado ?>" value="<?= $hoy ?>">
        </div>
        <div class="col-lg-4 col-md-4 col-sm-6 col-xs-12">
            <label for="peso" style="font-weight:bold;" class="required">PESO</label>
            <div class="input-group">
                <div class="input-group-prepend">
                    <span class="input-group-text" id="basic-addon1">
                        <i class="fas fa-weight-hanging"></i>
                    </span>
                </div>
                <input value="0.1" readonly name="peso" id="peso" type="text" class="form-control" placeholder="Peso" aria-label="Username" aria-describedby="basic-addon1">
            </div>
        </div>
        <div class="col-lg-4 col-md-4 col-sm-6 col-xs-12 mb-3">
            <label class="required" for="unidad" style="font-weight: bold;">UNIDAD</label>
            <select name="unidad" id="unidad" class="select2_form">
               <option value="KGM">KILOGRAMOS</option>
               <option value="TNE">TONELADAS</option>
            </select>
        </div>
        <div class="col-12 mb-3">
            <div class="form-group form-check">
                <input id="tipo_vehiculo" name="tipo_vehiculo" type="checkbox" class="form-check-input" id="exampleCheck1">
                <label class="form-check-label" for="tipo_vehiculo">VEHÍCULOS CATEGORÍA M1 O L</label>
            </div>
        </div>
    </div>

    @include('ventas.guias.subforms.sub_transporte_publico')
    @include('ventas.guias.subforms.sub_ubigeo')



</form>