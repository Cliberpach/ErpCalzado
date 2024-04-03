<div class="modal inmodal" id="modal_crear_caja" tabindex="-1" role="dialog" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content animated bounceInRight">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal">
                    <span aria-hidden="true">&times;</span>
                    <span class="sr-only">Close</span>
                </button>
                <i class="fa fa-cogs modal-icon"></i>
                <h4 class="modal-title">Caja</h4>
                <small class="font-bold">Apertura de Cajaa</small>
            </div>
            <div class="modal-body">
                <form role="form" action="{{ route('Caja.apertura') }}" method="POST" id="crear_caja_movimiento">
                    {{ csrf_field() }} {{ method_field('POST') }}
                    <div class="form-group">
                        <label for="">Cajas Disponible</label>
                        <select name="caja" id="caja"
                            class="form-control select2_form {{ $errors->has('caja') ? ' is-invalid' : '' }}">
                            <option value=""></option>
                            @foreach (cajas() as $caja)
                                <option value="{{ $caja->id }}">{{ $caja->nombre }}</option>
                            @endforeach
                        </select>
                        @if ($errors->has('caja'))
                            <span class="invalid-feedback" role="alert">
                                <strong id="error-caja">{{ $errors->first('caja') }}</strong>
                            </span>
                        @endif
                    </div>

                    <div class="form-group">
                        <label class="required">Cajeros:</label>

                        <select
                            class="form-control select2_form {{ $errors->has('colaborador_id') ? ' is-invalid' : '' }}"
                            style="text-transform: uppercase; width:100%" name="colaborador_id" id="colaborador_id">

                            @if (count($cajerosDesocupados) == 0)
                                <option disabled> Cajeros no disponibles</option>
                                <option value=""></option>
                            @else
                                <option></option>
                                @foreach ($cajerosDesocupados as $c)
                                    <option value="{{ $c->id }}">
                                        {{ $c->usuario }}
                                    </option>
                                @endforeach
                            @endif


                        </select>
                        @if ($errors->has('colaborador_id'))
                            <span class="invalid-feedback" role="alert">
                                <strong id="error-colaborador_id">{{ $errors->first('colaborador_id') }}</strong>
                            </span>
                        @endif
                    </div>
                    <div class="form-group">
                        <label class="required">Turno:</label>
                        <select class="form-control select2_form  {{ $errors->has('turno') ? ' is-invalid' : '' }}"
                            style="text-transform: uppercase; width:100%" name="turno" id="turno">
                            <option></option>
                            <option>Mañana</option>
                            <option>Tarde</option>
                            <option>Noche</option>
                        </select>
                        @if ($errors->has('turno'))
                            <span class="invalid-feedback" role="alert">
                                <strong id="error-turno">{{ $errors->first('turno') }}</strong>
                            </span>
                        @endif
                    </div>
                    <div class="form-group row">
                        <div class="col-md-6">
                            <label class="required">Saldo Inicial:</label>
                            <input type="text"
                                class="form-control {{ $errors->has('saldo_inicial') ? ' is-invalid' : '' }}"
                                id="saldo_inicial" name="saldo_inicial" value="{{ old('saldo_inicial') }}">
                            @if ($errors->has('saldo_inicial'))
                                <span class="invalid-feedback" role="alert">
                                    <strong id="error-saldo_inicial">{{ $errors->first('saldo_inicial') }}</strong>
                                </span>
                            @endif
                        </div>
                    </div>
            </div>
            <div class="table-responsive">
                <h3 class="text-center"> Colaboradores </h3>
                <table class="table table-sm table-striped table-bordered table-hover" style="text-transform:uppercase"
                    id="usuarios_venta">
                    <thead>
                        <tr>

                            <th class="text-center">

                            </th>
                            <th class="text-center">USUARIO</th>

                            <th class="text-center">NOMBRES Y APELLIDOS</th>


                        </tr>
                    </thead>
                    <tbody>
                        @if (count($usuariosDesocupados) == 0)
                            <tr>
                                <th colspan="3" class="text-center">Usuarios ventas no disponibles</th>
                            </tr>
                        @else
                            @foreach ($usuariosDesocupados as $u)
                                <tr>
                                    <th>
                                        <div class="form-check">
                                            <input class="form-check-input" type="checkbox"
                                                id="checkBox{{ $u->id }}"
                                                onclick="verificarSeleccion({{ $u->id }})">
                                        </div>
                                    </th>
                                    <th>
                                        <input type="hidden" id='idUsuario{{ $u->id }}'
                                            value="{{ $u->id }}"> {{ $u->usuario }}
                                        @if ($errors->has('usuarioVentas'))
                                            <span class="invalid-feedback" role="alert">
                                                <strong
                                                    id="error-usuarioVentas">{{ $errors->first('usuarioVentas') }}</strong>
                                            </span>
                                        @endif
                                    </th>
                                    <th>
                                        {{ $u->usuario }}
                                    </th>
                                </tr>
                            @endforeach
                        @endif

                        {{-- <tr>
                            <th>
                                <div class="form-check">
                                    <input class="form-check-input" type="checkbox" id="checkBox1"
                                        onclick="verificarSeleccion('1')">
                                </div>
                            </th>
                            <th><input type="hidden" id='idUsuario1' value="1">chinc chin</th>
                            <th>balton chinc chin</th>
                        </tr> --}}
                        {{-- <tr>
                            <th>
                                <div class="form-check">
                                    <input class="form-check-input" type="checkbox" id="checkBox2"
                                        onclick="verificarSeleccion('2')">
                                </div>
                            </th>
                            <th><input type="hidden" id='idUsuario2' value="2">chinc chin</th>
                            <th>balton chinc chin</th>
                        </tr> --}}

                    </tbody>
                    <tbody>

                    </tbody>
                </table>
            </div>
            <div class="modal-footer">
                <div class="col-md-6 text-left" style="color:#fcbc6c">
                    <i class="fa fa-exclamation-circle"></i> <small>Los campos marcados con asterisco (<label
                            class="required"></label>) son obligatorios.</small>
                </div>
                <div class="col-md-6 text-right">
                    <a href="#" class="btn btn-primary btn-sm" id="btnEnviarAperturaCaja" onclick="verificarUsuariosVentas(event)"><i
                            class="fa fa-save" ></i> Guardar</a>
                    <button type="button" class="btn btn-danger btn-sm" data-dismiss="modal"><i
                            class="fa fa-times"></i> Cancelar</button>
                </div>
            </div>


            </form>
        </div>
    </div>
</div>
@push('styles')
    <link href="{{ asset('Inspinia/css/plugins/select2/select2.min.css') }}" rel="stylesheet">
@endpush
@push('scripts')
    <!-- Select2 -->
    <script src="{{ asset('Inspinia/js/plugins/select2/select2.full.min.js') }}"></script>
    <script>

        // function verificarSeleccion(id) {
        //     alert('estoy en el create');
        //     let verificar = document.getElementById(`checkBox${id}`);
        //     if (verificar.checked) {
        //         // Se agregara el atributo name para que  se guarde ese dato
        //         document.getElementById(`idUsuario${id}`).setAttribute('name', 'usuarioVentas[]');

        //     } else {
        //         // Se quitara el atributo name para que no se guarde ese dato
        //         document.getElementById(`idUsuario${id}`).removeAttribute('name');
        //     }


        // }

        $(".select2_form").select2({
            placeholder: "SELECCIONAR",
            allowClear: true,
            height: '200px',
            width: '100%',
        });
    </script>
@endpush
