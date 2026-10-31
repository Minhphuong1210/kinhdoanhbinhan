@extends('Core::Admin.layouts.app')
@section('title', 'Sửa thành viên')
@section('page-title', 'Sửa thành viên')
@section('content')

    <style>
        a {
            text-decoration: none;
        }
.btn_login{
    padding: 14px;
    background-color: #c6c6e9;
    width: 20%;
    border-radius: 15px;
    border: 5px;
}
    </style>

<div class="card">
    <div class="card-header">
        <h5>Sửa: {{ $user->name }}</h5>
        <a href="{{ route('admin.users.index') }}" class="btn btn-secondary btn-sm"><i class="fas fa-arrow-left"></i> Quay lại</a>
    </div>

    <div class="btn_login">
        <a style="" href="{{route('admin.dangnhaptaikhoancuaho',$user->id)}}">Đăng nhập vào tài khoản này</a>
    </div>


    @if ($errors->any())
        <div class="alert alert-danger">
            <ul style="margin-bottom:0">
                @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <div class="card-body">
        <form method="POST" action="{{ route('admin.users.update', $user) }}" enctype="multipart/form-data">@csrf @method('PUT')

            {{-- ========= CƠ BẢN ========= --}}
            <h6 style="color:#4e8ef7;font-weight:700;margin-bottom:14px;padding-bottom:8px;border-bottom:2px solid #e9ecef"><i class="fas fa-user"></i> Thông tin cơ bản</h6>
            <div class="row">
                <div class="col-6"><div class="form-group">
                    <label>Họ tên <span style="color:red">*</span></label>
                    <input type="text" name="name" class="form-control @error('name') is-invalid @enderror" value="{{ old('name', $user->name) }}" required>
                    @error('name')<div class="invalid-feedback">{{ $message }}</div>@enderror
                </div></div>
                <div class="col-6"><div class="form-group">
                    <label>Email</label>
                    <input type="email" name="email" class="form-control @error('email') is-invalid @enderror" value="{{ old('email', $user->email) }}" >
                    @error('email')<div class="invalid-feedback">{{ $message }}</div>@enderror
                </div></div>
                <div class="col-6"><div class="form-group">
                    <label>Mật khẩu mới <small style="color:#8a9bb0">(để trống nếu không đổi)</small></label>
                    <input type="password" name="password" class="form-control">
                </div></div>
                <div class="col-6"><div class="form-group">
                    <label>Xác nhận mật khẩu mới</label>
                    <input type="password" name="password_confirmation" class="form-control">
                </div></div>
                <div class="col-6"><div class="form-group">
                    <label>Số điện thoại  <span style="color:red">*</span></label>
                    <input type="text" name="tel" class="form-control" value="{{ old('tel', $user->tel) }}">
                </div></div>
                <div class="col-6"><div class="form-group">
                    <label>Mã rút tiền (PIN)</label>
                    <input type="text" name="pin" class="form-control" value="{{ old('pin', $user->pin) }}" placeholder="VD: 123456">
                </div></div>
                <div class="col-12"><div class="form-group">
                    <label>Ảnh đại diện</label>
                    @if($user->avatar)
                    <div style="margin-bottom:10px">
                        <img src="{{ cdn($user->avatar) }}" id="current-avatar"
                             style="width:80px;height:80px;object-fit:cover;border-radius:50%;border:3px solid #4e8ef7">
                        <small style="display:block;color:#8a9bb0;margin-top:4px">Chọn ảnh mới để thay thế</small>
                    </div>
                    @endif
                    <input type="file" name="avatar" class="form-control" accept="image/*" onchange="previewAvatar(this)">
                    <div id="avatar-preview" style="margin-top:10px"></div>
                </div></div>
            </div>

            {{-- ========= NGƯỜI GIỚI THIỆU ========= --}}
            <h6 style="color:#4e8ef7;font-weight:700;margin:20px 0 14px;padding-bottom:8px;border-bottom:2px solid #e9ecef"><i class="fas fa-user-friends"></i> Người giới thiệu</h6>
            <div class="row">
                <div class="col-6">
                    <div class="form-group">
                        <label>Cha</label>
                        <select name="parent_id" id="parent_id" class="form-control"></select>
                    </div>
                </div>

                <div class="col-6">
                    <div class="form-group">
                        <label>Người giới thiệu</label>
                        <select name="invite_by" id="invite_by" class="form-control"></select>
                    </div>
                </div>
            </div>

            {{-- ========= NGÂN HÀNG ========= --}}
            <h6 style="color:#4e8ef7;font-weight:700;margin:20px 0 14px;padding-bottom:8px;border-bottom:2px solid #e9ecef"><i class="fas fa-university"></i> Ngân hàng & Ví tiền</h6>
            <div class="row">
                <div class="col-4"><div class="form-group">
                    <label>Tên ngân hàng</label>
                    <input type="text" name="ngan_hang" class="form-control" value="{{ old('ngan_hang', $user->ngan_hang) }}" placeholder="Vietcombank, MB Bank...">
                </div></div>
                <div class="col-4"><div class="form-group">
                    <label>Số tài khoản (STK)</label>
                    <input type="text" name="stk" class="form-control" value="{{ old('stk', $user->stk) }}">
                </div></div>
                <div class="col-4"><div class="form-group">
                    <label>Chủ tài khoản ngân hàng</label>
                    <input type="text" name="ten_chu_tk" class="form-control" value="{{ old('ten_chu_tk', $user->ten_chu_tk ?? '') }}" placeholder="Tên in hoa...">
                </div></div>
                <div class="col-4"><div class="form-group">
                    <label>Số dư ví (VNĐ)</label>
                    <input type="number" name="vi_tien" class="form-control" value="{{ old('vi_tien', $user->vi_tien ?? 0) }}" min="0">
                </div></div>
            </div>

            {{-- ========= ĐỊA CHỈ ========= --}}
            <h6 style="color:#4e8ef7;font-weight:700;margin:20px 0 14px;padding-bottom:8px;border-bottom:2px solid #e9ecef"><i class="fas fa-map-marker-alt"></i> Địa chỉ</h6>
            <div class="row">
                <div class="col-4"><div class="form-group">
                    <label>Tỉnh / Thành phố</label>
                    <select name="province_id" id="province_id" class="form-control" onchange="loadDistricts(this.value)">
                        <option value="">— Chọn Tỉnh/TP —</option>
                        @foreach($provinces as $prov)
                        <option value="{{ $prov->id }}" {{ old('province_id', $user->province_id) == $prov->id ? 'selected' : '' }}>{{ $prov->name }}</option>
                        @endforeach
                    </select>
                </div></div>
                <div class="col-4"><div class="form-group">
                    <label>Quận / Huyện</label>
                    <select name="district_id" id="district_id" class="form-control" onchange="loadWards(this.value)" {{ $districts->isEmpty() ? 'disabled' : '' }}>
                        <option value="">— Chọn Quận/Huyện —</option>
                        @foreach($districts as $d)
                        <option value="{{ $d->id }}" {{ old('district_id', $user->district_id) == $d->id ? 'selected' : '' }}>{{ $d->prefix ? $d->prefix.' ':'' }}{{ $d->name }}</option>
                        @endforeach
                    </select>
                </div></div>
                <div class="col-4"><div class="form-group">
                    <label>Phường / Xã</label>
                    <select name="ward_id" id="ward_id" class="form-control" {{ $wards->isEmpty() ? 'disabled' : '' }}>
                        <option value="">— Chọn Phường/Xã —</option>
                        @foreach($wards as $w)
                        <option value="{{ $w->id }}" {{ old('ward_id', $user->ward_id) == $w->id ? 'selected' : '' }}>{{ $w->prefix ? $w->prefix.' ':'' }}{{ $w->name }}</option>
                        @endforeach
                    </select>
                </div></div>
                <div class="col-12"><div class="form-group">
                    <label>Địa chỉ chi tiết</label>
                    <input type="text" name="address" class="form-control" value="{{ old('address', $user->address) }}" placeholder="Số nhà, tên đường...">
                </div></div>
            </div>

            {{-- ========= PHÂN QUYỀN ========= --}}
            <h6 style="color:#4e8ef7;font-weight:700;margin:20px 0 14px;padding-bottom:8px;border-bottom:2px solid #e9ecef"><i class="fas fa-key"></i> Phân quyền</h6>
            @if($roles->count())
            <div class="form-group">
                <div style="display:grid;grid-template-columns:repeat(3,1fr);gap:10px;border:1.5px solid #e2e8f0;border-radius:7px;padding:14px">
                    @foreach($roles as $role)
                    <label style="display:flex;align-items:center;gap:7px;cursor:pointer;font-size:13px">
                        <input type="checkbox" name="roles[]" value="{{ $role->id }}"
                            {{ in_array($role->id, old('roles', $userRoles)) ? 'checked' : '' }}>
                        <div>
                            <strong>{{ $role->display_name ?? $role->name }}</strong>
                            @if($role->description)<br><small style="color:#8a9bb0">{{ $role->description }}</small>@endif
                        </div>
                    </label>
                    @endforeach
                </div>
            </div>
            @endif
            <div class="form-group" style="display:flex;gap:24px;flex-wrap:wrap">
                <label style="display:flex;align-items:center;gap:8px;cursor:pointer"><input type="checkbox" name="active" value="1" {{ old('active', $user->active) ? 'checked' : '' }}> Hoạt động</label>
                <label style="display:flex;align-items:center;gap:8px;cursor:pointer"><input type="checkbox" name="is_admin" value="1" {{ old('is_admin', $user->is_admin) ? 'checked' : '' }}> <strong>Là Admin</strong> <small style="color:#8a9bb0">(bỏ qua role)</small></label>
            </div>
            <button type="submit" class="btn btn-primary"><i class="fas fa-save"></i> Cập nhật</button>
        </form>
    </div>
