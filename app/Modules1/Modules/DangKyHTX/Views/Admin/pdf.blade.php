<!DOCTYPE html>
<html>
<head>
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8"/>
    <title>Chi tiết đăng ký HTX - {{ $dangKyHTX->full_name }}</title>

    <style>
        body {
            font-family: DejaVu Sans, sans-serif;
            font-size: 13px;
            line-height: 1.5;
            color: #222;
        }

        h1 {
            font-size: 18px;
            text-align: center;
            margin-bottom: 20px;
            text-transform: uppercase;
        }

        .section {
            margin-bottom: 18px;
        }

        .section-title {
            font-weight: bold;
            font-size: 15px;
            border-bottom: 1px solid #ddd;
            padding-bottom: 5px;
            margin-bottom: 10px;
        }

        table {
            width: 100%;
            border-collapse: collapse;
        }

        td {
            padding: 5px 0;
            vertical-align: top;
        }

        .label {
            width: 160px;
            font-weight: bold;
        }

        .success { color: green; font-weight: bold; }
        .danger { color: red; font-weight: bold; }

        .img-box {
            text-align: center;
            margin-top: 10px;
        }

        img {
            max-width: 100%;
            border: 1px solid #ddd;
            padding: 3px;
        }

        .grid-2 {
            width: 50%;
            display: inline-block;
            vertical-align: top;
        }

        .cccd-img {
            width: 100%;
            height: auto;
        }

        .small-img {
            max-height: 140px;
        }
    </style>
</head>

<body>

<h1>THÔNG TIN ĐĂNG KÝ HỢP TÁC XÃ</h1>


{{-- ================= THÔNG TIN CÁ NHÂN ================= --}}
<div class="section">
    <div class="section-title">Thông tin cá nhân</div>

    <table>
        <tr>
            <td class="label">Họ tên:</td>
            <td>{{ $dangKyHTX->full_name }}</td>
        </tr>

        <tr>
            <td class="label">Ngày sinh:</td>
            <td>{{ $dangKyHTX->birthday ? \Carbon\Carbon::parse($dangKyHTX->birthday)->format('d/m/Y') : '' }}</td>
        </tr>

        <tr>
            <td class="label">Giới tính:</td>
            <td>
                {{ strtolower($dangKyHTX->gender) == 'nu' ? 'Nữ' : (strtolower($dangKyHTX->gender) == 'nam' ? 'Nam' : $dangKyHTX->gender) }}
            </td>
        </tr>

        <tr>
            <td class="label">Quốc tịch:</td>
            <td>{{ $dangKyHTX->nationality }}</td>
        </tr>

        <tr>
            <td class="label">SĐT:</td>
            <td>{{ $dangKyHTX->phone }}</td>
        </tr>

        <tr>
            <td class="label">Email:</td>
            <td>{{ $dangKyHTX->email }}</td>
        </tr>

        <tr>
            <td class="label">CCCD:</td>
            <td>{{ $dangKyHTX->identity_number }}</td>
        </tr>

        <tr>
            <td class="label">Ngày cấp:</td>
            <td>{{ $dangKyHTX->identity_date ? \Carbon\Carbon::parse($dangKyHTX->identity_date)->format('d/m/Y') : '' }}</td>
        </tr>

        <tr>
            <td class="label">Nơi cấp:</td>
            <td>{{ $dangKyHTX->identity_place }}</td>
        </tr>
    </table>

    {{-- ẢNH CHÂN DUNG --}}

    @php
        $portraitImg = cdn_base64($dangKyHTX->portrait);
    @endphp

    @if($portraitImg)
        <div class="img-box">
            <div class="section-title">Ảnh chân dung</div>
            <img src="{{ $portraitImg }}" style="max-height:220px;">
        </div>
    @endif
</div>

{{-- ================= LIÊN HỆ ================= --}}
<div class="section">
    <div class="section-title">Thông tin liên hệ & Công việc</div>

    <table>
        <tr><td class="label">Địa chỉ thường trú:</td><td>{{ $dangKyHTX->permanent_address }}</td></tr>
        <tr><td class="label">Địa chỉ hiện tại:</td><td>{{ $dangKyHTX->current_address }}</td></tr>
        <tr><td class="label">Nghề nghiệp:</td><td>{{ $dangKyHTX->job }}</td></tr>
        <tr><td class="label">Nơi làm việc:</td><td>{{ $dangKyHTX->workplace }}</td></tr>
        <tr><td class="label">Khu vực:</td><td>{{ $dangKyHTX->area }}</td></tr>
        <tr><td class="label">Người giới thiệu:</td><td>{{ $dangKyHTX->referrer }}</td></tr>
    </table>
