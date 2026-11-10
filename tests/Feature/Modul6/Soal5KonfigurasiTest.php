<?php

namespace Tests\Feature\Modul6;

use App\Models\User;
use Database\Seeders\RolePermissionSeeder;
use Database\Seeders\UserSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Route;
use Tests\TestCase;

/** Soal 5: penguatan konfigurasi. NIM 250441100107. */
class Soal5KonfigurasiTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        $this->withoutVite();
    }

    protected function tearDown(): void
    {
        putenv('ADMIN_PASSWORD');
        unset($_ENV['ADMIN_PASSWORD'], $_SERVER['ADMIN_PASSWORD']);
        parent::tearDown();
    }

    private function setAdminPassword(?string $nilai): void
    {
        if ($nilai === null) {
            putenv('ADMIN_PASSWORD');
            unset($_ENV['ADMIN_PASSWORD'], $_SERVER['ADMIN_PASSWORD']);

            return;
        }
        putenv("ADMIN_PASSWORD={$nilai}");
        $_ENV['ADMIN_PASSWORD'] = $_SERVER['ADMIN_PASSWORD'] = $nilai;
    }

    private function seedAkun(): void
    {
        $this->seed(RolePermissionSeeder::class);
        $this->seed(UserSeeder::class);
    }

    // (a) APP_DEBUG=false -> pesan error umum, tanpa jejak kode
    public function test_a_halaman_error_saat_debug_false_tidak_menampilkan_jejak_kode(): void
    {
        config(['app.debug' => false]);
        Route::get('/uji-error-test', fn () => throw new \RuntimeException('RAHASIA-250441100107'));

        $res = $this->get('/uji-error-test');

        $res->assertStatus(500);
        $res->assertDontSee('RAHASIA-250441100107');
        $res->assertDontSee('RuntimeException');
        $res->assertDontSee('vendor/laravel', false);
        $res->assertDontSee('Stack trace', false);
    }

    public function test_a_route_sementara_uji_error_sudah_dihapus(): void
    {
        $this->get('/uji-error')->assertNotFound();
    }

    public function test_env_example_memakai_debug_false(): void
    {
        $this->assertStringContainsString('APP_DEBUG=false', file_get_contents(base_path('.env.example')));
    }

    // (b) /.env tidak dapat dibuka
    public function test_b_env_tidak_dapat_dibuka_lewat_url(): void
    {
        $this->get('/.env')->assertNotFound();
    }

    public function test_b_htaccess_memblokir_berkas_tersembunyi(): void
    {
        $isi = file_get_contents(public_path('.htaccess'));

        $this->assertStringContainsString('RewriteRule (^|/)\.(?!well-known(/|$)) - [F,L]', $isi);
    }

    public function test_b_env_tidak_masuk_git(): void
    {
        $this->assertMatchesRegularExpression('/^\.env$/m', file_get_contents(base_path('.gitignore')));
    }

    // (c) password bawaan admin tidak lagi berlaku
    public function test_c_admin_tidak_bisa_login_dengan_password_bawaan(): void
    {
        $this->setAdminPassword('Kuat#250441100107!xYz');
        $this->seedAkun();

        $this->post('/login', ['email' => 'admin@perpustakaan.test', 'password' => 'password'])
            ->assertSessionHasErrors('email');
        $this->assertGuest();
    }

    public function test_c_admin_bisa_login_dengan_password_baru_dari_env(): void
    {
        $this->setAdminPassword('Kuat#250441100107!xYz');
        $this->seedAkun();

        $this->post('/login', ['email' => 'admin@perpustakaan.test', 'password' => 'Kuat#250441100107!xYz'])
            ->assertRedirect();
        $this->assertAuthenticated();
    }

    public function test_c_tanpa_env_password_admin_diacak_dan_bukan_password(): void
    {
        $this->setAdminPassword(null);
        $this->seedAkun();

        $this->post('/login', ['email' => 'admin@perpustakaan.test', 'password' => 'password'])
            ->assertSessionHasErrors('email');
        $this->assertGuest();
    }

    public function test_c_seeder_diulang_tetap_mengganti_password_admin_yang_sudah_ada(): void
    {
        $this->setAdminPassword(null);
        $this->seedAkun();
        $lama = User::where('email', 'admin@perpustakaan.test')->value('password');

        $this->setAdminPassword('Baru#250441100107!aBc');
        $this->seed(UserSeeder::class);

        $this->assertNotSame($lama, User::where('email', 'admin@perpustakaan.test')->value('password'));
        $this->assertTrue(Auth::validate(['email' => 'admin@perpustakaan.test', 'password' => 'Baru#250441100107!aBc']));
    }

    public function test_c_akun_petugas_dan_siswa_tidak_terpengaruh(): void
    {
        $this->setAdminPassword('Kuat#250441100107!xYz');
        $this->seedAkun();

        $this->assertTrue(Auth::validate(['email' => 'petugas@perpustakaan.test', 'password' => 'password']));
        $this->assertTrue(Auth::validate(['email' => 'siswa1@perpustakaan.test', 'password' => 'password']));
    }
}
