<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin - PresenSmart</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.0/font/bootstrap-icons.css">
    <link rel="stylesheet" href="{{ asset('css/admin.css') }}">
    @stack('styles')
</head>

<body>
    <!-- Sidebar -->
    <div class="sidebar d-flex flex-column" id="sidebar">
        <div class="sidebar-header">
            <span class="fs-4 fw-bold text-gradient link-text">PresenSmart</span>
            <i id="toggleSidebar" class="bi bi-list toggle-btn text-white fs-3 cursor-pointer" style="cursor: pointer;"></i>
        </div>
        
        <div class="py-4">
            <ul class="nav nav-pills flex-column mb-auto">
                <li class="nav-item">
                    <a href="{{ route('admin.dashboard') }}"
                        class="nav-link {{ request()->routeIs('admin.dashboard') ? 'active' : '' }}" title="Dashboard">
                        <i class="bi bi-grid-1x2-fill"></i>
                        <span class="link-text">Dashboard</span>
                    </a>
                </li>
                <li class="nav-item">
                    <a href="{{ route('admin.students.index') }}"
                        class="nav-link {{ request()->routeIs('admin.students.*') ? 'active' : '' }}" title="Students">
                        <i class="bi bi-mortarboard-fill"></i>
                        <span class="link-text">Students</span>
                    </a>
                </li>
                <li class="nav-item">
                    <a href="{{ route('admin.employees.index') }}"
                        class="nav-link {{ request()->routeIs('admin.employees.*') ? 'active' : '' }}" title="Employees">
                        <i class="bi bi-person-badge-fill"></i>
                        <span class="link-text">Employees</span>
                    </a>
                </li>
                <li class="nav-item">
                    <a href="{{ route('admin.attendances.index') }}"
                        class="nav-link {{ request()->routeIs('admin.attendances.*') ? 'active' : '' }}" title="Attendances">
                        <i class="bi bi-calendar-check-fill"></i>
                        <span class="link-text">Attendances</span>
                    </a>
                </li>
                <li class="nav-item">
                    <a href="{{ route('admin.location') }}"
                        class="nav-link {{ request()->routeIs('admin.location') || request()->routeIs('admin.locations.*') ? 'active' : '' }}" title="School Location">
                        <i class="bi bi-geo-alt-fill"></i>
                        <span class="link-text">Location</span>
                    </a>
                </li>
                <li class="nav-item">
                    <a href="{{ route('admin.attendance_settings') }}"
                        class="nav-link {{ request()->routeIs('admin.attendance_settings') ? 'active' : '' }}" title="Settings Time">
                        <i class="bi bi-stopwatch-fill"></i>
                        <span class="link-text">Time Settings</span>
                    </a>
                </li>
            </ul>
        </div>

        <div class="mt-auto p-3 border-top border-secondary border-opacity-10">
            <form action="{{ route('logout') }}" method="POST" id="logout-form">
                @csrf
                <button type="submit" class="nav-link w-100 text-danger bg-transparent border-0 d-flex align-items-center gap-3" title="Logout">
                    <i class="bi bi-box-arrow-right"></i>
                    <span class="link-text fw-semibold">Logout</span>
                </button>
            </form>
        </div>
    </div>

    <!-- Navbar -->
    <nav class="navbar navbar-expand-lg navbar-dark sticky-top">
        <div class="container-fluid">
            <div class="d-flex align-items-center gap-3">
                <span class="text-white-50 border-end pe-3 d-none d-md-inline">Terminal #01</span>
                <span class="navbar-brand fw-bold m-0 p-0 text-white">Administrator Portal</span>
            </div>
            
            <div class="ms-auto d-flex align-items-center gap-3">
                <div class="dropdown">
                    <a class="nav-link dropdown-toggle d-flex align-items-center gap-3" href="#" role="button"
                        data-bs-toggle="dropdown" aria-expanded="false">
                        <div class="avatar-sm">
                            <div class="bg-primary bg-gradient rounded-circle d-flex align-items-center justify-content-center text-white fw-bold" style="width: 38px; height: 38px;">
                                {{ strtoupper(substr(Auth::user()->name ?? 'A', 0, 1)) }}
                            </div>
                        </div>
                        <div class="d-none d-md-block text-start">
                            <div class="fw-semibold text-white line-height-1" style="font-size: 0.9rem;">{{ Auth::user()->name ?? 'Admin' }}</div>
                            <div class="text-white-50 small" style="font-size: 0.75rem;">Super Admin</div>
                        </div>
                    </a>
                    <ul class="dropdown-menu dropdown-menu-end glass shadow py-2 mt-2 border-0" style="min-width: 260px;">
                        <li class="px-3 py-3 border-bottom border-secondary border-opacity-10">
                            <div class="d-flex align-items-center gap-3">
                                <div class="bg-primary bg-gradient rounded-circle d-flex align-items-center justify-content-center text-white fw-bold" style="width: 48px; height: 48px; font-size: 1.25rem;">
                                    {{ strtoupper(substr(Auth::user()->name ?? 'A', 0, 1)) }}
                                </div>
                                <div class="overflow-hidden">
                                    <div class="fw-bold text-white text-truncate">{{ Auth::user()->name ?? 'Admin' }}</div>
                                    <div class="text-white-50 small text-truncate">{{ Auth::user()->email ?? '' }}</div>
                                </div>
                            </div>
                        </li>
                        <li><a class="dropdown-item py-2 text-white-50 mt-2" href="{{ route('admin.attendance_settings') }}"><i class="bi bi-gear me-2"></i> Settings</a></li>
                        <li><hr class="dropdown-divider border-secondary border-opacity-10"></li>
                        <li>
                            <button type="submit" form="logout-form" class="dropdown-item py-2 text-danger">
                                <i class="bi bi-box-arrow-right me-2"></i> Sign Out
                            </button>
                        </li>
                    </ul>
                </div>
            </div>
        </div>
    </nav>

    <!-- Content -->
    <div class="content-wrapper">
        <div class="container-fluid">
            @yield('content')
        </div>
    </div>

    <script src="https://code.jquery.com/jquery-3.7.1.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        // Sidebar Toggle
        const toggleBtn = document.getElementById('toggleSidebar');
        const sidebar = document.getElementById('sidebar');
        
        if (toggleBtn && sidebar) {
            toggleBtn.addEventListener('click', () => {
                sidebar.classList.toggle('collapsed');
                localStorage.setItem('sidebar-collapsed', sidebar.classList.contains('collapsed'));
            });

            // Restore State
            if (localStorage.getItem('sidebar-collapsed') === 'true') {
                sidebar.classList.add('collapsed');
            }
        }

        // Initialize Tooltips
        const tooltipTriggerList = [].slice.call(document.querySelectorAll('[data-bs-toggle="tooltip"]'))
        const tooltipList = tooltipTriggerList.map(function (tooltipTriggerEl) {
            return new bootstrap.Tooltip(tooltipTriggerEl)
        })
    </script>
    @stack('scripts')
</body>

</html>
