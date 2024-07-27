<div class="table-responsive">
    <table class="table table-sm table-striped table-bordered table-hover" id="table-stocks-pedidos">
      <thead>
        <tr>
          <th scope="col">PRODUCTO</th>
          @foreach ($tallas as $talla)
              <th style="background-color: rgb(210, 242, 242);text-align:center;" scope="col" data-talla={{$talla->id}}>{{$talla->descripcion}}</th>
              <th style="text-align:center;">CANT</th>
          @endforeach
          <th style="text-align: right;">PRECIO VENTA</th>
        </tr>
      </thead>
      <tbody>
          
      </tbody>
    </table>
</div>
  