@extends('Core::Admin.layouts.app')
@section('title', 'Thu nhập hoàn điểm')
@section('page-title', 'Thu nhập hoàn điểm tháng ' . now()->month . '/' . now()->year)
@section('content')

    @php
        $settingKeys = [
            1 => 'hoan_phi_nhom_mot',
            2 => 'hoan_phi_nhom_hai',
            3 => 'hoan_phi_nhom_ba',
            4 => 'hoan_phi_nhom_bon',
            5 => 'hoan_phi_nhom_nam',
            6 => 'hoan_phi_nhom_sau',
        ];
        $tong_diem_hoan = 0;
    //    for ($i = 1; $i <= 6; $i++) {
    //        $phan_tram      = $settings[$settingKeys[$i]] ?? 0;
    //        $so_tien        = $hoan_tien_groups[$i] ?? 0;
    //        $tong_diem_hoan += $so_tien * $phan_tram / 100;
    //    }
    //    $tong_diem = array_sum($hoan_tien_groups) + $hoan_tien_f1;
    @endphp

    <div class="card">
        <div class="card-header">
            <h5 style="margin:0"><i class="fas fa-chart-bar"></i> Thu nhập hoàn điểm người tiêu dùng mua gói sản phẩm</h5>
        </div>
        <div class="card-body" style="padding:0;overflow-x:auto">
            <table style="border-collapse:collapse;width:100%;min-width:700px">
                <thead>
                <tr style="background:#f2f8ff">
                    <th style="padding:12px 16px;border:1px solid #dde3ef;text-align:left;font-size:13px;color:#5a6a7e;min-width:220px"></th>
                    @for($i=1;$i<=6;$i++)
                        <th style="padding:12px 10px;border:1px solid #dde3ef;text-align:center;font-size:13px;font-weight:700;color:#4e8ef7">
                            Nhóm {{ $i }}
                        </th>
                    @endfor
                </tr>
                </thead>
                <tbody>

                {{-- Tổng TV mới trong tháng --}}
                <tr>
                    <td style="padding:10px 16px;border:1px solid #dde3ef;font-size:13px;font-weight:600">Tổng số người tiêu dùng mới trong tháng</td>
                    <td colspan="6" style="padding:10px;border:1px solid #dde3ef;text-align:center;font-weight:700;color:#1d4ed8;font-size:15px">
                        {{ $sltv_new }}
                    </td>
                </tr>

                {{-- Số lượng người tiêu dùng từng nhóm --}}
                <tr style="background:#f9fafb">
                    <td style="padding:10px 16px;border:1px solid #dde3ef;font-size:13px">Số lượng người tiêu dùng</td>
                    @for($i=1;$i<=6;$i++)
                        <td style="padding:10px;border:1px solid #dde3ef;text-align:center;font-weight:600">{{ $descendants[$i] ?? 0 }}</td>
                    @endfor
                </tr>

                {{-- Số TV đã mua hàng --}}
                <tr>
                    <td style="padding:10px 16px;border:1px solid #dde3ef;font-size:13px">Số người tiêu dùng đã mua hàng</td>
                    @for($i=1;$i<=6;$i++)
                        <td style="padding:10px;border:1px solid #dde3ef;text-align:center">
                            <span style="color:#16a34a;font-weight:700">{{ $thanh_vien_mua_hang[$i] ?? 0 }}</span>
                            @if(($descendants[$i] ?? 0) > 0)
                                <span style="color:#8a9bb0;font-size:11px">/{{ $descendants[$i] }}</span>
                            @endif
                        </td>
                    @endfor
                </tr>

                {{-- % Hoàn phí --}}
                <tr style="background:#f9fafb">
                    <td style="padding:10px 16px;border:1px solid #dde3ef;font-size:13px">% Hoàn Phí</td>
                    @for($i=1;$i<=6;$i++)
                        <td style="padding:10px;border:1px solid #dde3ef;text-align:center">
                            @php $pct = $settings[$settingKeys[$i]] ?? 0; @endphp
                            @if($pct > 0)
                                <span style="background:#fef9c3;color:#a16207;padding:2px 8px;border-radius:20px;font-weight:700;font-size:12px">{{ $pct }}%</span>
                            @else
                                <span style="color:#c4d0e0">—</span>
                            @endif
                        </td>
                    @endfor
                </tr>

                {{-- Điểm nhận được --}}
                <tr>
                    <td style="padding:10px 16px;border:1px solid #dde3ef;font-size:13px">Điểm nhận được</td>

                    {{--                    dump($hoan_tien_groups)--}}
                    {{--                    {{dd($hoan_tien_groups)}}--}}


                    @for($i=1;$i<=6;$i++)

                        <td style="padding:10px;border:1px solid #dde3ef;text-align:center;font-weight:700;color:{{ $i > 0 ? '#16a34a' : '#8a9bb0' }}">

                            {{ number_format($doanh_so_nhom_chung[$i], 0, ',', '.') }}₫


                        </td>


                            <?php
                            $tong_diem_hoan  += $doanh_so_nhom_chung[$i];




                            ?>

                    @endfor
                </tr>

                {{-- Số đơn mua trong tháng --}}
                <tr style="background:#f9fafb">
                    <td style="padding:10px 16px;border:1px solid #dde3ef;font-size:13px">Số lượng đơn mua trong tháng</td>
                    @for($i=1;$i<=6;$i++)
                        <td style="padding:10px;border:1px solid #dde3ef;text-align:center">{{ $so_luong_mua_theo_nhom[$i] ?? '—' }}</td>
                    @endfor
                </tr>

                {{-- Tổng Điểm Hoàn % theo nhóm --}}
                <tr style="background:#eff6ff">
                    <td style="padding:10px 16px;border:1px solid #dde3ef;font-size:13px;font-weight:700;color:#1d4ed8">Tổng Điểm Hoàn % theo nhóm</td>
                    <td colspan="6" style="padding:10px;border:1px solid #dde3ef;text-align:center;font-weight:800;font-size:15px;color:#1d4ed8">
                        {{ number_format($tong_diem_hoan, 0, ',', '.') }}₫
                    </td>
                </tr>

                {{-- Tổng Điểm Hoàn TN/TN F1 --}}
                <tr>
                    <td style="padding:10px 16px;border:1px solid #dde3ef;font-size:13px;font-weight:700">Tổng Điểm Hoàn Từ TN/TN F1</td>
                    <td colspan="6" style="padding:10px;border:1px solid #dde3ef;text-align:center;font-weight:800;font-size:15px;color:#7c3aed">
                        {{ number_format($hoan_tien_f1, 0, ',', '.') }}₫
                    </td>
                </tr>

                {{-- Tổng Điểm --}}
                <tr style="background:#f0fdf4">
                    <td style="padding:12px 16px;border:1px solid #dde3ef;font-size:14px;font-weight:800;color:#15803d">Tổng Điểm</td>
                    <td colspan="6" style="padding:12px;border:1px solid #dde3ef;text-align:center;font-weight:800;font-size:18px;color:#15803d">
                        {{ number_format($tong_diem, 0, ',', '.') }}₫
                    </td>
                </tr>

                {{-- Doanh số tháng --}}
                {{--                <tr>--}}
                {{--                    <td style="padding:10px 16px;border:1px solid #dde3ef;font-size:13px;font-weight:700">Doanh số tháng</td>--}}
                {{--                    <td colspan="6" style="padding:10px;border:1px solid #dde3ef;text-align:center;font-weight:800;font-size:15px;color:#ea580c">--}}
                {{--                        {{ number_format($doanh_so_nhanh, 0, ',', '.') }}₫--}}
                {{--                    </td>--}}
                {{--                </tr>--}}


                @if(Auth::user()->is_admin==1)

                    <tr>
                        <td style="padding:10px 16px;border:1px solid #dde3ef;font-size:13px;font-weight:700">Doanh số tháng</td>
                        <td colspan="6" style="padding:10px;border:1px solid #dde3ef;text-align:center;font-weight:800;font-size:15px;color:#ea580c">
                            {{ number_format($doanh_so_nhanh, 0, ',', '.') }}₫
                        </td>
                    </tr>
                @endif

                </tbody>
            </table>
        </div>
    </div>

@endsection
