<?php

use Illuminate\Database\Seeder;
use App\Permission\Model\Role;
use App\Permission\Model\Permission;
use App\User;
use Illuminate\Support\Facades\DB;

class PermissionsSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $useradmin = User::find(1);

        $roleadmin = Role::create([
            'name' => 'ADMIN',
            'slug' => 'ADMIN',
            'description' => 'Administrador',
            'full-access' => 'SI'
        ]);

        Role::create([
            'name' => 'USER',
            'slug' => 'USER',
            'description' => 'USER',
            'full-access' => 'NO'
        ]);

        $useradmin->roles()->sync([$roleadmin->id]);

        Permission::create([
            'name'        => 'Ver graficos de información',
            'slug'        => 'dashboard',
            'description' => 'El usuario podrá ver graficos de informacion (compras, ventas, etc)'
        ]);

        // Caja Chica
        Permission::create(['name' => 'Cajas', 'slug' => 'caja.caja.index', 'description' => 'El usuario puede acceder a cajas']);
        Permission::create(['name' => 'Apertura y Cierre Caja', 'slug' => 'caja.movimiento_caja.index', 'description' => 'El usuario puede acceder a apertura y cierre de caja']);
        Permission::create(['name' => 'Egreso Caja', 'slug' => 'caja.egreso.index', 'description' => 'El usuario puede acceder a egresos de caja']);
        Permission::create(['name' => 'Recibos Caja', 'slug' => 'caja.recibos_caja.index', 'description' => 'El usuario puede acceder a recibos de caja']);

        // Compras
        Permission::create(['name' => 'Proveedores', 'slug' => 'compra.proveedor.index', 'description' => 'El usuario puede acceder a proveedores']);
        Permission::create(['name' => 'Orden de Compra', 'slug' => 'compra.orden.index', 'description' => 'El usuario puede acceder a órdenes de compra']);
        Permission::create(['name' => 'Documento Compra', 'slug' => 'compra.documento_compra.index', 'description' => 'El usuario puede acceder a documentos de compra']);

        // Ventas
        Permission::create(['name' => 'Tipos Clientes', 'slug' => 'venta.tipo_cliente.index', 'description' => 'El usuario puede acceder a tipos de clientes']);
        Permission::create(['name' => 'Clientes', 'slug' => 'venta.cliente.index', 'description' => 'El usuario puede acceder a clientes']);
        Permission::create(['name' => 'Cotizaciones', 'slug' => 'venta.cotizacion.index', 'description' => 'El usuario puede acceder a cotizaciones']);
        Permission::create(['name' => 'Documento Venta', 'slug' => 'venta.documento_venta.index', 'description' => 'El usuario puede acceder a documentos de venta']);
        Permission::create(['name' => 'Ventas Caja', 'slug' => 'venta.ventascaja.index', 'description' => 'El usuario puede acceder a ventas por caja']);
        Permission::create(['name' => 'Guías de Remisión', 'slug' => 'venta.guia.index', 'description' => 'El usuario puede acceder a guías de remisión']);
        Permission::create(['name' => 'Resúmenes', 'slug' => 'venta.resumenes.index', 'description' => 'El usuario puede acceder a resúmenes de venta']);
        Permission::create(['name' => 'Despacho', 'slug' => 'venta.despachos.index', 'description' => 'El usuario puede acceder a despachos']);
        Permission::create(['name' => 'Reservas', 'slug' => 'venta.reservas.index', 'description' => 'El usuario puede acceder a reservas']);

        // Pedidos
        Permission::create(['name' => 'Pedidos', 'slug' => 'pedido.pedido.index', 'description' => 'El usuario puede acceder a pedidos']);
        Permission::create(['name' => 'Detalles de Pedidos', 'slug' => 'pedido.pedidos_detalles.index', 'description' => 'El usuario puede acceder a detalles de pedidos']);

        // Almacén
        Permission::create(['name' => 'Almacén', 'slug' => 'almacen.almacen.index', 'description' => 'El usuario puede acceder a almacén']);
        Permission::create(['name' => 'Categorías', 'slug' => 'almacen.categoria.index', 'description' => 'El usuario puede acceder a categorías']);
        Permission::create(['name' => 'Marcas', 'slug' => 'almacen.marca.index', 'description' => 'El usuario puede acceder a marcas']);
        Permission::create(['name' => 'Modelos', 'slug' => 'almacen.modelo.index', 'description' => 'El usuario puede acceder a modelos']);
        Permission::create(['name' => 'Colores', 'slug' => 'almacen.color.index', 'description' => 'El usuario puede acceder a colores']);
        Permission::create(['name' => 'Tallas', 'slug' => 'almacen.talla.index', 'description' => 'El usuario puede acceder a tallas']);
        Permission::create(['name' => 'Productos', 'slug' => 'almacen.producto.index', 'description' => 'El usuario puede acceder a productos']);
        Permission::create(['name' => 'Nota de Ingreso', 'slug' => 'almacen.nota_ingreso.index', 'description' => 'El usuario puede acceder a notas de ingreso']);
        Permission::create(['name' => 'Nota de Salida', 'slug' => 'almacen.nota_salida.index', 'description' => 'El usuario puede acceder a notas de salida']);
        Permission::create(['name' => 'Solicitudes de Traslado', 'slug' => 'almacen.solicitudes_traslado.index', 'description' => 'El usuario puede acceder a solicitudes de traslado']);
        Permission::create(['name' => 'Traslados', 'slug' => 'almacen.traslados.index', 'description' => 'El usuario puede acceder a traslados']);
        Permission::create(['name' => 'Vehículos', 'slug' => 'almacen.vehiculos.index', 'description' => 'El usuario puede acceder a vehículos']);
        Permission::create(['name' => 'Conductores', 'slug' => 'almacen.conductores.index', 'description' => 'El usuario puede acceder a conductores']);

        // Cuentas
        Permission::create(['name' => 'Cuenta Proveedor', 'slug' => 'cuenta.cuenta_proveedor.index', 'description' => 'El usuario puede acceder a cuentas de proveedor']);
        Permission::create(['name' => 'Cuenta Cliente', 'slug' => 'cuenta.cuenta_cliente.index', 'description' => 'El usuario puede acceder a cuentas de cliente']);

        // Consulta
        Permission::create(['name' => 'Consulta Documentos', 'slug' => 'consulta.consulta_documento.index', 'description' => 'El usuario puede acceder a consulta de documentos']);
        Permission::create(['name' => 'Consulta Venta Documento', 'slug' => 'consulta.consulta_venta_documento.index', 'description' => 'El usuario puede acceder a consulta de documentos de venta']);
        Permission::create(['name' => 'Consulta Venta Cotización', 'slug' => 'consulta.consulta_venta_cotizacion.index', 'description' => 'El usuario puede acceder a consulta de cotizaciones']);
        Permission::create(['name' => 'Consulta Venta No Enviados', 'slug' => 'consulta.consulta_venta_documento_no.index', 'description' => 'El usuario puede acceder a consulta de documentos no enviados']);
        Permission::create(['name' => 'Consulta Orden de Compra', 'slug' => 'consulta.consulta_compras_orden.index', 'description' => 'El usuario puede acceder a consulta de órdenes de compra']);
        Permission::create(['name' => 'Consulta Documento Compra', 'slug' => 'consulta.consulta_compras_documento.index', 'description' => 'El usuario puede acceder a consulta de documentos de compra']);
        Permission::create(['name' => 'Consulta Cuenta Proveedor', 'slug' => 'consulta.consulta_cuenta_proveedor.index', 'description' => 'El usuario puede acceder a consulta de cuentas de proveedor']);
        Permission::create(['name' => 'Consulta Cuenta Cliente', 'slug' => 'consulta.consulta_cuenta_cliente.index', 'description' => 'El usuario puede acceder a consulta de cuentas de cliente']);
        Permission::create(['name' => 'Consulta Nota Salida', 'slug' => 'consulta.consulta_nota_salida.index', 'description' => 'El usuario puede acceder a consulta de notas de salida']);
        Permission::create(['name' => 'Consulta Nota Ingreso', 'slug' => 'consulta.consulta_nota_ingreso.index', 'description' => 'El usuario puede acceder a consulta de notas de ingreso']);
        Permission::create(['name' => 'Consulta Utilidad Bruta', 'slug' => 'consulta.consulta_utilidad_bruta.index', 'description' => 'El usuario puede acceder a consulta de utilidad bruta']);

        // Contabilidad
        Permission::create(['name' => 'Contabilidad Documentos', 'slug' => 'contabilidad.documentos.index', 'description' => 'El usuario puede acceder a documentos de contabilidad']);
        Permission::create(['name' => 'Consulta SUNAT', 'slug' => 'contabilidad.sunat.index', 'description' => 'El usuario puede consultar comprobantes en SUNAT (Validador CPE)']);

        // Reportes
        Permission::create(['name' => 'Reporte Caja Diaria', 'slug' => 'reporte.reporte_cajadiaria.index', 'description' => 'El usuario puede acceder al reporte de caja diaria']);
        Permission::create(['name' => 'Reporte Egreso', 'slug' => 'reporte.reporte_egreso.index', 'description' => 'El usuario puede acceder al reporte de egresos']);
        Permission::create(['name' => 'Reporte Ventas', 'slug' => 'reporte.reporte_venta.index', 'description' => 'El usuario puede acceder al reporte de ventas']);
        Permission::create(['name' => 'Reporte Compras', 'slug' => 'reporte.reporte_compra.index', 'description' => 'El usuario puede acceder al reporte de compras']);
        Permission::create(['name' => 'Reporte Nota Salida', 'slug' => 'reporte.reporte_nota_salida.index', 'description' => 'El usuario puede acceder al reporte de notas de salida']);
        Permission::create(['name' => 'Reporte Nota Ingreso', 'slug' => 'reporte.reporte_nota_ingreso.index', 'description' => 'El usuario puede acceder al reporte de notas de ingreso']);
        Permission::create(['name' => 'Reporte Cuentas por Cobrar', 'slug' => 'reporte.reporte_cuenta_cobrar.index', 'description' => 'El usuario puede acceder al reporte de cuentas por cobrar']);
        Permission::create(['name' => 'Reporte Cuentas por Pagar', 'slug' => 'reporte.reporte_cuenta_pagar.index', 'description' => 'El usuario puede acceder al reporte de cuentas por pagar']);
        Permission::create(['name' => 'Reporte Stock Valorizado', 'slug' => 'reporte.reporte_stock_valorizado.index', 'description' => 'El usuario puede acceder al reporte de stock valorizado']);

        // Kardex
        Permission::create(['name' => 'Kardex Proveedor', 'slug' => 'kardex.kardex_proveedor.index', 'description' => 'El usuario puede acceder al kardex de proveedor']);
        Permission::create(['name' => 'Kardex Cliente', 'slug' => 'kardex.kardex_cliente.index', 'description' => 'El usuario puede acceder al kardex de cliente']);
        Permission::create(['name' => 'Kardex Producto', 'slug' => 'kardex.kardex_producto.index', 'description' => 'El usuario puede acceder al kardex de producto']);
        Permission::create(['name' => 'Kardex Venta', 'slug' => 'kardex.kardex_venta.index', 'description' => 'El usuario puede acceder al kardex de venta']);
        Permission::create(['name' => 'Kardex Cuenta', 'slug' => 'kardex.kardex_cuenta.index', 'description' => 'El usuario puede acceder al kardex de cuenta']);

        // Mantenimiento
        Permission::create(['name' => 'Colaboradores', 'slug' => 'mantenimiento.colaborador.index', 'description' => 'El usuario puede acceder a colaboradores']);
        Permission::create(['name' => 'Empresas', 'slug' => 'mantenimiento.empresa.index', 'description' => 'El usuario puede acceder a empresas']);
        Permission::create(['name' => 'Sedes', 'slug' => 'mantenimiento.sedes.index', 'description' => 'El usuario puede acceder a sedes']);
        Permission::create(['name' => 'Condiciones de Pago', 'slug' => 'mantenimiento.condicion.index', 'description' => 'El usuario puede acceder a condiciones de pago']);
        Permission::create(['name' => 'Tablas Generales', 'slug' => 'mantenimiento.tabla.index', 'description' => 'El usuario puede acceder a tablas generales']);
        Permission::create(['name' => 'Configuración', 'slug' => 'mantenimiento.configuracion.index', 'description' => 'El usuario puede acceder a configuración']);
        Permission::create(['name' => 'Métodos de Entrega', 'slug' => 'mantenimiento.metodo_entrega.index', 'description' => 'El usuario puede acceder a métodos de entrega']);
        Permission::create(['name' => 'Cuentas Bancarias', 'slug' => 'mantenimiento.cuentas.index', 'description' => 'El usuario puede acceder a cuentas bancarias']);
        Permission::create(['name' => 'Tipo de Pago', 'slug' => 'mantenimiento.tipo_pago.index', 'description' => 'El usuario puede acceder a tipos de pago']);
        Permission::create(['name' => 'Mantenedor Promociones', 'slug' => 'mantenimiento.promociones.index', 'description' => 'Mantenedor Promociones']);

        // Seguridad
        Permission::create(['name' => 'Usuarios', 'slug' => 'seguridad.user.index', 'description' => 'El usuario puede acceder a usuarios']);
        Permission::create(['name' => 'Roles', 'slug' => 'seguridad.role.index', 'description' => 'El usuario puede acceder a roles']);
    }
}
