<!doctype html>
<html lang="vi">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>API Docs</title>
    <style>
        :root {
            --bg: #f4f7fb;
            --panel: #ffffff;
            --line: #dde5ef;
            --text: #1d2939;
            --muted: #667085;
            --blue: #2563eb;
            --green: #16a34a;
            --orange: #ea580c;
            --red: #dc2626;
            --code: #0f172a;
        }

        * { box-sizing: border-box; }
        body {
            margin: 0;
            font-family: Arial, Helvetica, sans-serif;
            background: var(--bg);
            color: var(--text);
        }

        .layout {
            display: grid;
            grid-template-columns: 280px minmax(0, 1fr);
            min-height: 100vh;
        }

        .sidebar {
            background: #101828;
            color: #fff;
            padding: 22px 16px;
            position: sticky;
            top: 0;
            height: 100vh;
            overflow-y: auto;
        }

        .brand {
            font-size: 22px;
            font-weight: 800;
            margin-bottom: 6px;
        }

        .base-url {
            color: #cbd5e1;
            font-size: 12px;
            word-break: break-all;
            margin-bottom: 20px;
        }

        .token-box {
            padding: 12px;
            background: rgba(255,255,255,.08);
            border: 1px solid rgba(255,255,255,.12);
            border-radius: 8px;
            margin-bottom: 18px;
        }

        .token-box label {
            display: block;
            font-size: 12px;
            color: #cbd5e1;
            margin-bottom: 8px;
        }

        input, textarea {
            width: 100%;
            border: 1px solid var(--line);
            border-radius: 6px;
            padding: 9px 10px;
            font: inherit;
            background: #fff;
        }

        .token-box input {
            background: #fff;
            border: none;
            font-size: 12px;
        }

        .nav-group {
            border-top: 1px solid rgba(255,255,255,.12);
            padding-top: 12px;
            margin-top: 12px;
        }

        .nav-title {
            display: flex;
            justify-content: space-between;
            align-items: center;
            width: 100%;
            border: none;
            background: transparent;
            color: #fff;
            padding: 9px 4px;
            font-weight: 700;
            cursor: pointer;
            text-align: left;
        }

        .nav-items { display: none; }
        .nav-group.active .nav-items { display: block; }

        .nav-item {
            width: 100%;
            border: none;
            background: transparent;
            color: #dbe4f0;
            padding: 8px 4px 8px 12px;
            text-align: left;
            cursor: pointer;
            border-radius: 6px;
            font-size: 13px;
        }

        .nav-item:hover,
        .nav-item.active {
            background: rgba(255,255,255,.12);
            color: #fff;
        }

        .main {
            padding: 28px;
        }

        .topbar {
            display: flex;
            gap: 16px;
            align-items: center;
            justify-content: space-between;
            margin-bottom: 18px;
        }

        .topbar h1 {
            margin: 0;
            font-size: 28px;
        }

        .search {
            max-width: 360px;
        }

        .endpoint {
            background: var(--panel);
            border: 1px solid var(--line);
            border-radius: 8px;
            overflow: hidden;
            margin-bottom: 16px;
            box-shadow: 0 6px 20px rgba(16,24,40,.05);
        }

        .endpoint-head {
            display: flex;
            align-items: center;
            gap: 10px;
            padding: 14px 16px;
            border-bottom: 1px solid var(--line);
        }

        .method {
            min-width: 64px;
            text-align: center;
            color: #fff;
            border-radius: 5px;
            padding: 6px 8px;
            font-size: 12px;
            font-weight: 800;
        }

        .GET { background: var(--green); }
        .POST { background: var(--blue); }
        .PUT { background: var(--orange); }
        .DELETE { background: var(--red); }

        .path {
            font-family: Consolas, Monaco, monospace;
            font-weight: 800;
            font-size: 16px;
        }

        .auth {
            margin-left: auto;
            color: var(--muted);
            font-size: 12px;
        }

        .endpoint-body {
            padding: 16px;
            display: grid;
            grid-template-columns: minmax(0, 1fr) minmax(0, 1fr);
            gap: 16px;
        }

        .section {
            border: 1px solid var(--line);
            border-radius: 8px;
            padding: 14px;
            background: #fbfdff;
        }

        .section h3 {
            margin: 0 0 12px;
            font-size: 15px;
        }

        .field {
            display: grid;
            grid-template-columns: 150px minmax(0, 1fr);
            gap: 10px;
            align-items: center;
            margin-bottom: 10px;
        }

        .field label {
            color: var(--muted);
            font-size: 13px;
        }

        .hint {
            color: var(--muted);
            font-size: 12px;
            margin-top: -4px;
            margin-bottom: 10px;
        }

        textarea {
            min-height: 220px;
            font-family: Consolas, Monaco, monospace;
            font-size: 13px;
            resize: vertical;
        }

        .actions {
            display: flex;
            gap: 10px;
            margin-top: 12px;
        }

        button.run,
        button.copy {
            border: none;
            border-radius: 6px;
            padding: 10px 14px;
            cursor: pointer;
            font-weight: 700;
        }

        button.run {
            background: var(--blue);
            color: #fff;
        }

        button.copy {
            background: #e8eef8;
            color: var(--text);
        }

        pre {
            margin: 0;
            min-height: 300px;
            max-height: 560px;
            overflow: auto;
            padding: 14px;
            background: var(--code);
            color: #e2e8f0;
            border-radius: 8px;
            font-family: Consolas, Monaco, monospace;
            font-size: 13px;
            line-height: 1.55;
            white-space: pre-wrap;
            word-break: break-word;
        }

        .empty {
            padding: 30px;
            text-align: center;
            color: var(--muted);
            background: var(--panel);
            border: 1px dashed var(--line);
            border-radius: 8px;
        }

        @media (max-width: 900px) {
            .layout { grid-template-columns: 1fr; }
            .sidebar {
                position: relative;
                height: auto;
            }
            .endpoint-body { grid-template-columns: 1fr; }
            .topbar { display: block; }
            .search { max-width: none; margin-top: 12px; }
            .field { grid-template-columns: 1fr; }
        }
    </style>
