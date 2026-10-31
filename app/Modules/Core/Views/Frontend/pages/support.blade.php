@extends('Core::Frontend.layouts.app')

@section('title', 'Trung tâm hỗ trợ')
@section('meta_description', 'Hướng dẫn mua hàng, chính sách thanh toán, vận chuyển, đổi trả, bảo mật thông tin.')

@php
    $icons = [
        'huong-dan-mua-hang'             => 'fa-cart-shopping',
        'chinh-sach-thanh-toan'          => 'fa-credit-card',
        'chinh-sach-van-chuyen-giao-nhan'=> 'fa-truck-fast',
        'chinh-sach-kiem-hang'           => 'fa-box-open',
        'chinh-sach-doi-tra-hoan-tien'   => 'fa-rotate-left',
        'chinh-sach-bao-mat-thong-tin'   => 'fa-user-shield',
        'dieu-khoan-su-dung'             => 'fa-file-contract',
        'chinh-sach-xu-ly-khieu-nai'     => 'fa-comments',
    ];
    $faqs = [
        ['Làm thế nào để đặt mua sản phẩm?', 'Quý khách đăng ký/đăng nhập tài khoản, chọn sản phẩm và bấm “Mua ngay”, điền thông tin nhận hàng rồi thanh toán. Xem chi tiết tại Hướng dẫn mua hàng.'],
        ['Tôi có thể thanh toán bằng những cách nào?', 'Thanh toán bằng số dư ví mua hàng hoặc chuyển khoản qua mã QR (PayOS). Giao dịch được ghi nhận tự động.'],
        ['Bao lâu tôi nhận được hàng?', 'Thời gian giao dự kiến từ 2 – 5 ngày làm việc tùy khu vực. Phí vận chuyển được hiển thị trước khi thanh toán.'],
        ['Tôi có được kiểm tra hàng trước khi nhận không?', 'Có. Quý khách được kiểm tra bao bì, đúng sản phẩm, số lượng và hạn sử dụng trước khi nhận hàng.'],
        ['Thông tin cá nhân của tôi có được bảo mật không?', 'Thông tin chỉ dùng để xử lý đơn hàng và chăm sóc khách hàng, không cung cấp cho bên thứ ba ngoài phạm vi cần thiết. Xem Chính sách bảo mật thông tin.'],
    ];
@endphp

@push('styles')
<style>
    .support-search { max-width: 560px; margin: 18px auto 0; position: relative; z-index: 1; }
    .support-search input { border-radius: 999px; padding: .85rem 1.3rem; border: 0; }
    .policy-card { display: flex; gap: 14px; background: #fff; border: 1px solid var(--line); border-radius: var(--radius); padding: 20px; color: var(--ink); height: 100%; transition: .2s; }
    .policy-card:hover { border-color: var(--primary); box-shadow: var(--shadow); color: var(--ink); transform: translateY(-2px); }
    .policy-card .ic { flex: 0 0 50px; height: 50px; border-radius: 14px; background: var(--brand-gradient); color: #fff; display: grid; place-items: center; font-size: 20px; }
    .policy-card h6 { font-weight: 700; margin-bottom: 4px; }
    .policy-card p { color: var(--muted); font-size: 13.5px; margin: 0; }
    .faq .accordion-item { border: 1px solid var(--line); border-radius: 12px !important; overflow: hidden; margin-bottom: 10px; }
    .faq .accordion-button { font-weight: 600; }
    .faq .accordion-button:not(.collapsed) { background: var(--primary-soft); color: var(--primary-dark); box-shadow: none; }
    .faq .accordion-button:focus { box-shadow: none; }
</style>
@endpush

@section('content')
<section class="page-hero text-center">
    <div class="container">
        <h1>Trung tâm hỗ trợ khách hàng</h1>
        <p class="mb-0 position-relative" style="z-index:1;opacity:.9">Chúng tôi có thể giúp gì cho bạn?</p>
        <div class="support-search">
            <input type="search" class="form-control" id="policySearch" placeholder="Tìm chính sách: đổi trả, vận chuyển, thanh toán..." aria-label="Tìm chính sách">
        </div>
    </div>
</section>

<section class="section">
    <div class="container">
        <div class="section-title"><div><h2>Chính sách &amp; <span>hướng dẫn</span></h2></div></div>
        <div class="row g-3" id="policyGrid">
            @forelse($policies as $policy)
                <div class="col-md-6 col-lg-4" data-search="{{ \Illuminate\Support\Str::lower(\Illuminate\Support\Str::ascii($policy->title)) }}">
                    <a href="{{ route('frontend.policy.show', $policy->slug) }}" class="policy-card">
                        <span class="ic"><i class="fas {{ $icons[$policy->slug] ?? 'fa-file-lines' }}"></i></span>
                        <span>
                            <h6>{{ $policy->title }}</h6>
                            <p>{{ \Illuminate\Support\Str::limit(trim(html_entity_decode(strip_tags($policy->renderContent($site)))), 90) }}</p>
                        </span>
                    </a>
                </div>
            @empty
                <div class="col-12"><div class="card-soft p-4 text-center text-muted">Nội dung chính sách đang được cập nhật.</div></div>
            @endforelse
        </div>
    </div>
</section>

<section class="section bg-white">
    <div class="container">
        <div class="row g-5">
            <div class="col-lg-7">
                <div class="section-title"><div><h2>Câu hỏi <span>thường gặp</span></h2></div></div>
                <div class="accordion faq" id="faq">
                    @foreach($faqs as $i => [$q, $a])
                        <div class="accordion-item">
                            <h3 class="accordion-header">
                                <button class="accordion-button {{ $i ? 'collapsed' : '' }}" type="button" data-bs-toggle="collapse" data-bs-target="#faq{{ $i }}">{{ $q }}</button>
                            </h3>
                            <div id="faq{{ $i }}" class="accordion-collapse collapse {{ $i ? '' : 'show' }}" data-bs-parent="#faq">
                                <div class="accordion-body text-muted">{{ $a }}</div>
                            </div>
                        </div>
                    @endforeach
                </div>
            </div>
            <div class="col-lg-5">
                <div class="section-title"><div><h2>Liên hệ <span>hỗ trợ</span></h2></div></div>
                <p class="text-muted">Không tìm thấy câu trả lời? Liên hệ ngay, chúng tôi luôn sẵn sàng hỗ trợ.</p>
                @include('Core::Frontend.pages.partials.contact-channels', ['colClass' => 'col-sm-6'])
            </div>
        </div>
    </div>
</section>
@endsection

@push('scripts')
<script>
    (function () {
        var input = document.getElementById('policySearch');
        // Bỏ dấu tiếng Việt để tìm "doi tra" ra "Đổi trả"
        function normalize(s) { return s.normalize('NFD').replace(/[̀-ͯ]/g, '').replace(/đ/g, 'd').replace(/Đ/g, 'D').toLowerCase(); }
        input.addEventListener('input', function () {
            var q = normalize(input.value.trim());
            document.querySelectorAll('#policyGrid [data-search]').forEach(function (col) {
                col.style.display = !q || col.dataset.search.indexOf(q) !== -1 ? '' : 'none';
            });
        });
    })();
</script>
@endpush