</div>

{{-- ================= KHẢO SÁT ================= --}}
<div class="section">
    <div class="section-title">Khảo sát</div>

    <table>
        <tr>
            <td class="label">Biết đến qua:</td>
            <td>{{ $dangKyHTX->know_from }} {{ $dangKyHTX->know_from_other ? '('.$dangKyHTX->know_from_other.')' : '' }}</td>
        </tr>

        <tr>
            <td class="label">Mục tiêu:</td>
            <td>{{ $dangKyHTX->goals }} {{ $dangKyHTX->goal_other ? '('.$dangKyHTX->goal_other.')' : '' }}</td>
        </tr>
    </table>
</div>

{{-- ================= CAM KẾT ================= --}}
<div class="section">
    <div class="section-title">Cam kết</div>

    <div class="grid-2">
        - Tự nguyện:
        <span class="{{ $dangKyHTX->voluntary ? 'success' : 'danger' }}">
            {{ $dangKyHTX->voluntary ? 'Đồng ý' : 'Không' }}
        </span><br>

        - Hiểu mô hình:
        <span class="{{ $dangKyHTX->understand_model ? 'success' : 'danger' }}">
            {{ $dangKyHTX->understand_model ? 'Đồng ý' : 'Không' }}
        </span><br>

        - Tuân thủ:
        <span class="{{ $dangKyHTX->comply_rules ? 'success' : 'danger' }}">
            {{ $dangKyHTX->comply_rules ? 'Đồng ý' : 'Không' }}
        </span>
    </div>

    <div class="grid-2">
        - Truyền thông:
        <span class="{{ $dangKyHTX->correct_communication ? 'success' : 'danger' }}">
            {{ $dangKyHTX->correct_communication ? 'Đồng ý' : 'Không' }}
        </span><br>

        - Học nền tảng:
        <span class="{{ $dangKyHTX->foundation_lesson ? 'success' : 'danger' }}">
            {{ $dangKyHTX->foundation_lesson ? 'Đồng ý' : 'Không' }}
        </span><br>

        - Đồng ý dữ liệu:
        <span class="{{ $dangKyHTX->data_consent ? 'success' : 'danger' }}">
            {{ $dangKyHTX->data_consent ? 'Đồng ý' : 'Không' }}
        </span>
    </div>

    <p style="margin-top:10px;">
        <strong>Kênh nhận thông báo:</strong>
        {{ $dangKyHTX->notify_channels }}
    </p>
</div>

{{-- ================= GHI CHÚ ================= --}}
<div class="section">
    <div class="section-title">Ghi chú</div>
    <p>{{ $dangKyHTX->note ?: '—' }}</p>
</div>

{{-- ================= CCCD ================= --}}

@php
    $cccdFiles = [];

    if ($dangKyHTX->cccd_files) {
        $cccdFiles = explode(',', $dangKyHTX->cccd_files);
    }
@endphp


@if(count($cccdFiles))
    <div class="section">
        <div class="section-title">Hồ sơ đính kèm</div>

        <table>
            <tr>
                @foreach($cccdFiles as $file)
                    @php
                        $img = cdn_base64(trim($file));
                    @endphp

                    <td style="width:33%; text-align:center;">
                        @if($img)
                            <img src="{{ $img }}" class="small-img">
                        @else
                            <span style="color:red">Không load ảnh</span>
                        @endif
                    </td>

                    @if(($loop->iteration % 3) == 0)
            </tr><tr>
                @endif
                @endforeach
            </tr>
        </table>
    </div>
@endif

{{-- ================= TRẠNG THÁI ================= --}}
<div class="section">
    <div class="section-title">Trạng thái</div>

    <p>
        @if($dangKyHTX->status === 'approved')
            <span class="success">Đã duyệt</span>
        @elseif($dangKyHTX->status === 'rejected')
            <span class="danger">Từ chối</span>
        @else
            Chờ duyệt
        @endif
    </p>
</div>

</body>
</html>