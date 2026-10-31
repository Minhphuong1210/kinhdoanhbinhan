
    <div class="container" style="max-width:600px;margin-top:40px">

        <h3 style="margin-bottom:20px">Import Excel</h3>

        {{-- thông báo --}}
        @if(session('success'))
            <div style="background:#d4edda;padding:10px;margin-bottom:10px">
                {{ session('success') }}
            </div>
        @endif

        @if(session('error'))
            <div style="background:#f8d7da;padding:10px;margin-bottom:10px">
                {{ session('error') }}
            </div>
        @endif

        {{-- form --}}
        <form action="{{ route('import.submit') }}" method="POST" enctype="multipart/form-data">
            @csrf

            <div style="margin-bottom:15px">
                <input type="file" name="file" required>
            </div>

            <button type="submit" style="padding:8px 20px;background:#28a745;color:#fff;border:none">
                Import
            </button>
        </form>

        <div style="margin-top:20px;color:#666;font-size:13px">
            <p>✔ Hỗ trợ file .xls, .xlsx</p>
            <p>✔ Sheet 1: dữ liệu chính</p>
            <p>✔ Sheet 2: danh sách ngân hàng</p>
        </div>

    </div>
