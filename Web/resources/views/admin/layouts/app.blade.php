<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin - E-Presensi</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;600;700&display=swap" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@4.5.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.5/font/bootstrap-icons.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.3/css/all.min.css">
    <style>
        :root {
            --sidebar-bg: #004a99;
            --sidebar-hover: #0066cc;
            --navbar-bg: #005bb5;
            --link-active-bg: #007fff;
            --body-bg: #f7fbff;
            --card-bg: #ffffff;
            --card-shadow: rgba(0, 0, 0, 0.1);
            --text-main: #1e293b;
            --text-light: #ffffff;
        }

        body {
            font-family: 'Inter', sans-serif;
            background-color: var(--body-bg);
            color: var(--text-main);
        }

        /* Sidebar */
        .sidebar {
            width: 240px;
            min-height: 100vh;
            background-color: var(--sidebar-bg);
            position: fixed;
            transition: width 0.3s;
            box-shadow: 2px 0 12px var(--card-shadow);
        }

        .sidebar.collapsed {
            width: 70px;
        }

        .sidebar .nav-link {
            color: var(--text-light);
            padding: 0.75rem 1rem;
            margin: 0.25rem 0;
            border-radius: 0.375rem;
            transition: background 0.3s;
            display: flex;
            align-items: center;
            position: relative;
        }

        .sidebar .nav-link:hover {
            background-color: var(--sidebar-hover);
            text-decoration: none;
        }

        .sidebar .nav-link.active {
            background-color: var(--link-active-bg);
            box-shadow: inset 4px 0 0 var(--text-light);
        }

        .sidebar .bi {
            font-size: 1.3rem;
        }

        .sidebar .link-text {
            margin-left: 0.75rem;
            opacity: 1;
            transition: opacity 0.3s;
        }

        .sidebar.collapsed .link-text {
            opacity: 0;
            width: 0;
            margin: 0;
            pointer-events: none;
        }

        .sidebar .nav-link {
            position: relative;
        }

        .sidebar.collapsed .nav-link {
            justify-content: center;
        }

        .sidebar.collapsed .nav-link[title]:hover::after {
            content: attr(title);
            position: absolute;
            left: 60px;
            top: 50%;
            transform: translateY(-50%);
            background: #222;
            color: #fff;
            padding: 4px 12px;
            border-radius: 6px;
            white-space: nowrap;
            font-size: 0.95rem;
            z-index: 1000;
            box-shadow: 0 2px 8px rgba(0, 0, 0, 0.15);
        }

        /* Top Navbar */
        .navbar {
            margin-left: 240px;
            background-color: var(--navbar-bg) !important;
            box-shadow: 0 2px 8px var(--card-shadow);
            transition: margin-left 0.3s;
        }

        .sidebar.collapsed~.navbar {
            margin-left: 70px;
        }

        .navbar .navbar-brand {
            font-weight: 700;
            font-size: 1.3rem;
            color: var(--text-light);
        }

        .navbar .nav-link {
            color: var(--text-light) !important;
            transition: color 0.3s;
        }

        /* Content */
        .content-wrapper {
            margin-left: 240px;
            padding: 2rem;
            transition: margin-left 0.3s;
        }

        .sidebar.collapsed~.content-wrapper {
            margin-left: 70px;
        }

        /* Cards */
        .card-custom {
            background-color: var(--card-bg);
            border: none;
            border-radius: 0.75rem;
            box-shadow: 0 6px 18px var(--card-shadow);
            transition: transform 0.3s;
        }

        .card-custom:hover {
            transform: translateY(-6px);
        }

        /* Footer */
        .footer {
            text-align: center;
            padding: 1rem;
            color: var(--text-light);
            background-color: var(--navbar-bg);
        }

        /* Toggle Button */
        .toggle-btn {
            font-size: 1.5rem;
            color: var(--text-light);
            cursor: pointer;
            transition: transform 0.3s;
        }

        .toggle-btn:hover {
            transform: rotate(90deg);
        }
    </style>
</head>

