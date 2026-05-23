<?php

namespace Tests\Unit;

use Tests\TestCase;
use App\Models\User;
use Illuminate\Support\Facades\Hash;
use Illuminate\Foundation\Testing\RefreshDatabase;

class LoginUserTest extends TestCase
{
    use RefreshDatabase;

    /* =========================================
       TC-LOG-01
       Login dengan username dan password valid
    ========================================= */
    public function test_tc_log_01_login_valid()
    {
        $user = User::create([
            'nama' => 'Novita',
            'username' => 'novitaadmin',
            'password' => Hash::make('qwerty12'),
            'no_telepon' => '081234567890',
            'alamat' => 'Jember',
            'status' => 'owner'
        ]);

        $response = $this->post('/login', [
            'username' => 'novitaadmin',
            'password' => 'qwerty12'
        ]);

        $response->assertRedirect(route('dashboard'));

        $this->assertAuthenticatedAs($user);
    }

    /* =========================================
       TC-LOG-02
       Username valid dan password salah
    ========================================= */
    public function test_tc_log_02_password_salah()
    {
        $user = User::create([
            'nama' => 'Novita',
            'username' => 'novitaadmin',
            'password' => Hash::make('qwerty12'),
            'no_telepon' => '081234567890',
            'alamat' => 'Jember',
            'status' => 'owner'
        ]);

        $response = $this->from('/login')->post('/login', [
            'username' => 'novitaadmin',
            'password' => 'salah123'
        ]);

        $response->assertRedirect('/login');

        $response->assertSessionHasErrors([
            'username' => 'Username atau password salah'
        ]);

        $this->assertGuest();
    }

    /* =========================================
       TC-LOG-03
       Username tidak terdaftar
    ========================================= */
    public function test_tc_log_03_username_tidak_terdaftar()
    {
        $response = $this->from('/login')->post('/login', [
            'username' => 'adminbaru',
            'password' => 'qwerty12'
        ]);

        $response->assertRedirect('/login');

        $response->assertSessionHasErrors([
            'username' => 'Username atau password salah'
        ]);

        $this->assertGuest();
    }

    /* =========================================
       TC-LOG-04
       Username dan password salah
    ========================================= */
    public function test_tc_log_04_username_password_salah()
    {
        $response = $this->from('/login')->post('/login', [
            'username' => 'usertest',
            'password' => '123456'
        ]);

        $response->assertRedirect('/login');

        $response->assertSessionHasErrors([
            'username' => 'Username atau password salah'
        ]);

        $this->assertGuest();
    }

    /* =========================================
       TC-LOG-05
       Username kosong
    ========================================= */
    public function test_tc_log_05_username_kosong()
    {
        $response = $this->from('/login')->post('/login', [
            'username' => '',
            'password' => 'qwerty12'
        ]);

        $response->assertRedirect('/login');

        $response->assertSessionHasErrors('username');
    }

    /* =========================================
       TC-LOG-06
       Password kosong
    ========================================= */
    public function test_tc_log_06_password_kosong()
    {
        $response = $this->from('/login')->post('/login', [
            'username' => 'novitaadmin',
            'password' => ''
        ]);

        $response->assertRedirect('/login');

        $response->assertSessionHasErrors('password');
    }

    /* =========================================
       TC-LOG-07
       Username dan password kosong
    ========================================= */
    public function test_tc_log_07_username_password_kosong()
    {
        $response = $this->from('/login')->post('/login', [
            'username' => '',
            'password' => ''
        ]);

        $response->assertRedirect('/login');

        $response->assertSessionHasErrors([
            'username',
            'password'
        ]);
    }
}