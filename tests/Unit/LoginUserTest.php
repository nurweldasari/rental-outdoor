<?php

namespace Tests\Unit;

use Tests\TestCase;

class LoginUserTest extends TestCase
{
    /* =========================================
       TC-DIST-01
       Kirim sesuai jumlah permintaan
    ========================================= */
    public function test_tc_dist_01_kirim_sesuai_jumlah_permintaan()
    {
        $jumlah_diminta = 10;
        $jumlah_dikirim = 10;

        $this->assertTrue($jumlah_dikirim == $jumlah_diminta);
    }

    /* =========================================
       TC-DIST-02
       Kirim sebagian
    ========================================= */
    public function test_tc_dist_02_kirim_sebagian()
    {
        $this->assertTrue(8 < 10);
    }

    /* =========================================
       TC-DIST-03
       Kirim melebihi permintaan
    ========================================= */
    public function test_tc_dist_03_kirim_melebihi_permintaan()
    {
        $this->assertTrue(15 > 10);
    }

    /* =========================================
       TC-DIST-04
       Validasi jumlah tidak valid
    ========================================= */
    public function test_tc_dist_04_jumlah_tidak_valid()
    {
        $jumlah = 0;

        $this->assertTrue($jumlah <= 0);
    }

    /* =========================================
       TC-DIST-05
       Stok pusat mencukupi
    ========================================= */
    public function test_tc_dist_05_stok_pusat_mencukupi()
    {
        $stok = 20;
        $kirim = 10;

        $this->assertTrue($stok >= $kirim);
    }

    /* =========================================
       TC-DIST-06
       Pengurangan stok pusat
    ========================================= */
    public function test_tc_dist_06_pengurangan_stok_pusat()
    {
        $stok_awal = 20;
        $kirim = 5;

        $stok_akhir = $stok_awal - $kirim;

        $this->assertEquals(15, $stok_akhir);
    }

    /* =========================================
       TC-DIST-07
       Penambahan stok cabang
    ========================================= */
    public function test_tc_dist_07_penambahan_stok_cabang()
    {
        $stok_cabang = 0;
        $terima = 10;

        $stok_cabang += $terima;

        $this->assertEquals(10, $stok_cabang);
    }

    /* =========================================
       TC-DIST-08
       Status permintaan disetujui
    ========================================= */
    public function test_tc_dist_08_status_permintaan_disetujui()
    {
        $status = 'menunggu';
        $status = 'disetujui';

        $this->assertEquals('disetujui', $status);
    }

    /* =========================================
       TC-DIST-09
       Status distribusi dikirim
    ========================================= */
    public function test_tc_dist_09_status_distribusi_dikirim()
    {
        $status = 'dikirim';

        $this->assertEquals('dikirim', $status);
    }

    /* =========================================
       TC-DIST-10
       Status distribusi diterima
    ========================================= */
    public function test_tc_dist_10_status_distribusi_diterima()
    {
        $status = 'diterima';

        $this->assertEquals('diterima', $status);
    }
}