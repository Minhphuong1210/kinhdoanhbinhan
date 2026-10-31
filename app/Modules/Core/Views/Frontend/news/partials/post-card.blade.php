{{-- Thẻ bài viết dùng chung. Biến: $post --}}
<article class="post-card h-100">
    <a href="{{ route('frontend.news.show', $post->slug) }}" class="post-thumb">
        @if($post->image)
            <img src="{{ cdn($post->image) }}" alt="{{ $post->name }}" loading="lazy">
        @else
            <span class="post-thumb-empty"><i class="far fa-newspaper"></i></span>
        @endif
        @if($post->relationLoaded('category') && $post->category)
            <span class="post-cat">{{ $post->category->name }}</span>
        @endif
    </a>
    <div class="post-body">
        <div class="post-meta"><i class="far fa-calendar-alt me-1"></i>{{ $post->created_at?->format('d/m/Y') }} <span class="mx-1">·</span> <i class="far fa-eye me-1"></i>{{ number_format((int) $post->view) }}</div>
        <a href="{{ route('frontend.news.show', $post->slug) }}" class="post-title">{{ $post->name }}</a>
        @if($post->description)
            <p class="post-excerpt">{{ \Illuminate\Support\Str::limit(strip_tags($post->description), 120) }}</p>
        @endif
        <a href="{{ route('frontend.news.show', $post->slug) }}" class="post-more">Đọc tiếp <i class="fas fa-arrow-right ms-1"></i></a>
    </div>
</article>

@once
    @push('styles')
        <style>
            .post-card { background: #fff; border: 1px solid var(--line); border-radius: var(--radius); overflow: hidden; display: flex; flex-direction: column; transition: box-shadow .2s, transform .2s; }
            .post-card:hover { box-shadow: var(--shadow); transform: translateY(-3px); }
            .post-thumb { position: relative; display: block; aspect-ratio: 16 / 10; background: var(--primary-soft); overflow: hidden; }
            .post-thumb img { width: 100%; height: 100%; object-fit: cover; transition: transform .35s; }
            .post-card:hover .post-thumb img { transform: scale(1.05); }
            .post-thumb-empty { position: absolute; inset: 0; display: grid; place-items: center; font-size: 40px; color: var(--primary); opacity: .4; }
            .post-cat { position: absolute; left: 12px; bottom: 12px; background: rgba(8,104,109,.9); color: #fff; font-size: 12px; font-weight: 600; padding: 4px 10px; border-radius: 999px; }
            .post-body { padding: 16px; display: flex; flex-direction: column; flex: 1; }
            .post-meta { font-size: 12.5px; color: var(--muted); margin-bottom: 6px; }
            .post-title { color: var(--ink); font-weight: 700; font-size: 16px; line-height: 1.45; display: -webkit-box; -webkit-line-clamp: 2; -webkit-box-orient: vertical; overflow: hidden; }
            .post-title:hover { color: var(--primary); }
            .post-excerpt { color: var(--muted); font-size: 14px; margin: 8px 0 0; display: -webkit-box; -webkit-line-clamp: 3; -webkit-box-orient: vertical; overflow: hidden; }
            .post-more { margin-top: auto; padding-top: 12px; font-weight: 600; font-size: 14px; }
        </style>
    @endpush
@endonce
