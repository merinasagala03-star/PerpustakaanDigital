<?php

namespace Tests\Feature\Modul6;

use App\Models\Anggota;
use App\Models\Buku;
use App\Models\Kategori;
use App\Models\User;
use Database\Seeders\RolePermissionSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\Facades\Hash;
use PHPUnit\Framework\Attributes\DataProvider;
use Tests\TestCase;

/** Soal 3: Gate 'kelola-buku' + middleware can: + @can. NIM 250441100107. */
class Soal3GateTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        $this->withoutVite();
        $this->seed(RolePermissionSeeder::class);
    }

    private function user(string $role): User
    {
        $email = $role.'-'.uniqid().'@example.test';
        $anggota = Anggota::create(['nama' => ucfirst($role), 'email' => $email, 'no_telepon' => '0812']);
        $user = User::create(['name' => ucfirst($role), 'email' => $email, 'password' => Hash::make('password'), 'anggota_id' => $anggota->id]);
        $user->assignRole($role);

        return $user;
    }

    private function buku(): Buku
    {
        $k = Kategori::firstOrCreate(['nama' => 'Fiksi']);

        return Buku::create(['kategori_id' => $k->id, 'judul' => 'Laskar Pelangi', 'penulis' => 'Penulis Uji', 'tahun_terbit' => 2005]);
    }

    private function payload(): array
    {
        return [
            'kategori_id' => Kategori::firstOrCreate(['nama' => 'Fiksi'])->id,
            'judul' => 'Buku 250441100107', 'penulis' => 'Penulis Uji', 'tahun_terbit' => 2020,
        ];
    }

    public function test_gate_kelola_buku_hanya_admin_dan_petugas(): void
    {
        $this->assertTrue(Gate::forUser($this->user('admin'))->allows('kelola-buku'));
        $this->assertTrue(Gate::forUser($this->user('petugas'))->allows('kelola-buku'));
        $this->assertFalse(Gate::forUser($this->user('siswa'))->allows('kelola-buku'));
    }

    public function test_route_tambah_ubah_hapus_memakai_middleware_can(): void
    {
        foreach (['web.buku.create', 'web.buku.store', 'web.buku.edit', 'web.buku.update', 'web.buku.destroy'] as $nama) {
            $this->assertContains('can:kelola-buku', app('router')->getRoutes()->getByName($nama)->gatherMiddleware(), $nama);
        }
    }

    #[DataProvider('staf')]
    public function test_admin_dan_petugas_dapat_tambah_ubah_hapus(string $role): void
    {
        $staf = $this->user($role);
        $buku = $this->buku();

        $this->actingAs($staf)->get('/buku/create')->assertOk()->assertSee('Tambah Buku');
        $this->actingAs($staf)->post('/buku', $this->payload())->assertRedirect(route('web.buku.index'));
        $this->assertDatabaseHas('bukus', ['judul' => 'Buku 250441100107']);

        $this->actingAs($staf)->get("/buku/{$buku->id}/edit")->assertOk();
        $this->actingAs($staf)->put("/buku/{$buku->id}", $this->payload())->assertRedirect(route('web.buku.index'));

        $this->actingAs($staf)->delete("/buku/{$buku->id}")->assertRedirect(route('web.buku.index'));
        $this->assertDatabaseMissing('bukus', ['id' => $buku->id]);
    }

    public static function staf(): array
    {
        return ['admin' => ['admin'], 'petugas' => ['petugas']];
    }

    public function test_siswa_mengetik_url_buku_create_langsung_menerima_403(): void
    {
        $this->actingAs($this->user('siswa'))->get('/buku/create')->assertForbidden();
    }

    public function test_siswa_ditolak_pada_semua_route_kelola_buku(): void
    {
        $siswa = $this->user('siswa');
        $buku = $this->buku();

        $this->actingAs($siswa)->post('/buku', $this->payload())->assertForbidden();
        $this->actingAs($siswa)->get("/buku/{$buku->id}/edit")->assertForbidden();
        $this->actingAs($siswa)->put("/buku/{$buku->id}", $this->payload())->assertForbidden();
        $this->actingAs($siswa)->delete("/buku/{$buku->id}")->assertForbidden();
        $this->actingAs($siswa)->postJson('/api/buku', $this->payload())->assertForbidden();
        $this->actingAs($siswa)->deleteJson("/api/buku/{$buku->id}")->assertForbidden();
        $this->assertDatabaseHas('bukus', ['id' => $buku->id, 'judul' => 'Laskar Pelangi']);
    }

    public function test_tombol_kelola_tampil_untuk_petugas_dan_tidak_untuk_siswa(): void
    {
        $this->buku();

        $this->actingAs($this->user('siswa'))->get('/buku')->assertOk()
            ->assertDontSee('Tambah Buku')->assertDontSee('>Ubah<', false)->assertDontSee('>Hapus<', false);

        $this->actingAs($this->user('petugas'))->get('/buku')->assertOk()
            ->assertSee('Tambah Buku')->assertSee('>Ubah<', false)->assertSee('>Hapus<', false);
    }
}
