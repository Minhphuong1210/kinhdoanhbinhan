<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <title>Mật khẩu mới</title>
</head>
<body style="margin:0;padding:0;background:#eef5f8;font-family:Arial,sans-serif;color:#1e2a3a">
<table width="100%" cellpadding="0" cellspacing="0" style="background:#eef5f8;padding:32px 12px">
    <tr>
        <td align="center">
            <table width="100%" cellpadding="0" cellspacing="0" style="max-width:560px;background:#ffffff;border-radius:16px;overflow:hidden;border:1px solid #dbe7ee">
                <tr>
                    <td style="background:linear-gradient(135deg,#288098,#125e7e);padding:30px;color:#ffffff;text-align:center">
                        <div style="font-size:24px;font-weight:800">Mật khẩu mới</div>
                        <div style="font-size:14px;margin-top:8px;opacity:.9">Kinh Doanh Bình An</div>
                    </td>
                </tr>
                <tr>
                    <td style="padding:30px">
                        <p style="font-size:15px;line-height:1.6;margin:0 0 16px">Xin chào {{ $user->name }},</p>
                        <p style="font-size:15px;line-height:1.6;margin:0 0 20px">Hệ thống đã tạo mật khẩu mới cho tài khoản của bạn.</p>
                        <div style="background:#f7fbfd;border:1px dashed #8ab7c7;border-radius:12px;padding:20px;text-align:center;margin-bottom:20px">
                            <div style="font-size:12px;text-transform:uppercase;letter-spacing:1px;color:#64748b;font-weight:700">Mật khẩu mới</div>
                            <div style="font-size:28px;color:#125e7e;font-weight:800;margin-top:8px">{{ $password }}</div>
                        </div>
                        <p style="font-size:13px;line-height:1.6;color:#64748b;margin:0">Sau khi đăng nhập, bạn nên đổi lại mật khẩu để bảo mật tài khoản.</p>
                    </td>
                </tr>
            </table>
        </td>
    </tr>
</table>
</body>
</html>
