<?php

namespace Database\Factories;

use App\Models\Barang;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Barang>
 */
class BarangFactory extends Factory
{
    protected $model = Barang::class;

    public function definition(): array
    {
        return [
            'kode_barang' => fake()->unique()->bothify('BRG-####'),
            'nama' => fake()->words(3, true),
            'kategori' => fake()->randomElement(['Kemasan', 'Peralatan', 'Bahan Baku']),
            'satuan' => fake()->randomElement(['pcs', 'box', 'roll', 'unit']),
            'stok' => fake()->numberBetween(0, 500),
            'harga' => fake()->numberBetween(1000, 500000),
            'lokasi_rak' => strtoupper(fake()->bothify('?-##')),
        ];
    }
}
