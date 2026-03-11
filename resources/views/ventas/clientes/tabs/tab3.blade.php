<div class="row">
    <div class="col-12">

        <div class="row">

            <div class="col-md-6 b-r">

                <div class="form-group">
                    <label>
                        <i class="fas fa-user text-primary"></i> Nombre
                    </label>

                    <input type="text" id="nombre_propietario" name="nombre_propietario" class="form-control"
                        @if (isset($cliente)) value="{{ $cliente->nombre_propietario }}"@else value="" @endif
                        maxlength="191" onkeyup="return mayus(this)">

                    <span class="nombre_propietario_error msgError text-danger"></span>
                </div>


                <div class="form-group">
                    <label>
                        <i class="fas fa-map-marker-alt text-danger"></i> Dirección
                    </label>

                    <input type="text" id="direccion_propietario" name="direccion_propietario" class="form-control"
                        @if (isset($cliente)) value="{{ $cliente->direccion_propietario }}"@else value="" @endif
                        maxlength="191" onkeyup="return mayus(this)">

                    <span class="direccion_propietario_error msgError text-danger"></span>
                </div>

            </div>

            <div class="col-md-6">

                <div class="form-group row" id="fecha_nacimiento_propietario">

                    <div class="col-md-6">

                        <label>
                            <i class="fas fa-birthday-cake text-warning"></i>
                            Fecha de Nacimiento
                        </label>

                        <div class="input-group date">

                            <div class="input-group-prepend">
                                <span class="input-group-text">
                                    <i class="fas fa-calendar-alt text-primary"></i>
                                </span>
                            </div>

                            <input type="date" id="fecha_nacimiento_prop" name="fecha_nacimiento_prop"
                                class="form-control"
                                @if (isset($cliente)) value="{{ $cliente->fecha_nacimiento_prop }}"@else value="" @endif>

                        </div>

                        <span class="fecha_nacimiento_prop_error msgError text-danger"></span>

                    </div>


                    <div class="col-md-6">

                        <label>
                            <i class="fas fa-mobile-alt text-success"></i>
                            Celular
                        </label>

                        <input type="text" id="celular_propietario" name="celular_propietario" class="form-control"
                            @if (isset($cliente)) value="{{ $cliente->celular_propietario }}"@else value="" @endif
                            onkeypress="return isNumber(event)">

                        <span class="celular_propietario_error msgError text-danger"></span>

                    </div>

                </div>



                <div class="form-group">

                    <label>
                        <i class="fas fa-envelope text-info"></i>
                        Correo electrónico
                    </label>

                    <input type="email" id="correo_propietario" name="correo_propietario" class="form-control"
                        @if (isset($cliente)) value="{{ $cliente->correo_propietario }}"
                        @else
                            value="" @endif>

                    <span class="correo_propietario_error msgError text-danger"></span>

                </div>

            </div>

        </div>



        <div class="row">

            {{-- <div class="col-lg-6">

                <div class="row">

                    <div class="col-lg-12">
                        <label>
                            <i class="fas fa-map text-primary"></i>
                            Ubicación GPS
                        </label>
                    </div>

                    <div class="col-lg-12">
                        <div id="map" style="width:100%;height:300px;"></div>
                    </div>

                </div>

            </div> --}}

            <div class="col-lg-6">
                <label id="logo_label">
                    <i class="fas fa-image text-info"></i> Imagen
                </label>

                <input id="logo" type="file" name="logo" class="form-control" accept="image/*">

                <span class="logo_error msgError text-danger"></span>
            </div>

        </div>

    </div>
</div>
