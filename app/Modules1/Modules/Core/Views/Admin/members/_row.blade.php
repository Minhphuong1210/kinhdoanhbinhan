{{-- Partial: một dòng thành viên trong cây --}}
@php
    $hasChildren = $m->direct_count > 0;
    $paddingLeft = 16 + $depth * 20;
@endphp
<div class="member-row">
    <div class="member-indent" style="padding-left:{{ $paddingLeft }}px">
        {{-- Toggle + --}}
        @if($hasChildren)
        <button class="member-toggle" onclick="toggleMember(this, {{ $m->id }})">+</button>
        @else
        <button class="member-toggle no-child" disabled>·</button>
        @endif

        {{-- Avatar --}}
        @if($m->image)
        <img class="member-avatar" src="{{ cdn($m->image)  }}">
        @else
        <div class="member-avatar-char">{{ strtoupper(substr($m->name,0,1)) }}</div>
        @endif

        {{-- Info --}}
        <div class="member-info">
            <div class="member-name">{{ $m->name }}</div>
            <div class="member-email">{{ $m->email }}</div>
        </div>

        {{-- Meta --}}
        <div class="member-meta">
            @if($m->tel)<span style="color:#5a6a7e">{{ $m->tel }}</span>@endif
            <span style="color:#22c55e;font-weight:700">{{ number_format($m->vi_tien ?? 0, 0, ',', '.') }}₫</span>
            <span class="badge-members"><i class="fas fa-users"></i> {{ $m->direct_count }}</span>
            @if($m->active)
            <span class="badge-active">Hoạt động</span>
            @else
            <span class="badge-locked">Khoá</span>
            @endif
            <span style="color:#8a9bb0;font-size:11px">{{ $m->created_at?->format('d/m/Y') }}</span>
        </div>
    </div>
</div>
{{-- Container con — AJAX sẽ inject vào đây --}}
<div id="children-{{ $m->id }}" class="children-container"></div>
