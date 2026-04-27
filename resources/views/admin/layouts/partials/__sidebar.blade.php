<!-- Sidebar -->

<div class="sidebar" data-background-color="dark">
    <div class="sidebar-logo">
        <!-- Logo Header -->
        <div class="logo-header" data-background-color="dark">
            <a href="{{ route('admin.dashboard') }}" class="logo">
                @if($generalSettings && $generalSettings->logo)
                    <img src="{{ asset('storage/' . $generalSettings->logo) }}" alt="{{ $generalSettings->app_name ?? 'App Name' }}" class="navbar-brand" height="30">
                @endif
            </a>
            <div class="nav-toggle">
                <button class="btn btn-toggle toggle-sidebar">
                    <i class="gg-menu-right"></i>
                </button>
                <button class="btn btn-toggle sidenav-toggler">
                    <i class="gg-menu-left"></i>
                </button>
            </div>
            <button class="topbar-toggler more">
                <i class="gg-more-vertical-alt"></i>
            </button>
        </div>
        <!-- End Logo Header -->
    </div>

    <div class="sidebar-wrapper scrollbar scrollbar-inner">
        <div class="sidebar-content">
            <ul class="nav nav-secondary">

                <!-- Dashboard -->
                <li class="nav-item {{ request()->is('admin.dashboard') ? 'active' : '' }}">
                    <a href="{{ route('admin.dashboard') }}">
                        <i class="fas fa-home"></i>
                        <p>Dashboard</p>
                    </a>
                </li>


                <!-- Users -->
                <li class="nav-item {{ request()->is('admin.users') ? 'active' : '' }}">
                    <a href="{{ route('admin.users.index') }}">
                        <i class="fas fa-users"></i>
                        <p>All Users</p>
                    </a>
                </li>

                <!-- Tasks -->
                <li class="nav-item {{ request()->is('admin/tasks*') ? 'active' : '' }}">
                    <a href="{{ route('admin.tasks.list') }}">
                        <i class="fas fa-tasks"></i>
                        <p>All Tasks</p>
                    </a>
                </li>

                <!-- Settings -->
                <li class="nav-item nav-item {{ Str::contains(request()->path(), 'admin.general.settings') ? 'active' : '' }}">
                    <a data-bs-toggle="collapse" href="{{ route('admin.general.settings') }}">
                        <i class="fas fa-cog"></i>
                        <p>App Settings</p>
                    </a>
                </li>
            </ul>
        </div>
    </div>
</div>
<!-- End Sidebar -->
