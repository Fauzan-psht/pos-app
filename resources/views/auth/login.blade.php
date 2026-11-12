<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login - POS App</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.0/font/bootstrap-icons.css">
    <style>
        body {
            background: linear-gradient(135deg, #e0eafc 0%, #cfdef3 100%);
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            margin: 0;
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
        }
        .login-card {
            width: 100%;
            max-width: 420px;
            border: none;
            border-radius: 20px;
            box-shadow: 0 20px 40px rgba(0, 0, 0, 0.08);
            background: #ffffff;
            padding: 40px 30px;
        }
        .store-icon {
            width: 80px;
            height: 80px;
            background: #eef5ff;
            border-radius: 20px;
            display: flex;
            align-items: center;
            justify-content: center;
            margin: 0 auto 20px;
        }
        .store-icon svg {
            width: 45px;
            height: 45px;
            fill: #0d6efd;
        }
        .login-header h3 {
            font-weight: 800;
            color: #2b3445;
            margin-bottom: 5px;
            letter-spacing: -0.5px;
        }
        .login-header p {
            color: #8b96a5;
            font-size: 14px;
            margin-bottom: 30px;
        }
        .form-label {
            font-size: 13px;
            font-weight: 600;
            color: #4a5568;
            margin-bottom: 6px;
        }
        .input-group-text {
            background-color: #f8fafc;
            border: 1px solid #e2e8f0;
            border-right: none;
            border-radius: 12px 0 0 12px;
            color: #8b96a5;
        }
        .form-control {
            border: 1px solid #e2e8f0;
            border-left: none;
            border-radius: 0 12px 12px 0;
            padding: 14px 15px;
            font-size: 15px;
            background-color: #f8fafc;
            transition: all 0.3s ease;
        }
        .form-control:focus {
            background-color: #ffffff;
            box-shadow: 0 0 0 4px rgba(13, 110, 253, 0.1);
            border-color: #0d6efd;
        }
        .input-group:focus-within .input-group-text {
            background-color: #ffffff;
            border-color: #0d6efd;
            color: #0d6efd;
        }
        .btn-login {
            background: #0d6efd;
            border: none;
            border-radius: 12px;
            padding: 14px;
            font-weight: 700;
            font-size: 16px;
            letter-spacing: 0.5px;
            transition: all 0.3s ease;
            margin-top: 10px;
        }
        .btn-login:hover {
            background: #0b5ed7;
            transform: translateY(-2px);
            box-shadow: 0 10px 20px rgba(13, 110, 253, 0.25);
        }
    </style>
</head>
<body>

    <div class="card login-card">
        <div class="login-header text-center">
            <!-- IKON MINIMARKET MODERN -->
            <div class="store-icon">
                <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24">
                    <path d="M2 5h20v3H2V5zm2 5h16v11H4V10zm2 2v7h4v-7H6zm6 0v7h4v-7h-4z"/>
                    <circle cx="9" cy="4" r="1"/>
                    <circle cx="15" cy="4" r="1"/>
                    <path d="M10 2h4l1 1h-6l1-1z"/>
                </svg>
            </div>
            <h3>POS App</h3>
            <p>Silakan login untuk melanjutkan</p>
        </div>
        
        <div class="login-body">
            @if($errors->any())
                <div class="alert alert-danger alert-dismissible fade show" role="alert" style="border-radius: 12px; font-size: 14px; border: none; background-color: #fee2e2; color: #b91c1c;">
                    <i class="bi bi-exclamation-triangle-fill me-2"></i> {{ $errors->first() }}
                    <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                </div>
            @endif

            <form action="{{ route('login.process') }}" method="POST">
                @csrf
                <div class="mb-4">
                    <label class="form-label">Email Address</label>
                    <div class="input-group">
                        <span class="input-group-text"><i class="bi bi-envelope"></i></span>
                        <input type="email" name="email" class="form-control @error('email') is-invalid @enderror" value="{{ old('email') }}" placeholder="admin@pos.test" required autofocus>
                    </div>
                </div>

                <div class="mb-4">
                    <label class="form-label">Password</label>
                    <div class="input-group">
                        <span class="input-group-text"><i class="bi bi-lock"></i></span>
                        <input type="password" name="password" class="form-control @error('password') is-invalid @enderror" placeholder="••••••••" required>
                    </div>
                </div>

                <button type="submit" class="btn btn-primary w-100 btn-login">
                    Masuk <i class="bi bi-arrow-right ms-2"></i>
                </button>
            </form>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>