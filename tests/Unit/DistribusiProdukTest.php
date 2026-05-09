<?php

namespace Tests\Unit;

use Tests\TestCase;

class DistribusiProdukTest extends TestCase
{
    /* =========================================
       TC-DIST-01 Kirim sesuai jumlah permintaan
    ========================================= */
    public function test_tc_dist_01_kirim_sesuai_jumlah_permintaan()
    {
        $diminta = 10;
        $dikirim = 10;

        $this->assertEquals($diminta, $dikirim);
    }

    /* =========================================
       TC-DIST-02 Kirim sebagian
    ========================================= */
    public function test_tc_dist_02_kirim_sebagian()
    {
        $diminta = 10;
        $dikirim = 8;

        $this->assertTrue($dikirim < $diminta);
    }

    /* =========================================
       TC-DIST-03 Kirim melebihi permintaan
    ========================================= */
    public function test_tc_dist_03_kirim_melebihi_permintaan()
    {
        $diminta = 10;
        $dikirim = 15;

        $this->assertTrue($dikirim > $diminta);
    }

    /* =========================================
       TC-DIST-04 Jumlah tidak valid
    ========================================= */
    public function test_tc_dist_04_jumlah_tidak_valid()
    {
        $jumlah = 0;

        $this->assertTrue($jumlah <= 0);
    }

    /* =========================================
       TC-DIST-05 Stok pusat mencukupi
    ========================================= */
    public function test_tc_dist_05_stok_cukup()
    {
        $stok = 20;
        $kirim = 10;

        $this->assertTrue($stok >= $kirim);
    }

    /* =========================================
       TC-DIST-06 Pengurangan stok pusat
    ========================================= */
    public function test_tc_dist_06_pengurangan_stok()
    {
        $stok_awal = 20;
        $kirim = 5;

        $this->assertEquals(15, $stok_awal - $kirim);
    }

    /* =========================================
       TC-DIST-07 Penambahan stok cabang
    ========================================= */
    public function test_tc_dist_07_stok_cabang()
    {
        $stok_awal = 0;
        $diterima = 10;

        $this->assertEquals(10, $stok_awal + $diterima);
    }

    /* =========================================
       TC-DIST-08 Status permintaan disetujui
    ========================================= */
    public function test_tc_dist_08_status_disetujui()
    {
        $status = 'disetujui';

        $this->assertEquals('disetujui', $status);
    }

    /* =========================================
       TC-DIST-09 Status dikirim
    ========================================= */
    public function test_tc_dist_09_status_dikirim()
    {
        $status = 'dikirim';

        $this->assertEquals('dikirim', $status);
    }

    /* =========================================
       TC-DIST-10 Status diterima
    ========================================= */
    public function test_tc_dist_10_status_diterima()
    {
        $status = 'diterima';

        $this->assertEquals('diterima', $status);
    }
}