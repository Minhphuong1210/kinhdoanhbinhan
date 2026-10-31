@extends('Core::Admin.layouts.app')
@section('title', 'Lịch sử giao dịch')
@section('page-title', 'Lịch sử giao dịch')
@section('content')
<div class="card">
    <div class="card-header"><h5>Lịch sử giao dịch ({{ $items->total() }})</h5></div>
    <div class="card-body" style="padding:0">
        <table>
            <thead><tr><th>#</th><th>Người gửi</th><th>Người nhận</th><th>Số tiền</th><th>Loại</th><th>Ghi chú</th><th>Thời gian</th><th></th></tr></thead>
            <tbody>
                @forelse($items as $i)

                   


                <tr>
                    <td>{{ $i->id }}</td>
                    <td>{{ $i->sender?->name ?? '#'.$i->sender_id }}</td>
                    <td>{{ $i->receiver?->name ?? '#'.$i->receiver_id }}</td>
                    <td>{{ number_format($i->amount) }}đ</td>
                    <td><span class="badge badge-info">{{ $i->type }}</span></td>
                    <td>{{ $i->note }}</td>
                    <td>{{ $i->created_at?->format('d/m/Y H:i') }}</td>
                    <td>
                        <form method="POST" action="{{ route('admin.lich-su-giao-dich.destroy', $i) }}" style="display:inline" onsubmit="return confirm('Xóa?')">
                            @csrf @method('DELETE') <button class="btn btn-danger btn-sm"><i class="fas fa-trash"></i></button>
                        </form>
                    </td>
                </tr>
                @empty
                <tr><td colspan="8" style="text-align:center;color:#8a9bb0;padding:40px">Chưa có dữ liệu.</td></tr>
                @endforelse
            </tbody>
        </table>
        <div style="padding:16px 20px">{{ $items->links() }}</div>
    </div>
</div>
@endsection
