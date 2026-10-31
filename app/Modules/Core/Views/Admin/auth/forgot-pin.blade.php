<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Quên mã PIN</title>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;800&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
    <style>
        *{box-sizing:border-box;margin:0;padding:0}
        body{font-family:'Inter',sans-serif;min-height:100vh;background:linear-gradient(115deg,#288098 50%,#125e7e 50%);display:flex;align-items:center;justify-content:center;padding:16px}
        .card{background:#fff;border-radius:16px;padding:40px 36px;width:100%;max-width:430px;box-shadow:0 4px 24px rgba(0,0,0,.08);border:1px solid #e8edf2}
        .logo{text-align:center;margin-bottom:28px}
        .logo img{height:56px;object-fit:contain;margin:0 auto 12px;display:block}
        .logo-icon{width:56px;height:56px;border-radius:14px;background:linear-gradient(135deg,#4e8ef7,#6c63ff);display:flex;align-items:center;justify-content:center;margin:0 auto 12px;font-size:24px;color:#fff}
        .logo h2{font-size:20px;font-weight:800;color:#1e2a3a}
        .logo p{font-size:13px;color:#8a9bb0;margin-top:5px;line-height:1.5}
        .form-group{margin-bottom:16px}
        .form-group label{display:block;font-size:12px;font-weight:700;color:#374151;margin-bottom:6px}
        .input-wrap{position:relative}
        .input-wrap i{position:absolute;left:12px;top:50%;transform:translateY(-50%);color:#9ca3af;font-size:13px}
        .form-control{width:100%;padding:11px 14px 11px 37px;border:1.5px solid #e2e8f0;border-radius:9px;font-size:13px;font-family:inherit;color:#1e2a3a;background:#fff;transition:.2s;outline:none}
        .form-control:focus{border-color:#4e8ef7;box-shadow:0 0 0 3px rgba(78,142,247,.1)}
        .btn-submit{width:100%;padding:12px;background:linear-gradient(135deg,#4e8ef7,#6c63ff);color:#fff;border:none;border-radius:9px;font-size:14px;font-weight:800;cursor:pointer;font-family:inherit;transition:.2s;margin-top:4px}
        .btn-submit:hover{opacity:.92;transform:translateY(-1px);box-shadow:0 4px 14px rgba(78,142,247,.3)}
        .alert-err{background:#fef2f2;border:1px solid #fecaca;border-radius:9px;padding:10px 14px;font-size:13px;color:#dc2626;margin-bottom:18px}
        .alert-ok{background:#ecfdf5;border:1px solid #bbf7d0;border-radius:9px;padding:10px 14px;font-size:13px;color:#047857;margin-bottom:18px}
        .footer-link{text-align:center;margin-top:18px;font-size:13px;color:#8a9bb0}
        .footer-link a{color:#4e8ef7;font-weight:700;text-decoration:none}
        .footer-link a:hover{color:#3a7be8}
    </style>
</head>
<body>
<div class="card">
    <div class="logo">
        @if($logo)
            <img src="{{ cdn($logo) }}" alt="Logo">
        @else
            <div class="logo-icon"><i class="fas fa-dialpad"></i></div>
        @endif
        <h2>Quên mã PIN</h2>
        <p>Nhập email tài khoản, hệ thống sẽ tạo mã PIN mới và gửi về email của bạn.</p>
    </div>

    @if($errors->any())
        <div class="alert-err">{{ $errors->first() }}</div>
    @endif

    @if(session('success'))
        <div class="alert-ok">{{ session('success') }}</div>
    @endif

    <form method="POST" action="{{ route('admin.forgot-pin.submit') }}">
        @csrf
        <div class="form-group">
            <label>Email tài khoản</label>
            <div class="input-wrap">
                <i class="fas fa-envelope"></i>
                <input type="email" name="email" class="form-control" value="{{ old('email') }}" placeholder="email@example.com" required autocomplete="email">
            </div>
        </div>
        <button type="submit" class="btn-submit"><i class="fas fa-paper-plane"></i> Gửi mã PIN mới</button>
    </form>

    <div class="footer-link">
        Đã nhớ thông tin? <a href="{{ route('admin.login') }}">Quay lại đăng nhập</a>
    </div>
</div>
</body>
</html>
