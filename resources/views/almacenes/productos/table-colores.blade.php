<div class="panel panel-primary">
    <div class="panel-heading">
        <h4><b>ASIGNAR COLORES</b></h4>
    </div>
    <div class="panel-body">
        <div class="row">

          <div class="col-12">
            <div class="row">
              
              <div class="col-lg-4 col-md-4 col-sm-6 col-xs-12 d-flex align-items-center">
                <a style="padding:2.5px 4px;" data-toggle="modal" data-target="#modal_crear_color"  class="btn btn-primary" href="#">
                  NUEVO COLOR  <i class="fas fa-plus"></i>    
                </a> 
              </div>

              <div class="col-lg-4 col-md-4 col-sm-6 col-xs-12">
                <label style="font-weight: bold;" class="required">Almacén</label> 
                
                <select  id="almacen" name="almacen" class="select2_form form-control {{ $errors->has('sub_familia') ? ' is-invalid' : '' }}" required >
                  <option></option>
                  @foreach($almacenes as $almacen)
                    <option @if($almacen->tipo_almacen === 'PRINCIPAL') selected @endif value="{{ $almacen->id }}" {{ (old('almacen') == $almacen->id ? "selected" : "") }} >{{ $almacen->descripcion.'-'.$almacen->tipo_almacen }}</option>
                  @endforeach
                </select>
                @if ($errors->has('almacen'))
                  <span class="invalid-feedback" role="alert">
                    <strong>{{ $errors->first('almacen') }}</strong>
                  </span>
                @endif
              </div>
            </div>
          </div>

            <div class="col-lg-12">
              
              <div class="table-responsive">
                <table class="table table-striped table-hover" id="table-colores" width="100%">
                    <thead>
                      <tr>
                        <th scope="col" style="text-align: left;">#</th>
                        <th scope="col">NOMBRE</th>
                      </tr>
                    </thead>
                    <tbody>
                      @foreach ($colores as $color)
                        <tr>
                          <th style="text-align: left;" scope="row">{{$color->id}}</th>
                     
                          <td>
                            <div class="form-check">
                              <input class="form-check-input checkColor" type="checkbox" value="" id="checkColor_{{$color->id}}" data-color-id="{{$color->id}}">
                              <label class="form-check-label" for="checkColor_{{$color->id}}">
                                {{$color->descripcion}}
                              </label>
                            </div>
                          </td>
                          
                        </tr>
                      @endforeach
                      
                    </tbody>
                </table>
              </div> 
            </div>
        </div>
    </div>

</div>

