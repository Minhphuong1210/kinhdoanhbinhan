@extends('Core::Admin.layouts.app')
@section('title', 'Khách hàng trực tiếp')
@section('page-title', 'Khách hàng trực tiếp')
@section('content')

@php $total = $referrals->count(); $bought = $referrals->where('bought_this_month', true)->count(); @endphp

{{-- Tóm tắt --}}
<div style="display:grid;grid-template-columns:repeat(auto-fit,minmax(180px,1fr));gap:16px;margin-bottom:20px">
    <div class="stat-card">
        <div class="stat-icon blue"><i class="fas fa-users"></i></div>
        <div class="stat-info">
            <div class="value">{{ $total }}</div>
            <div class="label">Tổng giới thiệu trực tiếp</div>
        </div>
    </div>
    <div class="stat-card">
        <div class="stat-icon green"><i class="fas fa-check-circle"></i></div>
        <div class="stat-info">
            <div class="value">{{ $bought }}</div>
            <div class="label">Đã mua trong tháng {{ now()->month }}/{{ now()->year }}</div>
        </div>
    </div>
    <div class="stat-card">
        <div class="stat-icon red"><i class="fas fa-times-circle"></i></div>
        <div class="stat-info">
            <div class="value">{{ $total - $bought }}</div>
            <div class="label">Chưa mua trong tháng</div>
        </div>
    </div>
</div>

{{-- Bảng danh sách --}}
<div class="card">
    <div class="card-header" style="display:flex;align-items:center;gap:12px">
        <h5 style="margin:0;flex:1"><i class="fas fa-user-friends"></i> Danh sách khách hàng trực tiếp</h5>
        <span style="font-size:12px;color:#8a9bb0">Mã giới thiệu của bạn: <strong style="color:#4e8ef7">{{ $user->tel ?: $user->id }}</strong></span>
    </div>
    <div class="card-body" style="padding:0">
        @if($referrals->count())
        <table class="table" style="margin:0">
            <thead>
                <tr>
                    <th>#</th>
                    <th>Thành viên</th>
                    <th>Liên hệ</th>
                    <th>Ngày đăng ký</th>
                    <th style="text-align:center">Tháng {{ now()->month }}</th>
                    <th style="text-align:center">Trạng thái TK</th>
                </tr>
            </thead>
            <tbody>
            @foreach($referrals as $i => $m)
            <tr>
                <td style="color:#8a9bb0;font-size:12px">{{ $i + 1 }}</td>
                <td>
                    <div style="display:flex;align-items:center;gap:10px">
                        @if($m->image)
                        <img src="{{ cdn($m->image) }}" style="width:36px;height:36px;border-radius:50%;object-fit:cover">
                        @else
                        <div style="width:36px;height:36px;border-radius:50%;background:linear-gradient(135deg,#4e8ef7,#6c63ff);display:flex;align-items:center;justify-content:center;color:#fff;font-weight:700;font-size:14px;flex-shrink:0">
                            {{ strtoupper(substr($m->name, 0, 1)) }}
                        </div>
                        @endif
                        <div>
                            <div style="font-weight:600;font-size:13px;color:#1e2a3a">{{ $m->name }}</div>
                            <div style="font-size:11px;color:#8a9bb0">{{ $m->email }}</div>
                        </div>
                    </div>
                </td>
                <td style="font-size:13px">
                    @if($m->tel)<div><i class="fas fa-phone" style="color:#8a9bb0;width:14px"></i> {{ $m->tel }}</div>@endif
                </td>
                <td style="font-size:12px;color:#8a9bb0;white-space:nowrap">
                    {{ $m->created_at?->format('d/m/Y') }}
                </td>
                <td style="text-align:center">
                    @if($m->bought_this_month)
                    <span style="background:#dcfce7;color:#16a34a;padding:4px 12px;border-radius:20px;font-size:12px;font-weight:700;white-space:nowrap">
                        <i class="fas fa-check"></i> Đã mua
                    </span>
                    @else
                    <span style="background:#fff7ed;color:#ea580c;padding:4px 12px;border-radius:20px;font-size:12px;font-weight:700;white-space:nowrap">
                        <i class="fas fa-clock"></i> Chưa mua
                    </span>
                    @endif
                </td>
                <td style="text-align:center">
                    @if($m->active)
                    <span class="badge-active" style="padding:3px 10px;border-radius:6px;font-size:11px;font-weight:700">Hoạt động</span>
                    @else
                    <span class="badge-locked" style="padding:3px 10px;border-radius:6px;font-size:11px;font-weight:700">Khoá</span>
                    @endif
                </td>
            </tr>
            @endforeach
            </tbody>
        </table>
        @else
        <div style="text-align:center;padding:60px;color:#8a9bb0">
            <i class="fas fa-user-plus" style="font-size:40px;display:block;margin-bottom:12px;opacity:.25"></i>
            <div style="font-size:14px">Chưa có ai dùng mã giới thiệu của bạn.</div>
            <div style="font-size:12px;margin-top:6px">Chia sẻ link giới thiệu từ trang Dashboard để mời thêm thành viên.</div>
        </div>
        @endif
    </div>
</div>
@endsection
