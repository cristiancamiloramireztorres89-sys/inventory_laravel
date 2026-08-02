<?php

namespace Tests\Feature;

use App\Models\Usuario;
use Database\Seeders\DatabaseSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class UserScopingTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        $this->seed(DatabaseSeeder::class);
    }

    public function test_vendedor_one_only_sees_their_own_sales(): void
    {
        $vendedor1 = Usuario::where('correo', 'vendedor@inventario.com')->first();

        $response = $this->actingAs($vendedor1)->get(route('vendedor.ventas'));

        $response->assertStatus(200);
        // Vendedor 1 has venta #1 ($285,600.00)
        $response->assertSee('#00001');
        $response->assertSee('285,600.00');
        // Vendedor 1 should NOT see Vendedor 2's sale #2
        $response->assertDontSee('#00002');
    }

    public function test_vendedor_two_only_sees_their_own_sales(): void
    {
        $vendedor2 = Usuario::where('correo', 'vendedor2@inventario.com')->first();

        $response = $this->actingAs($vendedor2)->get(route('vendedor.ventas'));

        $response->assertStatus(200);
        // Vendedor 2 has venta #2 ($107,100.00)
        $response->assertSee('#00002');
        $response->assertSee('107,100.00');
        // Vendedor 2 should NOT see Vendedor 1's sale #1
        $response->assertDontSee('#00001');
    }

    public function test_admin_sees_all_sales_from_all_sellers(): void
    {
        $admin = Usuario::where('correo', 'admin@inventario.com')->first();

        $response = $this->actingAs($admin)->get(route('admin.ventas'));

        $response->assertStatus(200);
        // Admin sees all sales and all sellers
        $response->assertSee('#00001');
        $response->assertSee('#00002');
        $response->assertSee('285,600.00');
        $response->assertSee('107,100.00');
        $response->assertSee('Vendedor Uno');
        $response->assertSee('Vendedor Dos');
    }

    public function test_vendedor_one_only_sees_their_own_purchases(): void
    {
        $vendedor1 = Usuario::where('correo', 'vendedor@inventario.com')->first();

        $response = $this->actingAs($vendedor1)->get(route('vendedor.compras'));

        $response->assertStatus(200);
        // Vendedor 1 has compra #2 (1,071,000.00)
        $response->assertSee('1,071,000.00');
        // Vendedor 1 should NOT see Admin's purchase #1 (2,380,000.00)
        $response->assertDontSee('2,380,000.00');
    }

    public function test_admin_sees_all_purchases(): void
    {
        $admin = Usuario::where('correo', 'admin@inventario.com')->first();

        $response = $this->actingAs($admin)->get(route('admin.compras'));

        $response->assertStatus(200);
        // Admin sees both purchases
        $response->assertSee('2,380,000.00');
        $response->assertSee('1,071,000.00');
        $response->assertSee('Administrador Principal');
        $response->assertSee('Vendedor Uno');
    }
}
