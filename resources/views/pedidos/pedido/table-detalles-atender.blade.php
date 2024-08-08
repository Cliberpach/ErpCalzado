<div class="table-responsive">
    <table class="table table-sm table-striped table-bordered table-hover"  id="table-detalle-atender">
      <thead>
        <tr>
         
            <th  scope="col">PRODUCTO</th>
            @foreach ($tallas as $talla)
                <th style="background-color: rgb(210, 242, 242);"  scope="col" data-talla={{$talla->id}}>{{$talla->descripcion}}</th>
                <th width="7%">CANT</th>
            @endforeach
          
            <th style="text-align: right;">PRECIO VENTA</th>
            <th style="text-align: right;">SUBTOTAL</th>
            <th style="text-align: center;">DSCTO %</th>
          
        </tr>
      </thead>
      <tbody>
     
      </tbody>
     
     
      
    </table>
  </div>
  