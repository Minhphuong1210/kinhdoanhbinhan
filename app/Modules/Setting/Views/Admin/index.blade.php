@extends('Core::Admin.layouts.app')

@section('title', 'Cài đặt hệ thống')
@section('page-title', 'Cài đặt hệ thống')

@section('content')

    @php
        /*
        |--------------------------------------------------------------------------
        | Các setting dạng file
        |--------------------------------------------------------------------------
        */
        $imageSettings = ['logo', 'favicon'];
        $videoSettings = \App\Models\Setting::VIDEO_NAMES;

        /*
        |--------------------------------------------------------------------------
        | Tab mặc định
        |--------------------------------------------------------------------------
        */
        $firstTab = $settings->keys()->first();
        $activeTab = request('tab', $firstTab);
    @endphp

    <style>
        /* =========================================================
           TABS
        ========================================================= */

        .tabs {
            display: flex;
            gap: 4px;
            border-bottom: 2px solid #e9ecef;
            margin-bottom: 24px;
            flex-wrap: wrap;
        }

        .tab-btn {
            padding: 9px 18px;
            border: none;
            background: none;
            font-size: 13px;
            font-weight: 600;
            color: #6b7280;
            cursor: pointer;
            border-bottom: 3px solid transparent;
            margin-bottom: -2px;
            border-radius: 6px 6px 0 0;
            display: flex;
            align-items: center;
            gap: 7px;
            transition: .15s;
        }

        .tab-btn:hover {
            background: #f8faff;
            color: #4e8ef7;
        }

        .tab-btn.active {
            color: #4e8ef7;
            border-bottom-color: #4e8ef7;
            background: #f0f7ff;
        }

        /* =========================================================
           TAB CONTENT
        ========================================================= */

        .tab-pane {
            display: none;
        }

        .tab-pane.active {
            display: block;
        }

        /* =========================================================
           SETTING ROW
        ========================================================= */

        .setting-row {
            display: grid;
            grid-template-columns: 200px 1fr;
            align-items: start;
            gap: 16px;
            padding: 14px 0;
            border-bottom: 1px solid #f3f4f6;
        }

        .setting-row:last-child {
            border-bottom: none;
        }

        .setting-label {
            font-size: 13px;
            font-weight: 600;
            color: #374151;
        }

        .setting-key {
            font-size: 11px;
            color: #9ca3af;
            margin-top: 2px;
            font-family: monospace;
        }

        /* =========================================================
           IMAGE PREVIEW
        ========================================================= */

        .preview-img {
            max-width: 300px;
            max-height: 120px;
            border-radius: 7px;
            margin-top: 0;
            border: 1.5px solid #e5e7eb;
            object-fit: contain;
            background: #fff;
            display: block;
        }

        /* =========================================================
           VIDEO PREVIEW
        ========================================================= */

        .preview-video {
            width: 100%;
            max-width: 520px;
            max-height: 300px;
            border-radius: 8px;
            border: 1px solid #e5e7eb;
            background: #000;
            display: block;
        }

        /* =========================================================
           EMPTY PREVIEW
        ========================================================= */

        .preview-empty {
            width: 120px;
            height: 70px;
            background: #f3f4f6;
            border-radius: 7px;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 11px;
            color: #9ca3af;
            border: 1px dashed #d1d5db;
        }

        .video-empty {
            width: 300px;
            height: 170px;
            background: #f3f4f6;
            border-radius: 8px;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 13px;
            color: #9ca3af;
            border: 1px dashed #d1d5db;
        }

        /* =========================================================
           FILE INFO
        ========================================================= */

        .file-help {
            margin-top: 6px;
            font-size: 11px;
            color: #9ca3af;
        }

        .file-help strong {
            color: #6b7280;
        }

        /* =========================================================
           SAVE AREA
        ========================================================= */

        .setting-save {
            margin-top: 20px;
            padding-top: 16px;
            border-top: 2px solid #f0f2f5;
        }

        /* =========================================================
           RESPONSIVE
        ========================================================= */

        @media (max-width: 768px) {

            .setting-row {
                grid-template-columns: 1fr;
                gap: 8px;
            }

            .tab-btn {
                padding: 8px 12px;
            }

            .preview-video {
                max-width: 100%;
            }

            .video-empty {
                max-width: 100%;
            }
        }
    </style>


    <div class="card">

        {{-- =====================================================
             CARD HEADER
        ====================================================== --}}
        <div class="card-header">
            <h5 style="margin:0">
                <i class="fas fa-cog"></i>
                Cài đặt hệ thống
            </h5>
        </div>


        {{-- =====================================================
             CARD BODY
        ====================================================== --}}
        <div class="card-body">

            {{-- =================================================
                 TAB BUTTONS
            ================================================== --}}
            <div class="tabs" id="tabBar">

                @foreach($settings->keys() as $tabKey)

                    @php
                        $tabSlug = Str::slug($tabKey ?: 'chung');
                    @endphp

                    <button
                            type="button"
                            class="tab-btn {{ $activeTab === $tabKey ? 'active' : '' }}"
                            onclick="switchTab('{{ $tabKey }}', this)"
                    >
                        @if($tabKey === 'general' || $tabKey === 'chung')
                            <i class="fas fa-cog"></i>
                        @elseif($tabKey === 'seo')
                            <i class="fas fa-search"></i>
                        @elseif($tabKey === 'social')
                            <i class="fas fa-share-alt"></i>
                        @elseif($tabKey === 'media')
                            <i class="fas fa-photo-video"></i>
                        @else
                            <i class="fas fa-folder"></i>
                        @endif

                        {{ ucfirst($tabKey ?: 'Chung') }}
                    </button>

                @endforeach

            </div>


            {{-- =================================================
                 FORM
            ================================================== --}}
            <form
                    method="POST"
                    action="{{ route('admin.settings.update') }}"
                    enctype="multipart/form-data"
            >

                @csrf


                {{-- =================================================
                     TAB PANES
                ================================================== --}}
                @foreach($settings as $tabKey => $groupSettings)

                    @php
                        $tabSlug = Str::slug($tabKey ?: 'chung');
                    @endphp

                    <div
                            class="tab-pane {{ $activeTab === $tabKey ? 'active' : '' }}"
                            id="tab-{{ $tabSlug }}"
                    >

                        @foreach($groupSettings as $s)

                            <div class="setting-row">

                                {{-- =================================================
                                     LABEL
                                ================================================== --}}
                                <div>

                                    <div class="setting-label">
                                        {{ ucfirst(str_replace('_', ' ', $s->name)) }}
                                    </div>

                                    <div class="setting-key">
                                        {{ $s->name }}
                                    </div>

                                </div>


                                {{-- =================================================
                                     INPUT
                                ================================================== --}}
                                <div>


                                    {{-- =================================================
                                         VIDEO
                                    ================================================== --}}
                                    @if(in_array($s->name, $videoSettings) || $s->type === 'video')

                                        @if($s->value)

                                            <video
                                                    id="prev-{{ $s->id }}"
                                                    class="preview-video"
                                                    controls
                                                    preload="metadata"
                                            >

                                                <source
                                                        src="{{ cdn($s->value) }}"
                                                        type="video/mp4"
                                                >

                                                Trình duyệt của bạn không hỗ trợ video.

                                            </video>

                                        @else

                                            <div
                                                    id="prev-{{ $s->id }}"
                                                    class="video-empty"
                                            >
                                                <div>
                                                    <i
                                                            class="fas fa-video"
                                                            style="font-size:24px;margin-bottom:8px;display:block;text-align:center;"
                                                    ></i>

                                                    Chưa có video
                                                </div>
                                            </div>

                                        @endif


                                        <input
                                                type="file"
                                                name="uploads[{{ $s->id }}]"
                                                class="form-control"
                                                style="margin-top:8px"
                                                accept="video/mp4,video/webm,video/ogg"
                                                onchange="previewVideo(this, 'prev-{{ $s->id }}')"
                                        >

                                        <div class="file-help">
                                            <strong>Định dạng:</strong>
                                            MP4, WebM, OGG
                                            &nbsp;|&nbsp;
                                            <strong>Dung lượng:</strong>
                                            tối đa 2MB
                                        </div>


                                        {{-- =================================================
                                             IMAGE
                                        ================================================== --}}
                                    @elseif(in_array($s->name, $imageSettings) || $s->type === 'image')

                                        @if($s->value)

                                            <img
                                                    src="{{ cdn($s->value) }}"
                                                    alt="{{ $s->name }}"
                                                    class="preview-img"
                                                    id="prev-{{ $s->id }}"
                                            >

                                        @else

                                            <div
                                                    class="preview-empty"
                                                    id="prev-{{ $s->id }}"
                                            >
                                                Chưa có ảnh
                                            </div>

                                        @endif


                                        <input
                                                type="file"
                                                name="uploads[{{ $s->id }}]"
                                                class="form-control"
                                                style="margin-top:8px"
                                                accept="image/*"
                                                onchange="previewImg(this, 'prev-{{ $s->id }}')"
                                        >

                                        <div class="file-help">
                                            <strong>Định dạng:</strong>
                                            JPG, JPEG, PNG, WEBP
                                            &nbsp;|&nbsp;
                                            <strong>Dung lượng:</strong>
                                            tối đa 2MB
                                        </div>


                                        {{-- =================================================
                                             TEXTAREA
                                        ================================================== --}}
                                    @elseif($s->isSecret())

                                        <input
                                                type="password"
                                                name="settings[{{ $s->id }}]"
                                                class="form-control"
                                                value=""
                                                autocomplete="new-password"
                                                placeholder="{{ $s->value ? 'Đã lưu. Để trống nếu không đổi' : 'Chưa có giá trị' }}"
                                        >

                                    @elseif($s->type === 'textarea')

                                        <textarea
                                                name="settings[{{ $s->id }}]"
                                                class="form-control"
                                                rows="4"
                                        >{{ $s->value }}</textarea>


                                        {{-- =================================================
                                             TEXT
                                        ================================================== --}}
                                    @else

                                        <input
                                                type="text"
                                                name="settings[{{ $s->id }}]"
                                                class="form-control"
                                                value="{{ $s->value }}"
                                        >

                                    @endif

                                </div>

                            </div>

                        @endforeach

                    </div>

                @endforeach


                {{-- =================================================
                     SAVE BUTTON
                ================================================== --}}
                <div class="setting-save">

                    <button
                            type="submit"
                            class="btn btn-primary"
                    >
                        <i class="fas fa-save"></i>
                        Lưu cài đặt
                    </button>

                </div>

            </form>

        </div>

    </div>


    <script>

        /*
        |--------------------------------------------------------------------------
        | TAB
        |--------------------------------------------------------------------------
        */

        var _activeTabEl = null;

        function switchTab(key, btn) {

            /*
            |--------------------------------------------------------------------------
            | Remove active khỏi tất cả pane
            |--------------------------------------------------------------------------
            */

            document
                .querySelectorAll('.tab-pane')
                .forEach(function(el) {

                    el.classList.remove('active');

                });


            /*
            |--------------------------------------------------------------------------
            | Remove active khỏi tất cả button
            |--------------------------------------------------------------------------
            */

            document
                .querySelectorAll('.tab-btn')
                .forEach(function(el) {

                    el.classList.remove('active');

                });


            /*
            |--------------------------------------------------------------------------
            | Tạo slug giống Laravel Str::slug ở mức cơ bản
            |--------------------------------------------------------------------------
            */

            var slug = key
                .toString()
                .replace(/[^a-zA-Z0-9]+/g, '-')
                .replace(/^-+|-+$/g, '')
                .toLowerCase();

            if (!slug) {
                slug = 'chung';
            }


            /*
            |--------------------------------------------------------------------------
            | Active pane
            |--------------------------------------------------------------------------
            */

            var pane = document.getElementById('tab-' + slug);

            if (pane) {

                pane.classList.add('active');

            }


            /*
            |--------------------------------------------------------------------------
            | Active button
            |--------------------------------------------------------------------------
            */

            if (btn) {

                btn.classList.add('active');

            }


            /*
            |--------------------------------------------------------------------------
            | Đổi URL tab
            |--------------------------------------------------------------------------
            */

            try {

                var url = new URL(window.location.href);

                url.searchParams.set('tab', key);

                window.history.replaceState({}, '', url.toString());

            } catch (e) {

                // Không làm gì nếu browser không hỗ trợ URL API

            }

        }


        /*
        |--------------------------------------------------------------------------
        | PREVIEW IMAGE
        |--------------------------------------------------------------------------
        */

        function previewImg(input, previewId) {

            if (!input.files || !input.files[0]) {
                return;
            }


            var file = input.files[0];


            /*
            |--------------------------------------------------------------------------
            | Kiểm tra file ảnh
            |--------------------------------------------------------------------------
            */

            if (!file.type.startsWith('image/')) {

                alert('Vui lòng chọn file hình ảnh.');

                input.value = '';

                return;

            }


            /*
            |--------------------------------------------------------------------------
            | FileReader
            |--------------------------------------------------------------------------
            */

            var reader = new FileReader();


            reader.onload = function(e) {

                var prev = document.getElementById(previewId);

                if (!prev) {
                    return;
                }


                /*
                |--------------------------------------------------------------------------
                | Nếu đã là IMG
                |--------------------------------------------------------------------------
                */

                if (prev.tagName === 'IMG') {

                    prev.src = e.target.result;

                    return;

                }


                /*
                |--------------------------------------------------------------------------
                | Nếu đang là empty div
                |--------------------------------------------------------------------------
                */

                var img = document.createElement('img');

                img.src = e.target.result;

                img.className = 'preview-img';

                img.id = previewId;

                prev.replaceWith(img);

            };


            reader.readAsDataURL(file);

        }


        /*
        |--------------------------------------------------------------------------
        | PREVIEW VIDEO
        |--------------------------------------------------------------------------
        */

        function previewVideo(input, previewId) {

            if (!input.files || !input.files[0]) {
                return;
            }


            var file = input.files[0];


            /*
            |--------------------------------------------------------------------------
            | Kiểm tra file video
            |--------------------------------------------------------------------------
            */

            if (!file.type.startsWith('video/')) {

                alert('Vui lòng chọn file video.');

                input.value = '';

                return;

            }


            /*
            |--------------------------------------------------------------------------
            | Kiểm tra dung lượng
            | 2MB — khớp với giới hạn ở SettingController::update
            |--------------------------------------------------------------------------
            */

            var maxSize = 2 * 1024 * 1024;

            if (file.size > maxSize) {

                alert('Video không được vượt quá 2MB.');

                input.value = '';

                return;

            }


            /*
            |--------------------------------------------------------------------------
            | Tạo URL preview
            |--------------------------------------------------------------------------
            */

            var url = URL.createObjectURL(file);

            var prev = document.getElementById(previewId);


            if (!prev) {
                return;
            }


            /*
            |--------------------------------------------------------------------------
            | Nếu đã có VIDEO
            |--------------------------------------------------------------------------
            */

            if (prev.tagName === 'VIDEO') {

                var source = prev.querySelector('source');


                if (source) {

                    source.src = url;

                    source.type = file.type;

                } else {

                    source = document.createElement('source');

                    source.src = url;

                    source.type = file.type;

                    prev.appendChild(source);

                }


                prev.load();

                return;

            }


            /*
            |--------------------------------------------------------------------------
            | Tạo video mới
            |--------------------------------------------------------------------------
            */

            var video = document.createElement('video');

            video.id = previewId;

            video.className = 'preview-video';

            video.controls = true;

            video.preload = 'metadata';


            /*
            |--------------------------------------------------------------------------
            | Source
            |--------------------------------------------------------------------------
            */

            var source = document.createElement('source');

            source.src = url;

            source.type = file.type;


            video.appendChild(source);


            /*
            |--------------------------------------------------------------------------
            | Replace empty preview
            |--------------------------------------------------------------------------
            */

            prev.replaceWith(video);


            /*
            |--------------------------------------------------------------------------
            | Load video
            |--------------------------------------------------------------------------
            */

            video.load();

        }


        /*
        |--------------------------------------------------------------------------
        | Khi rời trang thì giải phóng Object URL
        |--------------------------------------------------------------------------
        */

        window.addEventListener('beforeunload', function() {

            /*
            |--------------------------------------------------------------------------
            | Browser tự quản lý Object URL trong đa số trường hợp.
            | Không cần xử lý thêm.
            |--------------------------------------------------------------------------
            */

        });

    </script>

@endsection