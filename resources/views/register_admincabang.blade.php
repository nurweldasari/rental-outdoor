<!DOCTYPE html>
<html lang="id">
<head>
<meta charset="UTF-8">
<title>Register Admin Cabang - Outdoorkriss</title>
<meta name="viewport" content="width=device-width, initial-scale=1.0">

<link rel="stylesheet" href="{{ asset('css/register_admincabang.css') }}">
<link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css" rel="stylesheet">
<link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600&display=swap" rel="stylesheet">
</head>

<body>

<div class="left-image"></div>

<div class="container">
<div class="register-box">

<h2>Register</h2>
<img src="assets/images/logo.png" class="logo">

<form method="POST" action="{{ route('register.admin_cabang') }}" enctype="multipart/form-data">
@csrf

<div class="input-group">
    <i class="fa-solid fa-user icon-left"></i>
    <input type="text" name="nama_cabang" placeholder="Nama Cabang" required>
</div>

<div class="input-group">
    <i class="fa-solid fa-location-dot icon-left"></i>
    <input type="text" name="lokasi" placeholder="Lokasi Cabang" required>
</div>

<div class="row">
    <div class="input-group">
        <i class="fa-solid fa-user icon-left"></i>
        <input type="text" name="nama" placeholder="Nama Admin Cabang" required>
    </div>

    <div class="input-group">
        <i class="fa-solid fa-phone icon-left"></i>
        <input type="text" name="no_telepon" placeholder="No. Telephone Admin Cabang" required>
    </div>
</div>

<div class="input-group textarea-group">
    <i class="fa-solid fa-location-dot icon-left"></i>
    <textarea name="alamat" rows="2" placeholder="Alamat Domisili Admin Cabang" required></textarea>
</div>

<div class="row">
    <div class="input-group">
        <i class="fa-solid fa-circle-user icon-left"></i>
        <input type="text" name="username" placeholder="Username" required>
    </div>

    <div class="input-group password-group">
        <i class="fa-solid fa-lock icon-left"></i>
        <input type="password" name="password" placeholder="Password" id="password" required>
        <i class="fa-solid fa-eye toggle-password" onclick="togglePassword()"></i>
    </div>
</div>

<div class="upload-box">
    <label class="upload-btn">
        Upload MOU
        <input type="file" name="gambar_mou" id="gambar_mou" hidden required accept=".jpg,.jpeg,.png">
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
const fileInput = document.getElementById('gambar_mou');
const fileName = document.getElementById('file-name');

fileInput.addEventListener('change', function () {
    if (this.files.length > 0) {
        fileName.innerHTML = `<i class="fa-solid fa-file-image"></i> ${this.files[0].name}`;
    }
});

function togglePassword() {
    const input = document.getElementById("password");
    const icon = document.querySelector(".toggle-password");

    if (input.type === "password") {
        input.type = "text";
        icon.classList.replace("fa-eye", "fa-eye-slash");
    } else {
        input.type = "password";
        icon.classList.replace("fa-eye-slash", "fa-eye");
    }
}
</script>

</body>
</html>