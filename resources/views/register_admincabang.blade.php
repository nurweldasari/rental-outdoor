<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Register Admin Cabang - Outdoorkriss</title>
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
        .left-image {
            width: 50%;
            background: url('https://images.unsplash.com/photo-1504280390367-361c6d9f38f4') no-repeat center;
            background-size: cover;
        }

        /* RIGHT FORM */
        .container {
            width: 50%;
            display: flex;
            justify-content: center;
            align-items: center;
            background: #fff;
        }

        .register-box {
            width: 420px;
            text-align: center;
        }

        .register-box h2 {
            color: #c86b00;
            margin-bottom: 12px;
        }

        .logo {
            width: 85px;
            margin-bottom: 20px;
        }

        .input-group {
            position: relative;
            margin-bottom: 14px;
        }

        .input-group input,
        .input-group textarea {
            width: 100%;
            padding: 12px 40px;
            border-radius: 30px;
            border: 1px solid #ccc;
            font-size: 13px;
            outline: none;
        }

        .input-group textarea {
            border-radius: 18px;
            resize: none;
        }

        .input-group i {
            position: absolute;
            top: 50%;
            transform: translateY(-50%);
            left: 15px;
            color: #999;
        }

        .input-group .fa-eye {
            right: 15px;
            left: auto;
            cursor: pointer;
        }

        .row {
            display: flex;
            gap: 10px;
        }

        /* UPLOAD */
        .upload-box {
            border: 1px dashed #c86b00;
            border-radius: 12px;
            padding: 15px;
            margin-bottom: 18px;
        }

        .upload-btn {
            display: inline-block;
            padding: 6px 12px;
            background-color: #c86b00;
            color: #fff;
            border-radius: 20px;
            font-size: 11px;
            margin-bottom: 10px;
        }

        .upload-area {
            border: 1px dashed #f0b57a;
            padding: 18px;
            border-radius: 10px;
            font-size: 12px;
            color: #999;
        }

        .upload-area i {
            display: block;
            font-size: 20px;
            color: #c86b00;
            margin-bottom: 5px;
        }

        .btn-register {
            width: 100%;
            padding: 12px;
            border-radius: 30px;
            border: none;
            background: #d17b00;
            color: #fff;
            font-size: 15px;
            font-weight: 500;
            cursor: pointer;
            transition: 0.3s;
        }

        .btn-register:hover {
            background: #b76800;
        }

        .login-link {
            margin-top: 12px;
            font-size: 13px;
        }

        .login-link a {
            color: #c86b00;
            text-decoration: none;
            font-weight: 500;
        }

        @media (max-width: 768px) {
            .left-image {
                display: none;
            }
            .container {
                width: 100%;
            }
        }
    </style>
</head>
<body>

    <!-- LEFT -->
    <div class="left-image"></div>

    <!-- RIGHT -->
    <div class="container">
        <div class="register-box">
            <h2>Register</h2>
            <img src="https://via.placeholder.com/100x100?text=LOGO" class="logo" alt="Logo">

<form method="POST"
      action="{{ route('register.admin_cabang') }}"
      enctype="multipart/form-data">

    @csrf

    <!-- ================= DATA CABANG ================= -->
    <div class="input-group">
        <i class="fa-solid fa-building"></i>
        <input type="text" name="nama_cabang" placeholder="Nama Cabang" required>
    </div>

    <div class="input-group">
        <i class="fa-solid fa-location-dot"></i>
        <input type="text" name="lokasi" placeholder="Lokasi Cabang" required>
    </div>

    <!-- ================= DATA ADMIN CABANG ================= -->
    <div class="row">
        <div class="input-group" style="flex:1">
            <i class="fa-solid fa-user-tie"></i>
            <input type="text" name="nama" placeholder="Nama Admin Cabang" required>
        </div>

        <div class="input-group" style="flex:1">
            <i class="fa-solid fa-phone"></i>
            <input type="text" name="no_telepon" placeholder="No. Telephone Admin Cabang" required>
        </div>
    </div>

    <div class="input-group">
        <i class="fa-solid fa-location-dot"></i>
        <textarea name="alamat" rows="2" placeholder="Alamat Domisili Admin Cabang" required></textarea>
    </div>

    <!-- ================= LOGIN ADMIN ================= -->
    <div class="row">
        <div class="input-group" style="flex:1">
            <i class="fa-solid fa-user"></i>
            <input type="text" name="username" placeholder="Username" required>
        </div>

        <div class="input-group" style="flex:1">
            <i class="fa-solid fa-lock"></i>
            <input type="password" name="password" placeholder="Password" id="password" required>
            <i class="fa-solid fa-eye" onclick="togglePassword()"></i>
        </div>
    </div>

    <!-- ================= UPLOAD NIB / MOU ================= -->
<div class="upload-box">
        <label class="upload-btn">
            Upload MOU
            <input type="file" name="gambar_mou" hidden required>
        </label>

        <div class="upload-area">
            <i class="fa-solid fa-cloud-arrow-up"></i>
            Drag and Drop Files
        </div>
    </div>

    <button type="submit" class="btn-register">Register</button>
</form>
<div class="login-link">
    Sudah Punya Akun? <a href="{{ route('login') }}">Login</a>
</div>
        </div>
    </div>

    <script>
        function togglePassword() {
            const pass = document.getElementById("password");
            pass.type = pass.type === "password" ? "text" : "password";
        }
    </script>

</body>
</html>

