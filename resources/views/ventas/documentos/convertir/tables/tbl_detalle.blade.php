<table class="table table-sm table-striped table-bordered table-hover" style="text-transform:uppercase" id="table-detalle-conv">
            <thead>
                <tr>

                    <th class="text-center">PRODUCTO</th>
                    @foreach ($tallas as $talla)
                        <th class="text-center">{{$talla->descripcion}}</th>
                    @endforeach
                    <th class="text-center">P. VENTA</th>
                    <th class="text-center">SUBTOTAL</th>
                    <th class="text-center">DSCTO %</th>

                </tr>
            </thead>
            <tbody>

            </tbody>
</table> 
