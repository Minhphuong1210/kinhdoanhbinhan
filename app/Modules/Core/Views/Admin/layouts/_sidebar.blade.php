
@php
    $authUser = auth()->user();
    $isAdmin = $authUser && $authUser->is_admin;
    $can = fn(string $perm): bool => $isAdmin || ($authUser && $authUser->hasPermission($perm));

    $inFinance = request()->routeIs(
        'admin.my-transactions',
        'admin.chuyen-diem*',
        'admin.my-withdrawals',
        'admin.my-income',
    );
    $inMember = request()->routeIs('admin.my-members.*', 'admin.my-referrals', 'admin.my-tree');
    $inShop = request()->routeIs('admin.shop.*');
    $inContent = request()->routeIs(
        'admin.categories.*',
        'admin.products.*',
        'admin.co-che-hoan-phi.*',
        'admin.category-posts.*',
        'admin.posts.*',
        'admin.tags.*',
    );
    $inIntro = request()->routeIs(
        'admin.gioi-thieus.*',
        'admin.chinh-sachs.*',
        'admin.leaders.*',
        'admin.thu-viens.*',
        'admin.widgets.*',
    );
    $inAdminFin = request()->routeIs(
        'admin.lich-su-giao-dich.*',
        'admin.yeu-cau-rut-tien.*',
        'admin.orders.*',
        'admin.users.*',
    );
    $siteLogo = \App\Models\Setting::forWeb()->where('name', 'logo')->value('value');
    $sidebarCoCheHoanPhis = \App\Models\CoCheHoanPhi::withCount([
        'products' => fn($q) => $q->where('active', 1),
    ])
        ->orderBy('name')
        ->get();

   

    $sidebarCoCheShopUrl = function ($coChe) use ($sidebarCoCheHoanPhis) {
        if ($coChe->trang_hoat_dong === 'tu_chon') {
            $customProduct = \App\Models\Product::where('active', 1)
                ->where('co_che_hoan_phi_id', $coChe->id)
                ->orderBy('stt')
                ->orderBy('id')
                ->first(['id']);

            if ($customProduct) {
                return route('admin.shop.buy', $customProduct->id) . '?mode=custom_co_che';
            }
        }

        return route('admin.shop.index', ['co_che' => $coChe->id]);
    };

@endphp

