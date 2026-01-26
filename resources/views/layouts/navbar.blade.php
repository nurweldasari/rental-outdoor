<header class="topbar">

  <h2>@yield('title')</h2>

  <div class="user-dropdown">

    <button class="user-btn" id="userBtn">
      <span>
        {{ ucwords(str_replace('_',' ', auth()->user()->status)) }}
      </span>
      <i class="fa-solid fa-chevron-down"></i>
    </button>

    <div class="dropdown-menu" id="dropdownMenu">

      {{-- ✅ PENGATURAN AKUN --}}
      <a href="/profil_cabang">
        <i class="fa-solid fa-gear"></i>
        <span>Pengaturan Akun</span>
      </a>

      <hr>

      {{-- LOGOUT --}}
      <form method="POST" action="{{ route('logout') }}">
        @csrf
        <a href="#"
           class="logout"
           onclick="event.preventDefault();this.closest('form').submit();">
          <i class="fa-solid fa-right-from-bracket"></i>
          <span>Logout</span>
        </a>
      </form>

    </div>
  </div>

</header>
