<?php

namespace Tests\Feature\Modul6;

use App\Models\Anggota;
use App\Models\Buku;
use App\Models\Kategori;
use App\Models\User;
use Database\Seeders\RolePermissionSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Hash;
use Tests\TestCase;

/** Soal 2: perbaikan A1 (XSS), A2 (SQL Injection), A3 (CSRF). NIM 250441100107. */
class Soal2PerbaikanTest extends TestCase
{
    use RefreshDatabase;

    private const NIM = '250441100107';

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

    private function buku(string $judul = 'Laskar Pelangi'): Buku
    {
        $k = Kategori::firstOrCreate(['nama' => 'Fiksi']);

        return Buku::create(['kategori_id' => $k->id, 'judul' => $judul, 'penulis' => 'Penulis Uji', 'tahun_terbit' => 2005]);
    }

    public function test_a1_judul_berisi_script_tampil_sebagai_teks_bukan_html(): void
    {
        $payload = "<script>alert('".self::NIM."')</script>";
        $this->buku($payload);

        $this->actingAs($this->user('siswa'))->get('/buku')
            ->assertOk()
            ->assertDontSee($payload, false)                                   // tidak ada tag script mentah
            ->assertSee(e($payload), false);                                    // tampil ter-escape (&lt;script&gt;...)
    }

    public function test_a2_sql_injection_menghasilkan_array_kosong(): void
    {
        $this->buku();
        $this->buku('Bumi Manusia');

        $this->actingAs($this->user('siswa'))->get("/buku-cari?judul=' OR '1'='1")
            ->assertOk()->assertExactJson([]);
    }

    public function test_a2_pencarian_judul_yang_sah_tetap_berfungsi(): void
    {
        $this->buku();
        $this->buku('Bumi Manusia');

        $this->actingAs($this->user('siswa'))->getJson('/buku-cari?judul=Bumi Manusia')
            ->assertOk()->assertJsonCount(1)->assertJsonPath('0.judul', 'Bumi Manusia');
    }

    public function test_a3_route_get_penghapusan_sudah_tidak_ada(): void
    {
        $buku = $this->buku();

        $this->actingAs($this->user('petugas'))->get("/buku/{$buku->id}/hapus")->assertNotFound();
        $this->assertDatabaseHas('bukus', ['id' => $buku->id]);
    }

    public function test_a3_penghapusan_lewat_delete_berhasil_untuk_petugas(): void
    {
        $buku = $this->buku();

        $this->actingAs($this->user('petugas'))->delete("/buku/{$buku->id}")
            ->assertRedirect(route('web.buku.index'));
        $this->assertDatabaseMissing('bukus', ['id' => $buku->id]);
    }

    public function test_a3_siswa_tidak_boleh_menghapus_buku(): void
    {
        $buku = $this->buku();

        $this->actingAs($this->user('siswa'))->delete("/buku/{$buku->id}")->assertForbidden();
        $this->assertDatabaseHas('bukus', ['id' => $buku->id]);
    }
}
