<div class="row">
    <div class="col-lg-6 b-r">
        <h4 class=""><b>Colores</b></h4>
        @foreach ($colores as $color)
            <div class="form-check d-flex mt-2 align-items-center">
                <input data-color-id="{{$color->id}}" data-color-nombre="{{$color->descripcion}}" style="flex:0.5;" type="checkbox" class="form-check-input color" id="color_{{$color->id}}">
                <label style="flex:0.2;" class="form-check-label" for="color_{{$color->id}}">{{$color->descripcion}} <span class="aviso_{{$color->id}} span-aviso"></span></label>
            </div>
        @endforeach
    </div>
    <div class="col-lg-6">
        <h4 class=""><b>Tallas: </b><span class="color_activo ml-2" style="font-weight:bold;">SELECCIONE UN COLOR</span></h4>

        @foreach ($colores as $color)
            <div class="color-tallas" id="color_tallas_{{$color->id}}"  hidden >
                @foreach ($tallas as $talla)
                <div class="form-check p-0 mb-2">
                    <label class="form-check-label" for="{{$color->id}}_{{$talla->id}}">{{$talla->descripcion}}</label>
                    <input data-color-id="{{$color->id}}" data-talla-id="{{$talla->id}}" placeholder="Ingresar stock" name="stocks[{{$color->id}}_{{$talla->id}}]" type="number" class="form-control talla" id="input_{{$color->id}}_{{$talla->id}}">
                </div>
                @endforeach
            </div>
        @endforeach
    </div>
</div>