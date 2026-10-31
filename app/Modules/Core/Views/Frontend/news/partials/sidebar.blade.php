{{-- Cột phải trang tin tức. Biến: $categories, $popularPosts, $activeCategory (có thể null) --}}
<div class="news-side">
    <form action="{{ route('frontend.news.index') }}" method="GET" class="card-soft p-3 mb-3">
        <h6 class="fw-bold mb-2">Tìm bài viết</h6>
        <div class="input-group">
            <input type="search" name="q" class="form-control" placeholder="Nhập từ khóa..." value="{{ request('q') }}">
            <button class="btn btn-brand" type="submit" aria-label="Tìm"><i class="fas fa-search"></i></button>
        </div>
    </form>

    @if($categories->isNotEmpty())
        <div class="card-soft p-3 mb-3">
            <h6 class="fw-bold mb-2">Chuyên mục</h6>
            <div class="filter-list">
                <a href="{{ route('frontend.news.index') }}" class="{{ empty($activeCategory) && request()->routeIs('frontend.news.index') ? 'active' : '' }}">Tất cả bài viết</a>
                @foreach($categories as $cat)
                    <a href="{{ route('frontend.news.index', ['slug' => $cat->slug]) }}" class="{{ ($activeCategory->id ?? null) === $cat->id ? 'active' : '' }}">
                        <span>{{ $cat->name }}</span><span class="count">{{ $cat->posts_count }}</span>
                    </a>
                @endforeach
            </div>
        </div>
    @endif

    @if($popularPosts->isNotEmpty())
        <div class="card-soft p-3">
            <h6 class="fw-bold mb-3">Đọc nhiều nhất</h6>
            @foreach($popularPosts as $item)
                <a href="{{ route('frontend.news.show', $item->slug) }}" class="popular-item">
                    <span class="popular-thumb">@if($item->image)<img src="{{ cdn($item->image) }}" alt="" loading="lazy">@endif</span>
                    <span>
                        <span class="popular-title">{{ $item->name }}</span>
                        <small class="text-muted"><i class="far fa-calendar-alt me-1"></i>{{ $item->created_at?->format('d/m/Y') }}</small>
                    </span>
                </a>
            @endforeach
        </div>
    @endif
</div>

@once
    @push('styles')
        <style>
            .news-side { position: sticky; top: 140px; }
            .filter-list a { display: flex; justify-content: space-between; align-items: center; padding: 9px 12px; border-radius: 10px; color: var(--ink); font-weight: 500; }
            .filter-list a:hover { background: var(--primary-soft); color: var(--primary-dark); }
            .filter-list a.active { background: var(--primary); color: #fff; }
            .filter-list a .count { font-size: 12px; background: rgba(0,0,0,.06); border-radius: 999px; padding: 1px 8px; }
            .filter-list a.active .count { background: rgba(255,255,255,.25); }
            .popular-item { display: flex; gap: 12px; padding: 10px 0; border-top: 1px solid var(--line); color: var(--ink); }
            .popular-item:first-of-type { border-top: 0; padding-top: 0; }
            .popular-thumb { flex: 0 0 78px; height: 58px; border-radius: 8px; overflow: hidden; background: var(--primary-soft); }
            .popular-thumb img { width: 100%; height: 100%; object-fit: cover; }
            .popular-title { display: -webkit-box; -webkit-line-clamp: 2; -webkit-box-orient: vertical; overflow: hidden; font-weight: 600; font-size: 14px; line-height: 1.4; margin-bottom: 2px; }
            .popular-item:hover .popular-title { color: var(--primary); }
            @media (max-width: 991.98px) { .news-side { position: static; } }
        </style>
    @endpush
@endonce
