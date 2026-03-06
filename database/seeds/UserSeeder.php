<?php
use App\User;
use App\Mantenimiento\Colaborador\Colaborador;
use Illuminate\Database\Seeder;

class UserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        // $persona = new Persona();
        // $persona->tipo_documento = 'DNI';
        // $persona->documento = '71114110';
        // $persona->codigo_verificacion = 2;
        // $persona->nombres = 'CARLOS';
        // $persona->apellido_paterno = 'CUBAS';
        // $persona->apellido_materno = 'RODRIGUEZ';
        // $persona->fecha_nacimiento = Carbon::parse('2000-01-01');
        // $persona->sexo = 'H';
        // $persona->estado_civil = 'S';
        // $persona->departamento_id = '02';
        // $persona->provincia_id = '0218';
        // $persona->distrito_id = '021809';
        // $persona->direccion = 'CHEPEN';
        // $persona->correo_electronico = 'CCUBAS@UNITRU.EDU.PE';
        // $persona->telefono_movil = '99999999999';
        // $persona->estado = 'ACTIVO';
        // $persona->save();

        $colaborador = new Colaborador();
        $colaborador->sede_id   =   1;
        $colaborador->tipo_documento_id =   6;
        $colaborador->cargo_id          =   30;
        $colaborador->nro_documento     =   '77777777';
        $colaborador->nombre            =   'ADMIN';
        $colaborador->direccion         =   'CENTRAL';
        $colaborador->telefono          =   '999999999';
        $colaborador->dias_trabajo      =   0;
        $colaborador->dias_descanso     =   0;
        $colaborador->pago_mensual      =   0;
        $colaborador->pago_dia          =   0;
        $colaborador->tipo_documento_nombre =   'DNI';
        $colaborador->save();

        $user           =   new User();
        $user->usuario  =   'ADMINISTRADOR';
        $user->email    =   'ADMIN@SISCOM.COM';
        $user->password =   bcrypt('123456789');
        $user->contra   =   '123456789';
        $user->sede_id  =   1;
        $user->save();

        // $user_persona=new UserPersona();
        // $user_persona->user_id=$user->id;
        // $user_persona->persona_id=$persona->id;
        // $user_persona->save();

    }
}
