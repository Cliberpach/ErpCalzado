<form role="form" action="{{ route('Caja.apertura') }}" method="POST" id="formAbrirCaja">

<div class="row">
    
    <div class="col-12">

        <label class="required" for="caja" style="font-weight: bold;">Cajas Disponibles</label>

        <select data-placeholder="SELECCIONAR" required name="caja" id="caja" class="select2_form">
            <option></option>
            
        </select>

        <p class="caja_error msgError" style="font-weight: bold;color:red;"></p>
        
    </div>

    <div class="col-12">

        <label class="required" for="cajero_id" style="font-weight: bold;">Cajero</label>

        <select required
            class="form-control select2_form"
            style="text-transform: uppercase; width:100%" name="cajero_id" id="cajero_id">

            <option value=""></option>
        </select>
        <p class="cajero_id_error msgError" style="font-weight: bold;color:red;"></p>

    </div>

    <div class="col-12">
        <label class="required" for="turno" style="font-weight: bold;">Turno</label>
        <select required class="form-control select2_form"
            style="text-transform: uppercase; width:100%" name="turno" id="turno">
            <option></option>
            <option>Mañana</option>
            <option>Tarde</option>
            <option>Noche</option>
        </select>
        <p class="turno_error msgError" style="font-weight: bold;color:red;"></p>
    </div>

    <div class="col-12">
        <label class="required" for="saldo_inicial" style="font-weight: bold;">Saldo Inicial</label>
        <input value="0" required type="text" class="form-control inputDecimalPositivo" id="saldo_inicial" name="saldo_inicial">
        <p class="saldo_inicial_error msgError" style="font-weight: bold;color:red;"></p>
    </div>

</div>

<div class="table-responsive">
    <h3 class="text-center"> Colaboradores</h3>
    @include('pos.MovimientoCaja.tables.tbl_colab_mov_caja')
</div>



</form>