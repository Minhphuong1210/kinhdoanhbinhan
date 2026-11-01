@extends('Core::Admin.layouts.app')

@section('content')
<div class="container-fluid">
    <div class="row">
        <div class="col-12">
            <div class="card card-primary">
                <div class="card-header">
                    <h3 class="card-title">Cấu hình Pancake POS</h3>
                </div>
                <form action="{{ route('admin.shipping.settings.save') }}" method="POST">
                    @csrf
                    <div class="card-body">
                        @if (session('success'))
                            <div class="alert alert-success">
                                {{ session('success') }}
                            </div>
                        @endif

                        <div class="form-group">
                            <label for="api_token">Pancake Access Token</label>
                            <input type="password" name="api_token" class="form-control" id="api_token" placeholder="{{ !empty($pancake?->api_token) ? 'Đã lưu. Để trống nếu không đổi' : 'Nhập Access Token' }}" value="" autocomplete="off" @empty($pancake?->api_token) required @endempty>
                            <small class="form-text text-muted">Lấy từ thiết lập POS -> API Key trên Pancake.</small>
                        </div>
                        <div class="form-group">
                            <label for="shop_id">Shop ID</label>
                            <input type="text" name="shop_id" class="form-control" id="shop_id" placeholder="Nhập Shop ID" value="{{ $pancake->config['shop_id'] ?? '' }}" required>
                        </div>
                        <div class="form-group">
                            <div class="custom-control custom-switch">
                                <input type="checkbox" class="custom-control-input" id="is_active" name="is_active" {{ ($pancake->is_active ?? false) ? 'checked' : '' }}>
                                <label class="custom-control-label" for="is_active">Kích hoạt đẩy đơn tự động</label>
                            </div>
                        </div>
                    </div>
                    <div class="card-footer">
                        <button type="submit" class="btn btn-primary">Lưu cấu hình</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
@endsection
