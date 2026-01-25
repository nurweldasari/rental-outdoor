<!DOCTYPE html>
<html lang="id">
<head>
<meta charset="UTF-8">
<title>Profil Cabang</title>
<meta name="viewport" content="width=device-width, initial-scale=1.0">

<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">

<style>
*{margin:0;padding:0;box-sizing:border-box;font-family:'Segoe UI',sans-serif}
body{background:#f7f3ee}
.layout{display:flex;min-height:100vh}

.profile-container {
  background: #ffffff;
  border: 1px solid #999;
  border-radius: 12px;
  padding: 24px;
  width: 80%;
  max-width: 100%;
  margin: 40px 120px;
}

/* TAB */
.profile-tabs {
  display: flex;
  gap: 40px;
  margin-bottom: 24px;
  justify-content: center;
}

.tab {
  display: flex;
  align-items: center;
  gap: 8px;
  color: #777;
  cursor: pointer;
  font-weight: 500;
}

.tab.active {
  color: #b45300;
  position: relative;
}

.tab.active::after {
  content: '';
  position: absolute;
  bottom: -10px;
  left: 0;
  width: 100%;
  height: 4px;
  background: #b45300;
  border-radius: 4px;
}

/* FORM */
.profile-form {
  margin-top: 30px;
}

.form-row {
  display: flex;
  gap: 16px;
  margin-bottom: 16px;
}

.form-row.full input {
  width: 100%;
}

.form-row.center {
  justify-content: center;
}

.profile-form input {
  width: 100%;
  padding: 12px 14px;
  border-radius: 10px;
  border: 1px solid #7d7dc5;
  font-size: 14px;
}

/* BUTTON */
.btn-simpan {
  width: 100%;
  margin-top: 20px;
  background: #b45300;
  color: #fff;
  border: none;
  padding: 14px;
  font-size: 16px;
  border-radius: 10px;
  cursor: pointer;
}

.btn-simpan:hover {
  background: #9c4600;
}


</style>
</head>

<body>

  <!-- CONTENT -->
<div class="content-wrapper">

  <div class="profile-container">

    <!-- TAB MENU -->
    <div class="profile-tabs">
      <div class="tab active">
        <i class="fa-solid fa-user"></i>
        <span>Profile</span>
      </div>

      <div class="tab">
        <i class="fa-solid fa-lock"></i>
        <span>Ganti Password</span>
      </div>
    </div>

    <!-- FORM PROFILE PENYEWA -->
    <form
        class="profile-form"
        method="POST"
        action="{{ route('profil.penyewa.update') }}"
    >
      @csrf

      <!-- NAMA -->
      <div class="form-row full">
        <input
          type="text"
          name="nama"
          placeholder="Nama"
          value="{{ old('nama', $user->nama) }}"
          required
        >
      </div>

      <!-- ALAMAT -->
      <div class="form-row full">
        <input
          type="text"
          name="alamat"
          placeholder="Alamat"
          value="{{ old('alamat', $user->alamat) }}"
          required
        >
      </div>

      <!-- USERNAME -->
      <div class="form-row full">
        <input
          type="text"
          name="username"
          placeholder="Username"
          value="{{ old('username', $user->username) }}"
          required
        >
      </div>

      <!-- TELEPHONE -->
      <div class="form-row full">
        <input
          type="text"
          name="no_telepon"
          placeholder="No. Telephone"
          value="{{ old('no_telepon', $user->no_telepon) }}"
          required
        >
      </div>

      <button type="submit" class="btn-simpan">
        Simpan
      </button>

    </form>

  </div>
</div>


<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

@if (session('success'))
<script>
Swal.fire({
    icon: 'success',
    title: 'Berhasil',
    text: "{{ session('success') }}",
    showConfirmButton: false,
    timer: 2000
});
</script>
@endif

</body>
</html>
