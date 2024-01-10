<?php

use App\Almacenes\Almacen;
use App\Mantenimiento\Persona\Persona;
use App\Mantenimiento\Vendedor\Vendedor;
use App\PersonaTrabajador;
use App\Pos\Caja;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     *
     * @return void
     */
    public function run()
    {
        $this->call(ProductoSeeder::class);

        $this->call(DepartamentoSeeder::class);
        $this->call(ProvinciaSeeder::class);
        $this->call(DistritoSeeder::class);
        $this->call(UserSeeder::class);
        $this->call(TablaSeeder::class);
        $this->call(TablaDetalleSeeder::class);
        $this->call(ParametroSeeder::class);
        $this->call(EmpresaSeeder::class);

        $this->call(PermissionsSeeder::class);
        $this->call(ConfiguracionSeeder::class);

        //--------Seeders Confirmados -----------
        $caja = new Caja();
        $caja->nombre = "Caja Principal";
        $caja->save();

        //$this->call(AlmacenSeeder::class);

        $almacen = new Almacen();
        $almacen->descripcion="CENTRAL";
        $almacen->ubicacion="TIENDA";
        $almacen->save();

        $persona = new Persona();
        $persona->tipo_documento = 'DNI';
        $persona->documento = '99999999';
        $persona->codigo_verificacion = '-';
        $persona->nombres = 'OFICINA';
        $persona->apellido_paterno = '';
        $persona->apellido_materno = '';
        $persona->fecha_nacimiento = '2000-01-01';
        $persona->sexo = 'M';
        $persona->estado_civil = '';
        $persona->departamento_id = 13;
        $persona->provincia_id = 1301;
        $persona->distrito_id = 130101;
        $persona->direccion = 'TRUJILLO';
        $persona->correo_electronico = 'CCUBAS@UNITRU.EDU.PE';
        $persona->telefono_movil = '';
        $persona->telefono_fijo = '';
        $persona->correo_corporativo = '';
        $persona->telefono_trabajo = '';
        $persona->estado_documento = '';
        $persona->save();

        $vendedor = new Vendedor();
        $vendedor->persona_id = $persona->id;
        $vendedor->area = 'COMERCIAL';
        $vendedor->profesion = 'INGENIERO(A) DE SISTEMAS';
        $vendedor->cargo = 'ASISTENTE DE CONTABILIDAD';
        $vendedor->telefono_referencia = '';
        $vendedor->contacto_referencia = '';
        $vendedor->grupo_sanguineo = '';
        $vendedor->alergias = '';
        $vendedor->numero_hijos = 0;
        $vendedor->sueldo = 1000;
        $vendedor->sueldo_bruto = 1000;
        $vendedor->sueldo_neto = 1000;
        $vendedor->moneda_sueldo = 'SOLES';
        $vendedor->fecha_inicio_actividad = '2020-04-02';
        $vendedor->tipo_banco = '';
        $vendedor->numero_cuenta = '';
        $vendedor->save();
    }
}