</div>
@push('scripts')

    <link href="https://cdn.jsdelivr.net/npm/tom-select/dist/css/tom-select.css" rel="stylesheet">
    <script src="https://cdn.jsdelivr.net/npm/tom-select/dist/js/tom-select.complete.min.js"></script>

<script>
function previewAvatar(input) {
    const preview = document.getElementById('avatar-preview');
    const cur     = document.getElementById('current-avatar');
    if (input.files && input.files[0]) {
        const reader = new FileReader();
        reader.onload = e => {
            preview.innerHTML = '<img src="'+e.target.result+'" style="width:80px;height:80px;object-fit:cover;border-radius:50%;border:3px solid #22c55e">';
            if (cur) cur.style.opacity = '0.4';
        };
        reader.readAsDataURL(input.files[0]);
    }
}

const DISTRICT_URL = '{{ route("admin.ajax.districts", "__ID__") }}';
const WARD_URL     = '{{ route("admin.ajax.wards", "__ID__") }}';

function loadDistricts(provinceId, selD) {
    const ds = document.getElementById('district_id');
    const ws = document.getElementById('ward_id');
    ds.innerHTML = '<option value="">— Chọn Quận/Huyện —</option>';
    ws.innerHTML  = '<option value="">— Chọn Phường/Xã —</option>';
    ds.disabled = ws.disabled = true;
    if (!provinceId) return;
    fetch(DISTRICT_URL.replace('__ID__', provinceId))
        .then(r => r.json()).then(data => {
            data.forEach(d => {
                const opt = new Option((d.prefix ? d.prefix+' ':'')+d.name, d.id);
                if (selD && String(d.id) === String(selD)) opt.selected = true;
                ds.add(opt);
            });
            ds.disabled = false;
        });
}

