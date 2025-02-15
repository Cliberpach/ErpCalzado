<form action="" id="formAddNumeracion">
    <div class="row">
        <div class="col-12 mb-3">
            <label for="comprobante_id" class="required" style="font-weight: bold;">TIPO COMPROBANTE</label>
            <select onchange="cambiarTipoComprobante(this)" data-placeholder="SELECCIONAR" required class="form-control select2_form" name="comprobante_id" id="comprobante_id">
                <option value="">SELECCIONAR</option>
                @foreach ($tipos_comprobantes as $tipo_comprobante)
                   <option value="{{$tipo_comprobante->id}}">{{$tipo_comprobante->descripcion}}</option> 
                @endforeach
            </select>
            <span style="font-weight: bold;color:red;" class="comprobante_id_error msgError"></span> 
        </div>
        <div class="col-lg-6 col-md-12 col-sm-12 col-xs-12">
            <label class="required" for="parametro" style="font-weight:bold;">PARÁMETRO</label>
            <div class="input-group mb-3">
                <div class="input-group-prepend">
                  <span class="input-group-text" id="basic-addon1">
                    <i class="fas fa-file-invoice"></i>
                  </span>
                </div>
                <input required id="parametro" name="parametro" readonly type="text" class="form-control" placeholder="Parámetro" aria-label="Username" aria-describedby="basic-addon1">
            </div>
            <span style="font-weight: bold;color:red;" class="parametro_error msgError"></span> 
        </div>
        <div class="col-lg-6 col-md-12 col-sm-12 col-xs-12 mb-3">
            <label class="required" for="serie" style="font-weight:bold;">CÓDIGO SERIE</label>
            <div class="input-group">
                <div class="input-group-prepend">
                  <span class="input-group-text" id="basic-addon1">
                    <i class="fas fa-list-ol"></i>
                  </span>
                </div>
                <input required value="" id="serie" name="serie"  type="text" class="form-control" placeholder="Serie" aria-label="Username" aria-describedby="basic-addon1">
            </div>
            <p id="pCodigoSerie"  class="m-0" style="color:blue;font-weight:bold;"></p>
            <span style="font-weight: bold;color:red;" class="serie_error msgError"></span> 
        </div>
        <div class="col-lg-6 col-md-12 col-sm-12 col-xs-12">
            <label class="required" for="nro_inicio" style="font-weight:bold;">N° INICIO</label>
            <div class="input-group mb-3">
                <div class="input-group-prepend">
                  <span class="input-group-text" id="basic-addon1">
                    <i class="fas fa-list-ol"></i>
                  </span>
                </div>
                <input value="1" required id="nro_inicio" name="nro_inicio" type="text" class="form-control inputEnteroPositivo" placeholder="N° inicio" aria-label="Username" aria-describedby="basic-addon1">
            </div>
            <span style="font-weight: bold;color:red;" class="nro_inicio_error msgError"></span> 
        </div>
    </div>
</form>