<style>
    .sidebar {
        width: 240px;
        background: linear-gradient(180deg, #1a2540 0%, #1e2d4a 100%);
        height: 100vh;
        position: fixed;
        top: 0;
        left: 0;
        z-index: 1050;
        display: flex;
        flex-direction: column;
        overflow: hidden
    }

    .sidebar .brand {
        padding: 18px 20px;
        background: rgba(0, 0, 0, .25);
        display: flex;
        align-items: center;
        gap: 10px;
        border-bottom: 1px solid rgba(255, 255, 255, .06)
    }

    .sidebar .brand span {
        color: #fff;
        font-size: 16px;
        font-weight: 700;
        letter-spacing: .3px
    }

    .sidebar .brand i {
        color: #60a5fa;
        font-size: 18px
    }

    .sidebar nav {
        flex: 1;
        overflow-y: auto;
        overflow-x: hidden;
        padding-bottom: 8px
    }

    .sidebar nav::-webkit-scrollbar {
        width: 3px
    }

    .sidebar nav::-webkit-scrollbar-thumb {
        background: rgba(255, 255, 255, .1);
        border-radius: 4px
    }

    /* Single links */
    .sidebar nav a,
    .sidebar nav button.nav-single {
        display: flex;
        align-items: center;
        gap: 10px;
        padding: 10px 20px;
        color: #9bb0c8;
        text-decoration: none;
        font-size: 13.5px;
        transition: .15s;
        border-left: 3px solid transparent;
        width: 100%;
        background: none;
        border-top: none;
        border-right: none;
        border-bottom: none;
        cursor: pointer;
        text-align: left
    }

    .sidebar nav a:hover,
    .sidebar nav a.active,
    .sidebar nav button.nav-single:hover {
        color: #fff;
        background: rgba(255, 255, 255, .07);
        border-left-color: #60a5fa
    }

    .sidebar nav a i,
    .sidebar nav button.nav-single i {
        width: 16px;
        text-align: center;
        font-size: 14px;
        flex-shrink: 0
    }

    /* Section label */
    .nav-group {
        padding: 14px 20px 4px;
        font-size: 10.5px;
        color: #4a6080;
        text-transform: uppercase;
        letter-spacing: 1px;
        font-weight: 600
    }

    /* Dropdown trigger */
    .nav-dropdown-btn {
        display: flex;
        align-items: center;
        gap: 10px;
        padding: 10px 20px;
        color: #9bb0c8;
        font-size: 13.5px;
        cursor: pointer;
        transition: .15s;
        border: none;
        background: none;
        width: 100%;
        text-align: left;
        border-left: 3px solid transparent
    }

    .nav-dropdown-btn:hover,
    .nav-dropdown-btn.open,
    .nav-dropdown-btn.active-group {
        color: #fff;
        background: rgba(255, 255, 255, .07);
        border-left-color: #60a5fa
    }

    .nav-dropdown-btn i.dd-icon {
        width: 16px;
        text-align: center;
        font-size: 14px;
        flex-shrink: 0
    }

    .nav-dropdown-btn .dd-arrow {
        margin-left: auto;
        font-size: 10px;
        transition: .25s;
        color: #4a6080
    }

    .nav-dropdown-btn.open .dd-arrow {
        transform: rotate(180deg)
    }

    .nav-dropdown-btn .badge-dot {
        width: 7px;
        height: 7px;
        background: #60a5fa;
        border-radius: 50%;
        margin-left: auto;
        flex-shrink: 0
    }

    /* Dropdown children */
    .nav-dropdown-body {
        display: none;
        background: rgba(0, 0, 0, .18)
    }

    .nav-dropdown-body.open {
        display: block
    }

    .nav-dropdown-body a {
        padding: 9px 20px 9px 46px;
        font-size: 13px;
        color: #7a95b0
    }

    .nav-dropdown-body a:hover,
    .nav-dropdown-body a.active {
        color: #e0edff;
        background: rgba(96, 165, 250, .12);
        border-left-color: #60a5fa
    }

    /* Logout btn */
    .nav-logout {
        display: flex;
        align-items: center;
        gap: 10px;
        padding: 12px 20px;
        color: #6a7e94;
        font-size: 13.5px;
        cursor: pointer;
        background: none;
        border: none;
        width: 100%;
        text-align: left;
        border-top: 1px solid rgba(255, 255, 255, .05);
        margin-top: 4px;
        transition: .15s
    }

    .nav-logout:hover {
        color: #f87171;
        background: rgba(239, 68, 68, .08)
    }
</style>

<div class="sidebar">
    <div class="brand" style="
    display: flex;
    justify-content: space-between;
">
        <div class="brand-content">
            @if ($siteLogo)
                <img src="{{ cdn($siteLogo) }}" alt="Logo"
                     style="max-height:30px;max-width:140px;object-fit:contain;border-radius:4px">
            @else
                <i class="fas fa-shield-alt"></i>
                <span>Admin Panel</span>
            @endif
        </div>

        <button class="close-sidebar" id="closeSidebar" aria-label="Đóng menu">
            <i class="fas fa-times"></i>
        </button>
    </div>
    <nav>
        {{-- ── TỔNG QUAN ── --}}
        <div class="nav-group">Tổng quan</div>
        <a href="{{ route('admin.dashboard') }}" class="{{ request()->routeIs('admin.dashboard') ? 'active' : '' }}">
            <i class="fas fa-tachometer-alt"></i> Trang cá nhân
        </a>
        <button class="nav-dropdown-btn {{ $inShop ? 'active-group open' : '' }}" onclick="toggleDD(this)">
            <i class="dd-icon fas fa-shopping-bag"></i> Gói ComBo
            <i class="fas fa-chevron-down dd-arrow"></i>
        </button>
        <div class="nav-dropdown-body {{ $inShop ? 'open' : '' }}">
            @foreach ($sidebarCoCheHoanPhis->whereNull('parent_id') as $parent)
                {{-- CHA --}}
                <a href="#"
                   class="{{ request('co_che') == $parent->id ? 'active' : '' }}">
                    <i class="fas fa-layer-group" style="margin-right:6px;font-size:12px"></i>
                    {{ $parent->name }}
                    <span style="margin-left:auto;opacity:.65;font-size:11px">
                        ({{ $parent->products_count }})
                    </span>
                </a>

                {{-- CON --}}
                @foreach ($sidebarCoCheHoanPhis->where('parent_id', $parent->id) as $child)
                    <a href="{{ $sidebarCoCheShopUrl($child) }}"
                       class="{{ request('co_che') == $child->id ? 'active' : '' }}" style="padding-left:58px">
                        <i class="fas fa-percentage" style="margin-right:6px;font-size:12px"></i>
                        {{ $child->name }}
                        <span style="margin-left:auto;opacity:.65;font-size:11px">
                            ({{ $child->products_count }})
                        </span>
                    </a>
                @endforeach
            @endforeach

        </div>

        {{-- ── MẠNG LƯỚI ── --}}
        <div class="nav-group"></div>

        {{-- Dropdown: Thành viên --}}
        <button class="nav-dropdown-btn {{ $inMember ? 'active-group open' : '' }}" onclick="toggleDD(this)">
            <i class="dd-icon fas fa-users"></i> Thành viên
            <i class="fas fa-chevron-down dd-arrow"></i>
        </button>
        <div class="nav-dropdown-body {{ $inMember ? 'open' : '' }}">



            @if ($isAdmin)
                <a href="{{ route('admin.my-members.index') }}"
                   class="{{ request()->routeIs('admin.my-members.*') ? 'active' : '' }}">
                    <i class="fas fa-sitemap" style="margin-right:6px;font-size:12px"></i> Cây thành viên
                </a>
            @endif

            <a href="{{ route('admin.my-referrals') }}"
               class="{{ request()->routeIs('admin.my-referrals') ? 'active' : '' }}">
                <i class="fas fa-user-friends" style="margin-right:6px;font-size:12px"></i> Thành viên trực tiếp
            </a>


            @if ($isAdmin)
                {{-- <a href="{{ route('admin.my-income') }}"
                    class="{{ request()->routeIs('admin.my-income') ? 'active' : '' }}"> --}}
                <a href="{{ route('admin.my-tree') }}"
                   class="{{ request()->routeIs('admin.my-tree') ? 'active' : '' }}">
                    <i class="fas fa-project-diagram" style="margin-right:6px;font-size:12px"></i> Sơ đồ tổ chức
                </a>

                @if ($isAdmin || $can('finance.transfer'))
                    <a href="{{ route('admin.chuyen-diem') }}"
                       class="{{ request()->routeIs('admin.chuyen-diem*') ? 'active' : '' }}">
                        <i class="fas fa-exchange-alt" style="margin-right:6px;font-size:12px"></i> Chuyển điểm
                    </a>
                @endif


            @endif

        </div>

        {{-- ── TÀI CHÍNH ── --}}
        <div class="nav-group">Hoạt động</div>

        @if ($isAdmin)
            <a href="{{ route('admin.my-income') }}"
               class="{{ request()->routeIs('admin.my-income') ? 'active' : '' }}">
                <i class="fas fa-chart-bar"></i> Thu nhập
            </a>
        @endif




        {{-- Dropdown: Ví & Giao dịch --}}
        <button class="nav-dropdown-btn {{ $inFinance ? 'active-group open' : '' }}" onclick="toggleDD(this)">
            <i class="dd-icon fas fa-wallet"></i> Giao dịch
            <i class="fas fa-chevron-down dd-arrow"></i>
        </button>
        <div class="nav-dropdown-body {{ $inFinance ? 'open' : '' }}">
            <a href="{{ route('admin.my-transactions') }}"
               class="{{ request()->routeIs('admin.my-transactions') ? 'active' : '' }}">
                <i class="fas fa-history" style="margin-right:6px;font-size:12px"></i> Lịch sử hoạt động
            </a>


            @if ($isAdmin || $can('finance.transfer'))
                <a href="{{ route('admin.chuyen-diem') }}"
                   class="{{ request()->routeIs('admin.chuyen-diem*') ? 'active' : '' }}">
                    <i class="fas fa-exchange-alt" style="margin-right:6px;font-size:12px"></i> Chuyển điểm
                </a>
            @endif


            {{--            <a href="{{ route('admin.nap-tien') }}" class="{{ request()->routeIs('admin.nap-tien') ? 'active' : '' }}"> --}}
            {{--                <i class="fas fa-plus-circle" style="margin-right:6px;font-size:12px"></i> Nạp tiền --}}
            {{--            </a> --}}
            {{-- <a href="{{ route('admin.my-withdrawals') }}"
                class="{{ request()->routeIs('admin.my-withdrawals') ? 'active' : '' }}"> --}}
            {{-- <i class="fas fa-hand-holding-usd" style="margin-right:6px;font-size:12px"></i> Rút tiền --}}
            {{-- </a> --}}
        </div>

        {{-- Đơn hàng của tôi (user thường) --}}
        @if (!$isAdmin && !$can('order.view'))
            <a href="{{ route('admin.orders.index') }}?my=1"
               class="{{ request()->routeIs('admin.orders.*') ? 'active' : '' }}">
                <i class="fas fa-shopping-cart"></i> Đơn hàng của tôi
            </a>
        @endif
        <a href="{{ route('admin.profile.info') }}"
           class="{{ request()->routeIs('profile.info.*') ? 'active' : '' }}">
            <i class="fas fa-solid fa-user"></i> Thông tin cá nhân
        </a>
        {{-- ── QUẢN TRỊ (admin only) ── --}}
        @if (
            $isAdmin ||
                $can('order.view') ||
                $can('user.view') ||
                $can('finance.view') ||
                $can('content.manage') ||
                $can('rbac.manage') ||
                $can('setting.view'))
            <div class="nav-group">Quản trị</div>
        @endif

        @if ($can('order.view') || $can('user.view') || $can('htx.manage') || $can('finance.view'))
            <button class="nav-dropdown-btn {{ $inAdminFin ? 'active-group open' : '' }}" onclick="toggleDD(this)">
                <i class="dd-icon fas fa-cogs"></i> Quản lý
                <i class="fas fa-chevron-down dd-arrow"></i>
            </button>
            <div class="nav-dropdown-body {{ $inAdminFin ? 'open' : '' }}">
                @if ($can('order.view'))
                    <a href="{{ route('admin.orders.index') }}"
                       class="{{ request()->routeIs('admin.orders.*') ? 'active' : '' }}">
                        <i class="fas fa-shopping-cart" style="margin-right:6px;font-size:12px"></i> Đơn hàng
                    </a>
                @endif
                @if ($can('user.view'))
                    <a href="{{ route('admin.users.index') }}"
                       class="{{ request()->routeIs('admin.users.*') ? 'active' : '' }}">
                        <i class="fas fa-users" style="margin-right:6px;font-size:12px"></i> Thành viên
                    </a>
                @endif
                @if ($can('htx.manage'))
                    <a href="{{ route('admin.dang-ky-htxs.index') }}"
                       class="{{ request()->routeIs('admin.dang-ky-htxs.*') ? 'active' : '' }}">
                        <i class="fas fa-clipboard-list" style="margin-right:6px;font-size:12px"></i> Đăng ký HTX
                    </a>
                @endif
                @if ($can('finance.view'))
                    <a href="{{ route('admin.lich-su-giao-dich.index') }}"
                       class="{{ request()->routeIs('admin.lich-su-giao-dich.*') ? 'active' : '' }}">
                        <i class="fas fa-exchange-alt" style="margin-right:6px;font-size:12px"></i> Lịch sử GD
                    </a>
                    <a href="{{ route('admin.yeu-cau-rut-tien.index') }}"
                       class="{{ request()->routeIs('admin.yeu-cau-rut-tien.*') ? 'active' : '' }}">
                        <i class="fas fa-money-bill-wave" style="margin-right:6px;font-size:12px"></i> Yêu cầu rút
                        tiền
                    </a>
                @endif
            </div>
        @endif

        @if ($can('category.view') || $can('product.view') || $can('post.view'))
            <button class="nav-dropdown-btn {{ $inContent ? 'active-group open' : '' }}" onclick="toggleDD(this)">
                <i class="dd-icon fas fa-folder"></i> Nội dung
                <i class="fas fa-chevron-down dd-arrow"></i>
            </button>
            <div class="nav-dropdown-body {{ $inContent ? 'open' : '' }}">
                @if ($can('category.view'))
                    <a href="{{ route('admin.categories.index') }}"
                       class="{{ request()->routeIs('admin.categories.*') ? 'active' : '' }}">
                        <i class="fas fa-folder" style="margin-right:6px;font-size:12px"></i> Danh mục SP
                    </a>
                @endif
                @if ($can('product.view'))
                    <a href="{{ route('admin.products.index') }}"
                       class="{{ request()->routeIs('admin.products.*') ? 'active' : '' }}">
                        <i class="fas fa-box" style="margin-right:6px;font-size:12px"></i> Sản phẩm
                    </a>
                    @if ($isAdmin)
                        <a href="{{ route('admin.co-che-hoan-phi.index') }}"
                           class="{{ request()->routeIs('admin.co-che-hoan-phi.*') ? 'active' : '' }}">
                            <i class="fas fa-percentage" style="margin-right:6px;font-size:12px"></i> Cơ chế hoàn phí
                        </a>
                    @endif
                    <a href="{{ route('admin.tags.index') }}"
                       class="{{ request()->routeIs('admin.tags.*') ? 'active' : '' }}">
                        <i class="fas fa-tags" style="margin-right:6px;font-size:12px"></i> Tags
                    </a>

                    <a href="{{ route('admin.promotions.index') }}"
                       class="{{ request()->routeIs('admin.promotions.*') ? 'active' : '' }}">
                        <i class="fas fa-gift" style="margin-right:6px;font-size:12px"></i> Khuyến mãi
                    </a>

                @endif
                @if ($can('post.view'))
                    <a href="{{ route('admin.category-posts.index') }}"
                       class="{{ request()->routeIs('admin.category-posts.*') ? 'active' : '' }}">
                        <i class="fas fa-folder-open" style="margin-right:6px;font-size:12px"></i> Danh mục bài viết
                    </a>
                    <a href="{{ route('admin.posts.index') }}"
                       class="{{ request()->routeIs('admin.posts.*') ? 'active' : '' }}">
                        <i class="fas fa-newspaper" style="margin-right:6px;font-size:12px"></i> Bài viết
                    </a>
                @endif
            </div>
        @endif

        @if ($can('content.manage'))
            <button class="nav-dropdown-btn {{ $inIntro ? 'active-group open' : '' }}" onclick="toggleDD(this)">
                <i class="dd-icon fas fa-info-circle"></i> Giới thiệu
                <i class="fas fa-chevron-down dd-arrow"></i>
            </button>
            <div class="nav-dropdown-body {{ $inIntro ? 'open' : '' }}">
                <a href="{{ route('admin.gioi-thieus.index') }}"
                   class="{{ request()->routeIs('admin.gioi-thieus.*') ? 'active' : '' }}">
                    <i class="fas fa-info-circle" style="margin-right:6px;font-size:12px"></i> Giới thiệu
                </a>
                <a href="{{ route('admin.chinh-sachs.index') }}"
                   class="{{ request()->routeIs('admin.chinh-sachs.*') ? 'active' : '' }}">
                    <i class="fas fa-file-alt" style="margin-right:6px;font-size:12px"></i> Chính sách
                </a>
                <a href="{{ route('admin.leaders.index') }}"
                   class="{{ request()->routeIs('admin.leaders.*') ? 'active' : '' }}">
                    <i class="fas fa-user-tie" style="margin-right:6px;font-size:12px"></i> Lãnh đạo
                </a>
                <a href="{{ route('admin.thu-viens.index') }}"
                   class="{{ request()->routeIs('admin.thu-viens.*') ? 'active' : '' }}">
                    <i class="fas fa-image" style="margin-right:6px;font-size:12px"></i> Thư viện
                </a>
                <a href="{{ route('admin.widgets.index') }}"
                   class="{{ request()->routeIs('admin.widgets.*') ? 'active' : '' }}">
                    <i class="fas fa-puzzle-piece" style="margin-right:6px;font-size:12px"></i> Widgets
                </a>
            </div>
        @endif

        @if ($can('content.manage'))
            <a href="{{ route('admin.thong-bao.index') }}"
               class="{{ request()->routeIs('admin.thong-bao.*') ? 'active' : '' }}">
                <i class="fas fa-bell"></i> Thông báo
            </a>
        @endif

        @if ($can('rbac.manage') || $can('setting.view'))
            <button
                    class="nav-dropdown-btn {{ request()->routeIs('admin.roles.*', 'admin.permissions.*', 'admin.settings.*') ? 'active-group open' : '' }}"
                    onclick="toggleDD(this)">
                <i class="dd-icon fas fa-shield-alt"></i> Hệ thống
                <i class="fas fa-chevron-down dd-arrow"></i>
            </button>
            <div
                    class="nav-dropdown-body {{ request()->routeIs('admin.roles.*', 'admin.permissions.*', 'admin.settings.*') ? 'open' : '' }}">
                @if ($can('rbac.manage'))
                    <a href="{{ route('admin.roles.index') }}"
                       class="{{ request()->routeIs('admin.roles.*') ? 'active' : '' }}">
                        <i class="fas fa-key" style="margin-right:6px;font-size:12px"></i> Roles
                    </a>
                    <a href="{{ route('admin.permissions.index') }}"
                       class="{{ request()->routeIs('admin.permissions.*') ? 'active' : '' }}">
                        <i class="fas fa-lock" style="margin-right:6px;font-size:12px"></i> Permissions
                    </a>
                @endif
                @if ($can('setting.view'))
                    <a href="{{ route('admin.settings.index') }}"
                       class="{{ request()->routeIs('admin.settings.*') ? 'active' : '' }}">
                        <i class="fas fa-cog" style="margin-right:6px;font-size:12px"></i> Cài đặt
                    </a>
                @endif
            </div>
        @endif

        {{-- Đăng xuất --}}
        <form method="POST" action="{{ route('admin.logout') }}" style="margin:0">
            @csrf
            <button type="submit" class="nav-logout">
                <i class="fas fa-sign-out-alt"></i> Đăng xuất
            </button>
        </form>

        @stack('sidebar-items')
    </nav>
</div>

<script>
    function toggleDD(btn) {
        const body = btn.nextElementSibling;
        const isOpen = body.classList.contains('open');
        // Đóng tất cả dropdown khác
        document.querySelectorAll('.nav-dropdown-body.open').forEach(el => {
            el.classList.remove('open');
            el.previousElementSibling.classList.remove('open');
        });
        // Toggle cái được click
        if (!isOpen) {
            body.classList.add('open');
            btn.classList.add('open');
        }
    }
</script>
