<div class="table-responsive">
    <table class="table table-sm table-striped table-bordered table-hover"   id="table-doc-productos">
      <thead>
        <tr>
            <th scope="col">PRODUCTO</th>
            <th scope="col">COLOR</th>
          @foreach ($tallas as $talla)
            <th style="background-color: rgb(210, 242, 242);"  scope="col" data-talla={{$talla->id}}>{{$talla->descripcion}}</th>
            <th >CANT</th>
          @endforeach
          
          <th style="text-align: right;">IMPORTE</th>
        </tr>
      </thead>
      <tbody>
       
      </tbody>
     
    </table>
  </div>
  