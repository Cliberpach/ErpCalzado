<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

/**
 * Vacía users.contra, que guardaba la contraseña de cada usuario en texto
 * plano junto al hash bcrypt.
 *
 * La columna NO se elimina todavía: primero conviene desplegar el código que
 * ya no la lee ni la escribe y comprobar en producción que nada se rompe. El
 * borrado de la columna irá en una migración posterior.
 *
 * down() NO RESTAURA NADA, y es intencionado: el objetivo es que esos valores
 * dejen de existir. No se guarda copia en ninguna parte. Revertir esta
 * migración sólo vuelve a permitir escribir en la columna, no devuelve las
 * contraseñas.
 *
 * doctrine/dbal no está instalado, así que el cambio de nulabilidad va con
 * DB::statement y SQL explícito en vez de ->change().
 *
 * Compatible con PHP 7.4 y 8.3: sin tipos de unión, sin match, sin promoción
 * de propiedades en el constructor y sin enum.
 */
class NullUsersContra extends Migration
{
    public function up()
    {
        if (!Schema::hasTable('users') || !Schema::hasColumn('users', 'contra')) {
            return; // idempotente: si la columna ya no está, no hay nada que hacer
        }

        // La columna es NOT NULL en el esquema original: hay que permitir NULL
        // antes de vaciarla.
        DB::statement('ALTER TABLE `users` MODIFY `contra` VARCHAR(191) NULL DEFAULT NULL');

        DB::table('users')->whereNotNull('contra')->update(array('contra' => null));
    }

    public function down()
    {
        // A propósito: no se restauran los valores. No existe copia de ellos.
        // Sólo se deja la columna como estaba en cuanto a nulabilidad, para que
        // el esquema vuelva a ser el de antes.
        if (!Schema::hasTable('users') || !Schema::hasColumn('users', 'contra')) {
            return;
        }

        DB::table('users')->whereNull('contra')->update(array('contra' => ''));
        DB::statement("ALTER TABLE `users` MODIFY `contra` VARCHAR(191) NOT NULL");
    }
}
