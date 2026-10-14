@php $siteName = \App\Models\Setting::siteName(); @endphp
<!DOCTYPE html>
<html lang="id">
<head>
    <meta name="description"
        content="Perpustakaan online untuk mencari koleksi buku, melihat informasi buku, dan mengakses layanan perpustakaan.">

    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">

        <title>@yield('title', $siteName)</title>

    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">

    {{-- Font tema sama dengan website User --}}
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&family=Poppins:wght@600;700&display=swap" rel="stylesheet">

    <style>
        /* === Palet warna diadaptasi dari tema User (css/perpus-site.css) === */
        :root {
            --navy: #111827;
            --navy-dark: #374151;
            --cream: #F7F8FA;
            --gold: #16C94A;
            --gold-dark: #109E3B;
            --ink: #111827;
            --border: #E8EAEE;
            --card-bg: #FFFFFF;
            --table-th-bg: #F8FAFB;
            --teks-hijau: #06330F;
            --muted: #667085;
            --link: #109E3B;
            --link-hover: #0C7A2D;
            --hijau-lembut: #E8FBEE;
            --hijau-border: #BEEBCB;
            --shadow-halus: 0 1px 2px rgba(17,24,39,.04), 0 2px 8px rgba(17,24,39,.04);
        }

        body {
            background-color: var(--cream);
            color: var(--ink);
            font-family: "Inter", system-ui, -apple-system, "Segoe UI", Roboto, sans-serif;
        }

        body h1,
        body h2,
        body h3,
        body h4,
        body h5,
        body h6 {
            font-family: "Poppins", "Inter", system-ui, sans-serif;
            font-weight: 700;
            color: var(--navy);
            letter-spacing: -0.01em;
        }

        body a {
            color: var(--link);
        }

        body a:hover {
            color: var(--link-hover);
        }

        .text-muted {
            color: var(--muted) !important;
        }

        .sidebar {
            position: fixed;
            inset: 0 auto 0 0;
            width: 250px;
            background: var(--navy);
            z-index: 1000;
            display: flex;
            flex-direction: column;
        }

        .sidebar .brand {
            padding: 18px 20px 16px;
            border-bottom: 1px solid rgba(255,255,255,.08);
            color: #fff;
            font-weight: 700;
            letter-spacing: .5px;
        }

        .sidebar .brand i {
            color: var(--gold);
        }

        .sidebar .brand small {
            display: block;
            color: #9CA3AF;
            font-size: .68rem;
            letter-spacing: 1.5px;
        }

        .sidebar .nav {
            padding: 10px 0;
            overflow-y: auto;
            overflow-x: hidden;
        }

        .sidebar .nav-link {
            color: #9CA3AF;
            padding: 11px 20px;
            display: flex;
            gap: 10px;
            align-items: center;
            border-left: 3px solid transparent;
            font-size: .93rem;
        }

        .sidebar .nav-link i {
            width: 20px;
            text-align: center;
        }

        .sidebar .nav-link:hover {
            background: rgba(255,255,255,.05);
            color: #fff;
        }

        .sidebar .nav-link.active {
            background: rgba(22,201,74,.12);
            color: var(--gold);
            border-left-color: var(--gold);
            font-weight: 600;
        }

        /* ===== Dropdown vertikal menu Report: submenu muncul ke bawah, tidak ke samping ===== */
        .report-submenu {
            max-height: 0;
            overflow: hidden;
            transition: max-height .25s ease;
        }

        .report-menu.open > .report-submenu {
            max-height: 600px;
        }

        .report-chevron {
            transition: transform .2s ease;
            transform: rotate(-90deg);
        }

        .report-menu.open > .report-toggle .report-chevron {
            transform: rotate(0);
        }

        .sidebar-footer {
            margin-top: auto;
            padding: 14px 16px;
            border-top: 1px solid rgba(255,255,255,.08);
        }

        .main-content {
            margin-left: 250px;
            min-height: 100vh;
            display: flex;
            flex-direction: column;
        }

        .topbar {
            background: #FFFFFF;
            border-bottom: 1px solid var(--border);
            padding: 14px 22px;
            display: flex;
            justify-content: space-between;
            align-items: center;
            gap: 12px;
            flex-wrap: wrap;
        }

        .content-area {
            padding: 22px;
            flex: 1;
        }

        .card {
            border: 1px solid var(--border);
            border-radius: 12px;
            background: var(--card-bg);
            box-shadow: var(--shadow-halus);
        }

        .card-header {
            background: var(--card-bg);
            border-bottom: 1px solid var(--border);
            font-weight: 600;
            color: var(--navy);
        }

        .table th {
            background: var(--table-th-bg);
            color: var(--muted);
            font-size: .85rem;
        }

        .table tbody td {
            border-bottom: 1px solid #F1F3F5;
        }

        .table tbody tr:last-child td {
            border-bottom: none;
        }

        .table tbody tr:hover td {
            background: #FAFCFA;
        }

        .btn-navy {
            background: var(--navy);
            border-color: var(--navy);
            color: #fff;
        }

        .btn-navy:hover {
            background: var(--navy-dark);
            border-color: var(--navy-dark);
            color: var(--gold);
        }

        .btn-gold {
            background: var(--gold);
            border-color: var(--gold);
            color: var(--teks-hijau);
            font-weight: 600;
        }

        .btn-gold:hover {
            background: var(--gold-dark);
            border-color: var(--gold-dark);
            color: var(--teks-hijau);
        }

        /* ===== Tombol aksi CRUD (warna diadaptasi dari palet User) ===== */
        .btn {
            font-weight: 600;
            border-radius: 9px;
        }

        .btn-warning {
            background-color: #FFE45C;
            border-color: #FFE45C;
            color: #8A6D00;
        }

        .btn-warning:hover {
            background-color: #8A6D00;
            border-color: #8A6D00;
            color: #FFFFFF;
        }

        .btn-outline-warning {
            background-color: #FFFFFF;
            border: 1.5px solid #FFE45C;
            color: #8A6D00;
        }

        .btn-outline-warning:hover {
            background-color: #FFFAE0;
            color: #8A6D00;
        }

        .btn-danger {
            background-color: #C0392B;
            border-color: #C0392B;
            color: #FFFFFF;
        }

        .btn-danger:hover {
            background-color: #A02C20;
            border-color: #A02C20;
            color: #FFFFFF;
        }

        .btn-outline-danger {
            background-color: #FFFFFF;
            border: 1.5px solid #C0392B;
            color: #C0392B;
        }

        .btn-outline-danger:hover {
            background-color: #FDF1EF;
            color: #A02C20;
        }

        .btn-outline-info {
            background-color: #FFFFFF;
            border: 1.5px solid #2F5FA8;
            color: #2F5FA8;
        }

        .btn-outline-info:hover {
            background-color: #EFF4FD;
            color: var(--navy);
        }

        .btn-outline-secondary {
            background-color: #FFFFFF;
            border: 1.5px solid #667085;
            color: #667085;
        }

        .btn-outline-secondary:hover {
            background-color: #667085;
            color: #FFFFFF;
        }

        .btn-outline-light {
            border: 1.5px solid #FFFFFF;
            color: #FFFFFF;
        }

        .btn-outline-light:hover {
            background-color: #FFFFFF;
            color: var(--navy);
        }

        /* ===== Badge (warna diadaptasi dari palet User) ===== */
        .badge.bg-success {
            background-color: #109E3B !important;
        }

        .badge.bg-primary {
            background-color: #2F5FA8 !important;
        }

        .badge.bg-secondary {
            background-color: #667085 !important;
        }

        .badge.bg-warning {
            background-color: #FFE45C !important;
            color: #8A6D00;
        }

        .badge.bg-danger {
            background-color: #C0392B !important;
        }

        .badge.bg-light.text-dark.border {
            background-color: #FFFFFF !important;
            color: #667085 !important;
            border-color: var(--border) !important;
        }

        /* ===== Form controls (dari User) ===== */
        .form-label {
            font-weight: 600;
            font-size: 0.88rem;
            color: var(--navy);
            margin-bottom: 0.35rem;
        }

        .form-control,
        .form-select {
            border-radius: 9px;
            border: 1px solid #D5D8DE;
            padding: 0.55rem 0.85rem;
            font-size: 0.95rem;
            color: var(--navy);
            background-color: #FFFFFF;
        }

        .form-control::placeholder {
            color: #98A2B3;
        }

        .form-control:focus,
        .form-select:focus {
            border-color: var(--gold);
            box-shadow: 0 0 0 0.2rem rgba(22,201,74,.14);
        }

        .form-check-input:checked {
            background-color: var(--gold);
            border-color: var(--gold);
        }

        .form-check-input:focus {
            box-shadow: 0 0 0 0.2rem rgba(22,201,74,.14);
            border-color: var(--gold);
        }

        /* ===== Stat card (dari User) ===== */
        .stat-card {
            background: var(--card-bg);
            border: 1px solid var(--border);
            border-radius: 12px;
            box-shadow: var(--shadow-halus);
            padding: 1.15rem 1.2rem;
            height: 100%;
        }

        /* ===== Alert (dari User) ===== */
        .alert-success {
            background-color: #E8FBEE;
            border-color: #BEEBCB;
            color: #0B6B2A;
        }

        .alert-danger {
            background-color: #FEF0EF;
            border-color: #F5CDC8;
            color: #A02C20;
        }

        @media (max-width: 991.98px) {
            .sidebar {
                transform: translateX(-100%);
                transition: transform .25s ease;
            }

            .sidebar.show {
                transform: none;
            }

            .main-content {
                margin-left: 0;
            }
        }

        @media (max-width: 575.98px) {
            .content-area {
                padding: 14px;
            }

            .table-responsive {
                font-size: .85rem;
            }
        }
    </style>
