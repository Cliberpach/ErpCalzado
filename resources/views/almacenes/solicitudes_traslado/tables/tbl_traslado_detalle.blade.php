<div class="table-responsive overflow-x-auto">
    <table class="table table-hover table-bordered"  class="display" style="width:100%" id="tbl_traslado_show">
        <thead>
          <tr>

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
  