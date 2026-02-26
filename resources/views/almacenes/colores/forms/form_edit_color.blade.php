<form role="form" method="POST" id="form_edit_color">
    {{ csrf_field() }}

    <div class="row">
        <div class="col-lg-12 col-md-12 col-sm-12 col-xs-12">
            <label class="required_field" style="font-weight: bold;">Descripción:</label>
            <input type="text" class="form-control" name="descripcion_edit" id="descripcion_edit"
                value="{{ old('descripcion') }}"required>
            <p class="descripcion_edit_error msgError"></p>
        </div>
        <div class="col-lg-12 col-md-12 col-sm-12 col-xs-12">
            <label style="font-weight: bold;">Color:</label>
            <input type="color" class="form-control" name="codigo_edit" id="codigo_edit"
                value="{{ old('codigo', '#ffffff') }}" title="Elige un color">
            <p class="codigo_edit_error msgError"></p>
        </div>
    </div>
</form>

<style>
    input[type="color"].form-control {
        height: 38px;
        padding: 0;
        width: 100%;
    }
</style>