</head>

<body>

    <!-- ============ SIDEBAR ============ -->
    <div class="sidebar" id="adminSidebar">

        <div class="brand">
                        <i class="bi bi-book-half fs-4"></i> {{ $siteName }}
            <small>PANEL ADMIN</small>
        </div>

        <ul class="nav flex-column flex-grow-1">

            <li>
                <a class="nav-link {{ request()->routeIs('admin.dashboard') ? 'active' : '' }}"
                   href="{{ route('admin.dashboard') }}">
                    <i class="bi bi-speedometer2"></i> Dashboard
                </a>
            </li>

            <li>
                <a class="nav-link {{ request()->routeIs('admin.buku.*') ? 'active' : '' }}"
                   href="{{ route('admin.buku.index') }}">
                    <i class="bi bi-book"></i> Data Buku
                </a>
            </li>

            <li>
                <a class="nav-link {{ request()->routeIs('admin.anggota.*') ? 'active' : '' }}"
                   href="{{ route('admin.anggota.index') }}">
                    <i class="bi bi-people"></i> Anggota
                </a>
            </li>

            <li>
                <a class="nav-link {{ request()->routeIs('admin.transaksi.*') ? 'active' : '' }}"
                   href="{{ route('admin.transaksi.index') }}">
                    <i class="bi bi-arrow-left-right"></i> Transaksi
                </a>
            </li>

                        <li>
                <a class="nav-link {{ request()->routeIs('admin.history') ? 'active' : '' }}"
                   href="{{ route('admin.history') }}">
                    <i class="bi bi-clock-history"></i> History
                </a>
            </li>

            <li>
                <a class="nav-link {{ request()->routeIs('admin.settings.*') ? 'active' : '' }}"
                   href="{{ route('admin.settings.index') }}">
                    <i class="bi bi-gear"></i> Pengaturan
                </a>
            </li>

            <li class="report-menu {{ request()->routeIs('admin.report.*') ? 'open' : '' }}">
                <a class="nav-link {{ request()->routeIs('admin.report.*') ? 'active' : '' }} report-toggle"
                   href="#"
                   role="button"
                   aria-expanded="{{ request()->routeIs('admin.report.*') ? 'true' : 'false' }}"
                   aria-controls="reportSubmenu">
                    <i class="bi bi-file-earmark-bar-graph"></i> Report
                    <i class="bi bi-chevron-down report-chevron ms-auto"></i>
                </a>

                                <ul class="report-submenu list-unstyled small"
                    id="reportSubmenu"
                    style="background:rgba(22,201,74,.08);">

                    <li>
                        <a class="nav-link py-1 ps-5"
                           href="{{ route('admin.report.peminjaman') }}">
                            Laporan Peminjaman
                        </a>
                    </li>

                    <li>
                        <a class="nav-link py-1 ps-5"
                           href="{{ route('admin.report.pengembalian') }}">
                            Laporan Pengembalian
                        </a>
                    </li>

                    <li>
                        <a class="nav-link py-1 ps-5"
                           href="{{ route('admin.report.dashboard') }}">
                            Statistik Perpustakaan
                        </a>
                    </li>

                </ul>
            </li>

            <li class="mt-auto">
                <hr style="border-color:rgba(255,255,255,.08);">

                <a class="nav-link {{ request()->routeIs('user.profile.*') ? 'active' : '' }}"
                   href="{{ route('user.profile.index') }}">
                    <i class="bi bi-person"></i> Profile
                </a>
            </li>

        </ul>

        <div class="sidebar-footer">

            <a href="{{ route('beranda') }}"
               class="btn btn-outline-light btn-sm w-100 mb-2">
                <i class="bi bi-globe2"></i> Lihat Website
            </a>

            <form method="POST"
                action="{{ route('logout') }}"
                class="m-0">

                @csrf

                <button type="submit"
                        class="btn btn-outline-warning btn-sm w-100">
                    <i class="bi bi-box-arrow-right"></i> Logout
                </button>

            </form>

        </div>

    </div>

    <!-- ============ MAIN ============ -->
    <div class="main-content">

        <div class="topbar">

            <div class="d-flex align-items-center gap-2">

                <button class="btn btn-outline-secondary btn-sm d-lg-none"
                        id="sidebarToggle"
                        type="button"
                        aria-label="Menu">
                    <i class="bi bi-list fs-5"></i>
                </button>

                <h5 class="mb-0" style="color:#111827;">
                    @yield('page-title', 'Dashboard')
                </h5>

            </div>

            <div class="d-flex align-items-center gap-2 small">

                <span class="badge text-bg-light border">
                    {{ strtoupper(auth()->user()->role) }}
                </span>

                <span class="fw-semibold">
                    {{ auth()->user()->nama_lengkap }}
                </span>

            </div>

        </div>

        <div class="content-area">

            @if(session('success'))

                <div class="alert alert-success alert-dismissible fade show"
                     role="alert">

                    <i class="bi bi-check-circle"></i>
                    {{ session('success') }}

                    <button type="button"
                            class="btn-close"
                            data-bs-dismiss="alert">
                    </button>

                </div>

            @endif

            @if(session('error'))

                <div class="alert alert-danger alert-dismissible fade show"
                    role="alert">

                    <i class="bi bi-exclamation-triangle"></i>
                    {{ session('error') }}

                    <button type="button"
                            class="btn-close"
                            data-bs-dismiss="alert">
                    </button>

                </div>

            @endif

            @yield('content')

        </div>

    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>

    <script>
        document.getElementById('sidebarToggle')?.addEventListener('click', function () {
            document.getElementById('adminSidebar').classList.toggle('show');
        });

        document.addEventListener('click', function (e) {
            const sb = document.getElementById('adminSidebar');

            if (
                window.innerWidth < 992 &&
                sb.classList.contains('show') &&
                !sb.contains(e.target) &&
                !document.getElementById('sidebarToggle').contains(e.target)
            ) {
                sb.classList.remove('show');
            }
        });

        /* ===== Dropdown vertikal menu Report (submenu muncul ke bawah) ===== */
        (function () {
            const menu = document.querySelector('#adminSidebar .report-menu');
            if (!menu) return;
            const toggle = menu.querySelector('.report-toggle');
            const submenu = menu.querySelector('.report-submenu');

            toggle.addEventListener('click', function (e) {
                e.preventDefault();
                const opened = menu.classList.toggle('open');
                toggle.setAttribute('aria-expanded', opened ? 'true' : 'false');
                submenu.style.maxHeight = opened ? (submenu.scrollHeight + 'px') : '0';
            });
        })();
    </script>

    @stack('scripts')

</body>
</html>