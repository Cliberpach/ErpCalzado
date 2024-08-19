<table class="table table-sm table-striped table-bordered table-hover" id="table-stocks-pedidos">
    <thead>
      <tr>
        <th scope="col">PRODUCTO</th>
        <th scope="col">COLOR</th>

        @foreach ($tallas as $talla)
            <th style="background-color: rgb(210, 242, 242);text-align:center;" scope="col" data-talla={{$talla->id}}>{{$talla->descripcion}}</th>
            <th style="text-align:center;">CANT</th>
        @endforeach
      </tr>
    </thead>
    <tbody>
        
    </tbody>
  </table>