@extends('Core::Frontend.layouts.app')

@section('title', $activeCategory?->name ?? ($keyword !== '' ? 'Tìm bài viết: ' . $keyword : 'Tin tức'))
@section('meta_description', 'Tin tức, kiến thức sức khỏe và hoạt động mới nhất.')

@php
    // Bài nổi bật chỉ hiện ở trang đầu, khi không lọc
    $showFeatured = $featuredPosts->isNotEmpty() && !$activeCategory && $keyword === '' && $posts->currentPage() === 1;
    $hero = $showFeatured ? $featuredPosts->first() : null;
@endphp

@push('styles')
<style>
    .featured-post { position: relative; display: block; border-radius: 20px; overflow: hidden; min-height: 360px; background: #0b2a29; color: #fff; }
    .featured-post img { position: absolute; inset: 0; width: 100%; height: 100%; object-fit: cover; opacity: .7; transition: transform .4s; }
    .featured-post:hover img { transform: scale(1.04); }
    .featured-post .inner { position: absolute; left: 0; right: 0; bottom: 0; padding: 28px; background: linear-gradient(180deg, transparent, rgba(0,0,0,.75)); }
    .featured-post h2 { font-weight: 800; font-size: 1.6rem; color: #fff; }
    .featured-post:hover h2 { text-decoration: underline; }
</style>
@endpush

@section('content')
<section class="page-hero">
    <div class="container">
        <h1>{{ $activeCategory?->name ?? 'Tin tức' }}</h1>
        <nav aria-label="breadcrumb">
            <ol class="breadcrumb">
                <li class="breadcrumb-item"><a href="{{ route('frontend.home') }}">Trang chủ</a></li>
                <li class="breadcrumb-item {{ $activeCategory ? '' : 'active' }}">
                    @if($activeCategory)<a href="{{ route('frontend.news.index') }}">Tin tức</a>@else Tin tức @endif
                </li>
                @if($activeCategory)<li class="breadcrumb-item active">{{ $activeCategory->name }}</li>@endif
            </ol>
        </nav>
    </div>
</section>

<section class="section">
    <div class="container">
        <div class="row g-4">
            <div class="col-lg-8">
                @if($hero)
                    <a href="{{ route('frontend.news.show', $hero->slug) }}" class="featured-post mb-4">
                        @if($hero->image)<img src="{{ cdn($hero->image) }}" alt="{{ $hero->name }}">@endif
                        <div class="inner">
                            <span class="badge bg-warning text-dark mb-2"><i class="fas fa-star me-1"></i>Nổi bật</span>
                            <h2>{{ $hero->name }}</h2>
                            @if($hero->description)<p class="mb-0" style="opacity:.9">{{ \Illuminate\Support\Str::limit(strip_tags($hero->description), 160) }}</p>@endif
                        </div>
                    </a>
                @endif

                @if($keyword !== '')
                    <p class="text-muted">Tìm thấy <strong class="text-dark">{{ $posts->total() }}</strong> bài viết cho “<strong class="text-dark">{{ $keyword }}</strong>”</p>
                @endif

                @if($posts->isEmpty())
                    <div class="card-soft text-center p-5 text-muted">
                        <i class="far fa-newspaper d-block mb-3" style="font-size:46px;color:var(--primary);opacity:.4"></i>
                        <h5 class="text-dark">Chưa có bài viết</h5>
                        <a href="{{ route('frontend.news.index') }}" class="btn btn-brand mt-2">Xem tất cả bài viết</a>
                    </div>
                @else
                    <div class="row g-4">
                        @foreach($posts as $post)
                            <div class="col-md-6">@include('Core::Frontend.news.partials.post-card', ['post' => $post])</div>
                        @endforeach
                    </div>
                    <div class="d-flex justify-content-center mt-4">
                        {{ $posts->onEachSide(1)->links('pagination::bootstrap-5') }}
                    </div>
                @endif
            </div>

            <aside class="col-lg-4">
                @include('Core::Frontend.news.partials.sidebar', compact('categories', 'popularPosts', 'activeCategory'))
            </aside>
        </div>
    </div>
</section>
@endsection
