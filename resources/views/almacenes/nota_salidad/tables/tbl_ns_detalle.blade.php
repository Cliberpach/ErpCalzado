<table class="table table-sm table-striped table-bordered table-hover" style="text-transform:uppercase" id="tabla_ns_detalle">
    <thead>
        <tr>

            <th class="text-center"></th>
            <th class="text-center">PRODUCTO</th>
            @foreach ($tallas as $talla)
                <th class="text-center">{{$talla->descripcion}}</th>
            @endforeach
                
        </tr>
    </thead>

    <tbody>

    </tbody>
</table> 
