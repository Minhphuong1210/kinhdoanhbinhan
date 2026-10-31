
<div class="container-fluid">
    <div class="row">
        <div class="col-md-6">
            <div class="card card-primary">
                <div class="card-header">
                    <h3 class="card-title">1. Gửi mã OTP</h3>
                </div>
                <div class="card-body">
                    <div class="form-group">
                        <label for="phone_send">Số điện thoại</label>
                        <input type="text" id="phone_send" class="form-control" placeholder="0xxx">
                    </div>
                </div>
                <div class="card-footer">
                    <button type="button" class="btn btn-primary" onclick="sendOtp()">Gửi OTP</button>
                </div>
            </div>
        </div>

        <div class="col-md-6">
            <div class="card card-success">
                <div class="card-header">
                    <h3 class="card-title">2. Xác minh mã OTP</h3>
                </div>
                <div class="card-body">
                    <div class="form-group">
                        <label for="phone_verify">Số điện thoại</label>
                        <input type="text" id="phone_verify" class="form-control" placeholder="0xxx">
                    </div>
                    <div class="form-group">
                        <label for="otp_code">Mã OTP</label>
                        <input type="text" id="otp_code" class="form-control" placeholder="xxxxxx">
                    </div>
                </div>
                <div class="card-footer">
                    <button type="button" class="btn btn-success" onclick="verifyOtp()">Xác minh</button>
                </div>
            </div>
        </div>
    </div>
    
    <div class="row">
        <div class="col-12">
            <div class="card mt-3">
                <div class="card-header">
                    <h3 class="card-title">Kết quả Test</h3>
                </div>
                <div class="card-body">
                    <pre id="test_result" style="background: #f4f4f4; padding: 15px; border-radius: 5px; min-height: 100px;"></pre>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
    function sendOtp() {
        const phone = document.getElementById('phone_send').value;
        const resultDiv = document.getElementById('test_result');
        
        resultDiv.innerText = 'Đang gửi...';
        
        fetch("{{ route('admin.sms.send') }}", {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': '{{ csrf_token() }}'
            },
            body: JSON.stringify({ phone: phone })
        })
        .then(response => response.json())
        .then(data => {
            resultDiv.innerText = JSON.stringify(data, null, 2);
            if(data.message === 'Gửi thành công') {
                document.getElementById('phone_verify').value = phone;
            }
        })
        .catch(error => {
            resultDiv.innerText = 'Lỗi: ' + error;
        });
    }

    function verifyOtp() {
        const phone = document.getElementById('phone_verify').value;
        const otp = document.getElementById('otp_code').value;
        const resultDiv = document.getElementById('test_result');
        
        resultDiv.innerText = 'Đang xác minh...';
        
        fetch("{{ route('admin.sms.verify') }}", {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': '{{ csrf_token() }}'
            },
            body: JSON.stringify({ phone: phone, otp: otp })
        })
        .then(response => response.json())
        .then(data => {
            resultDiv.innerText = JSON.stringify(data, null, 2);
        })
        .catch(error => {
            resultDiv.innerText = 'Lỗi: ' + error;
        });
    }
</script>

