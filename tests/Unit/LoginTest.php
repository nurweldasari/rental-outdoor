<?php

namespace Tests\Unit;

use Tests\TestCase;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Session\Store;
use Illuminate\Session\ArraySessionHandler;
use Mockery;

class LoginTest extends TestCase
{
    /** @test */
    public function tc_log_01_login_berhasil()
{
    $request = Request::create('/login', 'POST', [
        'username' => 'user',
        'password' => 'admin12'
    ]);

    // ✅ TAMBAHKAN SESSION MANUAL
    $session = new Store('test', new ArraySessionHandler(120));
$request->setLaravelSession($session);

    $user = (object)[
        'idusers' => 1,
        'status' => 'owner'
    ];

    Auth::shouldReceive('attempt')
        ->once()
        ->andReturn(true);

    Auth::shouldReceive('user')
        ->andReturn($user);

    $controller = new \App\Http\Controllers\AuthController();
    $response = $controller->login($request);

    $this->assertEquals(302, $response->getStatusCode());
}

    /** @test */
    public function tc_log_02_password_kosong()
    {
        $this->expectException(\Illuminate\Validation\ValidationException::class);

        $request = Request::create('/login', 'POST', [
            'username' => 'user',
            'password' => ''
        ]);

        $controller = new \App\Http\Controllers\AuthController();
        $controller->login($request);
    }

    /** @test */
    public function tc_log_03_username_kosong()
    {
        $this->expectException(\Illuminate\Validation\ValidationException::class);

        $request = Request::create('/login', 'POST', [
            'username' => '',
            'password' => 'admin12'
        ]);

        $controller = new \App\Http\Controllers\AuthController();
        $controller->login($request);
    }

    /** @test */
    public function tc_log_04_password_salah()
    {
        $request = Request::create('/login', 'POST', [
            'username' => 'user',
            'password' => '123456'
        ]);

        Auth::shouldReceive('attempt')
            ->once()
            ->andReturn(false);

        $controller = new \App\Http\Controllers\AuthController();
        $response = $controller->login($request);

        $this->assertEquals(302, $response->getStatusCode());
    }

    /** @test */
    public function tc_log_05_username_salah()
    {
        $request = Request::create('/login', 'POST', [
            'username' => 'pengguna',
            'password' => 'admin12'
        ]);

        Auth::shouldReceive('attempt')
            ->once()
            ->andReturn(false);

        $controller = new \App\Http\Controllers\AuthController();
        $response = $controller->login($request);

        $this->assertEquals(302, $response->getStatusCode());
    }
}