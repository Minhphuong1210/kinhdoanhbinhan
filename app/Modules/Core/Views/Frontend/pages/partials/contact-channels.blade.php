{{-- Các kênh liên hệ. Biến: $site, $colClass (tùy chọn — đặt trong cột hẹp thì truyền 'col-sm-6') --}}
@php
    $hotlineRaw = preg_replace('/[^0-9+]/', '', $site['hotline'] ?? '');
    $colClass   = $colClass ?? 'col-sm-6 col-lg-3';
@endphp
<div class="row g-3">
    @if(!empty($site['hotline']))
        <div class="{{ $colClass }}">
            <a href="tel:{{ $hotlineRaw }}" class="channel-card"><span class="ic" style="background:#fdecea;color:var(--danger)"><i class="fas fa-phone-alt"></i></span><span><small>Hotline</small><strong>{{ $site['hotline'] }}</strong></span></a>
        </div>
    @endif
    @if(!empty($site['email']))
        <div class="{{ $colClass }}">
            <a href="mailto:{{ $site['email'] }}" class="channel-card"><span class="ic"><i class="fas fa-envelope"></i></span><span><small>Email</small><strong class="text-break">{{ $site['email'] }}</strong></span></a>
        </div>
    @endif
    @if(!empty($site['zalo']))
        <div class="{{ $colClass }}">
            <a href="{{ $site['zalo'] }}" target="_blank" rel="noopener" class="channel-card"><span class="ic" style="background:#e6f0ff;color:#0068ff;font-weight:800;font-size:12px">Zalo</span><span><small>Chat Zalo</small><strong>Nhắn tin ngay</strong></span></a>
        </div>
    @endif
    @if(!empty($site['messenger']) || !empty($site['facebook']))
        <div class="{{ $colClass }}">
            <a href="{{ $site['messenger'] ?? $site['facebook'] }}" target="_blank" rel="noopener" class="channel-card"><span class="ic" style="background:#f0e8ff;color:#7b3fe4"><i class="fab fa-facebook-messenger"></i></span><span><small>Messenger</small><strong>Chat Facebook</strong></span></a>
        </div>
    @endif
</div>

@once
    @push('styles')
        <style>
            .channel-card { display: flex; align-items: center; gap: 14px; background: #fff; border: 1px solid var(--line); border-radius: var(--radius); padding: 16px; color: var(--ink); height: 100%; transition: .2s; }
            .channel-card:hover { border-color: var(--primary); box-shadow: var(--shadow); color: var(--ink); }
            .channel-card .ic { flex: 0 0 48px; height: 48px; border-radius: 12px; display: grid; place-items: center; background: var(--primary-soft); color: var(--primary); font-size: 19px; }
            .channel-card small { display: block; color: var(--muted); font-size: 12.5px; }
        </style>
    @endpush
@endonce
