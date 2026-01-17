<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Login - Outdoorkriss</title>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <!-- Font Awesome -->
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css" rel="stylesheet">

    <!-- Google Font -->
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600&display=swap" rel="stylesheet">

    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
            font-family: 'Poppins', sans-serif;
        }

        body {
            min-height: 100vh;
            display: flex;
        }

        /* LEFT IMAGE */
        .login-image {
            width: 50%;
            background: url('assets/images/login.png') no-repeat center center;
            background-size: cover;
            background-position: center top; 
            background-repeat: no-repeat;
  background-size: cover;
  background-position: center top;
  display: flex;
  align-items: center;
  justify-content: center;
}
        

        /* RIGHT LOGIN */
        .login-container {
            width: 50%;
            display: flex;
            justify-content: center;
            align-items: center;
            background-color: #ffffff;
        }

        .login-box {
            width: 360px;
            text-align: center;
        }

        .login-box h2 {
            color: #c86b00;
            margin-bottom: 15px;
        }

        .logo-img {
  width: 130px;      /* atur sesuai kebutuhan */
  height: auto;     /* menjaga rasio */
  margin: 20px 20px; 
}


        .input-group {
            position: relative;
            margin-bottom: 18px;
        }

        .input-group input {
            width: 100%;
            padding: 12px 40px;
            border-radius: 10px;
            border: 1px solid #ccc;
            outline: none;
            font-size: 14px;
        }

        .input-group i {
            position: absolute;
            top: 50%;
            transform: translateY(-50%);
            color: #999;
        }

        .input-group .fa-user,
        .input-group .fa-lock {
            left: 15px;
        }

        .input-group .fa-eye {
            right: 15px;
            cursor: pointer;
        }

        .forgot {
            text-align: right;
            font-size: 12px;
            margin-bottom: 20px;
        }

        .forgot a {
            color: #c86b00;
            text-decoration: none;
        }

        .btn-login {
            width: 100%;
            padding: 12px;
            border-radius: 10px;
            border: none;
            background-color: #c86b00;
            color: #fff;
            font-size: 15px;
            font-weight: 500;
            cursor: pointer;
            transition: 0.3s;
        }

        .btn-login:hover {
            background-color: #a85700;
        }

        .register {
            margin-top: 20px;
            font-size: 13px;
        }

        .register a {
            color: #c86b00;
            text-decoration: none;
            font-weight: 500;
        }

        @media (max-width: 768px) {
            .login-image {
                display: none;
            }

            .login-container {
                width: 100%;
            }
        }
    </style>
</head>
<body>

    <!-- LEFT -->
    <div><img src="assets/images/login.png" class=".login-image"></div>

    <!-- RIGHT -->
    <div class="login-container">
        <div class="login-box">
            <h2>Welcome Back!</h2>

            <!-- LOGO -->
<img src="assets/images/logo.png" class="logo-img">

<form method="POST" action="/login">
    @csrf
    <div class="input-group">
        <i class="fa-solid fa-user"></i>
        <input type="text" name="username" placeholder="Username" required>
    </div>

    <div class="input-group">
        <i class="fa-solid fa-lock"></i>
        <input type="password" name="password" placeholder="Password" id="password" required>
        <i class="fa-solid fa-eye" onclick="togglePassword()"></i>
    </div>

    <button type="submit" class="btn-login">Login</button>
</form>

<div id="loginError" style="color:red; margin-top:10px;"></div>

<div class="register">
    Belum Punya Akun? <a href="/register_penyewa">Daftar di sini</a>
</div>

<script>
    function togglePassword() {
        const pass = document.getElementById('password');
        pass.type = pass.type === "password" ? "text" : "password";
    }
    
</script>


</body>
</html>
