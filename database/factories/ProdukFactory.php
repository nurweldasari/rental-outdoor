<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use App\Models\Produk;
use App\Models\Kategori;
use App\Models\AdminPusat;

class ProdukFactory extends Factory
{
    protected $model = Produk::class;

    public function definition(): array
    {
        return [
            'nama_produk' => $this->faker->word(),
            'stok_pusat' => 10,
            'jenis_skala' => 'unit',
            'gambar_produk' => null,

            'kategori_idkategori' => Kategori::factory(),
            'admin_pusat_idadmin_pusat' => AdminPusat::factory(),
        ];
    }

    public function configure()
    {
        return $this->afterCreating(function ($produk) {

            $produk->harga()->create([
                'type' => 'produk',
                'harga' => 100000,
                'tanggal_berlaku' => now(),
            ]);

        });
    }
}