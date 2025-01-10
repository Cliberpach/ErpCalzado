
<table class="table table-sm table-striped table-bordered table-hover"  id="tabla_ns_productos">
    <thead>
        <tr>
       
          <th scope="col">COLOR</th>
          <th scope="col">PRODUCTO</th>
          @foreach ($tallas as $talla)
            <th style="background-color: rgb(210, 242, 242);"  scope="col" data-talla={{$talla->id}}>{{$talla->descripcion}}</th>
            <th>CANT</th> 
          @endforeach
            
        </tr>
    </thead>

    <tbody>
   
    </tbody>
</table>
  