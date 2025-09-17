<div class="table-responsive">
    <table class="table table-sm table-striped table-bordered table-hover" id="table_detalle">
        <thead>
            <tr>

                <th style="width:200px;" scope="col">PRODUCTO</th>
                <th scope="col">COLOR</th>

                @foreach ($tallas as $talla)
                    <th style="background-color: rgb(210, 242, 242);text-align:center;" scope="col"
                        data-talla={{ $talla->id }}>{{ $talla->descripcion }}</th>
                @endforeach

                <th style="text-align: center;">PRECIO VENTA</th>
                <th style="text-align: right;">SUBTOTAL</th>
                <th style="text-align: center;">DSCTO %</th>

            </tr>
        </thead>
        <tbody>

        </tbody>
    </table>
</div>