<body>
    <!-- Sidebar -->
    <div class="sidebar d-flex flex-column p-3" id="sidebar">
        <div class="d-flex align-items-center justify-content-between mb-4">
            <span class="fs-4 fw-bold text-light text-truncate">E-Presensi</span>
            <i id="toggleSidebar" class="bi bi-list toggle-btn"></i>
        </div>
        <ul class="nav nav-pills flex-column mb-auto">
            <li class="nav-item">
                <a href="{{ route('admin.dashboard') }}"
                    class="nav-link {{ request()->routeIs('admin.dashboard') ? 'active' : '' }}" title="Dashboard">
                    <i class="bi bi-speedometer2"></i>
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
                    <i class="bi bi-calendar-check"></i>
                    <span class="link-text">Attendances</span>
                </a>
            </li>
            <li class="nav-item">
                <a href="{{ route('admin.lokasi') }}"
                    class="nav-link {{ request()->routeIs('admin.lokasi') || request()->routeIs('admin.locations.*') ? 'active' : '' }}" title="School Location">
                    <i class="bi bi-geo-alt-fill"></i>
                    <span class="link-text">Location</span>
                </a>
            </li>
        </ul>
        <div class="mt-auto">
            <form action="{{ route('login') }}" method="POST" class="d-inline">
                @csrf
                <button type="submit" class="nav-link text-light bg-transparent border-0" title="Logout">
                    <i class="bi bi-box-arrow-right"></i>
                    <span class="link-text">Logout</span>
                </button>
            </form>
        </div>
    </div>

    <!-- Navbar -->
    <nav class="navbar navbar-expand-lg navbar-dark">
        <div class="container-fluid">
            <span class="navbar-brand">Admin</span>
            <ul class="navbar-nav ms-auto align-items-center">
                <li class="nav-item dropdown">
                    <a class="nav-link dropdown-toggle d-flex align-items-center" href="#" role="button"
                        data-bs-toggle="dropdown" aria-expanded="false">
                        <i class="bi bi-person-circle fs-4 me-2"></i>{{ Auth::user()->name ?? 'Admin' }}
                    </a>
                    <ul class="dropdown-menu dropdown-menu-end">
                        <li><a class="dropdown-item" href="#"><i class="bi bi-gear me-2"></i>Settings</a></li>
                        <li>
                            <hr class="dropdown-divider">
                        </li>
                        <li><form action="{{ route('login') }}" method="POST" class="d-inline">
                            @csrf
                            <button type="submit" class="dropdown-item text-danger">
                                <i class="bi bi-box-arrow-right me-2"></i>Logout
                            </button>
                        </form></li>
                    </ul>
                </li>
            </ul>
        </div>
    </nav>

    <!-- Content -->
    <div class="content-wrapper">
        <div class="container-fluid">
            @yield('content')
        </div>
    </div>
    <script src="https://code.jquery.com/jquery-3.5.1.slim.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@4.5.3/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        $(function () {
            $('[data-toggle="tooltip"]').tooltip()
        })

        // Logout Confirmation
        function confirmLogout(event) {
            event.preventDefault();
            
            // Logout confirmation modal
            const modal = `
                <div class="modal fade" id="logoutModal" tabindex="-1" role="dialog">
                    <div class="modal-dialog" role="document">
                        <div class="modal-content">
                            <div class="modal-header">
                                <h5 class="modal-title">Logout Confirmation</h5>
                                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                                    <span aria-hidden="true">&times;</span>
                                </button>
                            </div>
                            <div class="modal-body">
                                Are you sure you want to log out?
                            </div>
                            <div class="modal-footer">
                                <button type="button" class="btn btn-secondary" data-dismiss="modal">Cancel</button>
                                <button type="button" class="btn btn-danger" id="confirmLogoutBtn">Log Out</button>
                            </div>
                        </div>
                    </div>
                </div>
            `;

            // Append modal to body
            $('body').append(modal);
            
            // Show modal
            $('#logoutModal').modal('show');

            // Add event listener to confirm button
            $('#confirmLogoutBtn').on('click', function() {
                // Submit logout form
                $('form[action="{{ route('login') }}"]').submit();
            });
        }

        // Add event listener to all logout buttons
        $(document).ready(function() {
            $('button[type="submit"][title="Logout"]').on('click', confirmLogout);
            $('.dropdown-item.text-danger').on('click', confirmLogout);
        });
    </script>
</body>

</html>
