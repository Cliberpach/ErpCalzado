<table class="table table-hover table-bordered" id="table_programacion_produccion">
    <thead>
      <tr>
        <th scope="col">PEDIDO</th>
        <th scope="col">MODELO</th>
        <th scope="col">PRODUCTO</th>
        <th scope="col">COLOR</th>
        @foreach ($tallas as $talla)
            <th scope="col">{{$talla->descripcion}}</th>
        @endforeach
       
      </tr>
    </thead>
    <tbody>
     
    </tbody>
  </table>