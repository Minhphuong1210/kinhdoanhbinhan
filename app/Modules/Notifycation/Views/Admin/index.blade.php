@extends('Core::Admin.layouts.app')
@section('title', 'Thông báo')
@section('page-title', 'Thông báo chung')
@section('content')
@php
    $badges = ['cong_ty' => 'badge-info', 'hop_tac_xa' => 'badge-warning', 'tat_ca' => 'badge-success'];
@endphp
<div class="card">
    <div class="card-header">
        <h5>Thông báo đã gửi cho tất cả mọi người</h5>
        <a href="{{ route('admin.thong-bao.create') }}" class="btn btn-primary btn-sm"><i class="fas fa-paper-plane"></i> Gửi thông báo</a>
    </div>
    <div class="card-body" style="padding:0">
        <form method="GET" style="padding:16px 20px;border-bottom:1px solid #f0f2f5;display:flex;gap:10px;flex-wrap:wrap">
            <input type="text" name="search" class="form-control" style="max-width:300px" placeholder="Tìm tiêu đề, nội dung..." value="{{ request('search') }}">
            <select name="vi_tri" class="form-control" style="max-width:200px">
                <option value="">— Tất cả app —</option>
                @foreach($targets as $value => $label)
                    <option value="{{ $value }}" {{ request('vi_tri') === $value ? 'selected' : '' }}>{{ $label }}</option>
                @endforeach
            </select>
            <button type="submit" class="btn btn-secondary btn-sm"><i class="fas fa-search"></i> Lọc</button>
            @if(request('search') || request('vi_tri'))<a href="{{ route('admin.thong-bao.index') }}" class="btn btn-secondary btn-sm">Xóa lọc</a>@endif
        </form>
        <table>
            <thead>
                <tr><th>#</th><th>Tiêu đề</th><th>Nội dung</th><th>Gửi tới</th><th>Đã đọc</th><th>Thời gian</th><th>Thao tác</th></tr>
            </thead>
            <tbody>
                @forelse($items as $item)
                <tr>
                    <td>{{ $item->id }}</td>
                    <td>
                        @if($item->image)<img src="{{ cdn($item->image) }}" class="img-thumb" alt="" style="margin-right:8px;vertical-align:middle">@endif
                        <strong>{{ $item->title }}</strong>
                    </td>
                    <td style="max-width:360px;color:#4b5563">{{ \App\Modules\Notifycation\Services\BroadcastNotificationService::plainText($item->content, 120) }}</td>
                    <td><span class="badge {{ $badges[$item->vi_tri] ?? 'badge-info' }}">{{ $targets[$item->vi_tri] ?? $item->vi_tri }}</span></td>
                    <td>{{ number_format($item->reads_count) }}</td>
                    <td style="white-space:nowrap">{{ $item->created_at?->format('d/m/Y H:i') }}</td>
                    <td style="white-space:nowrap">
                        <a href="{{ route('admin.thong-bao.edit', $item) }}" class="btn btn-warning btn-sm"><i class="fas fa-edit"></i></a>
                        <form method="POST" action="{{ route('admin.thong-bao.destroy', $item) }}" style="display:inline" onsubmit="return confirm('Xóa thông báo này khỏi danh sách thông báo của app?')">
                            @csrf @method('DELETE')
                            <button class="btn btn-danger btn-sm"><i class="fas fa-trash"></i></button>
                        </form>
                    </td>
                </tr>
                @empty
                <tr><td colspan="7" style="text-align:center;color:#8a9bb0;padding:40px">Chưa gửi thông báo nào.</td></tr>
                @endforelse
            </tbody>
        </table>
        <div style="padding:16px 20px">{{ $items->links() }}</div>
    </div>
</div>
@endsection
