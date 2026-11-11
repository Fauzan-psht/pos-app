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
            background: linear-gradient(135deg, #f5f7fa 0%, #c3cfe2 100%);
            height: 100vh;
            margin: 0;
            display: flex;
            align-items: center;
            justify-content: center;
        }
        .login-card {
            width: 100%;
            max-width: 400px;
            border: none;
            border-radius: 15px;
            box-shadow: 0 10px 30px rgba(0, 0, 0, 0.1);
            background: #ffffff;
        }
        .login-header { text-align: center; padding: 30px 20px 10px; }
        .login-header h4 { font-weight: 700; color: #333; margin: 0; }
        .login-body { padding: 20px 30px 30px; }
        .form-control { border-radius: 10px; padding: 12px 15px; border: 1px solid #e1e1e1; }
        .form-control:focus { box-shadow: 0 0 0 0.25rem rgba(13, 110, 253, 0.15); border-color: #0d6efd; }
        .input-group-text { border-radius: 10px 0 0 10px; background-color: #f8f9fa; border: 1px solid #e1e1e1; border-right: none; color: #6c757d; }
        .input-group .form-control { border-radius: 0 10px 10px 0; border-left: none; }
        .input-group:focus-within .input-group-text { border-color: #0d6efd; }
        .btn-login {
            border-radius: 10px; padding: 12px; font-weight: 600; letter-spacing: 0.5px;
            background: #0d6efd; border: none; transition: all 0.3s ease;
        }
        .btn-login:hover { background: #0b5ed7; transform: translateY(-2px); box-shadow: 0 5px 15px rgba(13, 110, 253, 0.3); }
        .footer-text { text-align: center; font-size: 13px; color: #888; margin-top: 20px; }
        
        /* CSS untuk mewarnai SVG menjadi biru */
        .svg-logo-blue {
            width: 80px;
            height: auto;
            fill: #0d6efd;
            margin-bottom: 10px;
        }
    </style>
</head>
<body>
    <div class="card login-card">
        <div class="login-header">
            <!-- IKON TOKO (SVG) -->
            <svg class="svg-logo-blue" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24">
                <path d="M20 4H4v2h16V4zm1 10v-2l-1-5H4l-1 5v2h1v6h10v-6h4v6h2v-6h1zm-9 4H6v-4h6v4z"/>
                <path d="M12.5 2.5c.83 0 1.5.67 1.5 1.5s-.67 1.5-1.5 1.5S11 4.83 11 4s.67-1.5 1.5-1.5zM15 6h-3.5L9 9.5V14h1v-3.5h1V14h1v-3.5h1V14h1V8.5L15 6z"/>
            </svg>
            <h4>POS App</h4>
            <p class="text-muted" style="font-size: 14px;">Silakan login untuk melanjutkan</p>
        </div>
        
        <div class="login-body">
            @if($errors->any())
                <div class="alert alert-danger alert-dismissible fade show" role="alert" style="border-radius: 10px; font-size: 14px;">
                    <i class="bi bi-exclamation-triangle-fill me-2"></i> {{ $errors->first() }}
                    <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                </div>
            @endif

            <form action="{{ route('login.process') }}" method="POST">
                @csrf
                <div class="mb-3">
                    <label class="form-label text-muted" style="font-size: 13px; font-weight: 600;">Email Address</label>
                    <div class="input-group">
                        <span class="input-group-text"><i class="bi bi-envelope"></i></span>
                        <input type="email" name="email" class="form-control @error('email') is-invalid @enderror" value="{{ old('email') }}" placeholder="admin@pos.test" required autofocus>
                    </div>
                </div>

                <div class="mb-4">
                    <label class="form-label text-muted" style="font-size: 13px; font-weight: 600;">Password</label>
                    <div class="input-group">
                        <span class="input-group-text"><i class="bi bi-lock"></i></span>
                        <input type="password" name="password" class="form-control @error('password') is-invalid @enderror" placeholder="••••••••" required>
                    </div>
                </div>

                <button type="submit" class="btn btn-primary w-100 btn-login">
                    Masuk <i class="bi bi-arrow-right ms-1"></i>
                </button>
            </form>
        </div>
    </div>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>