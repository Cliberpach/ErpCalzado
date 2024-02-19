<div class="table-responsive">
    <table class="table table-hover" @if (!isset($carrito))  id="table-stocks" @else id="table-detalle" @endif>
      <thead>
        <tr>
          @if (isset($carrito))
              <th></th>
          @endif
          <th scope="col">PRODUCTO</th>
          @foreach ($tallas as $talla)
              <th style="background-color: rgb(210, 242, 242);" scope="col" data-talla={{$talla->id}}>{{$talla->descripcion}}</th>
              @if (!isset($carrito))
                <th>CANT</th>
              @endif
          @endforeach
        </tr>
      </thead>
      <tbody>
          
      </tbody>
    </table>
  </div>
  