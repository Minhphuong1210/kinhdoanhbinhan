@extends('Core::Admin.layouts.app')
@section('title', 'Lịch sử giao dịch')
@section('page-title', 'Lịch sử giao dịch')
@section('content')

    <div class="card">
        <div class="card-header" style="display:flex;align-items:center;justify-content:space-between">
            <h5 style="margin:0"><i class="fas fa-history"></i> Giao dịch của tôi</h5>
            <div style="background:#f0fdf4;border:1.5px solid #86efac;border-radius:8px;padding:6px 16px;font-size:14px;font-weight:700;color:#16a34a">
                <i class="fas fa-wallet"></i> Số dư: {{ number_format($user->vi_tien ?? 0, 0, ',', '.') }}₫
            </div>
        </div>
        <div class="card-body" style="padding:0;overflow-x:auto">
            @if($items->count())
                <table class="table" style="margin:0;min-width:700px">
                    <thead>
                    <tr>
                        <th>ID</th>
                        <th>Thời gian</th>
                        <th>Nội dung</th>
                        <th>Người chuyển</th>
                        <th>Người nhận</th>
                        <th>Loại</th>
                        <th style="text-align:right">Số tiền</th>
                        <th style="text-align:right">Số dư trước ví rút</th>
                        <th style="text-align:right">Số dư sau ví rút</th>
                        <th style="text-align:right">Số dư trước ví nạp</th>
                        <th style="text-align:right">Số dư sau ví nạp</th>
                    </tr>
                    </thead>
                    <tbody>
                    @foreach($items as $item)
                        @php
                            $type = (int)$item->type;
                            [$label, $badgeClass, $isIncome] = match($type) {
                                1 => ['Chi tiêu',    'badge-danger',   false],
                                2 => ['Hoan phí',   'badge-success',  true],
                                3 => ['Rút tiền',   'badge-warning',  false],
                                5 => ['Chuyển điểm','badge-info',     $item->sender_id != null], // nhận = income, gửi = not
                                default => ['Giao dịch', 'badge-secondary', false],
                            };
                            // type=5: nếu có sender_id thì đây là bản ghi của người NHẬN (income)
                            if ($type === 5) $isIncome = ($item->sender_id != null);
                        @endphp
                        <tr>
                            <td style="font-size:12px;color:#8a9bb0;white-space:nowrap">
                                {{ $item->id }}
                            </td>
                            <td style="font-size:12px;color:#8a9bb0;white-space:nowrap">
                                {{ $item->created_at?->format('d/m/Y H:i') }}
                            </td>
                            <td style="font-size:13px;max-width:220px">{{ $item->note }}</td>
                            {{-- Người chuyển --}}
                            <td style="font-size:13px">
                                @if($item->sender)
                                    <div style="font-weight:600">{{ $item->sender->name }}</div>
                                    <div style="font-size:11px;color:#8a9bb0">{{ $item->sender->tel }}</div>
                                @else
                                    <span style="color:#c4d0e0">—</span>
                                @endif
                            </td>
                            {{-- Người nhận --}}
                            <td style="font-size:13px">
                                @if($item->receiver)
                                    <div style="font-weight:600">{{ $item->receiver->name }}</div>
                                    <div style="font-size:11px;color:#8a9bb0">{{ $item->receiver->tel }}</div>
                                @elseif($type === 2 || $type === 5 && $item->sender_id)
                                    {{-- Chính mình là người nhận --}}
                                    <div style="font-weight:600;color:#16a34a">{{ $user->name }}</div>
                                @else
                                    <span style="color:#c4d0e0">—</span>
                                @endif
                            </td>
                            <td>
                    <span class="{{ $badgeClass }}" style="padding:2px 10px;border-radius:6px;font-size:11px;font-weight:700">
                        {{ $label }}
                    </span>
                            </td>
                            <td style="text-align:right;font-weight:700;font-size:14px;color:{{ $isIncome ? '#16a34a' : '#dc2626' }};white-space:nowrap">
                                {{ $isIncome ? '+' : '-' }}{{ number_format($item->amount, 0, ',', '.') }}₫
                            </td>
                            <td style="text-align:right;font-size:13px;color:#5a6a7e;white-space:nowrap">
                                {{ number_format($item->surplus_before ?? 0, 0, ',', '.') }}₫
                            </td>
                            <td style="text-align:right;font-size:13px;color:#5a6a7e;white-space:nowrap">
                                {{ number_format($item->surplus ?? 0, 0, ',', '.') }}₫
                            </td>

                            <td style="text-align:right;font-size:13px;color:#5a6a7e;white-space:nowrap">
                                {{ number_format($item->surplus_vi_mua_befor ?? 0, 0, ',', '.') }}₫
                            </td>

                            <td style="text-align:right;font-size:13px;color:#5a6a7e;white-space:nowrap">
                                {{ number_format($item->surplus_vi_mua ?? 0, 0, ',', '.') }}₫
                            </td>
                        </tr>
                    @endforeach
                    </tbody>
                </table>
                <div style="padding:16px">{{ $items->links() }}</div>
            @else
                <div style="text-align:center;padding:48px;color:#8a9bb0">
                    <i class="fas fa-history" style="font-size:36px;display:block;margin-bottom:10px;opacity:.3"></i>
                    Chưa có giao dịch nào.
                </div>
            @endif
        </div>
    </div>
@endsection
