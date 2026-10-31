{{--
Partial: _topbar.blade.php
--}}
@php
    $authUser = $authUser ?? auth()->user();
    $isAdmin = $authUser && $authUser->is_admin;

    $startDate = Illuminate\Support\Carbon::parse($authUser->ngay_mua_don_cuoi);
    $endDate = Illuminate\Support\Carbon::now();
    $interval = $startDate->diff($endDate);
    $days = $interval->format('%a');
@endphp

<style>
    .topbar {
        width: 100%;
        min-height: 64px;
        background: #fff;
        border-radius: 16px;
        padding: 10px 16px;
        display: flex;
        flex-wrap: wrap;
        align-items: center;
        justify-content: space-between;
        gap: 8px;
        box-shadow: 0 4px 18px rgba(15, 23, 42, .06);
        border: 1px solid #edf2f7;
        margin-bottom: 15px;
        position: sticky;
        top: 10px;
        z-index: 99;
        transition: all 0.3s ease;
    }

    .topbar-left {
        display: flex;
        align-items: center;
        gap: 10px;
    }

    .sidebar-toggle {
        width: 38px;
        height: 38px;
        border: none;
        border-radius: 10px;
        background: #f8fafc;
        color: #334155;
        cursor: pointer;
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 16px;
    }

    .sidebar-toggle:hover {
        background: #4f46e5;
        color: #fff;
    }

    .page-title {
        font-size: 17px;
        font-weight: 800;
        color: #0f172a;
        margin: 0;
        white-space: nowrap;
        overflow: hidden;
        text-overflow: ellipsis;
        max-width: 180px;
    }

    .topbar-warning {
        display: flex;
        align-items: center;
        gap: 6px;
        background: #fef2f2;
        border: 1px solid #fecaca;
        color: #dc2626;
        padding: 8px 12px;
        border-radius: 10px;
        font-size: 11.5px;
        font-weight: 600;
        justify-content: center;
    }

    .user-info {
        display: flex;
        align-items: center;
        gap: 6px;
        margin-left: auto;
    }

    .wallet-box {
        display: flex;
        align-items: center;
        gap: 6px;
        background: #f0fdf4;
        border: 1px solid #bbf7d0;
        border-radius: 8px;
        padding: 5px 10px;
        transition: .2s;
        min-width: max-content;
    }

    .wallet-icon {
        width: 22px;
        height: 22px;
        border-radius: 6px;
        background: #22c55e;
        color: #fff;
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 10px;
    }

    .wallet-content {
        display: flex;
        flex-direction: column;
        line-height: 1.1;
    }

    .wallet-label {
        font-size: 8.5px;
        color: #64748b;
        font-weight: 600;
        text-transform: uppercase;
    }

    .wallet-value {
        font-size: 11.5px;
        font-weight: 700;
        color: #15803d;
    }

    .avatar-box {
        width: 36px;
        height: 36px;
        border-radius: 10px;
        background: linear-gradient(135deg, #4f46e5, #7c3aed);
        display: flex;
        align-items: center;
        justify-content: center;
        color: #fff;
        font-weight: 700;
        font-size: 14px;
        box-shadow: 0 4px 12px rgba(79, 70, 229, .2);
    }

    /* ── Responsive ── */

    @media (min-width: 1201px) {
        .topbar-warning.mobile-only {
            display: none;
        }

        .topbar-warning.desktop-only {
            flex: 1;
            margin: 0 15px;
        }
    }

    @media (max-width: 1200px) {
        .topbar-warning.desktop-only {
            display: none;
        }
    }

    @media (max-width: 991px) {
        .user-meta {
            display: none;
        }

        .topbar {
            border-radius: 0;
            top: 0;
            margin-bottom: 10px;
            padding: 10px;
        }
    }

    @media (max-width: 767px) {
        .topbar-left {
            flex: 1;
        }

        .page-title {
            font-size: 15px;
            max-width: 130px;
        }

        .wallet-box {
            padding: 4px 8px;
        }

        .wallet-label {
            display: none;
        }

        .wallet-value {
            font-size: 11px;
        }

        .topbar-warning.mobile-only {
            width: 100%;
            order: 10;
            margin-top: 4px;
            padding: 6px 10px;
        }
    }

    @media (max-width: 480px) {
        .topbar {
            gap: 6px;
        }

        .topbar-left {
            width: 100%;
            flex: none;
            justify-content: flex-start;
            border-bottom: 1px solid #f1f5f9;
            padding-bottom: 6px;
        }

        .user-info {
            width: 100%;
            justify-content: space-between;
            margin-left: 0;
            padding: 2px 0;
        }

        .wallet-box {
            flex: 1;
            justify-content: center;
        }

        .avatar-box {
            width: 32px;
            height: 32px;
            font-size: 13px;
        }
    }
</style>

<div class="topbar">
    <div class="topbar-left">
        <button class="sidebar-toggle" id="sidebarToggle" aria-label="Mở menu">
            <i class="fas fa-bars"></i>
        </button>
        <span class="page-title">
            @yield('page-title', 'Dashboard')
        </span>
    </div>

    {{-- Warning Desktop --}}
    <div class="topbar-warning desktop-only">
        <i class="fas fa-clock"></i>
        <span>
            Bạn đã mua hàng được <strong>{{ sprintf('%02d', $days) }}</strong> ngày. Sau 60 ngày không hoàn phí.
        </span>
    </div>

    <div class="user-info">
        @if($authUser)
            <div class="wallet-box" title="Ví rút">
                <div class="wallet-icon">
                    <i class="fas fa-wallet"></i>
                </div>
                <div class="wallet-content">
                    <span class="wallet-label">Ví rút</span>
                    <span class="wallet-value">
                        {{ number_format($authUser->vi_tien ?? 0, 0, ',', '.') }}₫
                    </span>
                </div>
            </div>

            <div class="wallet-box" title="Ví mua">
                <div class="wallet-icon">
                    <i class="fas fa-coins"></i>
                </div>
                <div class="wallet-content">
                    <span class="wallet-label">Ví mua</span>
                    <span class="wallet-value">
                        {{ number_format($authUser->vi_tien_mua_hang ?? 0, 0, ',', '.') }}₫
                    </span>
                </div>
            </div>
        @endif

        <div class="avatar-box">
            {{ strtoupper(substr($authUser->name ?? 'A', 0, 1)) }}
        </div>
    </div>

    {{-- Warning Mobile --}}
    <div class="topbar-warning mobile-only">
        <i class="fas fa-clock"></i>
        <span>
            Bạn đã mua hàng được <strong>{{ $days }}</strong> ngày. Sau 60 ngày không hoàn phí.
        </span>
    </div>
</div>