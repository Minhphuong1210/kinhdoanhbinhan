@extends('Core::Frontend.layouts.app')

@section('title', 'Liên hệ')
@section('meta_description', 'Thông tin liên hệ ' . ($site['name'] ?? '') . ': địa chỉ, hotline, email.')

@push('styles')
<style>
    .contact-info { background: #fff; border: 1px solid var(--line); border-radius: 20px; padding: 28px; height: 100%; }
    .contact-info .row-item { display: flex; gap: 14px; padding: 14px 0; border-top: 1px solid var(--line); }
    .contact-info .row-item:first-of-type { border-top: 0; }
    .contact-info .ic { flex: 0 0 42px; height: 42px; border-radius: 12px; background: var(--primary-soft); color: var(--primary); display: grid; place-items: center; }
    .contact-info small { display: block; color: var(--muted); }
    .map-box { border-radius: 20px; overflow: hidden; border: 1px solid var(--line); background: #fff; height: 100%; min-height: 380px; }
    .map-box iframe { width: 100% !important; height: 100% !important; min-height: 380px; border: 0; display: block; }
</style>
@endpush

@section('content')
<section class="page-hero">
    <div class="container">
        <h1>Liên hệ</h1>
        <nav aria-label="breadcrumb"><ol class="breadcrumb"><li class="breadcrumb-item"><a href="{{ route('frontend.home') }}">Trang chủ</a></li><li class="breadcrumb-item active">Liên hệ</li></ol></nav>
    </div>
</section>

<section class="section">
    <div class="container">
        @include('Core::Frontend.pages.partials.contact-channels')

        <div class="row g-4 mt-1">
            <div class="col-lg-5">
                <div class="contact-info">
                    <h4 class="fw-bold mb-3">{{ $site['name'] ?? '' }}</h4>
                    @foreach([
                        ['fa-map-marker-alt', 'Địa chỉ', $site['address'] ?? null],
                        ['fa-id-card', 'Mã số doanh nghiệp', $site['ma_so_doanh_nghiep'] ?? null],
                        ['fa-file-signature', 'Đăng ký kinh doanh', $site['GCNDKDN'] ?? null],
                        ['fa-user-tie', 'Đại diện', $site['nguoi_dai_dien'] ?? null],
                        ['fa-phone-alt', 'Hotline', $site['hotline'] ?? null],
                        ['fa-envelope', 'Email', $site['email'] ?? null],
                        ['fa-clock', 'Giờ làm việc', 'Thứ 2 – Thứ 7: 8:00 – 17:30'],
                    ] as [$icon, $label, $value])
                        @if($value)
                            <div class="row-item">
                                <span class="ic"><i class="fas {{ $icon }}"></i></span>
                                <span><small>{{ $label }}</small><span class="fw-semibold">{{ $value }}</span></span>
                            </div>
                        @endif
                    @endforeach
                </div>
            </div>
            <div class="col-lg-7">
                <div class="map-box">
                    {{-- Mã nhúng Google Maps do admin nhập ở Cài đặt (google_map) --}}
                    @if(!empty($site['google_map']) && str_contains($site['google_map'], '<iframe'))
                        {!! $site['google_map'] !!}
                    @elseif(!empty($site['address']))
                        <iframe src="https://www.google.com/maps?q={{ urlencode($site['address']) }}&output=embed" loading="lazy" title="Bản đồ"></iframe>
                    @endif
                </div>
            </div>
        </div>
    </div>
</section>
@endsection
