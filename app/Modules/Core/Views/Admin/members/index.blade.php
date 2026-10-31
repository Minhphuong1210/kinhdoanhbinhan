@extends('Core::Admin.layouts.app')
@section('title', 'Thành viên của tôi')
@section('page-title', 'Thành viên')
@push('styles')
<style>
.member-tree{padding:0;margin:0}
.member-row{display:flex;align-items:center;gap:0;border-bottom:1px solid #f1f5f9;background:#fff;transition:.15s}
.member-row:hover{background:#f8faff}
.member-indent{display:flex;align-items:center;width:100%;padding:10px 16px;gap:10px}
.member-toggle{width:26px;height:26px;border-radius:7px;border:1.5px solid #cbd5e1;background:#fff;color:#4e8ef7;font-size:14px;font-weight:700;cursor:pointer;display:flex;align-items:center;justify-content:center;flex-shrink:0;transition:.2s}
.member-toggle:hover{background:#4e8ef7;color:#fff;border-color:#4e8ef7}
.member-toggle.expanded{background:#4e8ef7;color:#fff;border-color:#4e8ef7}
.member-toggle.no-child{border-color:#e2e8f0;color:#cbd5e1;cursor:default}
.member-toggle:hover.no-child{background:#fff;color:#cbd5e1;border-color:#e2e8f0}
.member-avatar{width:34px;height:34px;border-radius:50%;object-fit:cover;flex-shrink:0;border:2px solid #e2e8f0}
.member-avatar-char{width:34px;height:34px;border-radius:50%;background:linear-gradient(135deg,#4e8ef7,#6c63ff);color:#fff;font-weight:700;font-size:13px;display:flex;align-items:center;justify-content:center;flex-shrink:0}
.member-info{flex:1;min-width:0}
.member-name{font-weight:600;font-size:13px;color:#1e2a3a;white-space:nowrap;overflow:hidden;text-overflow:ellipsis}
.member-email{font-size:11px;color:#8a9bb0;white-space:nowrap;overflow:hidden;text-overflow:ellipsis}
.member-meta{display:flex;gap:16px;margin-left:auto;align-items:center;flex-shrink:0;font-size:12px}
.badge-active{background:#dcfce7;color:#16a34a;padding:2px 9px;border-radius:20px;font-weight:700;font-size:11px}
.badge-locked{background:#fee2e2;color:#dc2626;padding:2px 9px;border-radius:20px;font-weight:700;font-size:11px}
.badge-members{background:#eff6ff;color:#4e8ef7;padding:2px 9px;border-radius:20px;font-weight:600;font-size:11px}
.children-container{display:none;border-left:2px solid #e2e8f0;margin-left:22px}
</style>
@endpush
@section('content')

<div class="card" style="margin-bottom:20px">
    <div class="card-body" style="display:flex;align-items:center;gap:16px">
        <div style="flex:1">
            <div style="font-size:13px;color:#8a9bb0">Thành viên trực tiếp bên dưới bạn</div>
            <div style="font-size:26px;font-weight:800;color:#4e8ef7">{{ count($members) }}</div>
        </div>
        <i class="fas fa-sitemap" style="font-size:32px;color:#e2e8f0"></i>
    </div>
</div>

<div class="card">
    <div class="card-header"><h5><i class="fas fa-users"></i> Cây thành viên — ấn <b style="color:#4e8ef7">+</b> để mở rộng</h5></div>
    <div class="card-body" style="padding:0">
        <div id="tree-root" class="member-tree">
            @forelse($members as $m)
            @include('Core::Admin.members._row', ['m' => $m, 'depth' => 0])
            @empty
            <div style="text-align:center;color:#8a9bb0;padding:48px">
                <i class="fas fa-users-slash" style="font-size:36px;display:block;margin-bottom:10px;opacity:.35"></i>
                Bạn chưa có thành viên nào bên dưới.
            </div>
            @endforelse
        </div>
    </div>
</div>

@push('scripts')
<script>
const childrenUrl = "{{ route('admin.my-members.children', '__ID__') }}";

function toggleMember(btn, userId) {
    const container = document.getElementById('children-' + userId);
    const isExpanded = btn.classList.contains('expanded');

    if (isExpanded) {
        container.style.display = 'none';
        btn.classList.remove('expanded');
        btn.textContent = '+';
        return;
    }

    // Đã load rồi thì chỉ show
    if (container.dataset.loaded === '1') {
        container.style.display = 'block';
        btn.classList.add('expanded');
        btn.textContent = '−';
        return;
    }

    // AJAX load children
    btn.textContent = '…';
    btn.disabled = true;
    fetch(childrenUrl.replace('__ID__', userId))
        .then(r => r.json())
        .then(children => {
            container.dataset.loaded = '1';
            if (children.length === 0) {
                container.innerHTML = '<div style="padding:8px 16px;font-size:12px;color:#8a9bb0"><i>Không có thành viên bên dưới</i></div>';
            } else {
                container.innerHTML = children.map(m => buildRow(m, 1)).join('');
            }
            container.style.display = 'block';
            btn.classList.add('expanded');
            btn.textContent = '−';
            btn.disabled = false;
        })
        .catch(() => { btn.textContent = '+'; btn.disabled = false; });
}

function buildRow(m, depth) {
    const hasChildren = m.direct_count > 0;
    const CDN = 'https://cdn.kinhdoanhbinhan.com/shared';

    const avatar = m.image
        ? `<img class="member-avatar" src="${CDN}/${m.image}">`
        : `<div class="member-avatar-char">${m.avatar_char}</div>`;
    const toggle = hasChildren
        ? `<button class="member-toggle" onclick="toggleMember(this,${m.id})">+</button>`
        : `<button class="member-toggle no-child" disabled>·</button>`;
    const statusBadge = m.active
        ? `<span class="badge-active">Hoạt động</span>`
        : `<span class="badge-locked">Khoá</span>`;

    return `
    <div class="member-row">
        <div class="member-indent" style="padding-left:${16 + depth*20}px">
            ${toggle}
            ${avatar}
            <div class="member-info">
                <div class="member-name">${m.name}</div>
                <div class="member-email">${m.email || ''}</div>
            </div>
            <div class="member-meta">
                <span style="color:#22c55e;font-weight:700">${m.vi_tien}</span>
                <span class="badge-members"><i class="fas fa-users"></i> ${m.direct_count}</span>
                ${statusBadge}
                <span style="color:#8a9bb0">${m.joined || ''}</span>
            </div>
        </div>
    </div>
    <div id="children-${m.id}" class="children-container"></div>`;
}
</script>
@endpush
@endsection
