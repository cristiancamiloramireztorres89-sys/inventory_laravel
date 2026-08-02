<?php

namespace Tests\Feature;

use App\Models\Categoria;
use App\Models\Producto;
use App\Models\Usuario;
use Database\Seeders\DatabaseSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class CrudTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        $this->seed(DatabaseSeeder::class);
    }

    public function test_admin_can_create_user(): void
    {
        $admin = Usuario::where('correo', 'admin@inventario.com')->first();

        $response = $this->actingAs($admin)->post(route('admin.usuarios.store'), [
            'nombre' => 'Nuevo Vendedor',
            'correo' => 'nuevo@inventario.com',
            'id_rol' => 2,
            'contrasena' => 'password123',
        ]);

        $response->assertRedirect(route('admin.usuarios'));
        $this->assertDatabaseHas('usuarios', [
            'correo' => 'nuevo@inventario.com',
            'activo' => true,
        ]);
    }

    public function test_admin_can_update_user(): void
    {
        $admin = Usuario::where('correo', 'admin@inventario.com')->first();
        $vendedor = Usuario::where('correo', 'vendedor@inventario.com')->first();

        $response = $this->actingAs($admin)->put(route('admin.usuarios.update', $vendedor), [
            'nombre' => 'Vendedor Actualizado',
            'correo' => 'vendedor_nuevo@inventario.com',
            'id_rol' => 2,
        ]);

        $response->assertRedirect(route('admin.usuarios'));
        $this->assertDatabaseHas('usuarios', ['nombre' => 'Vendedor Actualizado']);
    }

    public function test_admin_can_deactivate_and_reactivate_user(): void
    {
        $admin = Usuario::where('correo', 'admin@inventario.com')->first();
        $vendedor = Usuario::where('correo', 'vendedor@inventario.com')->first();

        // 1. Desactivar
        $response = $this->actingAs($admin)->patch(route('admin.usuarios.toggle', $vendedor));
        $response->assertRedirect(route('admin.usuarios'));
        $this->assertDatabaseHas('usuarios', [
            'id_usuario' => $vendedor->id_usuario,
            'activo' => false,
        ]);

        // 2. Reactivar
        $response = $this->actingAs($admin)->patch(route('admin.usuarios.toggle', $vendedor));
        $response->assertRedirect(route('admin.usuarios'));
        $this->assertDatabaseHas('usuarios', [
            'id_usuario' => $vendedor->id_usuario,
            'activo' => true,
        ]);
    }

    public function test_admin_cannot_deactivate_themselves(): void
    {
        $admin = Usuario::where('correo', 'admin@inventario.com')->first();

        $response = $this->actingAs($admin)->patch(route('admin.usuarios.toggle', $admin));

        $response->assertRedirect(route('admin.usuarios'));
        $this->assertDatabaseHas('usuarios', [
            'id_usuario' => $admin->id_usuario,
            'activo' => true,
        ]);
    }

    public function test_deactivated_user_cannot_login(): void
    {
        $vendedor = Usuario::where('correo', 'vendedor@inventario.com')->first();
        $vendedor->activo = false;
        $vendedor->save();

        $response = $this->post(route('login.post'), [
            'correo' => 'vendedor@inventario.com',
            'contrasena' => 'vendedor123',
        ]);

        $response->assertSessionHasErrors('correo');
        $this->assertGuest();
    }

    public function test_admin_can_create_category(): void
    {
        $admin = Usuario::where('correo', 'admin@inventario.com')->first();

        $response = $this->actingAs($admin)->post(route('admin.categorias.store'), [
            'nombre' => 'Mobiliario',
            'descripcion' => 'Muebles de oficina',
        ]);

        $response->assertRedirect(route('admin.categorias'));
        $this->assertDatabaseHas('categorias', ['nombre' => 'Mobiliario']);
    }

    public function test_admin_can_create_product(): void
    {
        $admin = Usuario::where('correo', 'admin@inventario.com')->first();
        $cat = Categoria::first();

        $response = $this->actingAs($admin)->post(route('admin.productos.store'), [
            'nombre' => 'Monitor 27 Pulgadas',
            'id_categoria' => $cat->id_categoria,
            'marca' => 'Dell',
            'stock_actual' => 15,
            'stock_minimo' => 3,
            'precio_venta' => 850000.00,
            'descripcion' => 'Monitor IPS Full HD',
        ]);

        $response->assertRedirect(route('admin.productos'));
        $this->assertDatabaseHas('productos', ['nombre' => 'Monitor 27 Pulgadas']);
    }

    public function test_admin_can_create_product_with_image(): void
    {
        $admin = Usuario::where('correo', 'admin@inventario.com')->first();
        $cat = Categoria::first();

        $file = \Illuminate\Http\UploadedFile::fake()->image('teclado_gamer.jpg');

        $response = $this->actingAs($admin)->post(route('admin.productos.store'), [
            'nombre' => 'Teclado Mecánico Custom',
            'id_categoria' => $cat->id_categoria,
            'marca' => 'Corsair',
            'stock_actual' => 10,
            'stock_minimo' => 2,
            'precio_venta' => 350000.00,
            'descripcion' => 'Teclado con switches yellow',
            'imagen' => $file,
        ]);

        $response->assertRedirect(route('admin.productos'));

        $producto = Producto::where('nombre', 'Teclado Mecánico Custom')->first();
        $this->assertNotNull($producto);
        $this->assertNotNull($producto->imagen);
        // Assert that only the filename is stored (not a full url or long path)
        $this->assertStringEndsWith('.jpg', $producto->imagen);
        $this->assertStringNotContainsString('/', $producto->imagen);
        $this->assertStringNotContainsString('\\', $producto->imagen);

        // Clean up created fake file from public/uploads/productos if created
        if (file_exists(public_path('uploads/productos/' . $producto->imagen))) {
            @unlink(public_path('uploads/productos/' . $producto->imagen));
        }
    }

    public function test_admin_can_toggle_product_status(): void
    {
        $admin = Usuario::where('correo', 'admin@inventario.com')->first();
        $producto = Producto::first();

        // 1. Desactivar producto
        $response = $this->actingAs($admin)->patch(route('admin.productos.toggle', $producto));
        $response->assertRedirect(route('admin.productos'));
        $this->assertDatabaseHas('productos', [
            'id_producto' => $producto->id_producto,
            'activo' => false,
        ]);

        // 2. Reactivar producto
        $response = $this->actingAs($admin)->patch(route('admin.productos.toggle', $producto));
        $response->assertRedirect(route('admin.productos'));
        $this->assertDatabaseHas('productos', [
            'id_producto' => $producto->id_producto,
            'activo' => true,
        ]);
    }

    public function test_deactivated_product_is_hidden_from_seller_catalog(): void
    {
        $vendedor = Usuario::where('correo', 'vendedor@inventario.com')->first();
        $producto = Producto::where('nombre', 'Teclado Mecánico RGB')->first();

        // Desactivar el producto
        $producto->activo = false;
        $producto->save();

        $response = $this->actingAs($vendedor)->get(route('vendedor.productos'));

        $response->assertStatus(200);
        // Deactivated product must NOT appear in seller catalog
        $response->assertDontSee('Teclado Mecánico RGB');
        // Active product must still appear
        $response->assertSee('Mouse Ergonómico Inalámbrico');
    }

    public function test_admin_can_register_purchase_and_increment_stock(): void
    {
        $admin = Usuario::where('correo', 'admin@inventario.com')->first();
        $proveedor = \App\Models\Proveedor::first();
        $producto = Producto::first();
        $stockInicial = $producto->stock_actual;

        $response = $this->actingAs($admin)->post(route('admin.compras.store'), [
            'id_proveedor' => $proveedor->id_proveedor,
            'id_producto' => $producto->id_producto,
            'cantidad' => 10,
            'precio_unitario' => 50000.00,
        ]);

        $response->assertRedirect(route('admin.compras'));
        $this->assertDatabaseHas('compras', [
            'id_usuario' => $admin->id_usuario,
            'id_proveedor' => $proveedor->id_proveedor,
            'total' => 500000.00,
        ]);

        $this->assertEquals($stockInicial + 10, $producto->fresh()->stock_actual);
    }

    public function test_admin_can_register_sale_and_decrement_stock(): void
    {
        $admin = Usuario::where('correo', 'admin@inventario.com')->first();
        $cliente = \App\Models\Cliente::first();
        $producto = Producto::first();
        $stockInicial = $producto->stock_actual;

        $response = $this->actingAs($admin)->post(route('admin.ventas.store'), [
            'id_cliente' => $cliente->id_cliente,
            'id_producto' => $producto->id_producto,
            'cantidad' => 2,
            'precio_unitario' => 100000.00,
        ]);

        $response->assertRedirect(route('admin.ventas'));
        $this->assertDatabaseHas('ventas', [
            'id_usuario' => $admin->id_usuario,
            'id_cliente' => $cliente->id_cliente,
            'total' => 200000.00,
        ]);

        $this->assertEquals($stockInicial - 2, $producto->fresh()->stock_actual);
    }

    public function test_sale_cannot_exceed_available_stock(): void
    {
        $admin = Usuario::where('correo', 'admin@inventario.com')->first();
        $cliente = \App\Models\Cliente::first();
        $producto = Producto::first();

        $response = $this->actingAs($admin)->post(route('admin.ventas.store'), [
            'id_cliente' => $cliente->id_cliente,
            'id_producto' => $producto->id_producto,
            'cantidad' => $producto->stock_actual + 999,
            'precio_unitario' => 100000.00,
        ]);

        $response->assertSessionHas('error');
    }

    public function test_vendedor_can_register_purchase(): void
    {
        $vendedor = Usuario::where('correo', 'vendedor@inventario.com')->first();
        $proveedor = \App\Models\Proveedor::first();
        $producto = Producto::first();
        $stockInicial = $producto->stock_actual;

        $response = $this->actingAs($vendedor)->post(route('vendedor.compras.store'), [
            'id_proveedor' => $proveedor->id_proveedor,
            'id_producto' => $producto->id_producto,
            'cantidad' => 5,
            'precio_unitario' => 30000.00,
        ]);

        $response->assertRedirect(route('vendedor.compras'));
        $this->assertDatabaseHas('compras', [
            'id_usuario' => $vendedor->id_usuario,
            'id_proveedor' => $proveedor->id_proveedor,
            'total' => 150000.00,
        ]);

        $this->assertEquals($stockInicial + 5, $producto->fresh()->stock_actual);
    }

    public function test_vendedor_can_register_sale(): void
    {
        $vendedor = Usuario::where('correo', 'vendedor@inventario.com')->first();
        $cliente = \App\Models\Cliente::first();
        $producto = Producto::first();
        $stockInicial = $producto->stock_actual;

        $response = $this->actingAs($vendedor)->post(route('vendedor.ventas.store'), [
            'id_cliente' => $cliente->id_cliente,
            'id_producto' => $producto->id_producto,
            'cantidad' => 1,
            'precio_unitario' => 80000.00,
        ]);

        $response->assertRedirect(route('vendedor.ventas'));
        $this->assertDatabaseHas('ventas', [
            'id_usuario' => $vendedor->id_usuario,
            'id_cliente' => $cliente->id_cliente,
            'total' => 80000.00,
        ]);

        $this->assertEquals($stockInicial - 1, $producto->fresh()->stock_actual);
    }

    public function test_can_register_sale_with_new_custom_client_name(): void
    {
        $vendedor = Usuario::where('correo', 'vendedor@inventario.com')->first();
        $producto = Producto::first();

        $response = $this->actingAs($vendedor)->post(route('vendedor.ventas.store'), [
            'id_cliente' => 'nuevo',
            'nuevo_cliente_nombre' => 'María Alejandra Rodríguez',
            'nuevo_cliente_telefono' => '3119876543',
            'nuevo_cliente_correo' => 'maria.rodriguez@email.com',
            'id_producto' => $producto->id_producto,
            'cantidad' => 1,
            'precio_unitario' => 50000.00,
        ]);

        $response->assertRedirect(route('vendedor.ventas'));
        
        // Assert customer was created with name, phone, and email
        $this->assertDatabaseHas('clientes', [
            'nombre' => 'María Alejandra Rodríguez',
            'telefono' => '3119876543',
            'correo' => 'maria.rodriguez@email.com',
        ]);

        $clienteNuevo = \App\Models\Cliente::where('nombre', 'María Alejandra Rodríguez')->first();
        
        // Assert sale was linked to the newly created customer
        $this->assertDatabaseHas('ventas', [
            'id_usuario' => $vendedor->id_usuario,
            'id_cliente' => $clienteNuevo->id_cliente,
            'total' => 50000.00,
        ]);
    }

    public function test_can_register_purchase_with_new_custom_supplier_and_email(): void
    {
        $admin = Usuario::where('correo', 'admin@inventario.com')->first();
        $producto = Producto::first();
        $stockInicial = $producto->stock_actual;

        $response = $this->actingAs($admin)->post(route('admin.compras.store'), [
            'id_proveedor' => 'nuevo',
            'nuevo_proveedor_nombre' => 'Importaciones del Caribe S.A.',
            'nuevo_proveedor_telefono' => '3205551234',
            'nuevo_proveedor_correo' => 'ventas@importacionescaribe.com',
            'id_producto' => $producto->id_producto,
            'cantidad' => 15,
            'precio_unitario' => 45000.00,
        ]);

        $response->assertRedirect(route('admin.compras'));

        // Assert supplier was created with name, phone, and email
        $this->assertDatabaseHas('proveedores', [
            'nombre' => 'Importaciones del Caribe S.A.',
            'telefono' => '3205551234',
            'correo' => 'ventas@importacionescaribe.com',
        ]);

        $proveedorNuevo = \App\Models\Proveedor::where('nombre', 'Importaciones del Caribe S.A.')->first();

        $this->assertEquals($stockInicial + 15, $producto->fresh()->stock_actual);
    }

    public function test_admin_can_delete_sale_and_restore_stock(): void
    {
        $admin = Usuario::where('correo', 'admin@inventario.com')->first();
        $venta = \App\Models\Venta::with('detalles.producto')->first();
        $producto = $venta->detalles->first()->producto;
        $cantidadVendida = $venta->detalles->first()->cantidad;
        $stockAntes = $producto->stock_actual;

        $response = $this->actingAs($admin)->delete(route('admin.ventas.destroy', $venta));

        $response->assertRedirect(route('admin.ventas'));
        $this->assertDatabaseMissing('ventas', ['id_venta' => $venta->id_venta]);
        $this->assertEquals($stockAntes + $cantidadVendida, $producto->fresh()->stock_actual);
    }

    public function test_admin_can_delete_purchase_and_decrement_stock(): void
    {
        $admin = Usuario::where('correo', 'admin@inventario.com')->first();
        $compra = \App\Models\Compra::with('detalles.producto')->first();
        $producto = $compra->detalles->first()->producto;
        $cantidadComprada = $compra->detalles->first()->cantidad;
        $stockAntes = $producto->stock_actual;

        $response = $this->actingAs($admin)->delete(route('admin.compras.destroy', $compra));

        $response->assertRedirect(route('admin.compras'));
        $this->assertDatabaseMissing('compras', ['id_compra' => $compra->id_compra]);
        $this->assertEquals($stockAntes - $cantidadComprada, $producto->fresh()->stock_actual);
    }

    public function test_vendedor_can_delete_own_sale_and_restore_stock(): void
    {
        $vendedor = Usuario::where('correo', 'vendedor@inventario.com')->first();
        $venta = \App\Models\Venta::where('id_usuario', $vendedor->id_usuario)->first();
        $producto = $venta->detalles->first()->producto;
        $cantidadVendida = $venta->detalles->first()->cantidad;
        $stockAntes = $producto->stock_actual;

        $response = $this->actingAs($vendedor)->delete(route('vendedor.ventas.destroy', $venta));

        $response->assertRedirect(route('vendedor.ventas'));
        $this->assertDatabaseMissing('ventas', ['id_venta' => $venta->id_venta]);
        $this->assertEquals($stockAntes + $cantidadVendida, $producto->fresh()->stock_actual);
    }

    public function test_vendedor_cannot_delete_other_user_sale(): void
    {
        $vendedor = Usuario::where('correo', 'vendedor@inventario.com')->first();
        // Sale of vendedor 2
        $vendedor2 = Usuario::where('correo', 'vendedor2@inventario.com')->first();
        $venta2 = \App\Models\Venta::where('id_usuario', $vendedor2->id_usuario)->first();

        $response = $this->actingAs($vendedor)->delete(route('vendedor.ventas.destroy', $venta2));

        $response->assertStatus(403);
        $this->assertDatabaseHas('ventas', ['id_venta' => $venta2->id_venta]);
    }

    public function test_admin_can_view_ganancias_module(): void
    {
        $admin = Usuario::where('correo', 'admin@inventario.com')->first();

        $response = $this->actingAs($admin)->get(route('admin.ganancias'));

        $response->assertOk();
        $response->assertSee('Reporte de Ganancias');
        $response->assertSee('Ganancia Neta');
        $response->assertSee('Precio Compra (Proveedor)');
    }

    public function test_vendedor_can_view_scoped_ganancias_module(): void
    {
        $vendedor = Usuario::where('correo', 'vendedor@inventario.com')->first();

        $response = $this->actingAs($vendedor)->get(route('vendedor.ganancias'));

        $response->assertOk();
        $response->assertSee('Mis Ganancias y Utilidad Generada');
        $response->assertSee('Tu Ganancia Neta Total');
    }
}
