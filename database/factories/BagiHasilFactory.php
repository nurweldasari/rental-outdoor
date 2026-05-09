<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use App\Models\BagiHasil;
use App\Models\Cabang;
use App\Models\SkalaBagiHasil;

class BagiHasilFactory extends Factory
{
    protected $model = BagiHasil::class;

    public function definition(): array
    {
        return [
            'cabang_idcabang' => Cabang::factory(),
            'skala_id' => SkalaBagiHasil::factory(),

            'bulan' => now()->format('Y-m'),

            'presentase_owner' => 50,
            'presentase_cabang' => 50,

            'nominal_owner' => 500000,
            'nominal_cabang' => 500000,

            'status' => 'terkunci',
        ];
    }
}