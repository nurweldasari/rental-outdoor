<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Login - Outdoorkriss</title>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <!-- Font Awesome -->
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css" rel="stylesheet">

    <link rel="stylesheet" href="{{ asset('css/login.css') }}">
    <!-- Google Font -->
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600&display=swap" rel="stylesheet">
</head>
<body>

    <!-- LEFT -->
    <div class="login-image"></div>

    <!-- RIGHT -->
    <div class="login-container">
        <div class="login-box">
            <h2>Welcome Back!</h2>

            <!-- LOGO -->
            <img src="assets/images/logo.png" class="logo-img">

            <!-- NOTIFIKASI STATUS -->
            @if ($errors->any())
                <div class="alert alert-danger">
                    {{ $errors->first() }}
                </div>
            @endif

            @if (session('success'))
                <div class="alert alert-success">
                    {{ session('success') }}
                </div>
            @endif

            <form method="POST" action="/login">
                @csrf

                <div class="input-group">
                    <i class="fa-solid fa-user"></i>
                    <input type="text" name="username" placeholder="Username" required>
                </div>

                <div class="input-group">
                    <i class="fa-solid fa-lock"></i>
                    <input type="password" name="password" placeholder="Password" id="password" required>
                </div>

                <button type="submit" class="btn-login">Login</button>
            </form>

            <div id="loginError" style="color:red; margin-top:10px;"></div>

            <div class="register">
                Belum Punya Akun? <a href="/register_penyewa">Daftar di sini</a>
            </div>
        </div>
    </div>

</body>
</html>
