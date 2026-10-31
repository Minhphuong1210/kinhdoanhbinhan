<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Đăng nhập</title>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;800&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
    <style>
        * {
            box-sizing: border-box;
            margin: 0;
            padding: 0
        }

        body {
            font-family: 'Inter', sans-serif;
            min-height: 100vh;
            background: linear-gradient(115deg, #288098 50%, #125e7e 50%);
            display: flex;
            align-items: center;
            justify-content: center;
            padding: 16px
        }

        .card {
            background: #fff;
            border-radius: 16px;
            padding: 40px 36px;
            width: 100%;
            max-width: 420px;
            box-shadow: 0 4px 24px rgba(0, 0, 0, .08);
            border: 1px solid #e8edf2
        }

        .logo {
            text-align: center;
            margin-bottom: 28px
        }

        .logo img {
            height: 56px;
            object-fit: contain;
            margin-bottom: 12px;
            display: block;
            margin-left: auto;
            margin-right: auto
        }

        .logo-icon {
            width: 56px;
            height: 56px;
            border-radius: 14px;
            background: linear-gradient(135deg, #4e8ef7, #6c63ff);
            display: flex;
            align-items: center;
            justify-content: center;
            margin: 0 auto 12px;
            font-size: 24px;
            color: #fff
        }

        .logo h2 {
            font-size: 20px;
            font-weight: 700;
            color: #1e2a3a
        }

        .logo p {
            font-size: 13px;
            color: #8a9bb0;
            margin-top: 3px
        }

        .form-group {
            margin-bottom: 16px
        }

        .form-group label {
            display: block;
            font-size: 12px;
            font-weight: 600;
            color: #374151;
            margin-bottom: 6px
        }

        .input-wrap {
            position: relative
        }

        .input-wrap i {
            position: absolute;
            left: 12px;
            top: 50%;
            transform: translateY(-50%);
            color: #9ca3af;
            font-size: 13px
        }

        .form-control {
            width: 100%;
            padding: 10px 14px 10px 37px;
            border: 1.5px solid #e2e8f0;
            border-radius: 9px;
            font-size: 13px;
            font-family: inherit;
            color: #1e2a3a;
            background: #fff;
            transition: .2s;
            outline: none
        }

        .form-control:focus {
            border-color: #4e8ef7;
            box-shadow: 0 0 0 3px rgba(78, 142, 247, .1)
        }

        .btn-submit {
            width: 100%;
            padding: 12px;
            background: linear-gradient(135deg, #4e8ef7, #6c63ff);
            color: #fff;
            border: none;
            border-radius: 9px;
            font-size: 14px;
            font-weight: 700;
            cursor: pointer;
            font-family: inherit;
            transition: .2s;
            margin-top: 4px
        }

        .btn-submit:hover {
            opacity: .9;
            transform: translateY(-1px);
            box-shadow: 0 4px 14px rgba(78, 142, 247, .3)
        }

        .alert-err {
            background: #fef2f2;
            border: 1px solid #fecaca;
            border-radius: 9px;
            padding: 10px 14px;
            font-size: 13px;
            color: #dc2626;
            margin-bottom: 18px
        }

        .alert-ok {
            background: #ecfdf5;
            border: 1px solid #bbf7d0;
            border-radius: 9px;
            padding: 10px 14px;
            font-size: 13px;
            color: #047857;
            margin-bottom: 18px
        }

        .forgot-actions {
            display: flex;
            justify-content: space-between;
            gap: 10px;
            margin: 12px 0 4px;
            font-size: 12px
        }

        .forgot-actions a {
            color: #4e8ef7;
            font-weight: 700;
            text-decoration: none
        }

        .forgot-actions a:hover {
            color: #3a7be8
        }

        .footer-link {
            text-align: center;
            margin-top: 18px;
            font-size: 13px;
            color: #8a9bb0
        }

        .footer-link a {
            color: #4e8ef7;
            font-weight: 600;
            text-decoration: none
        }

        .footer-link a:hover {
            color: #3a7be8
        }

        .password-toggle{
            position:absolute;
            right:12px;
            top:50%;
            transform:translateY(-50%);
            cursor:pointer;
            color:#9ca3af;
            display:flex;
            align-items:center;
            justify-content:center;
        }

        .password-toggle:hover{
            color:#4e8ef7;
        }

    </style>
</head>
<body>
<div class="card">
    <div class="logo">
        @if($logo)
            <img src="{{  cdn($logo) }}" alt="Logo">
        @else
            <div class="logo-icon"><i class="fas fa-shield-alt"></i></div>
        @endif
        <h2>Đăng nhập</h2>
        <p>Nhập thông tin tài khoản của bạn</p>
    </div>

    @if($errors->any())
        <div class="alert-err">{{ $errors->first() }}</div>
    @endif

    @if(session('success'))
        <div class="alert-ok">{{ session('success') }}</div>
    @endif

    <form method="POST" action="{{ route('admin.login.submit') }}">
        @csrf
        <div class="form-group">
            <label>Email / Số điện thoại</label>
            <div class="input-wrap">
                <i class="fas fa-user"></i>
                <input type="text" name="email" class="form-control" value="{{ old('email') }}"
                       placeholder="Email hoặc số điện thoại" required autocomplete="username">
            </div>
        </div>
        <div class="form-group">
            <label>Mật khẩu</label>
            <div class="input-wrap">
                <i class="fas fa-lock"></i>
                <input type="password" name="password" id="password" class="form-control" placeholder="••••••••" required>
                <span class="password-toggle" id="togglePassword">
            <!-- Eye Icon -->
            <svg id="eyeOpen" xmlns="http://www.w3.org/2000/svg"
                 width="18" height="18" viewBox="0 0 24 24"
                 fill="none" stroke="currentColor"
                 stroke-width="2" stroke-linecap="round"
                 stroke-linejoin="round">
                <path d="M1 12s4-8 11-8 11 8 11 8-4 8-11 8-11-8-11-8z"/>
                <circle cx="12" cy="12" r="3"/>
            </svg>

                    <!-- Eye Slash -->
            <svg id="eyeClose" xmlns="http://www.w3.org/2000/svg"
                 width="18" height="18" viewBox="0 0 24 24"
                 fill="none" stroke="currentColor"
                 stroke-width="2" stroke-linecap="round"
                 stroke-linejoin="round"
                 style="display:none;">
                <path d="M17.94 17.94A10.94 10.94 0 0 1 12 20C5 20 1 12 1 12a21.8 21.8 0 0 1 5.06-6.94"/>
                <path d="M9.9 4.24A10.94 10.94 0 0 1 12 4c7 0 11 8 11 8a21.86 21.86 0 0 1-2.16 3.19"/>
                <path d="M1 1l22 22"/>
                <path d="M10.58 10.58A2 2 0 0 0 12 14a2 2 0 0 0 1.42-.58"/>
            </svg>
        </span>

            </div>
        </div>
        <button type="submit" class="btn-submit"><i class="fas fa-sign-in-alt"></i> Đăng nhập</button>
        <div class="forgot-actions">
            <a href="{{ route('admin.forgot-password') }}"><i class="fas fa-key"></i> Quên mật khẩu</a>
            <a href="{{ route('admin.forgot-pin') }}"><i class="fas fa-dialpad"></i> Quên mã PIN</a>
        </div>
    </form>

    <div class="footer-link">
        Chưa có tài khoản? <a href="{{ route('admin.register') }}">Đăng ký ngay</a>
    </div>
</div>

<script>
    document.addEventListener('DOMContentLoaded', function () {

        const passwordInput = document.getElementById('password');
        const togglePassword = document.getElementById('togglePassword');

        const eyeOpen = document.getElementById('eyeOpen');
        const eyeClose = document.getElementById('eyeClose');

        togglePassword.addEventListener('click', function () {

            const isPassword = passwordInput.type === 'password';

            passwordInput.type = isPassword ? 'text' : 'password';

            eyeOpen.style.display = isPassword ? 'none' : 'block';
            eyeClose.style.display = isPassword ? 'block' : 'none';
        });

    });
</script>


</body>
</html>
