<?php

namespace Tests\Feature\Modul6;

use App\Models\Anggota;
use App\Models\Buku;
use App\Models\Kategori;
use App\Models\Peminjaman;
use App\Models\User;
use Database\Seeders\RolePermissionSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Hash;
use Tests\TestCase;

/** Soal 4: PeminjamanPolicy::view + Gate::authorize pada show (menutup IDOR). NIM 250441100107. */
class Soal4PolicyTest extends TestCase
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
        $anggota = Anggota::create(['nama' => ucfirst($role).' 250441100107', 'email' => $email, 'no_telepon' => '0812']);
        $user = User::create(['name' => ucfirst($role), 'email' => $email, 'password' => Hash::make('password'), 'anggota_id' => $anggota->id]);
        $user->assignRole($role);

        return $user;
    }

    private function peminjamanMilik(User $user): Peminjaman
    {
        $k = Kategori::firstOrCreate(['nama' => 'Fiksi']);
        $buku = Buku::create(['kategori_id' => $k->id, 'judul' => 'Laskar Pelangi', 'penulis' => 'Penulis Uji', 'tahun_terbit' => 2005]);

        return Peminjaman::create([
            'anggota_id' => $user->anggota_id, 'buku_id' => $buku->id,
            'tanggal_pinjam' => now()->toDateString(), 'status' => Peminjaman::DIPINJAM,
        ]);
    }

    public function test_belum_login_dialihkan_ke_halaman_login(): void
    {
        $this->get('/peminjaman/1')->assertRedirect('/login');
    }

    public function test_siswa_membuka_peminjaman_miliknya_status_200(): void
    {
        $siswa = $this->user('siswa');
        $p = $this->peminjamanMilik($siswa);

        $this->actingAs($siswa)->get("/peminjaman/{$p->id}")->assertOk()->assertSee('Detail Peminjaman #'.$p->id);
    }

    public function test_siswa_membuka_peminjaman_siswa_lain_status_403(): void
    {
        $pemilik = $this->user('siswa');
        $penyerang = $this->user('siswa');
        $p = $this->peminjamanMilik($pemilik);

        $this->actingAs($penyerang)->get("/peminjaman/{$p->id}")
            ->assertForbidden()
            ->assertDontSee('250441100107');           // data pemilik tidak bocor
    }

    public function test_admin_dan_petugas_dapat_membuka_peminjaman_siapa_pun(): void
    {
        $p = $this->peminjamanMilik($this->user('siswa'));

        $this->actingAs($this->user('petugas'))->get("/peminjaman/{$p->id}")->assertOk();
        $this->actingAs($this->user('admin'))->get("/peminjaman/{$p->id}")->assertOk();
    }

    public function test_id_yang_tidak_ada_menghasilkan_404(): void
    {
        $this->actingAs($this->user('petugas'))->get('/peminjaman/99999')->assertNotFound();
    }

    public function test_policy_view_dipanggil_langsung(): void
    {
        $pemilik = $this->user('siswa');
        $lain = $this->user('siswa');
        $p = $this->peminjamanMilik($pemilik);

        $this->assertTrue($pemilik->can('view', $p));
        $this->assertFalse($lain->can('view', $p));
        $this->assertTrue($this->user('petugas')->can('view', $p));
    }
}
