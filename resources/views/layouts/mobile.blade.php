<!DOCTYPE html>
<html lang="th">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>ตรวจสอบถังดับเพลิง</title>
    <!-- Fonts -->
    <link href="https://fonts.googleapis.com/css2?family=Sarabun:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <!-- Bootstrap CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <!-- Bootstrap Icons -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.5/font/bootstrap-icons.css">
    
    <style>
        body {
            font-family: 'Sarabun', sans-serif;
            background-color: #f8f9fa;
            /* Mobile optimisations */
            -webkit-tap-highlight-color: transparent;
        }
        
        .mobile-header {
            background-color: #1e3a5f;
            color: #fff;
            padding: 15px;
            position: sticky;
            top: 0;
            z-index: 1000;
            box-shadow: 0 2px 10px rgba(0,0,0,0.1);
        }
        
        .mobile-header h5 {
            margin: 0;
            font-weight: 600;
        }
        
        .mobile-back-btn {
            color: #fff;
            text-decoration: none;
            font-size: 1.2rem;
            margin-right: 15px;
        }

        .floating-action {
            position: fixed;
            bottom: 20px;
            left: 20px;
            right: 20px;
            z-index: 999;
        }
        
        .floating-action .btn {
            border-radius: 50px;
            box-shadow: 0 4px 15px rgba(0,0,0,0.2);
            font-weight: 600;
            font-size: 18px;
            padding: 12px;
        }

        /* Toggles */
        .inspection-toggle {
            transform: scale(1.2);
        }
    </style>
    @yield('styles')
</head>
<body class="pb-5 mb-5">
    
    <div class="mobile-header d-flex align-items-center">
        <a href="{{ route('dashboard') }}" class="mobile-back-btn"><i class="bi bi-arrow-left"></i></a>
        <h5>@yield('page_title', 'ระบุถังที่ตรวจ')</h5>
    </div>

    <!-- Content Slot -->
    <div class="container-fluid p-3">
        @if(session('success'))
            <div class="alert alert-success alert-dismissible fade show shadow-sm" role="alert">
                {{ session('success') }}
                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
            </div>
        @endif
        
        @if(session('error'))
            <div class="alert alert-danger alert-dismissible fade show shadow-sm" role="alert">
                {{ session('error') }}
                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
            </div>
        @endif

        @yield('content')
    </div>

    <!-- Scripts -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    @yield('scripts')
</body>
</html>
