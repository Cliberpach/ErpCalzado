<div class="table-responsive overflow-x-auto">
    <table class="table table-hover"  class="display" style="width:100%"  id="tabla_ni_productos">
        <thead>
          <tr>

            <th scope="col" class="color_name">
                COLOR
            </th>
            
            
            <th scope="col" class="product_name">
              PRODUCTO
            </th>
            
            @foreach ($tallas as $talla)
                <th scope="col">{{$talla->descripcion}}</th>
                <th scope="col">CANT</th>
            @endforeach
            
           
          </tr>
        </thead>
        <tbody>
           
         
        </tbody>
    </table>
  </div>
  