<?php

namespace Database\Seeders;

use App\Models\Barang;
use App\Models\User;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    // Memakai firstOrCreate agar aman dijalankan berkali-kali (docker compose up berulang)
    public function run(): void
    {
        User::firstOrCreate(
            ['email' => 'admin@example.com'],
            ['name' => 'Admin Gudang', 'password' => 'password123'], // di-hash oleh cast 'hashed'
        );

        $barang = [
            ['kode_barang' => 'BRG-001', 'nama' => 'Kardus Ukuran Besar', 'kategori' => 'Kemasan', 'satuan' => 'pcs', 'stok' => 250, 'harga' => 3500, 'lokasi_rak' => 'A-01'],
            ['kode_barang' => 'BRG-002', 'nama' => 'Lakban Bening 48mm', 'kategori' => 'Kemasan', 'satuan' => 'roll', 'stok' => 120, 'harga' => 12000, 'lokasi_rak' => 'A-02'],
            ['kode_barang' => 'BRG-003', 'nama' => 'Pallet Plastik', 'kategori' => 'Peralatan', 'satuan' => 'unit', 'stok' => 40, 'harga' => 185000, 'lokasi_rak' => 'C-05'],
        ];

        foreach ($barang as $item) {
            Barang::firstOrCreate(['kode_barang' => $item['kode_barang']], $item);
        }
    }
}
