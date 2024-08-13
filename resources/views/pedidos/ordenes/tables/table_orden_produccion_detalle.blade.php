<table class="table table-sm table-striped table-bordered table-hover" id="table_orden_produccion_detalle" >
    <thead>
        <tr>
            <th></th>
            <th style="width:200px;"  scope="col">PRODUCTO</th>
            <th  scope="col">COLOR</th>
          
            @foreach ($tallas as $talla)
                <th style="background-color: rgb(210, 242, 242);text-align:center;"  scope="col" data-talla={{$talla->id}}>{{$talla->descripcion}}</th>                
            @endforeach
  
        </tr>
    </thead>
    <tbody>
      
    </tbody>
</table>
  