<div class="table-responsive">
    <table class="table table-sm table-striped table-bordered table-hover"  id="table-stocks-docno">
      <thead>
        <tr>
       
            <th scope="col">PRODUCTO</th>
            @foreach ($tallas as $talla)
                <th style="background-color: rgb(210, 242, 242);"  scope="col" data-talla={{$talla->id}}>{{$talla->descripcion}}</th>
                <th>CANT</th> 
            @endforeach
            <th>PRECIO VENTA</th>  
            
        </tr>
      </thead>
      <tbody>
          
      </tbody>
     
    </table>
</div>
  