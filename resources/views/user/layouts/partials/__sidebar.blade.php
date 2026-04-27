<nav class="sidebar sidebar-offcanvas" id="sidebar">
        <div class="sidebar-brand-wrapper d-none d-lg-flex align-items-center justify-content-center fixed-top">
          <a class="sidebar-brand brand-logo" href="{{ route('user.dashboard') }}">
            @if($generalSettings && $generalSettings->logo)
                    <img src="{{ asset('storage/' . $generalSettings->logo) }}" alt="{{ $generalSettings->app_name ?? 'App Name' }}" class="navbar-brand" height="50">
                @endif</a>
          <a class="sidebar-brand brand-logo-mini" href="{{ route('user.dashboard') }}">
            @if($generalSettings && $generalSettings->logo)
                <img src="{{ asset('storage/' . $generalSettings->logo) }}" alt="{{ $generalSettings->app_name ?? 'App Name' }}" class="navbar-brand" height="50">
            @endif
        </a>
        </div>
        <ul class="nav">
          <li class="nav-item nav-category">
            <span class="nav-link">Navigation</span>
          </li>
          <li class="nav-item menu-items {{ request()->routeIs('user.dashboard') ? 'active' : '' }}">
            <a class="nav-link" href="{{ route('user.dashboard') }}">
              <span class="menu-icon">
                <i class="mdi mdi-speedometer"></i>
              </span>
              <span class="menu-title">Dashboard</span>
            </a>
          </li>
        <li class="nav-item menu-items {{ request()->routeIs('user.todos.*') ? 'active' : '' }}">
            <a class="nav-link" href="{{ route('user.todos.history') }}">
                <span class="menu-icon">
                    <i class="mdi mdi-format-list-checks"></i>
                </span>
                <span class="menu-title">All Tasks</span>

                {{-- Optional: Pending Count --}}
                <span class=" text-danger ml-2">
                    ({{ auth()->user()->todos()->where('is_completed', false)->count() }})
                </span>
            </a>
        </li>
        </ul>
      </nav>
