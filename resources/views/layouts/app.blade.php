<!DOCTYPE html>
<html lang="id">
<head>
<meta charset="UTF-8">
<title>@yield('title')</title>
<meta name="viewport" content="width=device-width, initial-scale=1.0">

<link rel="icon" href="{{ asset('assets/images/logo1.png') }}">

<link rel="stylesheet" href="{{ asset('css/dashboard_cabang.css') }}">
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">

@stack('styles')

</head>

<body>

<div class="layout">

    {{-- SIDEBAR --}}
    @include('layouts.sidebar')

    <main class="main">

        {{-- NAVBAR --}}
        @include('layouts.navbar')

        {{-- ISI HALAMAN --}}
        <div class="content-wrapper">
            @yield('content')
        </div>

    </main>

</div>

{{-- ✅ SCRIPT GLOBAL --}}
<script>
const sidebar = document.getElementById('sidebar');
const toggleBtn = document.getElementById('toggleBtn');

if (toggleBtn) {
  toggleBtn.onclick = () => {
    sidebar.classList.toggle('collapsed');
  };
}

document.querySelectorAll('.menu-title').forEach(title => {
  title.addEventListener('click', () => {
    if (sidebar.classList.contains('collapsed')) return;

    let open = title.classList.toggle('open');
    let next = title.nextElementSibling;

    while (next && !next.classList.contains('menu-title')) {
      next.style.display = open ? 'none' : 'flex';
      next = next.nextElementSibling;
    }
  });
});

const userBtn = document.getElementById('userBtn');
const dropdownMenu = document.getElementById('dropdownMenu');

if (userBtn) {
  userBtn.addEventListener('click', e => {
    e.stopPropagation();
    dropdownMenu.classList.toggle('show');
  });
}

document.addEventListener('click', () => {
  dropdownMenu?.classList.remove('show');
});
</script>

@stack('scripts')

</body>
</html>
