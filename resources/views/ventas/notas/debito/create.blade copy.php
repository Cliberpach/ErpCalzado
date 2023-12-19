@extends('layout') @section('content')

@section('ventas-active', 'active')
@section('notas-active', 'active')
<div class="row wrapper border-bottom white-bg page-heading">
    <div class="col-lg-10 col-md-10">
       <h2  style="text-transform:uppercase"><b>REGISTRAR NUEVA NOTA DE DEBITO</b></h2>
        <ol class="breadcrumb">
            <li class="breadcrumb-item">
                <a href="{{route('home')}}">Panel de Control</a>
            </li>
            <li class="breadcrumb-item active">
                <strong>Notas de credito y debito</strong>
            </li>
        </ol>
    </div>
</div>

<div class="wrapper wrapper-content animated fadeInRight">

    <div class="row">
        <div class="col-lg-12">
            <div class="ibox">

                <div class="ibox-content">

                    <form action="{{route('compras.documento.store')}}" method="POST" id="enviar_documento">
                        {{csrf_field()}}

                        @if (!empty($orden))
                            <input type="hidden" name="orden_id" value="{{$orden->id}}" >
                        @endif
                        <div class="row">
                            <div class="col-sm-6 b-r">
                                <h4 class=""><b>Documento de compra</b></h4>
                                <div class="row">
                                    <div class="col-md-12">
                                        <p>Registrar datos de la nota de debito:</p>
                                    </div>
                                </div>

                                <div class="form-group row">

                                    <div class="col-lg-6 col-xs-12" id="fecha_documento">
                                        <label class="required">Fecha de Emisión</label>
                                        <div class="input-group  @if (empty($orden)) {{'date'}} @endif">
                                            <span class="input-group-addon">
                                                <i class="fa fa-calendar"></i>
                                            </span>
                                            @if (!empty($orden))
                                            <input type="text" id="fecha_documento_campo" name="fecha_emision"
                                                class="form-control {{ $errors->has('fecha_emision') ? ' is-invalid' : '' }}"
                                                value="{{old('fecha_emision',getFechaFormato($orden->fecha_emision, 'd/m/Y'))}}"
                                                autocomplete="off" required readonly disabled>
                                            @else
                                            <input type="text" id="fecha_documento_campo" name="fecha_emision"
                                                class="form-control {{ $errors->has('fecha_emision') ? ' is-invalid' : '' }}"
                                                value="{{old('fecha_emision',getFechaFormato($fecha_hoy, 'd/m/Y'))}}"
                                                autocomplete="off" required readonly >
                                            @endif

                                            @if ($errors->has('fecha_emision'))
                                            <span class="invalid-feedback" role="alert">
                                                <strong>{{ $errors->first('fecha_emision') }}</strong>
                                            </span>
                                            @endif
                                        </div>
                                    </div>

                                    <div class="col-lg-6 col-xs-12" id="fecha_entrega">
                                        <label class="required">Fecha de Entrega</label>
                                        <div class="input-group @if (empty($orden)) {{'date'}} @endif">
                                            <span class="input-group-addon" >
                                                <i class="fa fa-calendar"></i>
                                            </span>
                                            
                                            @if (!empty($orden))
                                            <input type="text" id="fecha_entrega_campo" name="fecha_entrega"
                                                class="form-control {{ $errors->has('fecha_entrega') ? ' is-invalid' : '' }}"
                                                value="{{old('fecha_entrega',getFechaFormato( $orden->fecha_entrega ,'d/m/Y'))}}"
                                                autocomplete="off" readonly disabled>
                                            @else

                                            <input type="text" id="fecha_entrega_campo" name="fecha_entrega"
                                                class="form-control {{ $errors->has('fecha_entrega') ? ' is-invalid' : '' }}"
                                                value="{{old('fecha_entrega',getFechaFormato( $fecha_hoy ,'d/m/Y'))}}"
                                                autocomplete="off" required readonly>

                                            @endif
                                            @if ($errors->has('fecha_entrega'))
                                            <span class="invalid-feedback" role="alert">
                                                <strong>{{ $errors->first('fecha_entrega') }}</strong>
                                            </span>
                                            @endif
                                        </div>
                                    </div>

                                </div>

                                <div class="form-group">
                                    <label class="required">Empresa: </label>

                                        @if (!empty($orden))
                                            <select
                                            class="select2_form form-control {{ $errors->has('empresa_id') ? ' is-invalid' : '' }}"
                                            style="text-transform: uppercase; width:100%" value="{{old('empresa_id')}}"
                                            name="empresa_id" id="empresa_id"  disabled>
                                            <option></option>
                                            @foreach ($empresas as $empresa)
                                            <option value="{{$empresa->id}}"  @if($empresa->id == '1' )
                                                {{'selected'}} @endif >{{$empresa->razon_social}}</option>
                                            @endforeach
                                        </select>
                                        @else
                                            <select
                                            class="select2_form form-control {{ $errors->has('empresa_id') ? ' is-invalid' : '' }}"
                                            style="text-transform: uppercase; width:100%" value="{{old('empresa_id')}}"
                                            name="empresa_id" id="empresa_id" disabled>
                                            <option></option>
                                            @foreach ($empresas as $empresa)
                                            <option value="{{$empresa->id}}"  @if($empresa->id == '1' )
                                                {{'selected'}} @endif >{{$empresa->razon_social}}</option>
                                            @endforeach
                                            </select>
                                        @endif


                                    
                                </div>

                                <hr>

                                <div class="row">
                                    <div class="col-md-12">
                                        <p>Registrar Proveedor:</p>
                                    </div>
                                </div>


                                <div class="form-group">
                                    <label class="required">Ruc / Dni: </label>

                                        @if (!empty($orden))
                                        <select
                                        class="select2_form form-control {{ $errors->has('proveedor_id') ? ' is-invalid' : '' }}"
                                        style="text-transform: uppercase; width:100%" value="{{old('proveedor_id', $orden->proveedor_id)}}"
                                        name="proveedor_id" id="proveedor_id" required disabled>
                                        <option></option>
                                            @foreach ($proveedores as $proveedor)
                                            @if($proveedor->ruc)
                                            <option value="{{$proveedor->id}}" @if(old('proveedor_id',$orden->proveedor_id)==$proveedor->id )
                                                {{'selected'}} @endif >{{$proveedor->ruc}}
                                            </option>
                                            @else
                                            @if($proveedor->dni)
                                            <option value="{{$proveedor->id}}" @if(old('proveedor_id',$orden->proveedor_id)==$proveedor->id )
                                                {{'selected'}} @endif >{{$proveedor->dni}}
                                            </option>
                                            @endif
                                            @endif
                                            @endforeach
                                            </select>


                                        @else
                                            <select
                                            class="select2_form form-control {{ $errors->has('proveedor_id') ? ' is-invalid' : '' }}"
                                            style="text-transform: uppercase; width:100%" value="{{old('proveedor_id')}}"
                                            name="proveedor_id" id="proveedor_id" required >
                                            <option></option>
                                            @foreach ($proveedores as $proveedor)
                                            @if($proveedor->ruc)
                                            <option value="{{$proveedor->id}}" @if(old('proveedor_id')==$proveedor->id )
                                                {{'selected'}} @endif >{{$proveedor->ruc}}
                                            </option>
                                            @else
                                            @if($proveedor->dni)
                                            <option value="{{$proveedor->id}}" @if(old('proveedor_id')==$proveedor->id )
                                                {{'selected'}} @endif >{{$proveedor->dni}}
                                            </option>
                                            @endif
                                            @endif
                                            @endforeach
                                            </select>
                                        @endif

                        
                                </div>

                                <div class="form-group">
                                    <label class="required">Razon Social: </label>
                                    @if (!empty($orden))
                                    <select
                                        class="select2_form form-control {{ $errors->has('proveedor_razon') ? ' is-invalid' : '' }}"
                                        style="text-transform: uppercase; width:100%" value="{{old('proveedor_razon')}}"
                                        name="proveedor_razon" id="proveedor_razon" required disabled>
                                        <option></option>
                                        @foreach ($proveedores as $proveedor)
                                            @if($proveedor->ruc)
                                            <option value="{{$proveedor->id}}" @if(old('proveedor_id',$orden->proveedor_id)==$proveedor->id )
                                                {{'selected'}} @endif >{{$proveedor->descripcion}}
                                            </option>
                                            @else
                                            @if($proveedor->dni)
                                            <option value="{{$proveedor->id}}" @if(old('proveedor_id',$orden->proveedor_id)==$proveedor->id )
                                                {{'selected'}} @endif >{{$proveedor->descripcion}}
                                            </option>
                                            @endif
                                        @endif
                                        @endforeach
                                    </select>
                                    @else
                                    <select
                                        class="select2_form form-control {{ $errors->has('proveedor_razon') ? ' is-invalid' : '' }}"
                                        style="text-transform: uppercase; width:100%" value="{{old('proveedor_razon')}}"
                                        name="proveedor_razon" id="proveedor_razon" required >
                                        <option></option>
                                        @foreach ($proveedores as $proveedor)
                                            @if($proveedor->ruc)
                                            <option value="{{$proveedor->id}}" @if(old('proveedor_id')==$proveedor->id )
                                                {{'selected'}} @endif >{{$proveedor->descripcion}}
                                            </option>
                                            @else
                                            @if($proveedor->dni)
                                            <option value="{{$proveedor->id}}" @if(old('proveedor_id')==$proveedor->id )
                                                {{'selected'}} @endif >{{$proveedor->descripcion}}
                                            </option>
                                            @endif
                                        @endif
                                        @endforeach
                                    </select>


                                    @endif
                                
                                
                                </div>





                            </div>

                            <div class="col-sm-6">

                                <div class="form-group row">
                                    
                                    <div class="col-md-6">
                                        <label class="required">Modo de Compra: </label>

                                        @if (!empty($orden))
                                        <select
                                            class="select2_form form-control {{ $errors->has('modo_compra') ? ' is-invalid' : '' }}"
                                            style="text-transform: uppercase; width:100%" value="{{old('modo_compra',$orden->modo_compra)}}"
                                            name="modo_compra" id="modo_compra" required>
                                            <option></option>
                                                @foreach ($modos as $modo)
                                                <option value="{{$modo->descripcion}}" @if(old('modo_compra',$orden->modo_compra)==$modo->
                                                    descripcion ) {{'selected'}} @endif
                                                    >{{$modo->simbolo.' - '.$modo->descripcion}}</option>
                                                @endforeach
                                                @if ($errors->has('modo_compra'))
                                                <span class="invalid-feedback" role="alert">
                                                    <strong>{{ $errors->first('modo_compra') }}</strong>
                                                </span>
                                                @endif
                                        </select>
                                        @else
                                        <select
                                            class="select2_form form-control {{ $errors->has('modo_compra') ? ' is-invalid' : '' }}"
                                            style="text-transform: uppercase; width:100%" value="{{old('modo_compra')}}"
                                            name="modo_compra" id="modo_compra" required>
                                            <option></option>
                                            @foreach ($modos as $modo)
                                            <option value="{{$modo->descripcion}}" @if(old('modo_compra')==$modo->
                                                descripcion ) {{'selected'}} @endif
                                                >{{$modo->simbolo.' - '.$modo->descripcion}}</option>
                                            @endforeach
                                            @if ($errors->has('modo_compra'))
                                            <span class="invalid-feedback" role="alert">
                                                <strong>{{ $errors->first('modo_compra') }}</strong>
                                            </span>
                                            @endif
                                        </select>

                                        @endif
                                    </div>

                                    <div class="col-md-6">
                                        <label class="required">Moneda: </label>

                                        @if (!empty($orden))
                                        <select
                                        class="select2_form form-control {{ $errors->has('moneda') ? ' is-invalid' : '' }}"
                                        style="text-transform: uppercase; width:100%" value="{{old('moneda',$orden->moneda)}}"
                                        name="moneda" id="moneda" disabled>
                                            <option></option>
                                            
                                            @foreach ($monedas as $moneda)
                                            <option value="{{$moneda->descripcion}}" @if(old('moneda',$orden->moneda)==$moneda->descripcion ) {{'selected'}} @endif
                                                >{{$moneda->simbolo.' - '.$moneda->descripcion}}</option>
                                            @endforeach
                                            @if ($errors->has('moneda'))
                                            <span class="invalid-feedback" role="alert">
                                                <strong>{{ $errors->first('moneda') }}</strong>
                                            </span>
                                            @endif
                                        </select> 
                                        @else
                                            <select
                                            class="select2_form form-control {{ $errors->has('moneda') ? ' is-invalid' : '' }}"
                                            style="text-transform: uppercase; width:100%" value="{{old('moneda')}}"
                                            name="moneda" id="moneda" required>
                                                <option></option>
                                            @foreach ($monedas as $moneda)
                                            <option value="{{$moneda->descripcion}}" @if(old('moneda')==$moneda->descripcion ) {{'selected'}} @endif
                                                >{{$moneda->simbolo.' - '.$moneda->descripcion}}</option>
                                            @endforeach
                                            @if ($errors->has('moneda'))
                                            <span class="invalid-feedback" role="alert">
                                                <strong>{{ $errors->first('moneda') }}</strong>
                                            </span>
                                            @endif

                                            </select> 

                                        @endif
                                    
                                    
                                    </div>


                                  
                                </div>
                                
                                <div class="form-group row">
                                    <div class="col-md-6">
                                        <label class="required">Tipo: </label>
                                        <select
                                            class="select2_form form-control {{ $errors->has('tipo_compra') ? ' is-invalid' : '' }}"
                                            style="text-transform: uppercase; width:100%" value="{{old('tipo_compra')}}"
                                            name="tipo_compra" id="tipo_compra" required onchange="activarNumero()">
                                            <option></option>
                                            
                                                @foreach (tipo_compra() as $modo)
                                                <option value="{{$modo->descripcion}}" @if(old('tipo_compra')==$modo->
                                                    descripcion ) {{'selected'}} @endif
                                                    >{{$modo->descripcion}}</option>
                                                @endforeach
                                                @if ($errors->has('tipo_compra'))
                                                <span class="invalid-feedback" role="alert">
                                                    <strong>{{ $errors->first('tipo_compra') }}</strong>
                                                </span>
                                                @endif
                                          


                                        </select>
                                    </div>

                                    <div class="col-md-6">
                                        <label class="" id="numero_comprobante">Nº: </label>
                                        <input type="text" id="numero_tipo" name="numero_tipo" class="form-control {{ $errors->has('numero_tipo') ? ' is-invalid' : '' }}" value="{{old('numero_tipo')}}" disabled>
                                        
                                        @if ($errors->has('numero_tipo'))
                                        <span class="invalid-feedback" role="alert">
                                            <strong>{{ $errors->first('tipo_compra') }}</strong>
                                        </span>
                                        @endif

                                                                             
                                    </div>

                                    
                                </div>

                                <div class="form-group row">

                                    <div class="col-md-6">
                                        <label class="" id="campo_tipo_cambio">Tipo de Cambio (S/.) :</label>
                                        @if (!empty($orden))
                                        <input type="text" id="tipo_cambio" name="tipo_cambio" class="form-control {{ $errors->has('tipo_cambio') ? ' is-invalid' : '' }}" value="{{old('tipo_cambio',$orden->tipo_cambio)}}" disabled>
                                        @else
                                        <input type="text" id="tipo_cambio" name="tipo_cambio" class="form-control {{ $errors->has('tipo_cambio') ? ' is-invalid' : '' }}" value="{{old('tipo_cambio')}}" disabled>
                                        @endif
                                        
                                        @if ($errors->has('tipo_cambio'))
                                        <span class="invalid-feedback" role="alert">
                                            <strong>{{ $errors->first('tipo_cambio') }}</strong>
                                        </span>
                                        @endif

                                    </div>

                                    <div class="col-md-6">
                                        <label id="igv_requerido">IGV (%):</label>
                                        <div class="input-group">
                                            @if (!empty($orden))
                                                <div class="input-group-prepend">
                                                    <span class="input-group-addon">
                                                        <input type="checkbox" id="igv_check" name="igv_check" disabled>
                                                    </span>
                                                </div>
                                            @else
                                                <div class="input-group-prepend">
                                                    <span class="input-group-addon">
                                                        <input type="checkbox" id="igv_check" name="igv_check">
                                                    </span>
                                                </div>
                                            @endif
                                            @if (!empty($orden))
                                            <input type="text" value="{{old('igv',$orden->igv)}}"
                                                class="form-control {{ $errors->has('igv') ? ' is-invalid' : '' }}"
                                                name="igv" id="igv" maxlength="3"  onkeyup="return mayus(this)" readonly>
                                            @else
                                            <input type="text" value="{{old('igv')}}"
                                                class="form-control {{ $errors->has('igv') ? ' is-invalid' : '' }}"
                                                name="igv" id="igv" maxlength="3"  onkeyup="return mayus(this)"
                                                required>
                                            @endif

                                            @if ($errors->has('igv'))
                                            <span class="invalid-feedback" role="alert">
                                                <strong>{{ $errors->first('igv') }}</strong>
                                            </span>
                                            @endif

                                        </div>
                                    </div>


                                    
                                </div>

                                <div class="form-group">
                                    <label>Observación:</label>
                                    @if (!empty($orden))
                                    <textarea type="text" placeholder=""
                                        class="form-control {{ $errors->has('observacion') ? ' is-invalid' : '' }}"
                                        name="observacion" id="observacion"  onkeyup="return mayus(this)"
                                        value="{{old('observacion',$orden->observacion)}}" @if ($orden) {{'disabled'}} @endif >{{old('observacion',$orden->observacion)}}</textarea>
                                    @else
                                    <textarea type="text" placeholder=""
                                        class="form-control {{ $errors->has('observacion') ? ' is-invalid' : '' }}"
                                        name="observacion" id="observacion"  onkeyup="return mayus(this)"
                                        value="{{old('observacion')}}" >{{old('observacion')}}</textarea>
                                    @endif

                                    @if ($errors->has('observacion'))
                                    <span class="invalid-feedback" role="alert">
                                        <strong>{{ $errors->first('observacion') }}</strong>
                                    </span>
                                    @endif


                                </div>


                                <input type="hidden" id="articulos_tabla" name="articulos_tabla[]">

                            </div>

                        </div>

                        <hr>

                        <div class="row">

                            <div class="col-lg-12">
                                <div class="panel panel-primary">
                                    <div class="panel-heading">
                                        <h4 class=""><b>Detalle del Documento de Compra</b></h4>
                                    </div>
                                    <div class="panel-body">

                                        @if (empty($orden))
                                            <div class="row">

                                                <div class="col-md-6">
                                                    <label class="required">Producto:</label>
                                                    <select class="select2_form form-control"
                                                        style="text-transform: uppercase; width:100%" name="articulo_id"
                                                        id="articulo_id" onchange="cargarPresentacion(this)">
                                                        <option></option>
                                                        @foreach ($articulos as $articulo)
                                                        <option value="{{$articulo->id}}">{{$articulo->descripcion}}
                                                        </option>
                                                        @endforeach
                                                    </select>
                                                    <div class="invalid-feedback"><b><span id="error-articulo"></span></b>
                                                    </div>
                                                </div>
                                                <div class="col-md-6">
                                                    <div class="form-group row">
                                                        <div class="col-md-6">
                                                            <label class="">Presentación:</label>
                                                            <input type="text" id="presentacion" name="presentacion" class="form-control" disabled>
                                                            <div class="invalid-feedback"><b><span id="error-presentacion"></span></b></div>
                                                        
                                                        </div>
                                                        <div class="col-md-6">
                                                            <label class="required">Costo Flete:</label>
                                                            <input type="text" id="costo_flete" name="costo_flete" class="form-control">
                                                            <div class="invalid-feedback"><b><span id="error-costo-flete"></span></b></div>
                                                        </div>
                                                    </div>

                                                </div>


                                            </div>

                                            <div class="row">
                                                <div class="col-sm-3">
                                                    <div class="form-group">
                                                        <label class="col-form-label required" for="amount">Precio:</label>
                                                        <input type="text" id="precio" class="form-control">
                                                        <div class="invalid-feedback"><b><span id="error-precio"></span></b>
                                                        </div>
                                                    </div>
                                                </div>
                                                <div class="col-sm-3">

                                                    <label class="col-form-label required">Cantidad:</label>
                                                    <input type="text" id="cantidad" class="form-control">
                                                    <div class="invalid-feedback"><b><span id="error-cantidad"></span></b>
                                                    </div>


                                                </div>
                                                <div class="col-sm-6">

                                                    <div class="form-group">
                                                        <label class="col-form-label" for="amount">&nbsp;</label>
                                                        <a class="btn btn-block btn-warning enviar_articulo"
                                                            style='color:white;'> <i class="fa fa-plus"></i> AGREGAR</a>
                                                    </div>

                                                </div>
                                            </div>
                                            <hr>
                                        @endif


                                       

                                        <div class="table-responsive">
                                            <table
                                                class="table dataTables-orden-detalle table-striped table-bordered table-hover"
                                                style="text-transform:uppercase">
                                                <thead>
                                                    <tr>
                                                        <th></th>
                                                        <th class="text-center">ACCIONES</th>
                                                        <th class="text-center">CANTIDAD</th>
                                                        <th class="text-center">PRESENTACION</th>
                                                        <th class="text-center">PRODUCTO</th>
                                                        <th class="text-center">COSTO FLETE</th>
                                                        <th class="text-center">PRECIO</th>
                                                        <th class="text-center">TOTAL</th>

                                                    </tr>
                                                </thead>
                                                <tbody>

                                                </tbody>
                                                <tfoot>
                                                    <tr>
                                                        <th colspan="7" style="text-align:right">Sub Total:</th>
                                                        <th><span id="subtotal">0.0</span></th>

                                                    </tr>
                                                    <tr>
                                                        <th colspan="7" class="text-center">IGV <span
                                                                id="igv_int"></span>:</th>
                                                        <th class="text-center"><span id="igv_monto">0.0</span></th>

                                                    </tr>
                                                    <tr>
                                                        <th colspan="7" class="text-center">TOTAL:</th>
                                                        <th class="text-center"><span id="total">0.0</span></th>

                                                    </tr>
                                                </tfoot>
                                            </table>
                                        </div>


                                    </div>
                                </div>
                            </div>

                        </div>

                        <input type="hidden" name="monto_sub_total" id="monto_sub_total" value="{{ old('monto_sub_total') }}">
                        <input type="hidden" name="monto_total_igv" id="monto_total_igv" value="{{ old('monto_total_igv') }}">
                        <input type="hidden" name="monto_total" id="monto_total" value="{{ old('monto_total') }}">


                        <div class="hr-line-dashed"></div>
                        <div class="form-group row">

                            <div class="col-md-6 text-left" style="color:#fcbc6c">
                                <i class="fa fa-exclamation-circle"></i> <small>Los campos marcados con asterisco
                                    (<label class="required"></label>) son obligatorios.</small>
                            </div>

                            <div class="col-md-6 text-right">
                                @if (!empty($orden))
                                <a href="{{route('compras.orden.index')}}" id="btn_cancelar"
                                    class="btn btn-w-m btn-default">
                                    <i class="fa fa-arrow-left"></i> Regresar
                                </a>
                                @else
                                <a href="{{route('compras.documento.index')}}" id="btn_cancelar"
                                    class="btn btn-w-m btn-default">
                                    <i class="fa fa-arrow-left"></i> Regresar
                                </a>
                                @endif
                                <button type="submit" id="btn_grabar" class="btn btn-w-m btn-primary">
                                    <i class="fa fa-save"></i> Grabar
                                </button>
                            </div>

                        </div>

                    </form>

                </div>


            </div>
        </div>

    </div>

</div>

@stop

