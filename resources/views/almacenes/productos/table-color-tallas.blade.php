<div class="table-responsive">
    <table class="table table-hover" id="tableShowStocks">
        <thead>
          <tr>
            <th scope="col" class="product_name"></th>
            @foreach ($tallas as $talla)
                <th scope="col">{{$talla->descripcion}}</th>
            @endforeach
          </tr>
        </thead>
        <tbody>

            {{-- @foreach ($colores as $color)
                <tr>
                    <th scope="row">{{$color->color_nombre}}</th>
                    @foreach ($tallas as $talla)
                        <td id="stock_{{$color->id}}_{{$talla->id}}"></td>
                    @endforeach
                </tr>
            @endforeach --}}
         
          
        </tbody>
    </table>
</div>