function loadWards(districtId, selW) {
    const ws = document.getElementById('ward_id');
    ws.innerHTML = '<option value="">— Chọn Phường/Xã —</option>';
    ws.disabled = true;
    if (!districtId) return;
    fetch(WARD_URL.replace('__ID__', districtId))
        .then(r => r.json()).then(data => {
            data.forEach(w => {
                const opt = new Option((w.prefix ? w.prefix+' ':'')+w.name, w.id);
                if (selW && String(w.id) === String(selW)) opt.selected = true;
                ws.add(opt);
            });
            ws.disabled = false;
        });
}



function initUserSelect(el, selected) {
    const select = new TomSelect(el, {
        valueField: 'id',
        labelField: 'name',
        searchField: ['name', 'email', 'tel'],
        maxItems: 1,
        create: false,

        placeholder: 'Nhập tên / email / SĐT...',

        render: {
            option: function(item, escape) {
                return `<div>
                    ${escape(item.name)}
                    ${item.tel ? ' - ' + escape(item.tel) : ''}
                    ${item.email ? ' - ' + escape(item.email) : ''}
                </div>`;
            },
            item: function(item, escape) {
                return `<div>
                    ${escape(item.name)}
                    ${item.tel ? ' - ' + escape(item.tel) : ''}
                    ${item.email ? ' - ' + escape(item.email) : ''}
                </div>`;
            }
        },

        load: function (query, callback) {
            if (!query.length) return callback();

            fetch(`{{ route("admin.users.search") }}?q=${query}`)
                .then(res => res.json())
                .then(json => callback(json))
                .catch(() => callback());
        }
    });


    if (selected && selected.id) {
        select.addOption(selected);
        select.setValue(selected.id);
    }
}

document.addEventListener('DOMContentLoaded', function () {

    initUserSelect('#parent_id', {
        id: '{{ $user->parent_id }}',
        name: '{{ optional($user->parent)->name }}',
        email: '{{ optional($user->parent)->email }}',
        tel: '{{ optional($user->parent)->tel }}'
    });

    initUserSelect('#invite_by', {
        id: '{{ $user->invite_by }}',
        name: '{{ optional($user->inviteBy)->name }}',
        email: '{{ optional($user->inviteBy)->email }}',
        tel: '{{ optional($user->inviteBy)->tel }}'
    });

});
</script>
@endpush
@endsection
