<div class="row">
    <div class="col-12">
        <div class="row">

            <div class="col-md-6 b-r">

                <div class="form-group">
                    <label>
                        <i class="fas fa-map-marker-alt text-danger"></i>
                        Dirección de Negocio (Dirección de Llegada)
                    </label>

                    <input type="text" id="direccion_negocio" name="direccion_negocio" class="form-control"
                        @if (isset($cliente)) value="{{ $cliente->direccion_negocio }}"
                        @else
                            value="" @endif
                        maxlength="191" onkeyup="return mayus(this)">

                    <span class="direccion_negocio_error msgError text-danger"></span>
                </div>


                <div class="form-group row" id="fecha_aniversario">
                    <div class="col-md-6">

                        <label>
                            <i class="fas fa-calendar-alt text-primary"></i>
                            Fecha de Aniversario
                        </label>

                        <div class="input-group date">
                            <div class="input-group-prepend">
                                <span class="input-group-text">
                                    <i class="fas fa-calendar-alt text-primary"></i>
                                </span>
                            </div>

                            <input type="date" id="fecha_aniversario" name="fecha_aniversario" class="form-control"
                                @if (isset($cliente)) value="{{ $cliente->fecha_aniversario }}"
                        @else
                            value="" @endif>
                        </div>

                        <span class="fecha_aniversario_error msgError text-danger"></span>

                    </div>
                </div>

            </div>


            <div class="col-md-6">

                <div class="form-group">

                    <label>
                        <i class="fas fa-comment-dots text-info"></i>
                        Observaciones
                    </label>

                    <textarea id="observaciones" name="observaciones" class="form-control" rows="3" onkeyup="return mayus(this)">
@if (isset($cliente))
{{ $cliente->observaciones }}
@else
@endif
</textarea>

                    <span class="observaciones_error msgError text-danger"></span>

                </div>

            </div>

        </div>



        <div class="row">

            <div class="col-md-6 b-r">

                <h3>
                    <i class="fas fa-share-alt text-success"></i>
                    REDES SOCIALES
                </h3>


                <div class="form-group">

                    <label>
                        <i class="fab fa-facebook text-primary"></i>
                        Facebook
                    </label>

                    <div class="input-group">

                        <div class="input-group-prepend">
                            <span class="input-group-text">
                                <i class="fab fa-facebook text-primary"></i>
                            </span>
                        </div>

                        <input type="text" id="facebook" name="facebook" class="form-control"
                            @if (isset($cliente)) value="{{ $cliente->facebook }}"@else value="" @endif>
                    </div>

                    <span class="facebook_error msgError text-danger"></span>

                </div>



                <div class="form-group">

                    <label>
                        <i class="fab fa-instagram text-danger"></i>
                        Instagram
                    </label>

                    <div class="input-group">

                        <div class="input-group-prepend">
                            <span class="input-group-text">
                                <i class="fab fa-instagram text-danger"></i>
                            </span>
                        </div>

                        <input type="text" id="instagram" name="instagram" class="form-control"
                            @if (isset($cliente)) value="{{ $cliente->instagram }}"@else value="" @endif>

                    </div>

                    <span class="instagram_error msgError text-danger"></span>

                </div>



                <div class="form-group">

                    <label>
                        <i class="fas fa-globe text-info"></i>
                        Web
                    </label>

                    <div class="input-group">

                        <div class="input-group-prepend">
                            <span class="input-group-text">
                                <i class="fas fa-globe text-info"></i>
                            </span>
                        </div>

                        <input type="text" id="web" name="web" class="form-control"
                            @if (isset($cliente)) value="{{ $cliente->web }}"@else value="" @endif>

                    </div>

                    <span class="web_error msgError text-danger"></span>

                </div>


            </div>



            <div class="col-md-6">

                <h3>
                    <i class="fas fa-clock text-warning"></i>
                    HORARIO DE ATENCIÓN
                </h3>


                <div class="form-group row">

                    <div class="col-md-6">

                        <label>
                            <i class="fas fa-hourglass-start text-success"></i>
                            Horario Inicio
                        </label>

                        <input type="time" name="hora_inicio" class="form-control"
                            @if (isset($cliente)) value="{{ $cliente->hora_inicio }}"@else value="" @endif>

                        <span class="hora_inicio_error msgError text-danger"></span>

                    </div>


                    <div class="col-md-6">

                        <label>
                            <i class="fas fa-hourglass-end text-danger"></i>
                            Horario Término
                        </label>

                        <input type="time" name="hora_termino" class="form-control"
                            @if (isset($cliente)) value="{{ $cliente->hora_termino }}"@else value="" @endif>

                        <span class="hora_termino_error msgError text-danger"></span>

                    </div>

                </div>

            </div>

        </div>

    </div>
</div>
