<?php
use App\User;
use App\Mantenimiento\Persona\Persona;
use App\Mantenimiento\Colaborador\Colaborador;
use App\PersonaTrabajador;
use App\UserPersona;
use Illuminate\Database\Seeder;
use Carbon\Carbon;

class UserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $persona = new Persona();
        $persona->tipo_documento = 'DNI';
        $persona->documento = '71114110';
        $persona->codigo_verificacion = 2;
        $persona->nombres = 'CARLOS';
        $persona->apellido_paterno = 'CUBAS';
        $persona->apellido_materno = 'RODRIGUEZ';
        $persona->fecha_nacimiento = Carbon::parse('2000-01-01');
        $persona->sexo = 'H';
        $persona->estado_civil = 'S';
        $persona->departamento_id = '02';
        $persona->provincia_id = '0218';
        $persona->distrito_id = '021809';
        $persona->direccion = 'CHEPEN';
        $persona->correo_electronico = 'CCUBAS@UNITRU.EDU.PE';
        $persona->telefono_movil = '99999999999';
        $persona->estado = 'ACTIVO';
        $persona->save();

        $colaborador = new Colaborador();
        $colaborador->persona_id = $persona->id;
        $colaborador->area = 'COMERCIAL';
        $colaborador->profesion = 'ING.SISTEMAS';
        $colaborador->cargo = 'GERENTE GENERAL';
        $colaborador->telefono_referencia = '2121212';
        $colaborador->contacto_referencia = 'LOPEZ';
        $colaborador->grupo_sanguineo = 'O-';
        $colaborador->numero_hijos = 10;
        $colaborador->sueldo = 1200;
        $colaborador->sueldo_bruto = 1200;
        $colaborador->sueldo_neto = 1200;
        $colaborador->moneda_sueldo = 'S/.';
        $colaborador->tipo_banco = 'BN';
        $colaborador->numero_cuenta = '2020202';
        $colaborador->fecha_inicio_actividad = Carbon::parse('2000-01-01');
        $colaborador->fecha_fin_actividad = Carbon::parse('2000-01-01');
        $colaborador->fecha_inicio_planilla = Carbon::parse('2000-01-01');
        $colaborador->fecha_fin_planilla = Carbon::parse('2000-01-01');
        $colaborador->estado = 'ACTIVO';
        $colaborador->save();

        $user = new User();
        $user->usuario = 'ADMINISTRADOR';
        $user->email = 'ADMIN@SISCOM.COM';
        $user->password = bcrypt('ADMIN');
        $user->contra = 'ADMIN';
        $user->save();

        $user_persona=new UserPersona();
        $user_persona->user_id=$user->id;
        $user_persona->persona_id=$persona->id;
        $user_persona->save();

    }
}