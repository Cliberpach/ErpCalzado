<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

/**
 * Fase 2 (docs/PLANIFICATIONS/2026-07-15-plan-despacho-web-auto.md §3.3):
 * nuevo valor de catálogo "Orígenes de Venta" (tabladetalles tabla_id=36)
 * para distinguir ventas generadas desde ecommerceMerris — hasta ahora
 * solo tenía WHATSAPP/FACEBOOK/INSTAGRAM/TIKTOK.
 */
class AddEcommerceWebOrigenVenta extends Migration
{
    public function up()
    {
        if (!DB::table('tabladetalles')->where('tabla_id', 36)->where('descripcion', 'ECOMMERCE WEB')->exists()) {
            DB::table('tabladetalles')->insert([
                'descripcion' => 'ECOMMERCE WEB',
                'simbolo'     => 'ECOMMERCE WEB',
                'tabla_id'    => 36,
                'editable'    => 1,
                'estado'      => 'ACTIVO',
                'created_at'  => now(),
                'updated_at'  => now(),
            ]);
        }
    }

    public function down()
    {
        DB::table('tabladetalles')->where('tabla_id', 36)->where('descripcion', 'ECOMMERCE WEB')->delete();
    }
}
