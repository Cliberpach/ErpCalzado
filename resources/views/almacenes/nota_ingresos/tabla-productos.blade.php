<div class="table-responsive overflow-x-auto">
  <table class="table table-hover" @if (!isset($carrito))  id="table-productos" @else id="table-detalle" @endif>
      <thead>
        <tr>
          @if (isset($carrito))
              <th></th>
          @endif
          <th scope="col" class="product_name">
            PRODUCTO
          </th>
          
          @foreach ($tallas as $talla)
              <th scope="col">{{$talla->descripcion}}</th>
          @endforeach
          
         
        </tr>
      </thead>
      <tbody>
         
       
      </tbody>
  </table>
</div>
