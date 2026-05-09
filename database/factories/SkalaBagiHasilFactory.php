<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use App\Models\SkalaBagiHasil;
use App\Models\Cabang;

class SkalaBagiHasilFactory extends Factory
{
    protected $model = SkalaBagiHasil::class;

    public function definition(): array
    {
        return [
            'cabang_idcabang' => Cabang::factory(),

            'owner' => 50,
            'cabang' => 50,
            'berlaku_mulai' => now()->startOfMonth(),
        ];
    }
}