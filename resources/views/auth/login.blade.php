<!DOCTYPE html>
<html lang="th">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>เข้าสู่ระบบ - {{ config('app.name', 'ระบบจัดการถังดับเพลิง') }}</title>
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
        .login-split {
            min-height: 100vh;
        }
        .login-sidebar {
            background: linear-gradient(135deg, #1e3a5f 0%, #0d213a 100%);
            color: white;
            display: flex;
            flex-direction: column;
            justify-content: center;
            align-items: center;
            padding: 3rem;
            position: relative;
            overflow: hidden;
        }
        .login-sidebar::before {
            content: '';
            position: absolute;
            top: -50%;
            left: -50%;
            width: 200%;
            height: 200%;
            background: radial-gradient(circle, rgba(255,255,255,0.05) 10%, transparent 40%);
            animation: pulse 8s infinite alternate;
        }
        @keyframes pulse {
            0% { transform: scale(1); }
            100% { transform: scale(1.1); }
        }
        .login-form-container {
            display: flex;
            align-items: center;
            justify-content: center;
            padding: 2rem;
            background-color: #ffffff;
        }
        .login-card {
            width: 100%;
            max-width: 450px;
            padding: 2.5rem;
            border-radius: 1rem;
            box-shadow: 0 10px 30px rgba(0,0,0,0.05);
            background: #fff;
            border: 1px solid rgba(0,0,0,0.05);
        }
        .form-control {
            padding: 0.75rem 1.25rem;
            border-radius: 0.5rem;
            border: 1px solid #dee2e6;
            background-color: #f8f9fa;
            transition: all 0.3s;
        }
        .form-control:focus {
            background-color: #ffffff;
            border-color: #1e3a5f;
            box-shadow: 0 0 0 0.25rem rgba(30,58,95,0.25);
        }
        .btn-primary {
            background-color: #1e3a5f;
            border-color: #1e3a5f;
            padding: 0.75rem 1.5rem;
            border-radius: 0.5rem;
            font-weight: 600;
            transition: all 0.3s ease;
        }
        .btn-primary:hover {
            background-color: #152b47;
            border-color: #152b47;
            transform: translateY(-2px);
            box-shadow: 0 5px 15px rgba(30,58,95,0.4);
        }
        .brand-icon {
            font-size: 5rem;
            color: #ff4757;
            margin-bottom: 1rem;
            filter: drop-shadow(0 0 15px rgba(255,71,87,0.4));
        }
    </style>
</head>
<body>

<div class="container-fluid p-0">
    <div class="row g-0 login-split">
        <!-- Left Side: Branding -->
        <div class="col-lg-5 col-xl-4 login-sidebar d-none d-lg-flex">
            <div class="text-center z-1">
                <i class="bi bi-fire brand-icon"></i>
                <h2 class="fw-bold mb-3 display-6">CFARM<br>FEM System</h2>
                <p class="lead opacity-75">ระบบบริหารจัดการและบำรุงรักษาอุปกรณ์ดับเพลิงอัจฉริยะ</p>
                <div class="mt-5 text-start border-start border-3 ps-3 border-danger">
                    <small class="d-block text-uppercase fw-bold text-danger mb-1"><i class="bi bi-shield-check me-1"></i> ความปลอดภัยต้องมาก่อน</small>
                    <small class="opacity-75">ติดตาม ตรวจเช็ค และแจ้งเตือนสถานะถังดับเพลิง<br>ได้อย่างมีประสิทธิภาพ</small>
                </div>
            </div>
        </div>

        <!-- Right Side: Login Form -->
        <div class="col-lg-7 col-xl-8 login-form-container">
            <div class="login-card">
                <!-- Mobile Header -->
                <div class="text-center mb-4 d-lg-none">
                    <i class="bi bi-fire text-danger" style="font-size: 3rem;"></i>
                    <h3 class="fw-bold text-dark mt-2">CFARM FEM System</h3>
                </div>

                <div class="mb-4">
                    <h3 class="fw-bold text-dark mb-1">ยินดีต้อนรับ 👋</h3>
                    <p class="text-muted">กรุณาเข้าสู่ระบบเพื่อดำเนินการต่อ</p>
                </div>

                <!-- Session Status -->
                @if (session('status'))
                    <div class="alert alert-success rounded-3 mb-4">
                        {{ session('status') }}
                    </div>
                @endif

                <form method="POST" action="{{ route('login') }}">
                    @csrf

                    <!-- Email Address -->
                    <div class="mb-4">
                        <label for="email" class="form-label fw-medium text-dark">อีเมล (Email)</label>
                        <div class="input-group">
                            <span class="input-group-text bg-light border-end-0 text-muted px-3"><i class="bi bi-envelope"></i></span>
                            <input id="email" type="email" class="form-control border-start-0 ps-0 @error('email') is-invalid @enderror" name="email" value="{{ old('email') }}" required autofocus placeholder="name@example.com">
                            @error('email')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>

                    <!-- Password -->
                    <div class="mb-4">
                        <label for="password" class="form-label fw-medium text-dark d-flex justify-content-between">
                            รหัสผ่าน (Password)
                            @if (Route::has('password.request'))
                                <a href="{{ route('password.request') }}" class="text-decoration-none small text-primary">ลืมรหัสผ่าน?</a>
                            @endif
                        </label>
                        <div class="input-group">
                            <span class="input-group-text bg-light border-end-0 text-muted px-3"><i class="bi bi-lock"></i></span>
                            <input id="password" type="password" class="form-control border-start-0 ps-0 @error('password') is-invalid @enderror" name="password" required placeholder="••••••••">
                            @error('password')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>

                    <!-- Remember Me -->
                    <div class="mb-4 form-check form-switch">
                        <input class="form-check-input" type="checkbox" id="remember_me" name="remember" style="cursor: pointer;">
                        <label class="form-check-label text-muted" for="remember_me" style="cursor: pointer;">จดจำการเข้าสู่ระบบ</label>
                    </div>

                    <div class="d-grid mt-5">
                        <button type="submit" class="btn btn-primary btn-lg shadow-sm">
                            <i class="bi bi-box-arrow-in-right me-2"></i> เข้าสู่ระบบ
                        </button>
                    </div>
                </form>
                
                <div class="text-center mt-5 text-muted small">
                    &copy; {{ date('Y') }} CFARM. All rights reserved.
                </div>
            </div>
        </div>
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
