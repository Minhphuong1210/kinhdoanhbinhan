@extends('Core::Admin.layouts.app')
@section('title', 'Cài đặt hệ thống')
@section('page-title', 'Cài đặt hệ thống')
@section('content')

@php
    // settings đã được groupBy('type') từ controller
    $fileSettings = ['logo', 'favicon'];
    $firstTab = $settings->keys()->first();
    $activeTab = request('tab', $firstTab);
@endphp

<style>
.tabs { display:flex; gap:4px; border-bottom:2px solid #e9ecef; margin-bottom:24px; flex-wrap:wrap; }
.tab-btn { padding:9px 18px; border:none; background:none; font-size:13px; font-weight:600; color:#6b7280; cursor:pointer; border-bottom:3px solid transparent; margin-bottom:-2px; border-radius:6px 6px 0 0; display:flex; align-items:center; gap:7px; transition:.15s; }
.tab-btn:hover { background:#f8faff; color:#4e8ef7; }
.tab-btn.active { color:#4e8ef7; border-bottom-color:#4e8ef7; background:#f0f7ff; }
.tab-pane { display:none; }
.tab-pane.active { display:block; }
.setting-row { display:grid; grid-template-columns:200px 1fr; align-items:start; gap:16px; padding:14px 0; border-bottom:1px solid #f3f4f6; }
.setting-row:last-child { border-bottom:none; }
.setting-label { font-size:13px; font-weight:600; color:#374151; }
.setting-key { font-size:11px; color:#9ca3af; margin-top:2px; font-family:monospace; }
.preview-img { max-height:52px; border-radius:7px; margin-top:8px; border:1.5px solid #e5e7eb; }
</style>

<div class="card">
    <div class="card-header">
        <h5 style="margin:0"><i class="fas fa-cog"></i> Cài đặt hệ thống</h5>
    </div>
    <div class="card-body">

        {{-- Tab buttons theo type --}}
        <div class="tabs" id="tabBar">
            @foreach($settings->keys() as $tabKey)
            <button class="tab-btn {{ $activeTab === $tabKey ? 'active' : '' }}"
                    onclick="switchTab('{{ $tabKey }}', this)">
                {{ ucfirst($tabKey ?: 'Chung') }}
            </button>
            @endforeach
        </div>

        {{-- Form chứa tất cả tab panes --}}
        <form method="POST" action="{{ route('admin.settings.update') }}" enctype="multipart/form-data">
            @csrf

            @foreach($settings as $tabKey => $groupSettings)
            <div class="tab-pane {{ $activeTab === $tabKey ? 'active' : '' }}" id="tab-{{ Str::slug($tabKey ?: 'chung') }}">
                @foreach($groupSettings as $s)
                <div class="setting-row">
                    {{-- Label --}}
                    <div>
                        <div class="setting-label">{{ ucfirst(str_replace('_', ' ', $s->name)) }}</div>
                        <div class="setting-key">{{ $s->name }}</div>
                    </div>

                    {{-- Input --}}
                    <div>
                        @if(in_array($s->name, $fileSettings))
                            {{-- Ảnh: hiện preview + input file --}}
                            @if($s->value)
                                <img src="{{ cdn($s->value) }}" alt="{{ $s->name }}"
                                     class="preview-img" id="prev-{{ $s->id }}">
                            @else
                                <div style="width:80px;height:40px;background:#f3f4f6;border-radius:6px;
                                            display:flex;align-items:center;justify-content:center;
                                            font-size:11px;color:#9ca3af" id="prev-{{ $s->id }}">Chưa có</div>
                            @endif
                            <input type="file" name="uploads[{{ $s->id }}]" class="form-control"
                                   style="margin-top:8px" accept="image/*"
                                   onchange="previewImg(this,'prev-{{ $s->id }}')">

                        @elseif($s->type === 'textarea')
                            <textarea name="settings[{{ $s->id }}]" class="form-control" rows="3">{{ $s->value }}</textarea>

                        @else
                            <input type="text" name="settings[{{ $s->id }}]"
                                   class="form-control" value="{{ $s->value }}">
                        @endif
                    </div>
                </div>
                @endforeach
            </div>
            @endforeach

            <div style="margin-top:20px;padding-top:16px;border-top:2px solid #f0f2f5">
                <button type="submit" class="btn btn-primary">
                    <i class="fas fa-save"></i> Lưu cài đặt
                </button>
            </div>
        </form>
    </div>
</div>

<script>
var _activeTabEl = null;

function switchTab(key, btn) {
    document.querySelectorAll('.tab-pane').forEach(el => el.classList.remove('active'));
    document.querySelectorAll('.tab-btn').forEach(el => el.classList.remove('active'));
    var slug = key.replace(/[^a-z0-9]/gi, '-').toLowerCase() || 'chung';
    var pane = document.getElementById('tab-' + slug);
    if (pane) pane.classList.add('active');
    if (btn) btn.classList.add('active');
}

function previewImg(input, previewId) {
    if (!input.files || !input.files[0]) return;
    var reader = new FileReader();
    reader.onload = function(e) {
        var prev = document.getElementById(previewId);
        if (prev && prev.tagName === 'IMG') {
            prev.src = e.target.result;
        } else if (prev) {
            var img = document.createElement('img');
            img.src   = e.target.result;
            img.className = 'preview-img';
            img.id    = previewId;
            prev.replaceWith(img);
        }
    };
    reader.readAsDataURL(input.files[0]);
}
</script>
@endsection
