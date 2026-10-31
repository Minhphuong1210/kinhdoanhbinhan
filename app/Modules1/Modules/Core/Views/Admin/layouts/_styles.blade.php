{{--
Partial: _styles.blade.php
Dùng cho: @include('Core::Admin.layouts._styles')
Thêm custom styles từ view con qua @push('styles') / @stack('styles')
--}}
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
<style>
    * {
        box-sizing: border-box;
        margin: 0;
        padding: 0
    }

    body {
        font-family: 'roboto';
        background: #f0f2f5;
        color: #333
    }

    /* sidebar CSS is now in _sidebar.blade.php */
    /* Sidebar layout */
    .main {
        margin-left: 240px;
        min-height: 100vh;
        display: flex;
        flex-direction: column;
        transition: margin-left 0.3s ease;
    }

    @media (max-width: 768px) {
        .main {
            margin-left: 0;
        }
    }

    .content {
        padding: 24px;
        flex: 1
    }

    .card {
        background: #fff;
        border-radius: 10px;
        box-shadow: 0 1px 6px rgba(0, 0, 0, .07);
        overflow: hidden
    }

    .card-header {
        padding: 16px 20px;
        border-bottom: 1px solid #f0f2f5;
        display: flex;
        align-items: center;
        justify-content: space-between
    }

    .card-header h5 {
        font-size: 15px;
        font-weight: 600;
        color: #1e2a3a
    }

    .card-body {
        padding: 20px
    }

    .btn {
        display: inline-flex;
        align-items: center;
        gap: 6px;
        padding: 8px 16px;
        border-radius: 7px;
        text-decoration: none;
        font-size: 13px;
        font-weight: 500;
        border: none;
        cursor: pointer;
        transition: .2s
    }

    .btn-primary {
        background: #4e8ef7;
        color: #fff
    }

    .btn-primary:hover {
        background: #3a7be8
    }

    .btn-success {
        background: #22c55e;
        color: #fff
    }

    .btn-success:hover {
        background: #16a34a
    }

    .btn-danger {
        background: #ef4444;
        color: #fff
    }

    .btn-danger:hover {
        background: #dc2626
    }

    .btn-warning {
        background: #f59e0b;
        color: #fff
    }

    .btn-warning:hover {
        background: #d97706
    }

    .btn-secondary {
        background: #e5e7eb;
        color: #374151
    }

    .btn-secondary:hover {
        background: #d1d5db
    }

    .btn-sm {
        padding: 5px 10px;
        font-size: 12px
    }

    table {
        width: 100%;
        border-collapse: collapse;
        font-size: 13.5px
    }

    table th {
        background: #f8f9fb;
        padding: 11px 14px;
        text-align: left;
        font-weight: 600;
        color: #5a6a7e;
        border-bottom: 2px solid #e9ecef;
        white-space: nowrap
    }

    table td {
        padding: 11px 14px;
        border-bottom: 1px solid #f0f2f5;
        vertical-align: middle
    }

    table tr:hover td {
        background: #fafbfc
    }

    .badge {
        display: inline-block;
        padding: 3px 9px;
        border-radius: 20px;
        font-size: 11.5px;
        font-weight: 500
    }

    .badge-success {
        background: #dcfce7;
        color: #16a34a
    }

    .badge-danger {
        background: #fee2e2;
        color: #dc2626
    }

    .badge-warning {
        background: #fef3c7;
        color: #d97706
    }

    .badge-info {
        background: #dbeafe;
        color: #2563eb
    }

    .badge-secondary {
        background: #f3f4f6;
        color: #4b5563
    }

    .form-group {
        margin-bottom: 18px
    }

    .form-group label {
        display: block;
        font-size: 13px;
        font-weight: 500;
        margin-bottom: 6px;
        color: #374151
    }

    .form-control {
        width: 100%;
        padding: 9px 12px;
        border: 1.5px solid #e2e8f0;
        border-radius: 7px;
        font-size: 13.5px;
        transition: .2s;
        background: #fff
    }

    .form-control:focus {
        outline: none;
        border-color: #4e8ef7;
        box-shadow: 0 0 0 3px rgba(78, 142, 247, .1)
    }

    .form-control.is-invalid {
        border-color: #ef4444
    }

    .invalid-feedback {
        color: #ef4444;
        font-size: 12px;
        margin-top: 4px
    }

    select.form-control {
        appearance: none
    }

    textarea.form-control {
        resize: vertical;
        min-height: 100px
    }

    .row {
        display: flex;
        gap: 20px;
        flex-wrap: wrap
    }

    .col-6 {
        flex: 0 0 calc(50% - 10px)
    }

    .col-4 {
        flex: 0 0 calc(33.333% - 14px)
    }

    .col-12 {
        flex: 0 0 100%
    }

    .alert {
        padding: 12px 16px;
        border-radius: 7px;
        font-size: 13.5px;
        margin-bottom: 16px;
        display: flex;
        align-items: center;
        gap: 8px
    }

    .alert-success {
        background: #dcfce7;
        color: #15803d;
        border: 1px solid #bbf7d0
    }

    .alert-danger {
        background: #fee2e2;
        color: #dc2626;
        border: 1px solid #fecaca
    }

    .pagination {
        display: flex;
        gap: 4px;
        justify-content: center;
        margin-top: 20px
    }

    .pagination a,
    .pagination span {
        padding: 6px 12px;
        border-radius: 6px;
        font-size: 13px;
        text-decoration: none;
        border: 1px solid #e2e8f0;
        color: #374151
    }

    .pagination .active span {
        background: #4e8ef7;
        color: #fff;
        border-color: #4e8ef7
    }

    .pagination a:hover {
        background: #f3f4f6
    }

    .stat-grid {
        display: grid;
        grid-template-columns: repeat(5, 1fr);
        gap: 16px;
        margin-bottom: 24px
    }

    .stat-card {
        background: #fff;
        border-radius: 10px;
        padding: 20px;
        box-shadow: 0 1px 6px rgba(0, 0, 0, .07);
        display: flex;
        align-items: center;
        gap: 16px
    }

    .stat-icon {
        width: 52px;
        height: 52px;
        border-radius: 12px;
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 22px
    }

    .stat-icon.blue {
        background: #dbeafe;
        color: #2563eb
    }

    .stat-icon.green {
        background: #dcfce7;
        color: #16a34a
    }

    .stat-icon.yellow {
        background: #fef3c7;
        color: #d97706
    }

    .stat-icon.red {
        background: #fee2e2;
        color: #dc2626
    }

    .stat-info .value {
        font-size: 24px;
        font-weight: 700;
        color: #1e2a3a
    }

    .stat-info .label {
        font-size: 12px;
        color: #8a9bb0;
        margin-top: 2px
    }

    .img-thumb {
        width: 44px;
        height: 44px;
        object-fit: cover;
        border-radius: 7px
    }

    /* ============================================
       RESPONSIVE — MOBILE ADMIN
       ============================================ */

    /* ── Hamburger button ── */
    .sidebar-toggle {
        display: none;
        background: none;
        border: none;
        cursor: pointer;
        padding: 4px 8px;
        font-size: 20px;
        color: #1e2a3a;
        line-height: 1;
    }

    @media (max-width: 768px) {

        /* Sidebar: ẩn mặc định, slide vào khi mở */
        .sidebar {
            position: fixed;
            top: 0;
            left: 0;
            height: 100vh;
            z-index: 1050;
            transform: translateX(-100%);
            transition: transform .28s cubic-bezier(.4, 0, .2, 1);
            box-shadow: 4px 0 24px rgba(0, 0, 0, .18);
        }

        .sidebar.open {
            transform: translateX(0);
        }

        /* Overlay bán trong suốt khi sidebar mở */
        .sidebar-overlay {
            display: none;
            position: fixed;
            inset: 0;
            background: rgba(0, 0, 0, .45);
            z-index: 1040;
        }

        .sidebar-overlay.show {
            display: block;
        }

        /* Nút hamburger hiện trên mobile */
        .sidebar-toggle {
            display: inline-flex;
            align-items: center;
        }

        /* Main không bị đẩy sang phải */
        .main {
            margin-left: 0 !important;
        }

        /* Topbar: thu gọn padding, để hamburger + tiêu đề + avatar */
        .topbar {
            padding: 0 12px;
            gap: 8px;
        }

        .topbar .page-title {
            font-size: 14px;
            flex: 1;
            overflow: hidden;
            text-overflow: ellipsis;
            white-space: nowrap;
        }

        /* Ẩn tên đầy đủ + email ở topbar, chỉ giữ avatar + ví */
        /*.topbar .user-info>div:nth-child(2) {*/
        /*    display: none;*/
        /*}*/

        /* Content padding nhỏ hơn */
        .content {
            padding: 14px 12px;
        }

        /* Table scroll ngang */
        .table-responsive-wrapper,
        .card-body>table,
        .content table {
            display: block;
            overflow-x: auto;
            -webkit-overflow-scrolling: touch;
            width: 100%;
        }

        /* Row form về 1 cột */
        .row {
            flex-direction: column;
            gap: 0;
        }

        .col-6,
        .col-4,
        .col-3,
        .col-8 {
            flex: 0 0 100%;
            width: 100%;
        }

        /* Stat grid: 2 cột trên mobile, 1 cột trên rất nhỏ */
        .stat-grid {
            grid-template-columns: repeat(2, 1fr);
            gap: 10px;
        }

        .stat-card {
            padding: 14px 12px;
            gap: 10px;
        }

        .stat-icon {
            width: 42px;
            height: 42px;
            font-size: 18px;
        }

        .stat-info .value {
            font-size: 20px;
        }

        /* Card header flex-wrap */
        .card-header {
            flex-wrap: wrap;
            gap: 8px;
            padding: 12px 14px;
        }

        /* Pagination nhỏ hơn */
        .pagination a,
        .pagination span {
            padding: 5px 9px;
            font-size: 12px;
        }

        /* Buttons trong table: đủ nhỏ */
        table .btn {
            padding: 4px 8px;
            font-size: 11.5px;
        }
    }

    @media (max-width: 480px) {
        .stat-grid {
            grid-template-columns: 1fr 1fr;
        }

        .content {
            padding: 10px 8px;
        }

        .card-header h5 {
            font-size: 13px;
        }

        table {
            font-size: 12.5px;
        }

        table th,
        table td {
            padding: 8px 10px;
        }
    }
</style>
@stack('styles')