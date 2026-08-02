<?php

namespace Tests\Feature;

use App\Models\Usuario;
use Database\Seeders\DatabaseSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class AuthTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        $this->seed(DatabaseSeeder::class);
    }

    public function test_login_screen_can_be_rendered(): void
    {
        $response = $this->get('/login');

        $response->assertStatus(200);
    }

    public function test_users_can_authenticate_using_the_login_screen(): void
    {
        $response = $this->post('/login', [
            'correo' => 'admin@inventario.com',
            'contrasena' => 'admin123',
        ]);

        $this->assertAuthenticated();
        $response->assertRedirect(route('admin.dashboard'));
    }

    public function test_seller_is_redirected_to_seller_dashboard(): void
    {
        $response = $this->post('/login', [
            'correo' => 'vendedor@inventario.com',
            'contrasena' => 'vendedor123',
        ]);

        $this->assertAuthenticated();
        $response->assertRedirect(route('vendedor.dashboard'));
    }

    public function test_users_can_not_authenticate_with_invalid_password(): void
    {
        $response = $this->post('/login', [
            'correo' => 'admin@inventario.com',
            'contrasena' => 'wrong-password',
        ]);

        $this->assertGuest();
    }

    public function test_unauthenticated_user_cannot_access_admin_dashboard(): void
    {
        $response = $this->get('/admin/dashboard');

        $response->assertRedirect('/login');
    }

    public function test_seller_cannot_access_admin_dashboard(): void
    {
        $user = Usuario::where('correo', 'vendedor@inventario.com')->first();

        $response = $this->actingAs($user)->get('/admin/dashboard');

        $response->assertRedirect(route('vendedor.dashboard'));
    }

    public function test_admin_can_access_user_list(): void
    {
        $user = Usuario::where('correo', 'admin@inventario.com')->first();

        $response = $this->actingAs($user)->get('/admin/usuarios');

        $response->assertStatus(200);
        $response->assertSee('usuarios registrados');
    }

    public function test_user_can_logout(): void
    {
        $user = Usuario::where('correo', 'admin@inventario.com')->first();

        $response = $this->actingAs($user)->post('/logout');

        $this->assertGuest();
        $response->assertRedirect('/');
    }
}
