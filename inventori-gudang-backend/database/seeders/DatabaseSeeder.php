<?php

namespace Database\Seeders;

use App\Models\Barang;
use App\Models\User;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        User::create([
            'name' => 'Admin Gudang',
            'email' => 'admin@example.com',
            'password' => 'password123', // otomatis di-hash oleh cast 'hashed'
        ]);

        Barang::create([
            'kode_barang' => 'BRG-001',
            'nama' => 'Kardus Ukuran Besar',
            'kategori' => 'Kemasan',
            'satuan' => 'pcs',
            'stok' => 250,
            'harga' => 3500,
            'lokasi_rak' => 'A-01',
        ]);

        Barang::create([
            'kode_barang' => 'BRG-002',
            'nama' => 'Lakban Bening 48mm',
            'kategori' => 'Kemasan',
            'satuan' => 'roll',
            'stok' => 120,
            'harga' => 12000,
            'lokasi_rak' => 'A-02',
        ]);

        Barang::create([
            'kode_barang' => 'BRG-003',
            'nama' => 'Pallet Plastik',
            'kategori' => 'Peralatan',
            'satuan' => 'unit',
            'stok' => 40,
            'harga' => 185000,
            'lokasi_rak' => 'C-05',
        ]);
    }
}
