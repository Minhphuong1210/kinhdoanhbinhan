@extends('Core::Frontend.layouts.app')

@section('title', $policy->title)
@section('meta_description', \Illuminate\Support\Str::limit(trim(html_entity_decode(strip_tags($policy->renderContent($site)))), 160))

@push('styles')
<style>
    .policy-nav { background: #fff; border: 1px solid var(--line); border-radius: var(--radius); padding: 10px; position: sticky; top: 140px; }
    .policy-nav a { display: block; padding: 10px 14px; border-radius: 10px; color: var(--ink); font-weight: 500; font-size: 14.5px; }
    .policy-nav a:hover { background: var(--primary-soft); color: var(--primary-dark); }
    .policy-nav a.active { background: var(--primary); color: #fff; }
    .policy-body { background: #fff; border: 1px solid var(--line); border-radius: 20px; padding: 34px; }
    .policy-body h3 { color: var(--primary-dark); }
    .policy-updated { color: var(--muted); font-size: 13px; }
    @media (max-width: 991.98px) { .policy-nav { position: static; } .policy-body { padding: 22px; } }
</style>
@endpush

@section('content')
<section class="page-hero">
    <div class="container">
        <h1>{{ $policy->title }}</h1>
        <nav aria-label="breadcrumb">
            <ol class="breadcrumb">
                <li class="breadcrumb-item"><a href="{{ route('frontend.home') }}">Trang chủ</a></li>
                <li class="breadcrumb-item"><a href="{{ route('frontend.support') }}">Hỗ trợ</a></li>
                <li class="breadcrumb-item active">{{ $policy->title }}</li>
            </ol>
        </nav>
    </div>
</section>

<section class="section">
    <div class="container">
        <div class="row g-4">
            <aside class="col-lg-4 order-2 order-lg-1">
                <nav class="policy-nav" aria-label="Danh sách chính sách">
                    @foreach($policies as $item)
                        <a href="{{ route('frontend.policy.show', $item->slug) }}" class="{{ $item->id === $policy->id ? 'active' : '' }}">{{ $item->title }}</a>
                    @endforeach
                </nav>
            </aside>
            <div class="col-lg-8 order-1 order-lg-2">
                <article class="policy-body">
                    @if($policy->image)<img src="{{ cdn($policy->image) }}" alt="{{ $policy->title }}" class="img-fluid rounded mb-4">@endif
                    <div class="rich-content">{!! $policy->renderContent($site) !!}</div>
                    <p class="policy-updated mt-4 mb-0"><i class="far fa-clock me-1"></i>Cập nhật lần cuối: {{ $policy->updated_at?->format('d/m/Y') }}</p>
                </article>
                <div class="card-soft p-3 mt-3 d-flex flex-wrap align-items-center justify-content-between gap-2">
                    <span class="text-muted">Cần hỗ trợ thêm?</span>
                    <span class="d-flex gap-2">
                        @if(!empty($site['hotline']))<a href="tel:{{ preg_replace('/[^0-9+]/', '', $site['hotline']) }}" class="btn btn-brand btn-sm"><i class="fas fa-phone-alt me-1"></i>{{ $site['hotline'] }}</a>@endif
                        <a href="{{ route('frontend.contact') }}" class="btn btn-outline-brand btn-sm">Liên hệ</a>
                    </span>
                </div>
            </div>
        </div>
    </div>
</section>
@endsection
