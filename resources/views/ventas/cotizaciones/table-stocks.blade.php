<div class="table-responsive">
  <table class="table table-sm table-striped table-bordered table-hover" @if (!isset($carrito))  id="table-stocks" @else id="table-detalle" @endif>
    <thead>
      <tr>
        @if (isset($carrito))
            <th></th>
        @endif
        <th style="width:200px;"  scope="col">PRODUCTO</th>
        <th  scope="col">COLOR</th>
        
        @foreach ($tallas as $talla)
            <th style="background-color: rgb(210, 242, 242);text-align:center;"  scope="col" data-talla={{$talla->id}}>{{$talla->descripcion}}</th>
            @if (!isset($carrito))
              <th style="text-align: center;">CANT</th>
            @endif
        @endforeach

        @if (isset($carrito))
          <th style="text-align: center;">PRECIO VENTA</th>
          <th style="text-align: right;">SUBTOTAL</th>
          <th style="text-align: center;">DSCTO %</th>
        @endif
      </tr>
    </thead>
    <tbody>
    
    </tbody>
  </table>
</div>
