<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Register - Outdoorkriss</title>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <!-- Font Awesome -->
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css" rel="stylesheet">

    <link rel="stylesheet" href="{{ asset('css/register_penyewa.css') }}">
    <!-- Google Font -->
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600&display=swap" rel="stylesheet">
</head>
<body>

    <!-- LEFT -->
    <div class="register-image"></div>

    <!-- RIGHT -->
    <div class="register-container">
        <div class="register-box">
            <h2>Register</h2>

            <img src="assets/images/logo.png" class="logo-img">

            <form method="POST" action="{{ route('register.penyewa') }}" enctype="multipart/form-data">
                @csrf

                <div class="input-group">
                    <i class="fa-solid fa-user"></i>
                    <input type="text" name="nama" placeholder="Nama Lengkap" required>
                </div>

                <div class="input-group">
                    <i class="fa-solid fa-user-circle"></i>
                    <input type="text" name="username" placeholder="Username" required>
                </div>

                <!-- PASSWORD — SAMA DENGAN LOGIN -->
                <div class="input-group">
                    <i class="fa-solid fa-lock"></i>
                    <input type="password" name="password" placeholder="Password" required>
                </div>

                <div class="input-group">
                    <i class="fa-solid fa-phone"></i>
                    <input type="text" name="no_telepon" placeholder="No. Telepon" required>
                </div>

                <div class="input-group">
                    <i class="fa-solid fa-location-dot"></i>
                    <textarea name="alamat" rows="2" placeholder="Alamat" required></textarea>
                </div>

                <!-- UPLOAD -->
                <div class="upload-box">
                    <label class="upload-btn">
                        Upload Identitas
                        <input type="file" name="gambar_identitas" id="gambar_identitas" hidden required
                               accept=".jpg,.jpeg,.png">
                    </label>

                    <div class="upload-area" id="file-name">
                        <i class="fa-solid fa-cloud-arrow-up"></i>
                        JPG / PNG max 2MB
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
        const fileInput = document.getElementById('gambar_identitas');
        const fileName = document.getElementById('file-name');

        fileInput.addEventListener('change', function () {
            if (this.files.length > 0) {
                fileName.innerHTML = `
                    <i class="fa-solid fa-file-image"></i>
                    ${this.files[0].name}
                `;
            }
        });
    </script>

</body>
</html>
