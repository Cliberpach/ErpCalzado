<table class="table table-sm table-striped table-bordered table-hover" id="tbl_kardex_stock">
    <thead>
        <tr>
            <th style="width:200px;" scope="col">PRODUCTO</th>
            <th scope="col">COLOR</th>

            @foreach ($tallas as $talla)
                <th style="background-color: rgb(231, 240, 255);text-align:center;" scope="col"
                    data-talla={{ $talla->id }}>{{ $talla->descripcion }}</th>
            @endforeach
        </tr>
    </thead>
    <tbody>

    </tbody>
</table>
