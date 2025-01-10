<div class="table-responsive overflow-x-auto">
    <table class="table table-hover"  class="display" style="width:100%" id="tabla_ni_detalle">
        <thead>
          <tr>

            <th></th>
         
            
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
  