</head>
<body>
<div class="layout">
    <aside class="sidebar">
        <div class="brand">API Docs</div>
        <div class="base-url" id="baseUrl"></div>

        <div class="token-box">
            <label for="tokenInput">Bearer token</label>
            <input id="tokenInput" placeholder="Dan access_token vao day">
        </div>

        <div id="nav"></div>
    </aside>

    <main class="main">
        <div class="topbar">
            <div>
                <h1>Kinh Doanh Binh An API</h1>
                <div class="hint">Chon nhom ben trai, bam endpoint de xem request va chay thu response that.</div>
            </div>
            <input class="search" id="searchInput" placeholder="Tim API theo ten, path, method">
        </div>
        <p>Chú ý khi mà đẩy code sang serve chính phải check lại xem cache giữa đt và serve đã đt đã đúng chưa  </p>

        <div id="content"></div>
    </main>



</div>

<script>
    const baseUrl = `${window.location.origin}/api`;
    document.getElementById('baseUrl').textContent = baseUrl;

    const groups = [
        {
            name: 'Auth',
            endpoints: [
                {
                    name: 'Dang nhap',
                    method: 'POST',
                    path: '/login',
                    auth: false,
                    body: {
                        login: 'email@example.com',
                        password: '123456',
                        device_name: 'iPhone 15',
                        fcm: 'FCM_REGISTRATION_TOKEN',
                        vi_tri: 'cong_ty'
                    },
                    note: 'fcm + vi_tri luu vao bang fcm_user (moi thiet bi 1 dong). vi_tri: cong_ty = nhan thong bao mua hang thanh cong, hop_tac_xa = nhan thong bao hoan phi. Bo trong vi_tri thi lay theo site (VI_TRI_WEB). Muon nhan ca 2 loai tren cung 1 may thi goi login voi tung vi_tri, hoac moi app gui vi_tri cua minh.'
                },
                {
                    name: 'Dang ky',
                    method: 'POST',
                    path: '/register',
                    auth: false,
                    body: {
                        name: 'Nguyen Van A',
                        email: 'email@example.com',
                        tel: '0987654321',
                        password: '123456',
                        password_confirmation: '123456',
                        invite_by: '0986112730',
                        pin: '123456',
                        address: 'Ha Noi',
                        province_id: '01',
                        district_id: '001',
                        ward_id: '00001',
                        cccd: '012345678901',
                        tong_ngan_hang_id: 1,
                        ngan_hang_chi_nhanh_id: 1,
                        stk: '123456789',
                        chu_tk: 'NGUYEN VAN A',
                        bank_area_id: 1,
                        device_name: 'android'
                    }
                },
                {
                    name: 'Quen mat khau',
                    method: 'POST',
                    path: '/forgot-password',
                    auth: false,
                    body: {
                        email: 'email@example.com'
                    }
                },
                {
                    name: 'Quen ma PIN',
                    method: 'POST',
                    path: '/forgot-pin',
                    auth: false,
                    body: {
                        email: 'email@example.com'
                    }
                },
                {
                    name: 'Dang xuat',
                    method: 'POST',
                    path: '/logout',
                    auth: true,
                    body: {
                        fcm: 'FCM_REGISTRATION_TOKEN',
                        vi_tri: ''
                    },
                    note: 'Gui fcm de may nay khong nhan thong bao nua. Bo trong vi_tri thi xoa token o moi vi_tri.'
                },
                {
                    name: 'Dong tai khoan',
                    method: 'POST',
                    path: '/account/close',
                    auth: true,
                    body: {},
                    note: 'Chuyen active = 0 (khong xoa du lieu), dang xuat moi thiet bi va xoa FCM. Admin co the bat lai tai khoan.'
                }
            ]
        },
        {
            name: 'Du lieu dang ky',
            endpoints: [
                {
                    name: 'Option form dang ky',
                    method: 'GET',
                    path: '/register-options',
                    auth: false,
                    query: {
                        province_id: '01',
                        district_id: '001',
                        bank_area_id: 1,
                        tong_ngan_hang_id: 1
                    }
                },
                {
                    name: 'API tinh / thanh pho',
                    method: 'GET',
                    path: '/provinces',
                    auth: false,
                    query: {}
                },
                {
                    name: 'API quan / huyen',
                    method: 'GET',
                    path: '/districts',
                    auth: false,
                    query: {
                        province_id: '01'
                    }
                },
                {
                    name: 'API phuong / xa',
                    method: 'GET',
                    path: '/wards',
                    auth: false,
                    query: {
                        district_id: '001'
                    }
                },
                {
                    name: 'API khu vuc ngan hang',
                    method: 'GET',
                    path: '/bank-areas',
                    auth: false,
                    query: {}
                },
                {
                    name: 'API ngan hang',
                    method: 'GET',
                    path: '/banks',
                    auth: false,
                    query: {}
                },
                {
                    name: 'API chi nhanh ngan hang',
                    method: 'GET',
                    path: '/bank-branches',
                    auth: false,
                    query: {
                        bank_area_id: 1,
                        tong_ngan_hang_id: 1
                    }
                }
            ]
        },
        {
            name: 'Ma PIN',
            endpoints: [
                {
                    name: 'Kiem tra PIN',
                    method: 'POST',
                    path: '/pin/check',
                    auth: true,
                    body: {
                        pin: '123456'
                    }
                },
                {
                    name: 'Cap nhat PIN',
                    method: 'POST',
                    path: '/pin/update',
                    auth: true,
                    body: {
                        current_pin: '123456',
                        pin: '654321',
                        pin_confirmation: '654321'
                    }
                }
            ]
        },
        {
            name: 'Thong bao',
            endpoints: [
                {
                    name: 'Danh sach thong bao',
                    method: 'GET',
                    path: '/notifications',
                    auth: true,
                    query: {
                        read: '',
                        page: 1
                    },
                    response: {
                        success: true,
                        message: 'Thanh cong',
                        data: [
                            {
                                id: 110,
                                title: 'Khuyen mai thang 10',
                                excerpt: 'Xin chao moi nguoi, chuong trinh khuyen mai...',
                                content: '<p>Xin chao <strong>moi nguoi</strong></p><p><img src="https://cdn.../anh.jpg"></p>',
                                image: 'https://cdn.../notifications/anh-dai-dien.jpg',
                                target_type: 'all',
                                target_id: 0,
                                is_broadcast: true,
                                read: false,
                                read_at: null,
                                order_id: null,
                                vi_tri: 'tat_ca',
                                created_at: '2026-10-20T03:15:00.000000Z',
                                updated_at: '2026-10-20T03:15:00.000000Z'
                            }
                        ],
                        meta: {
                            unread_count: 3,
                            pagination: { current_page: 1, per_page: 20, total: 45, last_page: 3, from: 1, to: 20 }
                        },
                        links: { first: '...?page=1', last: '...?page=3', prev: null, next: '...?page=2' }
                    },
                    note: 'Chi tra thong bao cua website dang goi API, theo VI_TRI_WEB cua site: cong_ty -> vi_tri=cong_ty, htx -> vi_tri=hop_tac_xa. Thong bao tao o site nao thi mang vi_tri cua site do. Kem them thong bao chung admin gui cho tat ca (target_type=all): vi_tri=cong_ty / hop_tac_xa / tat_ca (ca 2 app). Push cua thong bao chung co data.type=broadcast, data.notification_id, am thanh mac dinh. /unread-count va /read cung loc theo website. Phan trang 20 thong bao/trang, moi nhat truoc. read=0 chi lay chua doc, read=1 chi lay da doc. content la HTML (thong bao chung soan bang CKEditor, co the co anh), excerpt la doan chu thuong de hien o danh sach.'
                },
                {
                    name: 'Chi tiet thong bao',
                    method: 'GET',
                    path: '/notifications/{notification}',
                    auth: true,
                    pathParams: {
                        notification: 1
                    },
                    query: {},
                    note: 'Mo xem la tu danh dau da doc (khong can goi /read nua). Thong bao khong thuoc user hoac cua website khac tra 404. meta.unread_count la so chua doc sau khi mo.',
                    response: {
                        success: true,
                        message: 'Thanh cong',
                        data: {
                            id: 110,
                            title: 'Khuyen mai thang 10',
                            excerpt: 'Xin chao moi nguoi, chuong trinh khuyen mai...',
                            content: '<p>Xin chao <strong>moi nguoi</strong></p><p><img src="https://cdn.../anh.jpg"></p>',
                            image: 'https://cdn.../notifications/anh-dai-dien.jpg',
                            is_broadcast: true,
                            read: true,
                            read_at: '2026-10-20T08:01:12.000000Z',
                            vi_tri: 'tat_ca',
                            created_at: '2026-10-20T03:15:00.000000Z'
                        },
                        meta: { unread_count: 2 },
                        links: {}
                    }
                },
                {
                    name: 'So thong bao chua doc',
                    method: 'GET',
                    path: '/notifications/unread-count',
                    auth: true,
                    query: {}
                },
                {
                    name: 'Danh dau da doc',
                    method: 'POST',
                    path: '/notifications/{notification}/read',
                    auth: true,
                    pathParams: {
                        notification: 1
                    },
                    body: {}
                }
            ]
        },
        {
            name: 'Thong tin nguoi dung',
            endpoints: [
                {
                    name: 'User dang dang nhap (raw)',
                    method: 'GET',
                    path: '/user',
                    auth: true,
                    query: {},
                    note: 'Route mac dinh cua Laravel, tra ve nguyen ban ghi user. App nen dung /profile.'
                },
                {
                    name: 'Lay thong tin nguoi dung',
                    method: 'GET',
                    path: '/profile',
                    auth: true,
                    query: {}
                },
                {
                    name: 'Cap nhat thong tin nguoi dung',
                    method: 'POST',
                    path: '/profile/update',
                    auth: true,
                    body: {
                        name: 'Nguyen Van A',
                        email: 'email@example.com',
                        tel: '0987654321',
                        province_id: '01',
                        district_id: '001',
                        ward_id: '00001',
                        address: 'So nha, ten duong',
                        cccd: '012345678901',
                        bank_area_id: 1,
                        tong_ngan_hang_id: 1,
                        ngan_hang_chi_nhanh_id: 1,
                        stk: '123456789',
                        chu_tk: 'NGUYEN VAN A'
                    },
                    note: 'Neu upload anh dai dien, goi endpoint nay bang multipart/form-data va gui field image la file jpg/jpeg/png/webp <= 2MB. Form test hien tai chi gui JSON.'
                }
            ]
        },
        {
            name: 'So do to chuc',
            endpoints: [
                {
                    name: 'Lay cay thanh vien',
                    method: 'GET',
                    path: '/organization-chart',
                    auth: true,
                    query: {
                        max_depth: 6
                    }
                },
                {
                    name: 'Khach hang truc tiep chua mua hang',
                    method: 'GET',
                    path: '/khach-hang-truc-tiep',
                    auth: true,
                    query: {
                        search: '',
                        page: 1
                    },
                    note: 'Nguoi dang ky bang ma gioi thieu cua user dang dang nhap (invite_by = id) nhung chua mua don nao. data.total = tong so khach hang nay. Moi nhat truoc, 20 nguoi/trang. search tim theo ten hoac so dien thoai.',
                    response: {
                        success: true,
                        message: 'Thanh cong',
                        data: {
                            total: 2,
                            customers: [
                                { id: 120, name: 'Nguyen Van B', tel: '0987000111', created_at: '2026-10-16 09:12:00' },
                                { id: 118, name: 'Tran Thi C', tel: '0912000222', created_at: '2026-10-12 20:30:45' }
                            ]
                        },
                        meta: {
                            pagination: { current_page: 1, per_page: 20, total: 2, last_page: 1, from: 1, to: 2 }
                        },
                        links: { first: '...?page=1', last: '...?page=1', prev: null, next: null }
                    }
                }
            ]
        },
        {
            name: 'Don hang',
            endpoints: [
                {
                    name: 'Danh sach don hang',
                    method: 'GET',
                    path: '/orders',
                    auth: true,
                    query: {
                        search: '',
                        status: '',
                        date_from: '',
                        date_to: '',
                        product_id: '',
                        device: '',
                        page: 1,
                        per_page: 20,
                        all: 0
                    },
                    note: 'Sap xep moi nhat truoc (theo created_at). Moi don co them device va thoi_gian_mua (null voi don dat tren website).'
                },
                {
                    name: 'Don hang theo thoi gian mua (cu nhat -> moi nhat)',
                    method: 'GET',
                    path: '/orders/theo-thoi-gian-mua',
                    auth: true,
                    query: {
                        search: '',
                        status: '',
                        date_from: '',
                        date_to: '',
                        product_id: '',
                        device: '',
                        page: 1,
                        per_page: 20,
                        all: 0
                    },
                    note: 'Chi lay don dat qua app (co thoi_gian_mua), sap xep tang dan theo thoi_gian_mua (trung thi theo id). date_from/date_to loc theo thoi_gian_mua (Y-m-d). device loc gan dung. all=1 chi co tac dung voi admin / nguoi co quyen order.view.',
                    response: {
                        success: true,
                        message: 'Thanh cong',
                        data: [
                            {
                                id: 101,
                                ma_don_hang: 'TCCOMBO1A1B2C3',
                                name: 'Mua tự chọn: Combo 1',
                                status: 'completed',
                                total_price: 754000,
                                shipping_price: 54000,
                                device: 'iPhone 15 - iOS 18.1',
                                thoi_gian_mua: '2026-10-01 08:15:30',
                                created_at: '2026-10-01 08:15:32'
                            },
                            {
                                id: 102,
                                ma_don_hang: 'DH4D5E6F',
                                name: 'Combo 2',
                                status: 'completed',
                                total_price: 1500000,
                                shipping_price: 0,
                                device: 'Samsung S24 - Android 15',
                                thoi_gian_mua: '2026-10-02 19:40:00',
                                created_at: '2026-10-02 19:40:01'
                            }
                        ],
                        meta: { current_page: 1, from: 1, last_page: 1, per_page: 20, to: 2, total: 2 },
                        links: { first: '...', last: '...', prev: null, next: null }
                    }
                },
                {
                    name: 'Lay thong tin mua hang',
                    method: 'GET',
                    path: '/orders/buy-info/{product}',
                    auth: true,
                    pathParams: {
                        product: 1
                    },
                    query: {
                        mode: 'custom',
                        qty: 1
                    }
                },
                {
                    name: 'Xem truoc don mua tu chon',
                    method: 'POST',
                    path: '/orders/preview-tu-chon',
                    auth: true,
                    body: {
                        product_id: 1,
                        mode: 'custom',
                        items: [
                            {
                                product_id: 2,
                                quantity: 1
                            },
                            {
                                product_id: 3,
                                quantity: 2
                            }
                        ],
                        receive_user: 'Nguyen Van A',
                        receive_phone: '0987654321',
                        province_id: 1,
                        district_id: 1,
                        ward_id: 1,
                        receive_address: 'So nha, ten duong',
                        note: ''
                    }
                },
                {
                    name: 'Mua combo / mua goi',
                    method: 'POST',
                    path: '/orders/mua-combo',
                    auth: true,
                    body: {
                        product_id: 1,
                        qty: 1,
                        receive_user: 'Nguyen Van A',
                        receive_phone: '0987654321',
                        province_id: 1,
                        district_id: 1,
                        ward_id: 1,
                        receive_address: 'So nha, ten duong',
                        note: '',
                        device: 'iPhone 15 - iOS 18.1'
                    },
                    note: 'device: app gui len khi khach bam mua (khong bat buoc). thoi_gian_mua do server tu gan, app KHONG gui. Don dat tren website thi device va thoi_gian_mua deu null. Neu vi mua hang thieu tien, API tra status 402 kem data.requires_topup=true va checkout_url/payment_url de app mo link PayOS.'
                },
                {
                    name: 'Mua tu chon',
                    method: 'POST',
                    path: '/orders/mua-tu-chon',
                    auth: true,
                    body: {
                        product_id: 1,
                        mode: 'custom',
                        items: [
                            {
                                product_id: 2,
                                quantity: 1
                            },
                            {
                                product_id: 3,
                                quantity: 2
                            }
                        ],
                        receive_user: 'Nguyen Van A',
                        receive_phone: '0987654321',
                        province_id: 1,
                        district_id: 1,
                        ward_id: 1,
                        receive_address: 'So nha, ten duong',
                        note: '',
                        device: 'iPhone 15 - iOS 18.1'
                    },
                    note: 'device: app gui len khi khach bam mua (khong bat buoc). thoi_gian_mua do server tu gan, app KHONG gui. Don dat tren website thi device va thoi_gian_mua deu null. Tong tien = tien hang + 54.000d phi ship. Neu vi mua hang thieu tien, API tra status 402 kem data.requires_topup=true va checkout_url/payment_url de app mo link PayOS (so tien nap = phan con thieu, da gom phi ship). Thanh toan xong he thong tu tao don.',
                    response: {
                        success: false,
                        message: 'Số dư không đủ. Cần nạp thêm 254.000đ, hiện có 500.000đ.',
                        data: {
                            requires_topup: true,
                            payment_id: 123,
                            order_code: 123456789,
                            amount: 254000,
                            checkout_url: 'https://pay.payos.vn/web/...',
                            payment_url: 'https://pay.payos.vn/web/...',
                            wallet: 500000,
                            missing_amount: 254000
                        },
                        meta: {},
                        links: {}
                    }
                },
                {
                    name: 'Chi tiet don hang',
                    method: 'GET',
                    path: '/orders/{order}',
                    auth: true,
                    pathParams: {
                        order: 1
                    },
                    query: {}
                }
            ]
        },
        {
            name: 'Gio hang (mua tu chon)',
            endpoints: [
                {
                    name: 'Xem gio hang',
                    method: 'GET',
                    path: '/cart',
                    auth: true,
                    query: {},
                    note: 'Gio gom theo combo (groups). Moi group co checkout { product_id, mode, items } de gui kem thong tin nguoi nhan len /orders/preview-tu-chon hoac /orders/mua-tu-chon. Dat thanh cong thi san pham da mua tu xoa khoi gio. Item co is_available=false (ngung ban/het hang) khong duoc tinh tien.',
                    response: {
                        success: true,
                        message: 'Thanh cong',
                        data: {
                            groups: [
                                {
                                    parent_product: { id: 1, name: 'Combo 1', slug: 'combo-1', price: 0, old_price: 0, price_hoan_tien: 0, quantity: null, in_stock: true, image: null, category: null },
                                    mode: 'custom',
                                    items: [
                                        {
                                            id: 10,
                                            product_id: 2,
                                            quantity: 2,
                                            unit_price: 350000,
                                            line_total: 700000,
                                            is_available: true,
                                            product: { id: 2, name: 'San pham A', slug: 'san-pham-a', price: 350000, old_price: 0, price_hoan_tien: 0, quantity: 50, in_stock: true, image: null, category: { id: 3, name: 'Thuc pham' } }
                                        }
                                    ],
                                    summary: { total_product_price: 700000, shipping_price: 54000, payable_total: 754000 },
                                    checkout: { product_id: 1, mode: 'custom', items: [ { product_id: 2, quantity: 2 } ] }
                                }
                            ],
                            total_quantity: 2
                        },
                        meta: {},
                        links: {}
                    }
                },
                {
                    name: 'Dem so luong trong gio',
                    method: 'GET',
                    path: '/cart/count',
                    auth: true,
                    query: {},
                    response: { success: true, message: 'Thanh cong', data: { count: 2 }, meta: {}, links: {} }
                },
                {
                    name: 'Them san pham vao gio',
                    method: 'POST',
                    path: '/cart/items',
                    auth: true,
                    body: {
                        parent_product_id: 1,
                        product_id: 2,
                        quantity: 1,
                        mode: 'custom'
                    },
                    note: 'Chi nhan san pham thuoc danh sach mua tu chon cua combo: mode=custom la san pham con cua combo, mode=custom_co_che la san pham cung co che hoan phi. San pham khac, het hang hoac vuot ton kho tra 422. Them san pham da co trong gio thi cong don so luong (toi da 99). Thanh cong tra 201 kem toan bo gio (giong GET /cart). Response mau ben duoi la truong hop loi.',
                    response: {
                        success: false,
                        message: 'Sản phẩm này không thuộc danh sách mua tự chọn của combo hoặc đã hết hàng.',
                        data: { groups: [], total_quantity: 0 },
                        meta: {},
                        links: {}
                    }
                },
                {
                    name: 'Cap nhat so luong',
                    method: 'PUT',
                    path: '/cart/items/{item}',
                    auth: true,
                    pathParams: {
                        item: 1
                    },
                    body: {
                        quantity: 2
                    },
                    note: 'Tra ve toan bo gio (giong GET /cart). Item khong thuoc user tra 404.'
                },
                {
                    name: 'Xoa san pham khoi gio',
                    method: 'DELETE',
                    path: '/cart/items/{item}',
                    auth: true,
                    pathParams: {
                        item: 1
                    },
                    query: {}
                },
                {
                    name: 'Xoa gio hang',
                    method: 'DELETE',
                    path: '/cart',
                    auth: true,
                    query: {
                        parent_product_id: ''
                    },
                    note: 'Bo trong parent_product_id de xoa ca gio.'
                }
            ]
        },
        {
            name: 'Lich su hoat dong',
            endpoints: [
                {
                    name: 'Danh sach lich su hoat dong',
                    method: 'GET',
                    path: '/activity-history',
                    auth: true,
                    query: {
                        type: '',
                        search: '',
                        date_from: '',
                        date_to: '',
                        page: 1,
                        per_page: 20
                    }
                }
            ]
        },
        {
            name: 'Danh muc',
            endpoints: [
                {
                    name: 'Danh sach co che hoan phi',
                    method: 'GET',
                    path: '/categories',
                    auth: false,
                    query: {}
                },
                {
                    name: 'San pham theo co che',
                    method: 'GET',
                    path: '/categories/{category}/products',
                    auth: false,
                    pathParams: {
                        category: 1
                    },
                    query: {
                        search: '',
                        q: '',
                        page: 1,
                        per_page: 12
                    }
                },
                {
                    name: 'Danh sach danh muc tin tuc',
                    method: 'GET',
                    path: '/news-categories',
                    auth: false,
                    query: {}
                }
            ]
        },
        {
            name: 'San pham',
            endpoints: [
                {
                    name: 'Danh sach san pham',
                    method: 'GET',
                    path: '/products',
                    auth: false,
                    query: {
                        search: '',
                        q: '',
                        category: '',
                        category_id: '',
                        featured: '',
                        page: 1,
                        per_page: 12
                    }
                },
                {
                    name: 'San pham mua tu chon cung co che',
                    method: 'GET',
                    path: '/products/{product}/san-pham-tu-chon',
                    auth: false,
                    pathParams: {
                        product: 1
                    },
                    query: {
                        search: '',
                        page: 1
                    },
                    note: 'Dung khi bam vao san pham co is_tu_chon=true (co_che_hoan_phi.trang_hoat_dong = tu_chon). Tra san pham dang ban cung co_che_hoan_phi_id, sap xep theo stt, phan trang 20 san pham/trang (?page=). total = tong so san pham, chi tiet trang o meta.pagination va links. San pham khong thuoc co che tu chon tra 422. Them vao gio: POST /cart/items voi parent_product_id = {product}, mode = custom_co_che.',
                    response: {
                        success: true,
                        message: 'Thanh cong',
                        data: {
                            product: { id: 1, name: 'San pham A', mode: 'custom_co_che', is_tu_chon: true },
                            co_che_hoan_phi: { id: 5, name: 'Co che tu chon 40%', parent_id: 10, trang_hoat_dong: 'tu_chon', products_count: null, children: null },
                            mode: 'custom_co_che',
                            total: 45,
                            products: [
                                { id: 1, name: 'San pham A', price: 350000, in_stock: true, mode: 'custom_co_che', is_tu_chon: true },
                                { id: 2, name: 'San pham B', price: 420000, in_stock: true, mode: 'custom_co_che', is_tu_chon: true }
                            ]
                        },
                        meta: {
                            pagination: { current_page: 1, per_page: 20, total: 45, last_page: 3, from: 1, to: 20 }
                        },
                        links: {
                            first: '.../api/products/1/san-pham-tu-chon?page=1',
                            last: '.../api/products/1/san-pham-tu-chon?page=3',
                            prev: null,
                            next: '.../api/products/1/san-pham-tu-chon?page=2'
                        }
                    }
                },
                {
                    name: 'Chi tiet san pham',
                    method: 'GET',
                    path: '/products/{slug}',
                    auth: false,
                    pathParams: {
                        slug: 'combo-1'
                    },
                    query: {}
                }
            ]
        },
        {
            name: 'Tin tuc',
            endpoints: [
                {
                    name: 'Danh sach tin tuc',
                    method: 'GET',
                    path: '/news',
                    auth: false,
                    query: {
                        search: '',
                        category: '',
                        category_id: '',
                        featured: '',
                        page: 1,
                        per_page: 12
                    }
                },
                {
                    name: 'Chi tiet tin tuc',
                    method: 'GET',
                    path: '/news/{slug}',
                    auth: false,
                    pathParams: {
                        slug: 'tin-moi'
                    },
                    query: {}
                },
                {
                    name: 'Gioi thieu ve chung toi',
                    method: 'GET',
                    path: '/gioi-thieu-chung-toi',
                    auth: false,
                    pathParams: {},
                    query: {},
                    note: 'nam_kinh_doanh, khach_hang, san_pham la so lieu admin nhap o Gioi thieu (dang chu, vd 10+), co the null.',
                    response: {
                        success: true,
                        message: 'Thanh cong',
                        data: {
                            name: 'Ve chung toi',
                            image: 'https://cdn.example.com/gioi-thieu/anh.jpg',
                            content: '<p>Noi dung HTML</p>',
                            vi_tri: 've_chung_toi',
                            year: '2014',
                            vi_tri_web: 'cong_ty',
                            nam_kinh_doanh: '10+',
                            khach_hang: '5.000+',
                            san_pham: '200+'
                        },
                        meta: {},
                        links: {}
                    }
                },
                {
                    name: 'Video gioi thieu',
                    method: 'GET',
                    path: '/video-gioi-thieu',
                    auth: false,
                    query: {},
                    note: 'Tra 404 neu chua cau hinh video (setting video_kinhdoanhbinhan).',
                    response: {
                        success: true,
                        message: 'Lấy video giới thiệu thành công.',
                        data: {
                            name: 'video_kinhdoanhbinhan',
                            video: 'https://cdn.example.com/video/gioi-thieu.mp4'
                        }
                    }
                },
                {
                    name: 'Video gioi thieu ve chung toi',
                    method: 'GET',
                    path: '/video-gioi-thieu-ve-chung-toi',
                    auth: false,
                    query: {},
                    note: 'Admin upload o Cai dat > tab chung, setting video_gioi_thieu_ve_chung_toi (moi website 1 video, theo VI_TRI_WEB). Tra 404 neu chua upload.',
                    response: {
                        success: true,
                        message: 'Lấy video giới thiệu về chúng tôi thành công.',
                        data: {
                            name: 'video_gioi_thieu_ve_chung_toi',
                            video: 'https://cdn.example.com/uploads/settings/abc.mp4'
                        }
                    }
                }
            ]
        }
    ];

    let activeEndpoint = groups[0].endpoints[0];

    function endpointId(endpoint) {
        return `${endpoint.method}:${endpoint.path}`;
    }

    function filteredGroups() {
        const keyword = document.getElementById('searchInput').value.trim().toLowerCase();
        if (!keyword) return groups;

        return groups
            .map(group => ({
                ...group,
                endpoints: group.endpoints.filter(endpoint => {
                    return `${group.name} ${endpoint.name} ${endpoint.method} ${endpoint.path}`.toLowerCase().includes(keyword);
                })
            }))
            .filter(group => group.endpoints.length);
    }

    function renderNav() {
        const nav = document.getElementById('nav');
        nav.innerHTML = filteredGroups().map((group, groupIndex) => `
            <div class="nav-group ${groupIndex === 0 ? 'active' : ''}">
                <button class="nav-title" onclick="toggleGroup(this)">
                    <span>${group.name}</span>
                    <span>+</span>
                </button>
                <div class="nav-items">
                    ${group.endpoints.map((endpoint, endpointIndex) => `
                        <button class="nav-item ${endpointId(endpoint) === endpointId(activeEndpoint) ? 'active' : ''}"
                            onclick="selectEndpointAt(${groupIndex}, ${endpointIndex})">
                            ${endpoint.method} ${endpoint.path}
                        </button>
                    `).join('')}
                </div>
            </div>
        `).join('');
    }

    function toggleGroup(button) {
        button.closest('.nav-group').classList.toggle('active');
    }

    function selectEndpointAt(groupIndex, endpointIndex) {
        selectEndpoint(filteredGroups()[groupIndex].endpoints[endpointIndex]);
    }

    function selectEndpoint(endpoint) {
        activeEndpoint = endpoint;
        renderNav();
        renderEndpoint(endpoint);
    }

    function escapeHtml(text) {
        return String(text).replace(/[&<>"']/g, ch => ({ '&': '&amp;', '<': '&lt;', '>': '&gt;', '"': '&quot;', "'": '&#39;' }[ch]));
    }

    function pretty(value) {
        return JSON.stringify(value, null, 2);
    }

    function renderFields(type, values = {}) {
        const entries = Object.entries(values);
        if (!entries.length) {
            return '<div class="hint">Khong co tham so.</div>';
        }

        return entries.map(([key, value]) => `
            <div class="field">
                <label>${key}</label>
                <input data-${type}="${key}" value="${value ?? ''}">
            </div>
        `).join('');
    }

    function renderEndpoint(endpoint) {
        const content = document.getElementById('content');
        content.innerHTML = `
            <div class="endpoint">
                <div class="endpoint-head">
                    <span class="method ${endpoint.method}">${endpoint.method}</span>
                    <span class="path">${endpoint.path}</span>
                    <span class="auth">${endpoint.auth ? 'Can token' : 'Public'}</span>
                </div>
                <div class="endpoint-body">
                    <div>
                        <div class="section">
                            <h3>${endpoint.name}</h3>
                            <div class="hint">URL se duoc tao tu Base URL + path + query.</div>
                            ${endpoint.note ? `<div class="hint">${endpoint.note}</div>` : ''}

                            <h3>Path params</h3>
                            ${renderFields('path', endpoint.pathParams || {})}

                            <h3>Query</h3>
                            ${renderFields('query', endpoint.query || {})}

                            <h3>Body JSON</h3>
                            <textarea id="bodyInput">${pretty(endpoint.body || {})}</textarea>

                            <div class="actions">
                                <button class="run" onclick="runEndpoint()">Chay API</button>
                                <button class="copy" onclick="copyCurl()">Copy cURL</button>
                            </div>
                        </div>
                    </div>

                    <div class="section">
                        <h3>Response</h3>
                        <pre id="responseBox">Bam "Chay API" de xem du lieu tra ve.</pre>
                        ${endpoint.response ? `
                            <h3>Response mau</h3>
                            <pre>${escapeHtml(pretty(endpoint.response))}</pre>
                        ` : ''}
                    </div>
                </div>
            </div>
        `;
    }

    function currentRequest() {
        let path = activeEndpoint.path;
        document.querySelectorAll('[data-path]').forEach(input => {
            path = path.replace(`{${input.dataset.path}}`, encodeURIComponent(input.value));
        });

        const params = new URLSearchParams();
        document.querySelectorAll('[data-query]').forEach(input => {
            if (input.value !== '') {
                params.set(input.dataset.query, input.value);
            }
        });

        const url = `${baseUrl}${path}${params.toString() ? `?${params.toString()}` : ''}`;
        let body = null;
        const bodyText = document.getElementById('bodyInput')?.value.trim();

        if (activeEndpoint.method !== 'GET' && bodyText) {
            body = JSON.parse(bodyText);
        }

        return { url, body };
    }

    async function runEndpoint() {
        const responseBox = document.getElementById('responseBox');
        responseBox.textContent = 'Dang goi API...';

        try {
            const { url, body } = currentRequest();
            const headers = {
                Accept: 'application/json',
                'Content-Type': 'application/json'
            };
            const token = document.getElementById('tokenInput').value.trim();
            if (token) headers.Authorization = `Bearer ${token}`;

            const response = await fetch(url, {
                method: activeEndpoint.method,
                headers,
                body: body ? JSON.stringify(body) : undefined
            });

            const text = await response.text();
            let data;
            try {
                data = JSON.parse(text);
            } catch (e) {
                data = text;
            }

            responseBox.textContent = pretty({
                status: response.status,
                ok: response.ok,
                body: data
            });
        } catch (error) {
            responseBox.textContent = pretty({
                error: error.message
            });
        }
    }

    async function copyCurl() {
        try {
            const { url, body } = currentRequest();
            const token = document.getElementById('tokenInput').value.trim();
            const parts = [
                'curl',
                '-X', activeEndpoint.method,
                JSON.stringify(url),
                '-H', JSON.stringify('Accept: application/json'),
                '-H', JSON.stringify('Content-Type: application/json')
            ];

            if (token) {
                parts.push('-H', JSON.stringify(`Authorization: Bearer ${token}`));
            }

            if (body && activeEndpoint.method !== 'GET') {
                parts.push('-d', JSON.stringify(JSON.stringify(body)));
            }

            await navigator.clipboard.writeText(parts.join(' '));
            document.getElementById('responseBox').textContent = 'Da copy cURL.';
        } catch (error) {
            document.getElementById('responseBox').textContent = pretty({ error: error.message });
        }
    }

    document.getElementById('searchInput').addEventListener('input', renderNav);
    renderNav();
    renderEndpoint(activeEndpoint);
</script>
</body>
</html>
