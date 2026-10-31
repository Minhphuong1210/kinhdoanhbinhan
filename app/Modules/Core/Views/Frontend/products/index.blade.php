@extends('Core::Frontend.layouts.app')

@section('title', $activeCategory?->name ?? ($keyword !== '' ? 'Tìm kiếm: ' . $keyword : 'Tất cả sản phẩm'))
@section('meta_description', 'Danh sách sản phẩm ' . ($activeCategory?->name ?? 'dinh dưỡng, hàng tiêu dùng') . ' chính hãng, giá tốt.')

@push('styles')
<style>
    .filter-card { background: #fff; border: 1px solid var(--line); border-radius: var(--radius); padding: 18px; position: sticky; top: 140px; }
    .filter-card h6 { font-weight: 700; margin-bottom: 12px; }
    .filter-list a { display: flex; justify-content: space-between; align-items: center; padding: 9px 12px; border-radius: 10px; color: var(--ink); font-weight: 500; }
    .filter-list a:hover { background: var(--primary-soft); color: var(--primary-dark); }
    .filter-list a.active { background: var(--primary); color: #fff; }
    .filter-list a .count { font-size: 12px; background: rgba(0,0,0,.06); border-radius: 999px; padding: 1px 8px; }
    .filter-list a.active .count { background: rgba(255,255,255,.25); }
    .toolbar { background: #fff; border: 1px solid var(--line); border-radius: var(--radius); padding: 12px 16px; }
    .toolbar .form-select { width: auto; border-radius: 10px; }
    .empty-state { background: #fff; border: 1px dashed var(--line); border-radius: var(--radius); padding: 56px 20px; text-align: center; color: var(--muted); }
    .empty-state i { font-size: 46px; color: var(--primary); opacity: .4; }
</style>
@endpush

@section('content')
<section class="page-hero">
    <div class="container">
        <h1>{{ $activeCategory?->name ?? 'Tất cả sản phẩm' }}</h1>
        <nav aria-label="breadcrumb">
            <ol class="breadcrumb">
                <li class="breadcrumb-item"><a href="{{ route('frontend.home') }}">Trang chủ</a></li>
                <li class="breadcrumb-item {{ $activeCategory ? '' : 'active' }}">
                    @if($activeCategory)<a href="{{ route('frontend.product.index') }}">Sản phẩm</a>@else Sản phẩm @endif
                </li>
                @if($activeCategory)<li class="breadcrumb-item active">{{ $activeCategory->name }}</li>@endif
            </ol>
        </nav>
    </div>
</section>

<section class="section">
    <div class="container">
        <div class="row g-4">
            {{-- Bộ lọc --}}
            <aside class="col-lg-3">
                <button class="btn btn-outline-brand w-100 d-lg-none mb-2" type="button" data-bs-toggle="collapse" data-bs-target="#filterBox">
                    <i class="fas fa-filter me-1"></i> Danh mục &amp; tìm kiếm
                </button>
                <div class="collapse d-lg-block" id="filterBox">
                    <div class="filter-card">
                        <form method="GET" action="{{ route('frontend.product.index') }}" class="mb-4">
                            @if($activeCategory)<input type="hidden" name="category" value="{{ $activeCategory->slug }}">@endif
                            <input type="hidden" name="sort" value="{{ $sort }}">
                            <h6>Tìm kiếm</h6>
                            <div class="input-group">
                                <input type="search" name="q" value="{{ $keyword }}" class="form-control" placeholder="Tên sản phẩm...">
                                <button class="btn btn-brand" type="submit" aria-label="Tìm"><i class="fas fa-search"></i></button>
                            </div>
                        </form>

                        <h6>Danh mục sản phẩm</h6>
                        <div class="filter-list">
                            <a href="{{ route('frontend.product.index', array_filter(['q' => $keyword, 'sort' => $sort])) }}" class="{{ $activeCategory ? '' : 'active' }}">
                                Tất cả
                            </a>
                            @foreach($categories as $cat)
                                <a href="{{ route('frontend.product.index', array_filter(['category' => $cat->slug, 'q' => $keyword, 'sort' => $sort])) }}"
                                   class="{{ $activeCategory?->id === $cat->id ? 'active' : '' }}">
                                    <span>{{ $cat->name }}</span><span class="count">{{ $cat->products_count }}</span>
                                </a>
                            @endforeach
                        </div>
                    </div>
                </div>
            </aside>

            {{-- Danh sách --}}
            <div class="col-lg-9">
                <div class="toolbar d-flex flex-wrap justify-content-between align-items-center gap-2 mb-3">
                    <div class="text-muted">
                        @if($keyword !== '')Kết quả cho “<strong class="text-dark">{{ $keyword }}</strong>”: @endif
                        <strong class="text-dark">{{ number_format($products->total()) }}</strong> sản phẩm
                    </div>
                    <form method="GET" action="{{ route('frontend.product.index') }}" class="d-flex align-items-center gap-2">
                        @if($activeCategory)<input type="hidden" name="category" value="{{ $activeCategory->slug }}">@endif
                        @if($keyword !== '')<input type="hidden" name="q" value="{{ $keyword }}">@endif
                        <label for="sort" class="text-muted small text-nowrap">Sắp xếp</label>
                        <select name="sort" id="sort" class="form-select form-select-sm" onchange="this.form.submit()">
                            @foreach($sorts as $value => $label)
                                <option value="{{ $value }}" {{ $sort === $value ? 'selected' : '' }}>{{ $label }}</option>
                            @endforeach
                        </select>
                    </form>
                </div>

                @if($products->isEmpty())
                    <div class="empty-state">
                        <i class="fas fa-box-open d-block mb-3"></i>
                        <h5 class="text-dark">Không tìm thấy sản phẩm phù hợp</h5>
                        <p class="mb-3">Thử từ khóa khác hoặc xem tất cả sản phẩm.</p>
                        <a href="{{ route('frontend.product.index') }}" class="btn btn-brand">Xem tất cả sản phẩm</a>
                    </div>
                @else
                    <div class="row g-3">
                        @foreach($products as $product)
                            <div class="col-6 col-md-4">@include('Core::Frontend.home.partials.product-card', ['product' => $product])</div>
                        @endforeach
                    </div>
                    <div class="d-flex justify-content-center mt-4">
                        {{ $products->onEachSide(1)->links('pagination::bootstrap-5') }}
                    </div>
                @endif
            </div>
        </div>
    </div>
</section>
@endsection
