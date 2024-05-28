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
            'name'=>'ADMIN',
            'slug'=>'ADMIN',
            'description'=>'Administrador',
            'full-access'=>'SI'
        ]);

        Role::create([
            'name'=>'USER',
            'slug'=>'USER',
            'description'=>'USER',
            'full-access'=>'NO'
        ]);

        $useradmin->roles()->sync([$roleadmin->id]);

        //Dahsboard permission

        Permission::create([
            'name'  => 'Ver graficos de información',
            'slug' => 'dashboard',
            'description' => 'El usuario podrá ver graficos de informacion (compras, ventas, etc)'
        ]);

        //User permission

        Permission::create([
            'name'  => 'Listar Usuarios',
            'slug'=>'user.index',
            'description'=>'El usuario puede listar usuarios'
        ]);


        Permission::create([
            'name'  => 'Crear Usuario',
            'slug'=>'user.create',
            'description'=>'El usuario puede crear usuarios'
        ]);


        Permission::create([
            'name'  => 'Editar Usuario',
            'slug'=>'user.edit',
            'description'=>'El usuario puede editar usuarios'
        ]);


        Permission::create([
            'name'  => 'Ver Usuario',
            'slug'=>'user.show',
            'description'=>'El usuario puede ver usuarios'
        ]);


        Permission::create([
            'name'  => 'Editar mi Usuario',
            'slug'=>'userown.edit',
            'description'=>'El usuario puede editar su propio usuario'
        ]);


        Permission::create([
            'name'  => 'Ver mi Usuario',
            'slug'=>'userown.show',
            'description'=>'El usuario puede ver su propio usuario'
        ]);


        Permission::create([
            'name'  => 'Eliminar Usuario',
            'slug'=>'user.delete',
            'description'=>'El usuario puede eliminar usuarios'
        ]);


        //Roles permission

        Permission::create([
            'name'  => 'Mantenedor Roles',
            'slug'=>'role.index',
            'description'=>'El usuario puede acceder al mantenedor de Roles'
        ]);

        //Almacenes permission

        Permission::create([
            'name'  => 'Mantenedor Almacén',
            'slug'=>'almacen.index',
            'description'=>'El usuario puede acceder al mantenedor de Almacenes'
        ]);

        //Categoria permission

        Permission::create([
            'name'  => 'Mantenedor Categoria',
            'slug'=>'categoria.index',
            'description'=>'El usuario puede acceder al mantenedor de Categorias'
        ]);

        //Unidad de Producto permission

        Permission::create([
            'name'  => 'Mantenedor Unidad de Producto',
            'slug'=>'unidadProducto.index',
            'description'=>'El usuario puede acceder al mantenedor de unidad de Producto'
        ]);

        //Lote Produco permission

        Permission::create([
            'name'  => 'Consulta Lote Producto',
            'slug'=>'lote_producto.index',
            'description'=>'El usuario puede acceder a la consulta de Lote Productos'
        ]);

        //Marca permission

        Permission::create([
            'name'  => 'Mantenedor Marca',
            'slug'=>'marca.index',
            'description'=>'El usuario puede acceder al mantenedor de Marcas'
        ]);

        //Producto permission

        Permission::create([
            'name'  => 'Mantenedor Producto',
            'slug'=>'producto.index',
            'description'=>'El usuario puede acceder al mantenedor de Productos'
        ]);

        //Nota Ingreso permission

        Permission::create([
            'name'  => 'Mantenedor Notas de Ingreso',
            'slug'=>'nota_ingreso.index',
            'description'=>'El usuario puede acceder al mantenedor de Notas de Ingreso'
        ]);

        //Nota Salida permission

        Permission::create([
            'name'  => 'Mantenedor Notas de Salida',
            'slug'=>'nota_salida.index',
            'description'=>'El usuario puede acceder al mantenedor de Notas de Salida'
        ]);

        //Tipo Cliente permission

        Permission::create([
            'name'  => 'Mantenedor Tipos de Cliente',
            'slug'=>'tipo_cliente.index',
            'description'=>'El usuario puede acceder al mantenedor de Tipos de Cliente'
        ]);

        //Orden compra permission

        Permission::create([
            'name'  => 'Mantenedor Ordenes de Compra',
            'slug'=>'orden_compra.index',
            'description'=>'El usuario puede acceder al mantenedor de Ordenes de Compra'
        ]);

        //Documento de compra permission

        Permission::create([
            'name'  => 'Mantenedor Documentos de compra',
            'slug'=>'documento_compra.index',
            'description'=>'El usuario puede acceder al mantenedor de Documentos de Compra'
        ]);

        //Proveedor permission

        Permission::create([
            'name'  => 'Mantenedor Proveedores',
            'slug'=>'proveedor.index',
            'description'=>'El usuario puede acceder al mantenedor de Proveedores'
        ]);

        //Cuenta proveedor permission

        Permission::create([
            'name'  => 'Mantenedor Cuentas de proveedor',
            'slug'=>'cuenta_proveedor.index',
            'description'=>'El usuario puede acceder al mantenedor de Cuentas de Proveedor'
        ]);

        //Colaborador permission

        Permission::create([
            'name'  => 'Mantenedor Colaboradores',
            'slug'=>'colaborador.index',
            'description'=>'El usuario puede acceder al mantenedor de Colaboradores'
        ]);

        //Empresa permission

        Permission::create([
            'name'  => 'Mantenedor Empresas',
            'slug'=>'empresa.index',
            'description'=>'El usuario puede acceder al mantenedor de Empresas'
        ]);

         //Condicion permission

         Permission::create([
            'name'  => 'Mantenedor Condiciones',
            'slug'=>'condicion.index',
            'description'=>'El usuario puede acceder al mantenedor de Condiciones'
        ]);

        //Configuracion permission

        Permission::create([
            'name'  => 'Mantenedor de Configuracion',
            'slug' => 'configuracion.index',
            'description' => 'El usuario puede acceder al mantenedor de Configuracion'
        ]);

        //Personas permission

        Permission::create([
            'name'  => 'Mantenedor de Personas',
            'slug'=>'persona.index',
            'description'=>'El usuario puede acceder al mantenedor de Personas'
        ]);

        //Tabla permission

        Permission::create([
            'name'  => 'Mantenedor Tablas General',
            'slug'=>'tabla.index',
            'description'=>'El usuario puede acceder al mantenedor de Tablas Generales'
        ]);

        //Vendedor permission

        Permission::create([
            'name'  => 'Mantenedor Vendedores',
            'slug'=>'vendedor.index',
            'description'=>'El usuario puede acceder al mantenedor de Vendedores'
        ]);

        //MANTENEDOR METODO ENTREGA
        Permission::create([
            'name'  => 'Mantenedor Métodos Entrega',
            'slug'=>'metodo_entrega.index',
            'description'=>'El usuario puede acceder al mantenedor de métodos entrega'
        ]);

        //Caja permission

        Permission::create([
            'name'  => 'Mantenedor Cajas',
            'slug'=>'caja.index',
            'description'=>'El usuario puede acceder al mantenedor de Cajas'
        ]);

        //Movimiento Caja permission

        Permission::create([
            'name'  => 'Listar Movimientos Caja',
            'slug'=>'movimiento_caja.index',
            'description'=>'El usuario puede acceder al mantenedor de Movimientos Caja'
        ]);

        Permission::create([
            'name'  => 'Aperturar Caja',
            'slug'=>'movimiento_caja.create',
            'description'=>'El usuario puede aperturar Caja'
        ]);

        //Cliente permission

        Permission::create([
            'name'  => 'Mantenedor Clientes',
            'slug'=>'cliente.index',
            'description'=>'El usuario puede acceder al mantenedor de Clientes'
        ]);

        //Cotizacion permission

        Permission::create([
            'name'  => 'Mantenedor Cotizaciones',
            'slug'=>'cotizacion.index',
            'description'=>'El usuario puede acceder al mantenedor de Notas de Salida'
        ]);

        //Egreso permission

        Permission::create([
            'name'  => 'Mantenedor Egresos',
            'slug'=>'egreso.index',
            'description'=>'El usuario puede acceder al mantenedor de Egresos'
        ]);

        //Documento Venta permission

        Permission::create([
            'name'  => 'Mantenedor Documentos Venta',
            'slug'=>'documento_venta.index',
            'description'=>'El usuario puede acceder al mantenedor de Documentos de Venta'
        ]);

        //Resúmenes Permission
        Permission::create([
            'name'  => 'Mantenedor Resúmenes',
            'slug'=>'resumenes.index',
            'description'=>'El usuario puede acceder al mantenedor de Resúmenes'
        ]);

        //Ventas Caja Permission
        Permission::create([
            'name'  => 'Ventas Caja',
            'slug'=>'ventascaja.index',
            'description'=>'El usuario puede acceder a Ventas Caja'
        ]);

        //Ventas Pedidos Permission
        Permission::create([
            'name'  => 'Mantenedor Pedidos',
            'slug'=>'pedidos.index',
            'description'=>'El usuario puede acceder al mantenedor de Pedidos'
        ]);

        //Cuenta Cliente permission
        Permission::create([
            'name'  => 'Mantenedor Cuentas Cliente',
            'slug'=>'cuenta_cliente.index',
            'description'=>'El usuario puede acceder al mantenedor de Cuentas Cliente'
        ]);

        //Guia permission

        Permission::create([
            'name'  => 'Mantenedor Guias de Remision',
            'slug'=>'guia.index',
            'description'=>'El usuario puede acceder al mantenedor de Guias de Remision'
        ]);

        //Nota Electronica permission

        Permission::create([
            'name'  => 'Mantenedor Notas Electronicas',
            'slug'=>'nota_electronica.index',
            'description'=>'El usuario puede acceder al mantenedor de Notas Electronicas'
        ]);

        //Utilidad Mensual
        Permission::create([
            'name'  => 'Vista de Utilidad Mensual',
            'slug'=>'utilidad_mensual.index',
            'description'=>'El usuario puede acceder a la vista de Utilidad Mensual'
        ]);

        /*Nuevos permisos*/

        /*====================CONSULTAS=====================*/

        //Consulta documentos
        Permission::create([
            'name'  => 'Vista de Consulta Documentos',
            'slug'=>'consulta_documento.index',
            'description'=>'El usuario puede acceder a la vista de consulta documentos'
        ]);

        //Consulta - venta - documento
        Permission::create([
            'name'  => 'Vista de Consulta - Ventas - Documento',
            'slug'=>'consulta_venta_documento.index',
            'description'=>'El usuario puede acceder a la vista de Consulta - Ventas - Documento'
        ]);

        //Consulta - venta - cotizacion
        Permission::create([
            'name'  => 'Vista de Consulta - Ventas - Cotizacion',
            'slug'=>'consulta_venta_cotizacion.index',
            'description'=>'El usuario puede acceder a la vista de Consulta - Ventas - Cotizacion'
        ]);

        //Consulta - venta - Documentos No Enviados
        Permission::create([
            'name'  => 'Vista de Consulta - Ventas - Documentos No Enviados',
            'slug'=>'consulta_venta_documento_no.index',
            'description'=>'El usuario puede acceder a la vista de Consulta - Ventas - Documentos No Enviados'
        ]);

        //Consulta Compras - Orden
        Permission::create([
            'name'  => 'Vista de Consulta Compras - Orden',
            'slug'=>'consulta_compras_orden.index',
            'description'=>'El usuario puede acceder a la vista de Consulta - Compras - Orden'
        ]);

        //Consulta Compras - Documento
        Permission::create([
            'name'  => 'Vista de Consulta Compras - Documento',
            'slug'=>'consulta_compras_documento.index',
            'description'=>'El usuario puede acceder a la vista de Consulta - Compras - Documento'
        ]);

        //Consulta Compras - Cuenta Proveedor
        Permission::create([
            'name'  => 'Vista de Consulta Cuenta Proveedor',
            'slug'=>'consulta_cuenta_proveedor.index',
            'description'=>'El usuario puede acceder a la vista de Consulta Cuenta Proveedor'
        ]);

        //Consulta Compras - Cuenta Cliente
        Permission::create([
            'name'  => 'Vista de Consulta Cuenta Cliente',
            'slug'=>'consulta_cuenta_cliente.index',
            'description'=>'El usuario puede acceder a la vista de Consulta Cuenta Cliente'
        ]);

        //Consulta Compras - Nota de Salida
        Permission::create([
            'name'  => 'Vista de Consulta Nota de Salida',
            'slug'=>'consulta_nota_salida.index',
            'description'=>'El usuario puede acceder a la vista de Consulta Nota de Salida'
        ]);

        //Consulta Compras - Nota de Ingreso
        Permission::create([
            'name'  => 'Vista de Consulta Nota de Ingreso',
            'slug'=>'consulta_nota_ingreso.index',
            'description'=>'El usuario puede acceder a la vista de Consulta Nota de Ingreso'
        ]);

        //Consulta Compras - Utilidad bruta
        Permission::create([
            'name'  => 'Vista de Consulta Utilidad bruta',
            'slug'=>'consulta_utilidad_bruta.index',
            'description'=>'El usuario puede acceder a la vista de Consulta Utilidad Bruta'
        ]);


        /*====================REPORTES=====================*/

        //Reporte - Caja Diaria
        Permission::create([
            'name'  => 'Vista de Reporte Caja Diaria',
            'slug'=>'reporte_cajadiaria.index',
            'description'=>'El usuario puede acceder a la vista de Reporte Caja Diaria'
        ]);

        //Reporte - Venta
        Permission::create([
            'name'  => 'Vista de Reporte Venta',
            'slug'=>'reporte_venta.index',
            'description'=>'El usuario puede acceder a la vista de Reporte Venta'
        ]);

        //Reporte - Compra
        Permission::create([
            'name'  => 'Vista de Reporte Compra',
            'slug'=>'reporte_compra.index',
            'description'=>'El usuario puede acceder a la vista de Reporte Compra'
        ]);

        //Reporte - Nota de Salida
        Permission::create([
            'name'  => 'Vista de Reporte Nota de Salida',
            'slug'=>'reporte_nota_salida.index',
            'description'=>'El usuario puede acceder a la vista de Reporte Nota de Salida'
        ]);

        //Reporte - Nota de Ingreso
        Permission::create([
            'name'  => 'Vista de Reporte Nota de Ingreso',
            'slug'=>'reporte_nota_ingreso.index',
            'description'=>'El usuario puede acceder a la vista de Reporte Nota de Ingreso'
        ]);

        //Reporte - Cuentas por Cobrar
        Permission::create([
            'name'  => 'Vista de Reporte Cuentas por Cobrar',
            'slug'=>'reporte_cuenta_cobrar.index',
            'description'=>'El usuario puede acceder a la vista de Reporte Cuentas por Cobrar'
        ]);

        //Reporte - Cuentas por Pagar
        Permission::create([
            'name'  => 'Vista de Reporte Cuentas por Pagar',
            'slug'=>'reporte_cuenta_pagar.index',
            'description'=>'El usuario puede acceder a la vista de Reporte Cuentas por Pagar'
        ]);

        //Reporte - Stock - Valorizado
        Permission::create([
            'name'  => 'Vista de Reporte Stock Valorizado',
            'slug'=>'reporte_stock_valorizado.index',
            'description'=>'El usuario puede acceder a la vista de Reporte Stock Valorizado'
        ]);

        //Kardex - Proveedor
        Permission::create([
            'name'  => 'Vista de Kardex Proveedor',
            'slug'=>'kardex_proveedor.index',
            'description'=>'El usuario puede acceder a la vista de Kardex Proveedor'
        ]);

        //Kardex - Cliente
        Permission::create([
            'name'  => 'Vista de Kardex Cliente',
            'slug'=>'kardex_cliente.index',
            'description'=>'El usuario puede acceder a la vista de Kardex Cliente'
        ]);

        //Kardex producto permission

        Permission::create([
            'name'  => 'Consulta kardex producto',
            'slug'=>'kardex_producto.index',
            'description'=>'El usuario puede acceder a la consulta de Kardex producto'
        ]);

        //Kardex - Ventas
        Permission::create([
            'name'  => 'Vista de Kardex Venta',
            'slug'=>'kardex_venta.index',
            'description'=>'El usuario puede acceder a la vista de Kardex Venta'
        ]);

        //Kardex - Salidas
        Permission::create([
            'name'  => 'Vista de Kardex Nota de Salida',
            'slug'=>'kardex_salida.index',
            'description'=>'El usuario puede acceder a la vista de Kardex Nota de Salida'
        ]);

        //VENTAS CAJA
        Permission::create([
            'name'  => 'Ventas Caja',
            'slug'=>'ventascaja.index',
            'description'=>'El usuario puede pagar los documentos de venta.'
        ]);

        //====== VENTAS RESÚMENES ==========
        Permission::create([
            'name'  => 'Ventas Resúmenes',
            'slug'=>'resumenes.index',
            'description'=>'El usuario puede acceder a los resúmenes.'
        ]);

        //====== VENTAS DESPACHOS ==========
        Permission::create([
            'name'  => 'Ventas Despacho',
            'slug'=>'despachos.index',
            'description'=>'El usuario puede acceder al mantenedor despachos.'
        ]);

        //====== VENTAS PEDIDOS ==========
        Permission::create([
            'name'  => 'Ventas Despacho',
            'slug'=>'pedidos.index',
            'description'=>'El usuario puede acceder al mantenedor pedidos.'
        ]);

        //====== VENTAS RECIBOS CAJA ==========
        Permission::create([
            'name'  => 'Mantenedor Recibos Caja',
            'slug'=>'recibos_caja.index',
            'description'=>'El usuario podrá acceder al mantenedor de recibos caja.'
        ]);

        //====== VENTAS RECIBOS CAJA ==========
        Permission::create([
            'name'  => 'Contabilidad',
            'slug'=>'contabilidad.index',
            'description'=>'El usuario puede acceder a consultas de de documentos de venta.'
        ]);
    }
}
