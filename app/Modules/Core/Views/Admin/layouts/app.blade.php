<!DOCTYPE html>
<html lang="vi">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('title', 'Admin Panel')</title>
    @include('Core::Admin.layouts._styles')
</head>

<body>

@include('Core::Admin.layouts._sidebar')

{{-- Overlay khi sidebar mở trên mobile --}}
<div class="" id="sidebarOverlay"></div>

<div class="main">
    @include('Core::Admin.layouts._topbar')

    <div class="content">
        @if(session('success'))
            <div class="alert alert-success"><i class="fas fa-check-circle"></i> {{ session('success') }}</div>
        @endif
        @if(session('error'))
            <div class="alert alert-danger"><i class="fas fa-exclamation-circle"></i> {{ session('error') }}</div>
        @endif

        @yield('content')
    </div>
</div>

@include('Core::Admin.layouts._scripts')

<script>
    /* ── Mobile Sidebar Toggle ── */
    document.addEventListener('DOMContentLoaded', function () {
        var sidebar = document.querySelector('.sidebar');
        var overlay = document.getElementById('sidebarOverlay');
        var toggler = document.getElementById('sidebarToggle');

        if (!toggler) return; // không phải trang admin mobile

        function openSidebar() {
            sidebar && sidebar.classList.add('open');
            overlay && overlay.classList.add('show');
            document.body.style.overflow = 'hidden'; // chặn scroll khi sidebar mở
        }
        function closeSidebar() {
            sidebar && sidebar.classList.remove('open');
            overlay && overlay.classList.remove('show');
            document.body.style.overflow = '';
        }

        function toggleSidebar() {
            if (sidebar.classList.contains('open')) {
                closeSidebar();
            } else {
                openSidebar();
            }
        }

        toggler.addEventListener('click', toggleSidebar);
        overlay && overlay.addEventListener('click', closeSidebar);

        // Nút X đóng sidebar bên trong sidebar
        var closer = document.getElementById('closeSidebar');
        closer && closer.addEventListener('click', closeSidebar);

        /* Đóng sidebar khi click link trong sidebar */
        sidebar && sidebar.querySelectorAll('a').forEach(function (a) {
            a.addEventListener('click', function () {
                if (window.innerWidth <= 991) closeSidebar();
            });
        });
    });
</script>

</body>

</html>