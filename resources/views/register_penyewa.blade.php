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

@if ($errors->any())
    <div class="alert-error">
        <i class="fa-solid fa-circle-exclamation"></i>

        <div class="error-text">
            @foreach ($errors->all() as $error)
                <div>• {{ $error }}</div>
            @endforeach
        </div>
    </div>
@endif
            <img src="assets/images/logo.png" class="logo-img">
            

           <form method="POST" action="{{ route('register.penyewa') }}" enctype="multipart/form-data">
    @csrf

    <!-- NAMA -->
    <div class="input-group">
        <i class="fa-solid fa-user"></i>
        <input type="text"
               name="nama"
               placeholder="Nama Lengkap"
               required
               minlength="3"
               maxlength="30"
               pattern="[A-Za-z\s]+"
               title="Nama 3-30 huruf, tanpa angka">
    </div>

    <!-- USERNAME -->
    <div class="input-group">
        <i class="fa-solid fa-user-circle"></i>
        <input type="text"
               name="username"
               placeholder="Username"
               required>
    </div>

    <!-- PASSWORD -->
    <div class="input-group">
        <i class="fa-solid fa-lock"></i>

        <input type="password"
               name="password"
               id="password"
               placeholder="Password"
               required
               minlength="6"
               title="Minimal 6 karakter">

        <i class="fa-solid fa-eye toggle-password" onclick="togglePassword()"></i>
    </div>

    <!-- TELEPON -->
    <div class="input-group">
        <i class="fa-solid fa-phone"></i>
        <input type="text"
               name="no_telepon"
               placeholder="No. Telepon"
               required
               pattern="[0-9]{10,15}"
               title="Nomor harus 10-15 digit angka">
    </div>

    <!-- ALAMAT -->
    <div class="input-group textarea-group">
        <i class="fa-solid fa-location-dot"></i>
        <textarea name="alamat"
                  rows="2"
                  placeholder="Alamat"
                  required></textarea>
    </div>

    <!-- FILE -->
    <div class="upload-box">
        <label class="upload-btn">
            Upload Identitas
            <input type="file"
                   name="gambar_identitas"
                   id="gambar_identitas"
                   hidden
                   required
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
        function togglePassword() {
    const input = document.getElementById("password");
    const icon = document.querySelector(".toggle-password");

    if (input.type === "password") {
        input.type = "text";
        icon.classList.remove("fa-eye");
        icon.classList.add("fa-eye-slash");
    } else {
        input.type = "password";
        icon.classList.remove("fa-eye-slash");
        icon.classList.add("fa-eye");
    }
}
    </script>

</body>
</html>
