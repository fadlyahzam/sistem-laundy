<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Hash;
use Tests\TestCase;

class LandingAndAuthCleanlinessTest extends TestCase
{
    use RefreshDatabase;

    public function test_guest_sees_clean_landing_page_navbar_without_active_session(): void
    {
        $response = $this->get('/');

        $response->assertStatus(200);
        // Verify guest navigation buttons exist
        $response->assertSee('Daftar Akun');
        $response->assertSee('Masuk');
        // Verify capsule theme toggle switcher exists
        $response->assertSee('toggleTheme()', false);
        $response->assertSee('color-theme');
        // Verify authenticated dashboard links are NOT present for guest
        $response->assertDontSee('Ke Dashboard Saya');
        $response->assertDontSee('Keluar (Logout)');
    }

    public function test_authenticated_user_sees_avatar_dropdown_and_dashboard_link(): void
    {
        $user = User::factory()->create([
            'name' => 'Budi Santoso',
            'role' => 'pelanggan',
        ]);

        $response = $this->actingAs($user)->get('/');

        $response->assertStatus(200);
        $response->assertSee('Budi Santoso');
        $response->assertSee('Pelanggan');
        $response->assertSee('Ke Dashboard Saya');
        $response->assertSee('Pesanan Saya');
        $response->assertSee('Keluar (Logout)');
        $response->assertSee('toggleTheme()', false);
    }

    public function test_login_page_is_production_clean_and_links_to_forgot_password(): void
    {
        $response = $this->get('/login');

        $response->assertStatus(200);
        // Standard production elements
        $response->assertSee('Alamat Email');
        $response->assertSee('Kata Sandi');
        $response->assertSee('Ingat saya');
        $response->assertSee('Lupa Password?');
        $response->assertSee(route('password.request'), false);
        $response->assertSee('Masuk ke Aplikasi');
        $response->assertSee('Daftar sekarang');
        $response->assertSee('toggleTheme()', false);

        // Assert NO quick demo switcher or fillCreds scripts exist
        $response->assertDontSee('Akun Demo');
        $response->assertDontSee('⚡ Akun Demo');
        $response->assertDontSee('Klik untuk Isi Cepat');
        $response->assertDontSee('fillCreds');
    }

    public function test_layouts_contain_dark_mode_script_and_capsule_switcher(): void
    {
        $admin = User::factory()->create(['role' => 'admin']);
        $driver = User::factory()->create(['role' => 'driver']);
        $pelanggan = User::factory()->create(['role' => 'pelanggan']);

        // Admin Dashboard
        $adminRes = $this->actingAs($admin)->get('/admin/dashboard');
        $adminRes->assertStatus(200);
        $adminRes->assertSee('toggleTheme()', false);
        $adminRes->assertSee("color-theme");
        $adminRes->assertSee("https://cdn.tailwindcss.com");

        // Pelanggan Dashboard
        $pelangganRes = $this->actingAs($pelanggan)->get('/pelanggan/dashboard');
        $pelangganRes->assertStatus(200);
        $pelangganRes->assertSee('toggleTheme()', false);
        $pelangganRes->assertSee("color-theme");
        $pelangganRes->assertSee("https://cdn.tailwindcss.com");

        // Driver Dashboard
        $driverRes = $this->actingAs($driver)->get('/driver/dashboard');
        $driverRes->assertStatus(200);
        $driverRes->assertSee('toggleTheme()', false);
        $driverRes->assertSee("color-theme");
        $driverRes->assertSee("https://cdn.tailwindcss.com");
    }

    public function test_direct_forgot_password_page_loads_cleanly(): void
    {
        $response = $this->get(route('password.request'));

        $response->assertStatus(200);
        $response->assertSee('Atur Ulang Kata Sandi');
        $response->assertSee('Email / No. Telepon');
        $response->assertSee('Password Baru');
        $response->assertSee('Konfirmasi Password Baru');
        $response->assertSee('Perbarui Password');
        $response->assertSee('toggleTheme()', false);
    }

    public function test_direct_password_reset_action_successfully_updates_password(): void
    {
        $user = User::factory()->create([
            'email' => 'pelanggan.reset@laundryku.com',
            'password' => Hash::make('oldpassword123'),
        ]);

        $response = $this->post(route('password.update'), [
            'email' => 'pelanggan.reset@laundryku.com',
            'password' => 'newsecretpassword',
            'password_confirmation' => 'newsecretpassword',
        ]);

        $response->assertRedirect(route('login'));
        $response->assertSessionHas('success', 'Password berhasil diperbarui! Silakan masuk dengan password baru Anda.');

        // Verify user can now log in with the new password
        $this->assertTrue(Hash::check('newsecretpassword', $user->fresh()->password));

        $loginResponse = $this->post(route('login'), [
            'email' => 'pelanggan.reset@laundryku.com',
            'password' => 'newsecretpassword',
        ]);
        $loginResponse->assertRedirect(route('pelanggan.dashboard'));
    }

    public function test_direct_password_reset_action_validates_non_existent_email(): void
    {
        $response = $this->post(route('password.update'), [
            'email' => 'tidak.ada@laundryku.com',
            'password' => 'newsecretpassword',
            'password_confirmation' => 'newsecretpassword',
        ]);

        $response->assertSessionHasErrors('email');
    }
}
