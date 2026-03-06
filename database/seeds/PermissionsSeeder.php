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

        Permission::create([
            'name'        => 'Listar Usuarios',
            'slug'        => 'user.index',
            'description' => 'El usuario puede listar usuarios'
        ]);

        Permission::create([
            'name'        => 'Crear Usuario',
            'slug'        => 'user.create',
            'description' => 'El usuario puede crear usuarios'
        ]);

        Permission::create([
            'name'        => 'Editar Usuario',
            'slug'        => 'user.edit',
            'description' => 'El usuario puede editar usuarios'
        ]);

        Permission::create([
            'name'        => 'Ver Usuario',
            'slug'        => 'user.show',
            'description' => 'El usuario puede ver usuarios'
        ]);

        Permission::create([
            'name'        => 'Editar mi Usuario',
            'slug'        => 'userown.edit',
            'description' => 'El usuario puede editar su propio usuario'
        ]);

        Permission::create([
            'name'        => 'Ver mi Usuario',
            'slug'        => 'userown.show',
            'description' => 'El usuario puede ver su propio usuario'
        ]);

        Permission::create([
            'name'        => 'Eliminar Usuario',
            'slug'        => 'user.delete',
            'description' => 'El usuario puede eliminar usuarios'
        ]);

        Permission::create([
            'name'        => 'Mantenedor Roles',
            'slug'        => 'role.index',
            'description' => 'El usuario puede acceder al mantenedor de Roles'
        ]);

        Permission::create([
            'name'        => 'Mantenedor Almacén',
            'slug'        => 'almacen.index',
            'description' => 'El usuario puede acceder al mantenedor de Almacenes'
        ]);

        Permission::create([
            'name'        => 'Mantenedor Categoria',
            'slug'        => 'categoria.index',
            'description' => 'El usuario puede acceder al mantenedor de Categorias'
        ]);

        Permission::create([
            'name'        => 'Mantenedor Unidad de Producto',
            'slug'        => 'unidadProducto.index',
            'description' => 'El usuario puede acceder al mantenedor de unidad de producto'
        ]);

        Permission::create([
            'name'        => 'Consulta Lote Producto',
            'slug'        => 'lote_producto.index',
            'description' => 'El usuario puede acceder a la consulta de Lote Producto'
        ]);

        Permission::create([
            'name'        => 'Mantenedor Marca',
            'slug'        => 'marca.index',
            'description' => 'El usuario puede acceder al mantenedor de Marcas'
        ]);

        Permission::create([
            'name'        => 'Mantenedor Producto',
            'slug'        => 'producto.index',
            'description' => 'El usuario puede acceder al mantenedor de Productos'
        ]);

        Permission::create([
            'name'        => 'Mantenedor Notas de Ingreso',
            'slug'        => 'nota_ingreso.index',
            'description' => 'El usuario puede acceder al mantenedor de Notas de Ingreso'
        ]);

        Permission::create([
            'name'        => 'Mantenedor Notas de Salida',
            'slug'        => 'nota_salida.index',
            'description' => 'El usuario puede acceder al mantenedor de Notas de Salida'
        ]);

        Permission::create([
            'name'        => 'Mantenedor Tipos de Cliente',
            'slug'        => 'tipo_cliente.index',
            'description' => 'El usuario puede acceder al mantenedor de Tipos de Cliente'
        ]);

        Permission::create([
            'name'        => 'Mantenedor Ordenes de Compra',
            'slug'        => 'orden_compra.index',
            'description' => 'El usuario puede acceder al mantenedor de Ordenes de Compra'
        ]);

        Permission::create([
            'name'        => 'Mantenedor Documentos de compra',
            'slug'        => 'documento_compra.index',
            'description' => 'El usuario puede acceder al mantenedor de Documentos de Compra'
        ]);

        Permission::create([
            'name'        => 'Mantenedor Proveedores',
            'slug'        => 'proveedor.index',
            'description' => 'El usuario puede acceder al mantenedor de Proveedores'
        ]);

        Permission::create([
            'name'        => 'Mantenedor Cuentas de proveedor',
            'slug'        => 'cuenta_proveedor.index',
            'description' => 'El usuario puede acceder al mantenedor de Cuentas de Proveedor'
        ]);

        Permission::create([
            'name'        => 'Mantenedor Colaboradores',
            'slug'        => 'colaborador.index',
            'description' => 'El usuario puede acceder al mantenedor de Colaboradores'
        ]);

        Permission::create([
            'name'        => 'Mantenedor Empresas',
            'slug'        => 'empresa.index',
            'description' => 'El usuario puede acceder al mantenedor de Empresas'
        ]);

        Permission::create([
            'name'        => 'Mantenedor Condiciones',
            'slug'        => 'condicion.index',
            'description' => 'El usuario puede acceder al mantenedor de Condiciones'
        ]);

        Permission::create([
            'name'        => 'Mantenedor de Configuracion',
            'slug'        => 'configuracion.index',
            'description' => 'El usuario puede acceder al mantenedor de Configuración'
        ]);

        Permission::create([
            'name'        => 'Mantenedor de Personas',
            'slug'        => 'persona.index',
            'description' => 'El usuario puede acceder al mantenedor de Personas'
        ]);

        Permission::create([
            'name'        => 'Mantenedor Tablas General',
            'slug'        => 'tabla.index',
            'description' => 'El usuario puede acceder al mantenedor de Tablas Generales'
        ]);

        Permission::create([
            'name'        => 'Mantenedor Vendedores',
            'slug'        => 'vendedor.index',
            'description' => 'El usuario puede acceder al mantenedor de Vendedores'
        ]);

        Permission::create([
            'name'        => 'Mantenedor Cajas',
            'slug'        => 'caja.index',
            'description' => 'El usuario puede acceder al mantenedor de Cajas'
        ]);

        Permission::create([
            'name'        => 'Listar Movimientos Caja',
            'slug'        => 'movimiento_caja.index',
            'description' => 'El usuario puede acceder al mantenedor de Movimientos de Caja'
        ]);

        Permission::create([
            'name'        => 'Aperturar Caja',
            'slug'        => 'movimiento_caja.create',
            'description' => 'El usuario puede aperturar Caja'
        ]);

        Permission::create([
            'name'        => 'Mantenedor Clientes',
            'slug'        => 'cliente.index',
            'description' => 'El usuario puede acceder al mantenedor de Clientes'
        ]);

        Permission::create([
            'name'        => 'Mantenedor Cotizaciones',
            'slug'        => 'cotizacion.index',
            'description' => 'El usuario puede acceder al mantenedor de Notas de Cotización'
        ]);

        Permission::create([
            'name'        => 'Mantenedor Egresos',
            'slug'        => 'egreso.index',
            'description' => 'El usuario puede acceder al mantenedor de Egresos'
        ]);

        Permission::create([
            'name'        => 'Mantenedor Documentos Venta',
            'slug'        => 'documento_venta.index',
            'description' => 'El usuario puede acceder al mantenedor de Documentos de Venta'
        ]);

        Permission::create([
            'name'        => 'Mantenedor Cuentas Cliente',
            'slug'        => 'cuenta_cliente.index',
            'description' => 'El usuario puede acceder al mantenedor de Cuentas de Cliente'
        ]);

        Permission::create([
            'name'        => 'Mantenedor Guias de Remision',
            'slug'        => 'guia.index',
            'description' => 'El usuario puede acceder al mantenedor de Guias de Remisión'
        ]);

        Permission::create([
            'name'        => 'Mantenedor Notas Electronicas',
            'slug'        => 'nota_electronica.index',
            'description' => 'El usuario puede acceder al mantenedor de Notas Electrónicas'
        ]);

        Permission::create([
            'name'        => 'Vista de Utilidad Mensual',
            'slug'        => 'utilidad_mensual.index',
            'description' => 'El usuario puede acceder a la vista de Utilidad Mensual'
        ]);

        Permission::create([
            'name'        => 'Vista de Consulta Documentos',
            'slug'        => 'consulta_documento.index',
            'description' => 'El usuario puede acceder a la vista de consulta de Documentos'
        ]);

        Permission::create([
            'name'        => 'Vista de Consulta - Ventas - Documento',
            'slug'        => 'consulta_venta_documento.index',
            'description' => 'El usuario puede acceder a la vista de Consulta - Ventas - Documento'
        ]);

        Permission::create([
            'name'        => 'Vista de Consulta - Ventas - Cotizacion',
            'slug'        => 'consulta_venta_cotizacion.index',
            'description' => 'El usuario puede acceder a la vista de Consulta - Ventas - Cotización'
        ]);

        Permission::create([
            'name'        => 'Vista de Consulta - Ventas - Documentos No Enviados',
            'slug'        => 'consulta_venta_documento_no.index',
            'description' => 'El usuario puede acceder a la vista de Consulta - Ventas - Documentos No Enviados'
        ]);

        Permission::create([
            'name'        => 'Vista de Consulta Compras - Orden',
            'slug'        => 'consulta_compras_orden.index',
            'description' => 'El usuario puede acceder a la vista de Consulta - Compras - Orden'
        ]);

        Permission::create([
            'name'        => 'Vista de Consulta Compras - Documento',
            'slug'        => 'consulta_compras_documento.index',
            'description' => 'El usuario puede acceder a la vista de Consulta - Compras - Documento'
        ]);

        Permission::create([
            'name'        => 'Vista de Consulta Cuenta Proveedor',
            'slug'        => 'consulta_cuenta_proveedor.index',
            'description' => 'El usuario puede acceder a la vista de Consulta Cuenta Proveedor'
        ]);

        Permission::create([
            'name'        => 'Vista de Consulta Cuenta Cliente',
            'slug'        => 'consulta_cuenta_cliente.index',
            'description' => 'El usuario puede acceder a la vista de Consulta Cuenta Cliente'
        ]);

        Permission::create([
            'name'        => 'Vista de Consulta Nota de Salida',
            'slug'        => 'consulta_nota_salida.index',
            'description' => 'El usuario puede acceder a la vista de Consulta Nota de Salida'
        ]);

        Permission::create([
            'name'        => 'Vista de Consulta Nota de Ingreso',
            'slug'        => 'consulta_nota_ingreso.index',
            'description' => 'El usuario puede acceder a la vista de Consulta Nota de Ingreso'
        ]);

        Permission::create([
            'name'        => 'Vista de Consulta Utilidad bruta',
            'slug'        => 'consulta_utilidad_bruta.index',
            'description' => 'El usuario puede acceder a la vista de Consulta de Utilidad Bruta'
        ]);

        Permission::create([
            'name'        => 'Vista de Reporte Caja Diaria',
            'slug'        => 'reporte_cajadiaria.index',
            'description' => 'El usuario puede acceder a la vista de Reporte Caja Diaria'
        ]);

        Permission::create([
            'name'        => 'Vista de Reporte Venta',
            'slug'        => 'reporte_venta.index',
            'description' => 'El usuario puede acceder a la vista de Reporte Venta'
        ]);

        Permission::create([
            'name'        => 'Vista de Reporte Compra',
            'slug'        => 'reporte_compra.index',
            'description' => 'El usuario puede acceder a la vista de Reporte Compra'
        ]);

        Permission::create([
            'name'        => 'Vista de Reporte Nota de Salida',
            'slug'        => 'reporte_nota_salida.index',
            'description' => 'El usuario puede acceder a la vista de Reporte Nota de Salida'
        ]);

        Permission::create([
            'name'        => 'Vista de Reporte Nota de Ingreso',
            'slug'        => 'reporte_nota_ingreso.index',
            'description' => 'El usuario puede acceder a la vista de Reporte Nota de Ingreso'
        ]);

        Permission::create([
            'name'        => 'Vista de Reporte Cuentas por Cobrar',
            'slug'        => 'reporte_cuenta_cobrar.index',
            'description' => 'El usuario puede acceder a la vista de Reporte Cuentas por Cobrar'
        ]);

        Permission::create([
            'name'        => 'Vista de Reporte Cuentas por Pagar',
            'slug'        => 'reporte_cuenta_pagar.index',
            'description' => 'El usuario puede acceder a la vista de Reporte Cuentas por Pagar'
        ]);

        Permission::create([
            'name'        => 'Vista de Reporte Stock Valorizado',
            'slug'        => 'reporte_stock_valorizado.index',
            'description' => 'El usuario puede acceder a la vista de Reporte Stock Valorizado'
        ]);

        Permission::create([
            'name'        => 'Vista de Kardex Proveedor',
            'slug'        => 'kardex_proveedor.index',
            'description' => 'El usuario puede acceder a la vista de Kardex Proveedor'
        ]);

        Permission::create([
            'name'        => 'Vista de Kardex Cliente',
            'slug'        => 'kardex_cliente.index',
            'description' => 'El usuario puede acceder a la vista de Kardex Cliente'
        ]);

        Permission::create([
            'name'        => 'Consulta kardex producto',
            'slug'        => 'kardex_producto.index',
            'description' => 'El usuario puede acceder a la consulta de Kardex producto'
        ]);

        Permission::create([
            'name'        => 'Vista de Kardex Venta',
            'slug'        => 'kardex_venta.index',
            'description' => 'El usuario puede acceder a la vista de Kardex Venta'
        ]);

        Permission::create([
            'name'        => 'Vista de Kardex Nota de Salida',
            'slug'        => 'kardex_salida.index',
            'description' => 'El usuario puede acceder a la vista de Kardex Nota de Salida'
        ]);

        Permission::create([
            'name'        => 'Ventas Caja',
            'slug'        => 'ventascaja.index',
            'description' => 'El usuario puede pagar los documentos de venta'
        ]);

        Permission::create([
            'name'        => 'Ventas Resúmenes',
            'slug'        => 'resumenes.index',
            'description' => 'El usuario puede acceder a los resúmenes'
        ]);

        Permission::create([
            'name'        => 'Ventas Despacho',
            'slug'        => 'despachos.index',
            'description' => 'El usuario puede acceder al mantenedor despachos'
        ]);

        Permission::create([
            'name'        => 'Mantenedor Pedidos',
            'slug'        => 'pedido.index',
            'description' => 'El usuario puede acceder al mantenedor pedidos'
        ]);

        Permission::create([
            'name'        => 'Mantenedor Recibos Caja',
            'slug'        => 'recibos_caja.index',
            'description' => 'El usuario podrá acceder al mantenedor de recibos de caja'
        ]);

        Permission::create([
            'name'        => 'Contabilidad',
            'slug'        => 'contabilidad.index',
            'description' => 'El usuario puede acceder a consultas de documentos contables'
        ]);

        Permission::create([
            'name'        => 'Vista Detalles de Pedidos',
            'slug'        => 'pedidos_detalles.index',
            'description' => 'El usuario podrá consultar los detalles de todos los pedidos'
        ]);

        Permission::create([
            'name'        => 'Traslados',
            'slug'        => 'traslados.index',
            'description' => 'El usuario puede realizar traslados'
        ]);

        Permission::create([
            'name'        => 'Solicitudes Traslado',
            'slug'        => 'solicitudes_traslado.index',
            'description' => 'El usuario puede marcar los traslados como recibidos'
        ]);

        Permission::create([
            'name'        => 'Reservas',
            'slug'        => 'reservas.index',
            'description' => 'Gestionar reservas de pedidos'
        ]);
    }
}
