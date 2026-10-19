<?php

namespace Tests\Feature;

use App\Models\Barang;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Laravel\Sanctum\Sanctum;
use Tests\TestCase;

class BarangTest extends TestCase
{
    use RefreshDatabase;

    private function payload(array $override = []): array
    {
        return array_merge([
            'kode_barang' => 'BRG-100',
            'nama' => 'Troli Barang Lipat',
            'kategori' => 'Peralatan',
            'satuan' => 'unit',
            'stok' => 15,
            'harga' => 450000,
            'lokasi_rak' => 'C-07',
        ], $override);
    }

    public function test_akses_tanpa_token_mengembalikan_401_json(): void
    {
        $this->getJson('/api/barang')->assertUnauthorized();
        $this->postJson('/api/barang', $this->payload())->assertUnauthorized();

        // Tanpa header Accept pun balasan tetap JSON
        $this->get('/api/barang')
            ->assertUnauthorized()
            ->assertJsonStructure(['message']);
    }

    public function test_tampil_semua_barang(): void
    {
        Sanctum::actingAs(User::factory()->create());
        Barang::factory()->count(3)->create();

        $this->getJson('/api/barang')
            ->assertOk()
            ->assertJsonCount(3, 'data')
            ->assertJsonStructure(['data', 'current_page', 'total']);
    }

    public function test_tampil_satu_barang_dan_404_jika_tidak_ada(): void
    {
        Sanctum::actingAs(User::factory()->create());
        $barang = Barang::factory()->create();

        $this->getJson("/api/barang/{$barang->id}")
            ->assertOk()
            ->assertJsonPath('data.id', $barang->id)
            ->assertJsonPath('data.kode_barang', $barang->kode_barang);

        $this->getJson('/api/barang/99999')->assertNotFound();
    }

    public function test_tambah_barang_mengembalikan_201_dan_tersimpan(): void
    {
        Sanctum::actingAs(User::factory()->create());

        $this->postJson('/api/barang', $this->payload())
            ->assertCreated()
            ->assertJsonPath('data.kode_barang', 'BRG-100');

        $this->assertDatabaseHas('barang', [
            'kode_barang' => 'BRG-100',
            'nama' => 'Troli Barang Lipat',
        ]);
    }

    public function test_tambah_barang_dengan_data_tidak_valid_mengembalikan_422(): void
    {
        Sanctum::actingAs(User::factory()->create());

        $this->postJson('/api/barang', ['kode_barang' => '', 'stok' => -5, 'harga' => 'abc'])
            ->assertStatus(422)
            ->assertJsonValidationErrors(['kode_barang', 'nama', 'satuan', 'stok', 'harga']);

        $this->assertDatabaseCount('barang', 0);
    }

    public function test_kode_barang_harus_unik(): void
    {
        Sanctum::actingAs(User::factory()->create());
        Barang::factory()->create(['kode_barang' => 'BRG-100']);

        $this->postJson('/api/barang', $this->payload())
            ->assertStatus(422)
            ->assertJsonValidationErrors(['kode_barang']);
    }

    public function test_ubah_barang(): void
    {
        Sanctum::actingAs(User::factory()->create());
        $barang = Barang::factory()->create(['stok' => 10]);

        $this->putJson("/api/barang/{$barang->id}", ['stok' => 99])
            ->assertOk()
            ->assertJsonPath('data.stok', 99);

        $this->assertDatabaseHas('barang', ['id' => $barang->id, 'stok' => 99]);
    }

    public function test_hapus_barang(): void
    {
        Sanctum::actingAs(User::factory()->create());
        $barang = Barang::factory()->create();

        $this->deleteJson("/api/barang/{$barang->id}")
            ->assertOk()
            ->assertJson(['message' => 'Barang berhasil dihapus.']);

        $this->assertDatabaseMissing('barang', ['id' => $barang->id]);
    }
}
