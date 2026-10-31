@extends('Core::Admin.layouts.app')
@section('title', 'Co che hoan phi')
@section('page-title', 'Quan ly co che hoan phi')
@section('content')
<div class="card">
    <div class="card-header">
        <h5>Danh sach co che hoan phi ({{ $items->total() }})</h5>
        <a href="{{ route('admin.co-che-hoan-phi.create') }}" class="btn btn-primary btn-sm">
            <i class="fas fa-plus"></i> Them moi
        </a>
    </div>
    <div class="card-body" style="padding:0">
        <table>
            <thead>
                <tr>
                    <th>Ten co che</th>
                    <th>Tang 1</th>
                    <th>Tang 2</th>
                    <th>Tang 3</th>
                    <th>Tang 4</th>
                    <th>Tang 5</th>
                    <th>Tang 6</th>
                    <th>Cha</th>
                    <th>Trang hoat dong</th>
                    <th>Thao tac</th>
                </tr>
            </thead>
            <tbody>
                @forelse($items as $item)
                    <tr>
                        <td><strong>{{ $item->name }}</strong></td>
                        <td>{{ $item->tang_1 }}%</td>
                        <td>{{ $item->tang_2 }}%</td>
                        <td>{{ $item->tang_3 }}%</td>
                        <td>{{ $item->tang_4 }}%</td>
                        <td>{{ $item->tang_5 }}%</td>
                        <td>{{ $item->tang_6 }}%</td>
                        <td>
                            {{ $item->parent?->name ?? '-' }}
                        </td>
                        <td>
                            @if(($item->trang_hoat_dong ?? 'combo') === 'tu_chon')
                                <span class="badge badge-warning">Tu chon</span>
                            @else
                                <span class="badge badge-primary">Combo</span>
                            @endif
                        </td>
                        <td style="white-space:nowrap">
                            <a href="{{ route('admin.co-che-hoan-phi.edit', $item) }}" class="btn btn-warning btn-sm">
                                <i class="fas fa-edit"></i>
                            </a>
                            <form method="POST" action="{{ route('admin.co-che-hoan-phi.destroy', $item) }}" style="display:inline" onsubmit="return confirm('Xoa co che nay?')">
                                @csrf @method('DELETE')
                                <button class="btn btn-danger btn-sm"><i class="fas fa-trash"></i></button>
                            </form>
                        </td>
                    </tr>
                @empty
                    <tr><td colspan="10" style="text-align:center;color:#8a9bb0;padding:40px">Chua co co che nao.</td></tr>
                @endforelse
            </tbody>
        </table>
        <div style="padding:16px 20px">{{ $items->links() }}</div>
    </div>
</div>
@endsection
