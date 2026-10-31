@extends('Core::Admin.layouts.app')
@section('title', 'Chi tiết đăng ký HTX')
@section('page-title', 'Chi tiết đăng ký HTX')
@section('content')
    <div class="card" style="max-width:1000px; margin: 0 auto;">
        <div class="card-header d-flex justify-content-between align-items-center">
            <h5 class="mb-0"><i class="fas fa-id-card mr-2"></i> {{ $dangKyHTX->full_name }}</h5>
            <div>
                <a href="{{ route('admin.dang-ky-htxs.pdf', $dangKyHTX) }}" class="btn btn-primary btn-sm mr-2"><i class="fas fa-file-pdf"></i> Xuất PDF</a>
                <a href="{{ route('admin.dang-ky-htxs.index') }}" class="btn btn-secondary btn-sm"><i class="fas fa-arrow-left"></i> Quay lại</a>
            </div>
        </div>
        <div class="card-body">
            <div class="row">
                {{-- Thông tin cá nhân --}}
                <div class="col-md-8">
                    <div class="row">
                        <div class="col-sm-6 mb-3"><strong>Họ tên:</strong> <p class="text-muted">{{ $dangKyHTX->full_name }}</p></div>
                        <div class="col-sm-6 mb-3"><strong>Ngày sinh:</strong> <p class="text-muted">{{ $dangKyHTX->birthday ? \Carbon\Carbon::parse($dangKyHTX->birthday)->format('d/m/Y') : '' }}</p></div>
                        <div class="col-sm-6 mb-3"><strong>Giới tính:</strong> <p class="text-muted">{{ strtolower($dangKyHTX->gender) == 'nu' ? 'Nữ' : (strtolower($dangKyHTX->gender) == 'nam' ? 'Nam' : $dangKyHTX->gender) }}</p></div>
                        <div class="col-sm-6 mb-3"><strong>Quốc tịch:</strong> <p class="text-muted">{{ $dangKyHTX->nationality }}</p></div>
                        <div class="col-sm-6 mb-3"><strong>SĐT:</strong> <p class="text-muted">{{ $dangKyHTX->phone }}</p></div>
                        <div class="col-sm-6 mb-3"><strong>Email:</strong> <p class="text-muted">{{ $dangKyHTX->email }}</p></div>
                        <div class="col-sm-6 mb-3"><strong>Số CCCD:</strong> <p class="text-muted">{{ $dangKyHTX->identity_number }}</p></div>
                        <div class="col-sm-6 mb-3"><strong>Ngày cấp:</strong> <p class="text-muted">{{ $dangKyHTX->identity_date ? \Carbon\Carbon::parse($dangKyHTX->identity_date)->format('d/m/Y') : '' }}</p></div>
                        <div class="col-sm-12 mb-3"><strong>Nơi cấp:</strong> <p class="text-muted">{{ $dangKyHTX->identity_place }}</p></div>
                    </div>
                </div>
                {{-- Ảnh chân dung --}}
                <div class="col-md-4 text-center">
                    <div class="border rounded p-2 bg-light">
                        <strong>Ảnh chân dung</strong><br>
                        @if($dangKyHTX->portrait)
                            <img src="{{ cdn($dangKyHTX->portrait) }}" alt="Portrait" class="img-fluid rounded mt-2" style="max-height: 200px; border: 1px solid #ddd">
                        @else
                            <div class="text-muted mt-3" style="height: 150px; display: flex; align-items: center; justify-content: center; border: 1px dashed #ccc">
                                Chưa có ảnh
                            </div>
                        @endif
                    </div>
                </div>
            </div>

            <div class="row mt-3">
                <div class="col-12 mb-3"><strong>Địa chỉ thường trú:</strong> <p class="text-muted">{{ $dangKyHTX->permanent_address }}</p></div>
                <div class="col-12 mb-3"><strong>Địa chỉ hiện tại:</strong> <p class="text-muted">{{ $dangKyHTX->current_address }}</p></div>
                <div class="col-sm-6 mb-3"><strong>Nghề nghiệp:</strong> <p class="text-muted">{{ $dangKyHTX->job }}</p></div>
                <div class="col-sm-6 mb-3"><strong>Nơi làm việc:</strong> <p class="text-muted">{{ $dangKyHTX->workplace }}</p></div>
                <div class="col-sm-6 mb-3"><strong>Khu vực:</strong> <p class="text-muted">{{ $dangKyHTX->area }}</p></div>
                <div class="col-sm-6 mb-3"><strong>Người giới thiệu:</strong> <p class="text-muted">{{ $dangKyHTX->referrer }}</p></div>
            </div>

            <hr>

            <div class="row">
                <div class="col-md-6 mb-3">
                    <strong>Biết đến qua:</strong>
                    <p class="text-muted">{{ $dangKyHTX->know_from }} {{ $dangKyHTX->know_from_other ? '('.$dangKyHTX->know_from_other.')' : '' }}</p>
                </div>
                <div class="col-md-6 mb-3">
                    <strong>Mục tiêu tham gia:</strong>
                    <p class="text-muted">{{ $dangKyHTX->goals }} {{ $dangKyHTX->goal_other ? '('.$dangKyHTX->goal_other.')' : '' }}</p>
                </div>
            </div>

            {{-- Cam kết / Điều khoản --}}
            <div class="row bg-light p-3 rounded mb-4" style="font-size: 0.9rem">
                <div class="col-md-6">
                    <ul class="list-unstyled mb-0">
                        <li class="mb-1"><i class="fas {{ $dangKyHTX->voluntary ? 'fa-check-circle text-success' : 'fa-times-circle text-danger' }}"></i> Tự nguyện tham gia</li>
                        <li class="mb-1"><i class="fas {{ $dangKyHTX->understand_model ? 'fa-check-circle text-success' : 'fa-times-circle text-danger' }}"></i> Hiểu mô hình hoạt động</li>
                        <li class="mb-1"><i class="fas {{ $dangKyHTX->comply_rules ? 'fa-check-circle text-success' : 'fa-times-circle text-danger' }}"></i> Cam kết tuân thủ nội quy</li>
                    </ul>
                </div>
                <div class="col-md-6">
                    <ul class="list-unstyled mb-0">
                        <li class="mb-1"><i class="fas {{ $dangKyHTX->correct_communication ? 'fa-check-circle text-success' : 'fa-times-circle text-danger' }}"></i> Truyền thông đúng mực</li>
                        <li class="mb-1"><i class="fas {{ $dangKyHTX->foundation_lesson ? 'fa-check-circle text-success' : 'fa-times-circle text-danger' }}"></i> Đã học bài học nền tảng</li>
                        <li class="mb-1"><i class="fas {{ $dangKyHTX->data_consent ? 'fa-check-circle text-success' : 'fa-times-circle text-danger' }}"></i> Đồng ý cung cấp dữ liệu</li>
                    </ul>
                </div>
                <div class="col-12 mt-2">
                    <i class="fas fa-bell text-primary mr-1"></i> <span class="font-weight-bold">Kênh nhận thông báo:</span> {{ $dangKyHTX->notify_channels }}
                </div>
            </div>

            <div class="row">
                <div class="col-12 mb-3"><strong>Ghi chú:</strong> <p class="text-muted border p-2 rounded bg-light">{{ $dangKyHTX->note ?: '—' }}</p></div>
            </div>

            <div class="row mb-4">
                <div class="col-12">
                    <strong>Ảnh CCCD / Hồ sơ đính kèm:</strong>
                    <div class="row mt-2">
                        @php
                            $cccdFiles = [];
                            if ($dangKyHTX->cccd_files) {
                                // Thử parse JSON trước, nếu không được thì split theo dấu phẩy
                                $decoded = json_decode($dangKyHTX->cccd_files, true);
                                $cccdFiles = is_array($decoded) ? $decoded : explode(',', $dangKyHTX->cccd_files);
                            }
                        @endphp
                        @forelse($cccdFiles as $file)
                            <div class="col-sm-4 mb-2">
                                <a href="{{ cdn(trim($file)) }}" target="_blank">
                                    <img src="{{ cdn(trim($file)) }}" class="img-fluid rounded border" style="max-height: 150px; width: 100%; object-fit: cover">
                                </a>
                            </div>
                        @empty
                            <div class="col-12 text-muted italic">Chưa có ảnh đính kèm.</div>
                        @endforelse
                    </div>
                </div>
            </div>

            <hr>
            <div class="d-flex align-items-center gap-3" style="gap: 15px">
                <div>
                    Trạng thái:
                    <span class="badge {{ $dangKyHTX->status==='approved'?'badge-success':($dangKyHTX->status==='rejected'?'badge-danger':'badge-warning') }}" style="font-size:14px;padding:8px 16px">
                    {{ $dangKyHTX->status==='approved'?'Đã duyệt':($dangKyHTX->status==='rejected'?'Từ chối':'Chờ duyệt') }}
                </span>
                </div>

                @if($dangKyHTX->status==='pending' || $dangKyHTX->status==0  )
                    <div class="ml-auto" style="margin-left: auto">
                        <form method="POST" action="{{ route('admin.dang-ky-htxs.update', $dangKyHTX) }}" style="display:inline">@csrf @method('PUT')<input type="hidden" name="status" value="approved"><button class="btn btn-success"><i class="fas fa-check"></i> Phê duyệt</button></form>
                        <form method="POST" action="{{ route('admin.dang-ky-htxs.update', $dangKyHTX) }}" style="display:inline">@csrf @method('PUT')<input type="hidden" name="status" value="rejected"><button class="btn btn-danger"><i class="fas fa-times"></i> Từ chối</button></form>
                    </div>
                @endif
            </div>
        </div>
    </div>
@endsection
