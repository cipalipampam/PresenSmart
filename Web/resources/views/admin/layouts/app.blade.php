<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin - PresenSmart</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;800&display=swap" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.0/font/bootstrap-icons.css">
    <link rel="stylesheet" href="{{ asset('css/admin.css') }}">
    @stack('styles')
</head>

<body>
    <!-- ===================== SIDEBAR ===================== -->
    <div class="sidebar d-flex flex-column" id="sidebar">
        <div class="sidebar-header">
            <div class="sidebar-logo-full">
                <div class="sidebar-logo-icon">PS</div>
                <span class="fs-6 fw-bold text-gradient link-text">PresenSmart</span>
            </div>
            <button id="toggleSidebar" class="btn p-1 border-0 bg-transparent text-white-50 ms-auto" style="font-size:1.25rem;" title="Toggle sidebar">
                <i class="bi bi-layout-sidebar-reverse"></i>
            </button>
        </div>

        <div class="py-3 flex-grow-1">
            <p class="text-white-50 px-3 mb-2" style="font-size:0.65rem; font-weight:700; letter-spacing:0.1em; text-transform:uppercase;">Main Navigation</p>
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
                    <a href="{{ route('admin.announcements.index') }}"
                        class="nav-link {{ request()->routeIs('admin.announcements.*') ? 'active' : '' }}" title="Announcements">
                        <i class="bi bi-megaphone-fill"></i>
                        <span class="link-text">Announcements</span>
                    </a>
                </li>
            </ul>

            <p class="text-white-50 px-3 mb-2 mt-3" style="font-size:0.65rem; font-weight:700; letter-spacing:0.1em; text-transform:uppercase;">Configuration</p>
            <ul class="nav nav-pills flex-column">
                <li class="nav-item">
                    <a href="{{ route('admin.settings.index') }}"
                        class="nav-link {{ request()->routeIs('admin.settings.*') ? 'active' : '' }}" title="System Settings">
                        <i class="bi bi-gear-fill"></i>
                        <span class="link-text">System Settings</span>
                    </a>
                </li>
            </ul>
        </div>

        <!-- Hidden logout form (used by navbar dropdown) -->
        <form action="{{ route('logout') }}" method="POST" id="logout-form">
            @csrf
        </form>
    </div>

    <!-- ===================== NAVBAR ===================== -->
    <nav class="navbar navbar-expand-lg navbar-dark sticky-top">
        <div class="container-fluid">
            <div class="d-flex align-items-center gap-2">
                <span class="fw-semibold text-white-50" style="font-size:0.875rem;">Admin Portal</span>
            </div>

            <div class="ms-auto d-flex align-items-center gap-2">
                <!-- Bell notification placeholder -->
                <button class="btn btn-sm p-2 border-0 bg-transparent text-white-50 position-relative" title="Notifikasi" style="font-size:1.15rem;">
                    <i class="bi bi-bell"></i>
                </button>

                <!-- Profile dropdown -->
                <div class="dropdown">
                    <a class="nav-link dropdown-toggle d-flex align-items-center gap-2 px-2 py-1 rounded-2" href="#" role="button"
                        data-bs-toggle="dropdown" aria-expanded="false" style="background:rgba(255,255,255,0.05);">
                        <div class="bg-primary rounded-circle d-flex align-items-center justify-content-center text-white fw-bold"
                             style="width:32px;height:32px;font-size:0.8rem;background:linear-gradient(135deg,#06b6d4,#0d9488)!important;">
                            {{ strtoupper(substr(Auth::user()->name ?? 'A', 0, 1)) }}
                        </div>
                        <div class="d-none d-md-block text-start">
                            <div class="fw-semibold text-white" style="font-size:0.82rem;line-height:1.2;">{{ Auth::user()->name ?? 'Admin' }}</div>
                            <div class="text-white-50" style="font-size:0.7rem;">Super Admin</div>
                        </div>
                        <i class="bi bi-chevron-down text-white-50 ms-1" style="font-size:0.65rem;"></i>
                    </a>
                    <ul class="dropdown-menu dropdown-menu-end glass shadow py-2 mt-2 border-0" style="min-width:240px;">
                        <li class="px-3 py-2 border-bottom border-secondary border-opacity-10">
                            <div class="d-flex align-items-center gap-3">
                                <div class="rounded-circle d-flex align-items-center justify-content-center text-white fw-bold"
                                     style="width:42px;height:42px;font-size:1.1rem;background:linear-gradient(135deg,#06b6d4,#0d9488);">
                                    {{ strtoupper(substr(Auth::user()->name ?? 'A', 0, 1)) }}
                                </div>
                                <div class="overflow-hidden">
                                    <div class="fw-bold text-white text-truncate" style="font-size:0.875rem;">{{ Auth::user()->name ?? 'Admin' }}</div>
                                    <div class="text-white-50 small text-truncate">{{ Auth::user()->email ?? '' }}</div>
                                </div>
                            </div>
                        </li>
                        <li>
                            <a class="dropdown-item mt-1" href="{{ route('admin.settings.index') }}">
                                <i class="bi bi-gear me-2 text-white-50"></i>System Settings
                            </a>
                        </li>
                        <li><hr class="dropdown-divider"></li>
                        <li>
                            <button type="submit" form="logout-form" class="dropdown-item text-danger">
                                <i class="bi bi-box-arrow-right me-2"></i>Logout
                            </button>
                        </li>
                    </ul>
                </div>
            </div>
        </div>
    </nav>

    <!-- ===================== CONTENT ===================== -->
    <div class="content-wrapper">
        <div class="container-fluid animate-fade-up">
            @yield('content')
        </div>
    </div>

    <!-- ===================== GLOBAL DELETE MODAL ===================== -->
    <div class="modal fade" id="deleteModal" tabindex="-1" aria-labelledby="deleteModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content glass border-0 shadow-lg">
                <div class="modal-header border-0 pb-0">
                    <div class="d-flex align-items-center gap-3">
                        <div class="rounded-circle d-flex align-items-center justify-content-center"
                             style="width:44px;height:44px;background:rgba(239,68,68,0.15);">
                            <i class="bi bi-trash3-fill text-danger fs-5"></i>
                        </div>
                        <div>
                            <h6 class="modal-title fw-bold text-white mb-0" id="deleteModalLabel">Confirm Deletion</h6>
                            <p class="text-white-50 small mb-0">This action cannot be undone</p>
                        </div>
                    </div>
                    <button type="button" class="btn-close btn-close-white ms-auto" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body pt-3">
                    <p class="text-white-50 mb-0">
                        Are you sure you want to delete <strong class="text-white" id="deleteTargetName">—</strong>?
                        Deleted data cannot be recovered.
                    </p>
                </div>
                <div class="modal-footer border-0 pt-0">
                    <button type="button" class="btn btn-sm btn-outline-secondary border-0 bg-light-soft text-white px-4"
                            data-bs-dismiss="modal">Cancel</button>
                    <form id="deleteForm" method="POST">
                        @csrf
                        @method('DELETE')
                        <button type="submit" class="btn btn-sm btn-danger px-4 fw-semibold">
                            <i class="bi bi-trash3 me-1"></i>Yes, Delete
                        </button>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <!-- ===================== COPY TOAST ===================== -->
    <div id="copy-toast">
        <i class="bi bi-clipboard-check me-1"></i> Copied!
    </div>

    @stack('modals')

    <script src="https://code.jquery.com/jquery-3.7.1.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        // ---- Sidebar toggle ----
        const toggleBtn  = document.getElementById('toggleSidebar');
        const sidebar    = document.getElementById('sidebar');

        if (toggleBtn && sidebar) {
            toggleBtn.addEventListener('click', () => {
                sidebar.classList.toggle('collapsed');
                localStorage.setItem('sidebar-collapsed', sidebar.classList.contains('collapsed'));
            });
            if (localStorage.getItem('sidebar-collapsed') === 'true') {
                sidebar.classList.add('collapsed');
            }
        }

        // ---- Global delete modal ----
        document.querySelectorAll('[data-delete-url]').forEach(btn => {
            btn.addEventListener('click', e => {
                e.preventDefault();
                document.getElementById('deleteForm').action = btn.dataset.deleteUrl;
                document.getElementById('deleteTargetName').textContent = btn.dataset.deleteName || 'data ini';
                new bootstrap.Modal(document.getElementById('deleteModal')).show();
            });
        });

        // ---- Copy to clipboard ----
        const copyToast = document.getElementById('copy-toast');
        let toastTimer;

        window.copyToClipboard = function(text) {
            navigator.clipboard.writeText(text).then(() => {
                clearTimeout(toastTimer);
                copyToast.classList.add('show');
                toastTimer = setTimeout(() => copyToast.classList.remove('show'), 2000);
            });
        };

        // ---- Tooltips ----
        document.querySelectorAll('[data-bs-toggle="tooltip"]').forEach(el => new bootstrap.Tooltip(el));
    </script>
    @stack('scripts')
</body>

</html>
