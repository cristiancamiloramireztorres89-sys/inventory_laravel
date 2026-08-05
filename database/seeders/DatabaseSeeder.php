<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        // 1. Roles iniciales
        DB::table('roles')->updateOrInsert(
            ['id_rol' => 1],
            ['nombre' => 'administrador']
        );
        DB::table('roles')->updateOrInsert(
            ['id_rol' => 2],
            ['nombre' => 'vendedor']
        );

        // 2. Usuarios por defecto (Admin, Vendedor 1 y Vendedor 2)
        DB::table('usuarios')->updateOrInsert(
            ['id_usuario' => 1],
            [
                'id_rol' => 1,
                'nombre' => 'Administrador Principal',
                'correo' => 'admin@inventario.com',
                'contrasena' => Hash::make('admin123'),
                'activo' => true,
            ]
        );

        DB::table('usuarios')->updateOrInsert(
            ['id_usuario' => 2],
            [
                'id_rol' => 2,
                'nombre' => 'Vendedor Uno',
                'correo' => 'vendedor@inventario.com',
                'contrasena' => Hash::make('vendedor123'),
                'activo' => true,
            ]
        );

        DB::table('usuarios')->updateOrInsert(
            ['id_usuario' => 3],
            [
                'id_rol' => 2,
                'nombre' => 'Vendedor Dos',
                'correo' => 'vendedor2@inventario.com',
                'contrasena' => Hash::make('vendedor123'),
                'activo' => true,
            ]
        );

        // 3. Categorías iniciales
        DB::table('categorias')->updateOrInsert(
            ['id_categoria' => 1],
            ['nombre' => 'Electrónica', 'descripcion' => 'Dispositivos y accesorios electrónicos']
        );
        DB::table('categorias')->updateOrInsert(
            ['id_categoria' => 2],
            ['nombre' => 'Oficina y Papelería', 'descripcion' => 'Artículos para oficina']
        );

        // 4. Proveedor inicial
        DB::table('proveedores')->updateOrInsert(
            ['id_proveedor' => 1],
            [
                'nombre' => 'Distribuciones Globales S.A.S',
                'telefono' => '+57 3188145842',
                'correo' => 'proveedor@inventario.com',
            ]
        );

        // 5. Clientes iniciales
        DB::table('clientes')->updateOrInsert(
            ['id_cliente' => 1],
            [
                'nombre' => 'Cliente General',
                'telefono' => '3001234567',
                'correo' => 'cliente@inventario.com',
            ]
        );

        DB::table('clientes')->updateOrInsert(
            ['id_cliente' => 2],
            [
                'nombre' => 'Comercializadora del Norte',
                'telefono' => '3159876543',
                'correo' => 'comercial@norte.com',
            ]
        );

        // 6. Productos iniciales
        DB::table('productos')->updateOrInsert(
            ['id_producto' => 1],
            [
                'id_categoria' => 1,
                'nombre' => 'Teclado Mecánico RGB',
                'marca' => 'Logitech',
                'stock_actual' => 25,
                'stock_minimo' => 5,
                'precio_venta' => 120000.00,
                'descripcion' => 'Teclado mecánico con switches red y retroiluminación RGB',
                'imagen' => null,
            ]
        );

        DB::table('productos')->updateOrInsert(
            ['id_producto' => 2],
            [
                'id_categoria' => 1,
                'nombre' => 'Mouse Ergonómico Inalámbrico',
                'marca' => 'Genius',
                'stock_actual' => 40,
                'stock_minimo' => 10,
                'precio_venta' => 45000.00,
                'descripcion' => 'Mouse inalámbrico 2.4GHz con sensor óptico de alta precisión',
                'imagen' => null,
            ]
        );

        // 7. Compras de prueba
        DB::table('compras')->updateOrInsert(
            ['id_compra' => 1],
            [
                'id_usuario' => 1, // Registrada por Admin
                'id_proveedor' => 1,
                'fecha' => now()->subDays(3),
                'subtotal' => 2000000.00,
                'iva' => 380000.00,
                'total' => 2380000.00,
            ]
        );

        DB::table('compras')->updateOrInsert(
            ['id_compra' => 2],
            [
                'id_usuario' => 2, // Registrada por Vendedor Uno
                'id_proveedor' => 1,
                'fecha' => now()->subDays(1),
                'subtotal' => 900000.00,
                'iva' => 171000.00,
                'total' => 1071000.00,
            ]
        );

        // 8. Detalles de compras
        DB::table('detalle_compra')->updateOrInsert(
            ['id_detalle_compra' => 1],
            [
                'id_compra' => 1,
                'id_producto' => 1,
                'cantidad' => 20,
                'precio_unitario' => 100000.00,
                'subtotal' => 2000000.00,
            ]
        );

        DB::table('detalle_compra')->updateOrInsert(
            ['id_detalle_compra' => 2],
            [
                'id_compra' => 2,
                'id_producto' => 2,
                'cantidad' => 30,
                'precio_unitario' => 30000.00,
                'subtotal' => 900000.00,
            ]
        );

        // 9. Ventas de prueba (Asignadas a Vendedor 1 y Vendedor 2)
        DB::table('ventas')->updateOrInsert(
            ['id_venta' => 1],
            [
                'id_usuario' => 2, // Venta de Vendedor Uno
                'id_cliente' => 1,
                'fecha' => now()->subHours(5),
                'subtotal' => 240000.00,
                'iva' => 45600.00,
                'total' => 285600.00,
            ]
        );

        DB::table('ventas')->updateOrInsert(
            ['id_venta' => 2],
            [
                'id_usuario' => 3, // Venta de Vendedor Dos
                'id_cliente' => 2,
                'fecha' => now()->subHours(2),
                'subtotal' => 90000.00,
                'iva' => 17100.00,
                'total' => 107100.00,
            ]
        );

        // 10. Detalles de ventas
        DB::table('detalle_venta')->updateOrInsert(
            ['id_detalle_venta' => 1],
            [
                'id_venta' => 1,
                'id_producto' => 1,
                'cantidad' => 2,
                'costo_unitario' => 100000.00,
                'precio_unitario' => 120000.00,
                'subtotal' => 240000.00,
            ]
        );

        DB::table('detalle_venta')->updateOrInsert(
            ['id_detalle_venta' => 2],
            [
                'id_venta' => 2,
                'id_producto' => 2,
                'cantidad' => 2,
                'costo_unitario' => 30000.00,
                'precio_unitario' => 45000.00,
                'subtotal' => 90000.00,
            ]
        );
    }
}
