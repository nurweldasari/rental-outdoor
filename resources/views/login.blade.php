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

                    <!-- ICON MATA -->
                    <i class="fa-solid fa-eye toggle-password" onclick="togglePassword()"></i>
                </div>

                <div class="lupa-password">
                    <button type="button" class="lupa-pswd" onclick="openModal()">Lupa Password?</button>
                </div>

                <button type="submit" class="btn-login">Login</button>
            </form>

            <div id="loginError" style="color:red; margin-top:10px;"></div>

            <div class="register">
                Belum Punya Akun? <a href="/register_penyewa">Daftar di sini</a>
            </div>
        </div>
    </div>
<!-- MODAL -->
 <div id="modalOtp" class="modal">
    <div class="modal-content">

        <!-- LOGO (SELALU ADA) -->
        <img src="assets/images/logo.png" class="modal-logo">

        <h3 id="modalTitle">Lupa Password</h3>

        <!-- STEP 1 -->
        <div id="step1">
            <div class="input-group modal-input">
                <i class="fa-solid fa-key"></i>
                <input type="text" id="no_wa" placeholder="Masukkan Nomor Whatsapp">
            </div>
            <div class="btn-group">
                <button onclick="kirimOtp()" class="btn-green">Kirim OTP</button>
                <button onclick="closeModal()" class="btn-red">Tutup</button>
            </div>
        </div>

        <!-- STEP 2 -->
        <div id="step2" style="display:none;">
           <div class="input-group modal-input">
                <i class="fa-solid fa-key"></i>
                <input type="text" id="otp" placeholder="Masukkan Kode OTP">
            </div>
            <div class="btn-group">
                <button onclick="verifikasiOtp()" class="btn-green">Verifikasi OTP</button>
                <button onclick="closeModal()" class="btn-red">Tutup</button>
            </div>
        </div>

        <!-- STEP 3 -->
        <div id="step3" style="display:none;">

            <form method="POST" action="/reset-password">
                @csrf

                <div class="reset-group password-group">
                    <input type="password" name="password" id="new_password" placeholder="Password Baru" required>
                    <i class="fa-solid fa-eye toggle-new-pass" onclick="toggleNewPassword()"></i>
                </div>

                <div class="reset-group password-group">
                    <input type="password" name="password_confirmation" id="confirm_password" placeholder="Konfirmasi Password" required>
                    <i class="fa-solid fa-eye toggle-confirm-pass" onclick="toggleConfirmPassword()"></i>
                </div>

                <button type="submit" class="btn-green reset-btn">Simpan</button>
            </form>
        </div>

    </div>
</div>
</body>
</html>
<script>
function setTitle(text) {
    document.getElementById('modalTitle').innerText = text;
}
function openModal() {
    let modal = document.getElementById('modalOtp');
    modal.style.display = 'flex'; // 🔥 penting
    setTitle('Lupa Password');
    document.getElementById('step1').style.display = 'block';
    document.getElementById('step2').style.display = 'none';
    document.getElementById('step3').style.display = 'none';
}

function closeModal() {
    document.getElementById('modalOtp').style.display = 'none';
}

// STEP 1
function kirimOtp() {
    let no_wa = document.getElementById('no_wa').value;

    fetch('/kirim-otp', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
            'X-CSRF-TOKEN': '{{ csrf_token() }}'
        },
        body: JSON.stringify({ no_wa: no_wa })
    })
    .then(res => res.json())
    .then(data => {
        if (data.status === 'success') {
            document.getElementById('step1').style.display = 'none';
            document.getElementById('step2').style.display = 'block';
        }
    });
}

// STEP 2
function verifikasiOtp() {
    let otp = document.getElementById('otp').value;

    fetch('/verifikasi-otp', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
            'X-CSRF-TOKEN': '{{ csrf_token() }}'
        },
        body: JSON.stringify({ otp: otp })
    })
    .then(res => res.json())
    .then(data => {
        if (data.status === 'success') {
            // ❌ jangan redirect
            setTitle('Reset Password');
            document.getElementById('step2').style.display = 'none';
            document.getElementById('step3').style.display = 'block';
        } else {
            alert('OTP salah!');
        }
    });
}
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

function toggleNewPassword() {
    const input = document.getElementById("new_password");
    const icon = document.querySelector(".toggle-new-pass");

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

function toggleConfirmPassword() {
    const input = document.getElementById("confirm_password");
    const icon = document.querySelector(".toggle-confirm-pass");

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