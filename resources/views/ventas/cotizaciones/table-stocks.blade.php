<div class="table-responsive">
  <table class="table table-hover" @if (!isset($carrito))  id="table-stocks" @else id="table-detalle" @endif>
    <thead>
      <tr>
        <th scope="col">PRODUCTO</th>
        @foreach ($tallas as $talla)
            <th scope="col" data-talla={{$talla->id}}>{{$talla->descripcion}}</th>
            @if (!isset($carrito))
              <th>CANT</th>
            @endif
        @endforeach
        
          <th>PRECIO VENTA</th>  
          @if (isset($carrito))
              <th>SUBTOTAL</th>
           @endif
      </tr>
    </thead>
    <tbody>
        
    </tbody>
  </table>
</div>
