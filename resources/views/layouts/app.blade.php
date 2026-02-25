<!DOCTYPE html>
<html lang="th">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>{{ config('app.name', 'ระบบจัดการถังดับเพลิง') }}</title>
    <!-- Fonts -->
    <link href="https://fonts.googleapis.com/css2?family=Sarabun:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <!-- Bootstrap CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <!-- Bootstrap Icons -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.5/font/bootstrap-icons.css">
    
    <style>
        body {
            font-family: 'Sarabun', sans-serif;
            background-color: #f4f6f9;
        }
        
        .sidebar {
            min-height: 100vh;
            background-color: #1e3a5f;
            color: #fff;
        }
        
        .sidebar-link {
            color: rgba(255,255,255,.8);
            text-decoration: none;
            padding: 10px 15px;
            display: block;
            border-radius: 5px;
            margin-bottom: 5px;
        }
        
        .sidebar-link:hover, .sidebar-link.active {
            color: #fff;
            background-color: rgba(255,255,255,.1);
        }
        
        .sidebar-link i {
            width: 20px;
            text-align: center;
            margin-right: 10px;
        }
        
        .nav-header {
            padding: 15px;
            font-size: 1.2rem;
            font-weight: 600;
            border-bottom: 1px solid rgba(255,255,255,.1);
            margin-bottom: 15px;
        }
        
        .main-content {
            padding: 20px;
            width: 100%;
        }
        
        .topbar {
            background-color: #fff;
            box-shadow: 0 2px 4px rgba(0,0,0,.05);
            padding: 10px 20px;
            margin-bottom: 20px;
            border-radius: 8px;
            display: flex;
            justify-content: space-between;
            align-items: center;
        }
        
        /* Helpers */
        .color-primary { color: #1e3a5f; }
        .bg-primary-dark { background-color: #1e3a5f; }
    </style>
    @yield('styles')
</head>
<body>
    <div class="d-flex">
        <!-- Sidebar -->
        <nav class="sidebar flex-shrink-0 p-3" style="width: 250px;">
            <div class="nav-header d-flex align-items-center">
                <i class="bi bi-fire text-danger fs-4 me-2"></i>
                CFARM FEM System
            </div>
            
            <ul class="nav flex-column mb-auto">
                <li class="nav-item">
                    <a href="{{ route('dashboard') }}" class="sidebar-link {{ request()->routeIs('dashboard') ? 'active' : '' }}">
                        <i class="bi bi-speedometer2"></i> Dashboard
                    </a>
                </li>
                <li>
                    <a href="{{ route('extinguishers.index') }}" class="sidebar-link {{ request()->routeIs('extinguishers.*') ? 'active' : '' }}">
                        <i class="bi bi-heptagon"></i> จัดการถังดับเพลิง
                    </a>
                </li>
                <li>
                    <a href="{{ route('inspections.index') }}" class="sidebar-link {{ request()->routeIs('inspections.*') ? 'active' : '' }}">
                        <i class="bi bi-clipboard2-check"></i> ตรวจเช็ค (Inspections)
                    </a>
                </li>
                <li>
                    <a href="{{ route('repair-logs.index') }}" class="sidebar-link {{ request()->routeIs('repair-logs.*') ? 'active' : '' }}">
                        <i class="bi bi-tools"></i> ซ่อมบำรุง
                    </a>
                </li>
                <li>
                    <a href="{{ route('reports.index') }}" class="sidebar-link {{ request()->routeIs('reports.*') ? 'active' : '' }}">
                        <i class="bi bi-file-earmark-bar-graph"></i> รายงาน
                    </a>
                </li>

                <!-- Admin Only -->
                @if(auth()->check() && auth()->user()->role == 'admin')
                <hr style="border-color: rgba(255,255,255,.2)">
                <div class="text-uppercase text-muted px-3 mb-2" style="font-size: 0.75rem;">ผู้ดูแลระบบ</div>
                <li>
                    <a href="{{ route('locations.index') }}" class="sidebar-link {{ request()->routeIs('locations.*') ? 'active' : '' }}">
                        <i class="bi bi-geo-alt"></i> พื้นที่ติดตั้ง
                    </a>
                </li>
                <li>
                    <a href="{{ route('users.index') }}" class="sidebar-link {{ request()->routeIs('users.*') ? 'active' : '' }}">
                        <i class="bi bi-people"></i> ผู้ใช้งานระบบ
                    </a>
                </li>
                <li>
                    <a href="{{ route('settings.index') }}" class="sidebar-link {{ request()->routeIs('settings.*') ? 'active' : '' }}">
                        <i class="bi bi-gear"></i> ตั้งค่าระบบ
                    </a>
                </li>
                @endif
            </ul>
        </nav>

        <!-- Main Content -->
        <main class="main-content">
            <!-- Topbar -->
            <header class="topbar">
                <div class="d-flex align-items-center">
                    <button class="btn btn-light d-md-none me-2" id="sidebarToggle">
                        <i class="bi bi-list"></i>
                    </button>
                    <h5 class="mb-0 text-dark fw-bold">@yield('page_title', 'Dashboard')</h5>
                </div>
                
                <div class="d-flex align-items-center">
                    <div class="dropdown me-3">
                        <button class="btn btn-light position-relative" type="button" id="notifDropdown" data-bs-toggle="dropdown" aria-expanded="false">
                            <i class="bi bi-bell"></i>
                            <span class="position-absolute top-0 start-100 translate-middle p-1 bg-danger border border-light rounded-circle">
                                <span class="visually-hidden">New alerts</span>
                            </span>
                        </button>
                        <ul class="dropdown-menu dropdown-menu-end" aria-labelledby="notifDropdown">
                            <li><h6 class="dropdown-header">การแจ้งเตือนล่าสุด</h6></li>
                            <li><a class="dropdown-item" href="#">แจ้งเตือนจำลอง 1</a></li>
                            <li><hr class="dropdown-divider"></li>
                            <li><a class="dropdown-item text-center" href="#">ดูทั้งหมด</a></li>
                        </ul>
                    </div>

                    <div class="dropdown">
                        <a href="#" class="d-flex align-items-center link-dark text-decoration-none dropdown-toggle" id="userDropdown" data-bs-toggle="dropdown" aria-expanded="false">
                            <img src="https://ui-avatars.com/api/?name={{ auth()->user()->name ?? 'User' }}&background=0D8ABC&color=fff" alt="User" width="32" height="32" class="rounded-circle me-2">
                            <strong>{{ auth()->user()->name ?? 'Guest User' }}</strong>
                        </a>
                        <ul class="dropdown-menu dropdown-menu-end text-small shadow" aria-labelledby="userDropdown">
                            <li><a class="dropdown-item" href="#">โปรไฟล์</a></li>
                            <li><hr class="dropdown-divider"></li>
                            <li>
                                <form method="POST" action="/logout">
                                    @csrf
                                    <button type="submit" class="dropdown-item">ออกจากระบบ</button>
                                </form>
                            </li>
                        </ul>
                    </div>
                </div>
            </header>

            <!-- Alerts -->
            @if(session('success'))
            <div class="alert alert-success alert-dismissible fade show shadow-sm" role="alert">
                <i class="bi bi-check-circle-fill me-2"></i> {{ session('success') }}
                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
            </div>
            @endif
            
            @if(session('error'))
            <div class="alert alert-danger alert-dismissible fade show shadow-sm" role="alert">
                <i class="bi bi-exclamation-triangle-fill me-2"></i> {{ session('error') }}
                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
            </div>
            @endif

            <!-- Content Slot -->
            <div class="container-fluid px-0">
                @yield('content')
            </div>

        </main>
    </div>

    <!-- Scripts -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    @yield('scripts')
</body>
</html>
