<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

/**
 * - Setting link_bo_cong_thuong: link hồ sơ trên online.gov.vn. Chỉ khi có link thì chân trang mới
 *   hiện logo "Đã thông báo Bộ Công Thương" (gắn logo khi chưa thông báo là sai quy định).
 * - Các trang chính sách mẫu (bảng chinh_sach, url = slug). Nội dung dùng biến {ten_cong_ty}, {hotline}...
 *   được thay bằng thông tin ở Cài đặt khi hiển thị. Admin cần đọc lại và sửa cho đúng thực tế.
 */
return new class extends Migration
{
    public function up(): void
    {
        foreach (['cong_ty', 'htx'] as $viTriWeb) {
            $hasLink = DB::table('setting')->where('name', 'link_bo_cong_thuong')->where('vi_tri_web', $viTriWeb)->exists();
            if (!$hasLink) {
                DB::table('setting')->insert([
                    'name'       => 'link_bo_cong_thuong',
                    'value'      => null,
                    'type'       => 'general_tab',
                    'vi_tri_web' => $viTriWeb,
                    'created_at' => now(),
                    'updated_at' => now(),
                ]);
            }

            foreach ($this->policies() as $slug => [$title, $content]) {
                $exists = DB::table('chinh_sach')->where('url', $slug)->where('vi_tri_web', $viTriWeb)->exists();
                if ($exists) {
                    continue;
                }

                DB::table('chinh_sach')->insert([
                    'title'      => $title,
                    'url'        => $slug,
                    'content'    => trim($content),
                    'image'      => null,
                    'status'     => 1,
                    'vi_tri_web' => $viTriWeb,
                    'created_at' => now(),
                    'updated_at' => now(),
                ]);
            }
        }
    }

    public function down(): void
    {
        DB::table('setting')->where('name', 'link_bo_cong_thuong')->delete();
        DB::table('chinh_sach')->whereIn('url', array_keys($this->policies()))->delete();
    }

    private function policies(): array
    {
        return [
            'huong-dan-mua-hang' => ['Hướng dẫn mua hàng', <<<'HTML'
<p>Quý khách có thể đặt mua sản phẩm của {ten_cong_ty} trên website {website} hoặc trên ứng dụng di động theo các bước sau:</p>
<h3>Bước 1: Đăng ký / đăng nhập tài khoản</h3>
<p>Quý khách đăng ký tài khoản bằng số điện thoại, email và mã giới thiệu (nếu có). Nếu đã có tài khoản, quý khách đăng nhập để tiếp tục.</p>
<h3>Bước 2: Chọn sản phẩm</h3>
<p>Tìm sản phẩm tại mục <strong>Sản phẩm</strong>, xem thông tin chi tiết (giá, thành phần, công dụng, hướng dẫn sử dụng) và bấm <strong>Mua ngay</strong>. Với sản phẩm mua tự chọn, quý khách chọn từng sản phẩm và số lượng mong muốn.</p>
<h3>Bước 3: Nhập thông tin nhận hàng</h3>
<p>Điền họ tên người nhận, số điện thoại, địa chỉ nhận hàng (tỉnh/thành, quận/huyện, phường/xã, số nhà) và ghi chú nếu có. Kiểm tra lại tổng tiền, phí vận chuyển trước khi xác nhận.</p>
<h3>Bước 4: Thanh toán</h3>
<p>Quý khách thanh toán bằng số dư ví mua hàng hoặc chuyển khoản qua mã QR (PayOS). Xem chi tiết tại <a href="/chinh-sach/chinh-sach-thanh-toan">Chính sách thanh toán</a>.</p>
<h3>Bước 5: Nhận hàng</h3>
<p>Đơn hàng được xác nhận và giao bởi đơn vị vận chuyển. Quý khách được kiểm tra hàng trước khi nhận theo <a href="/chinh-sach/chinh-sach-kiem-hang">Chính sách kiểm hàng</a>.</p>
<p>Mọi thắc mắc vui lòng liên hệ hotline <strong>{hotline}</strong> hoặc email <strong>{email}</strong>.</p>
HTML],

            'chinh-sach-thanh-toan' => ['Chính sách thanh toán', <<<'HTML'
<p>{ten_cong_ty} áp dụng các hình thức thanh toán sau cho đơn hàng đặt trên website {website} và ứng dụng:</p>
<h3>1. Thanh toán bằng ví mua hàng</h3>
<p>Số dư ví mua hàng trong tài khoản được trừ trực tiếp khi quý khách xác nhận đơn hàng. Quý khách có thể xem lịch sử giao dịch trong mục tài khoản.</p>
<h3>2. Chuyển khoản qua mã QR (PayOS)</h3>
<p>Khi số dư ví chưa đủ, hệ thống tạo mã QR thanh toán qua cổng PayOS. Quý khách quét mã bằng ứng dụng ngân hàng và giữ nguyên số tiền, nội dung chuyển khoản. Giao dịch thành công sẽ được ghi nhận tự động và đơn hàng được tạo ngay sau đó. Mã QR có hiệu lực trong 10 phút.</p>
<h3>3. Lưu ý</h3>
<ul>
<li>Tổng tiền, phí vận chuyển được hiển thị rõ ràng trước khi quý khách xác nhận thanh toán.</li>
<li>{ten_cong_ty} không yêu cầu khách hàng cung cấp mật khẩu, mã OTP hay thông tin thẻ ngân hàng dưới bất kỳ hình thức nào.</li>
<li>Trường hợp đã trừ tiền nhưng chưa có đơn hàng, vui lòng liên hệ hotline {hotline} để được kiểm tra và xử lý.</li>
</ul>
HTML],

            'chinh-sach-van-chuyen-giao-nhan' => ['Chính sách vận chuyển và giao nhận', <<<'HTML'
<h3>1. Phạm vi giao hàng</h3>
<p>{ten_cong_ty} giao hàng trên toàn quốc thông qua các đơn vị vận chuyển đối tác.</p>
<h3>2. Thời gian giao hàng</h3>
<p>Đơn hàng được xử lý và bàn giao cho đơn vị vận chuyển sau khi xác nhận. Thời gian giao dự kiến từ 2 – 5 ngày làm việc tùy khu vực, không tính thứ Bảy, Chủ nhật và ngày lễ. Thời gian có thể thay đổi do thời tiết, dịch bệnh hoặc sự cố khách quan.</p>
<h3>3. Phí vận chuyển</h3>
<p>Phí vận chuyển (nếu có) được hiển thị cụ thể tại bước xác nhận đơn hàng trước khi quý khách thanh toán.</p>
<h3>4. Giao nhận</h3>
<ul>
<li>Nhân viên giao hàng liên hệ theo số điện thoại người nhận trước khi giao.</li>
<li>Quý khách vui lòng kiểm tra tình trạng bên ngoài kiện hàng và sản phẩm theo <a href="/chinh-sach/chinh-sach-kiem-hang">Chính sách kiểm hàng</a> trước khi ký nhận.</li>
<li>Nếu không liên lạc được với người nhận sau nhiều lần, đơn hàng có thể được hoàn về và chúng tôi sẽ liên hệ lại với quý khách.</li>
</ul>
<p>Hỗ trợ giao nhận: hotline <strong>{hotline}</strong>.</p>
HTML],

            'chinh-sach-kiem-hang' => ['Chính sách kiểm hàng', <<<'HTML'
<p>Nhằm đảm bảo quyền lợi của khách hàng, {ten_cong_ty} cho phép quý khách kiểm tra hàng hóa khi nhận hàng.</p>
<h3>1. Nội dung được kiểm tra</h3>
<ul>
<li>Tình trạng bao bì, niêm phong của kiện hàng.</li>
<li>Đúng tên sản phẩm, số lượng, quy cách so với đơn đã đặt.</li>
<li>Hạn sử dụng in trên bao bì sản phẩm.</li>
</ul>
<h3>2. Khi phát hiện sai sót</h3>
<p>Nếu sản phẩm bị móp méo, rách bao bì, sai sản phẩm hoặc thiếu số lượng, quý khách có quyền từ chối nhận hàng và liên hệ ngay hotline <strong>{hotline}</strong>. Chúng tôi sẽ kiểm tra và gửi lại hàng đúng cho quý khách.</p>
<h3>3. Lưu ý</h3>
<p>Quý khách vui lòng không bóc niêm phong, sử dụng thử sản phẩm khi chưa thanh toán/nhận hàng. Nên quay video khi mở kiện hàng để làm căn cứ giải quyết khi cần.</p>
HTML],

            'chinh-sach-doi-tra-hoan-tien' => ['Chính sách đổi trả và hoàn tiền', <<<'HTML'
<h3>1. Trường hợp được đổi trả</h3>
<ul>
<li>Sản phẩm giao sai so với đơn hàng (sai loại, sai số lượng).</li>
<li>Sản phẩm bị lỗi từ nhà sản xuất, hư hỏng do quá trình vận chuyển.</li>
<li>Sản phẩm hết hạn sử dụng tại thời điểm nhận hàng.</li>
</ul>
<h3>2. Điều kiện đổi trả</h3>
<ul>
<li>Quý khách thông báo cho chúng tôi trong vòng 07 ngày kể từ ngày nhận hàng.</li>
<li>Sản phẩm còn nguyên tem, nhãn, bao bì; chưa qua sử dụng (trừ trường hợp lỗi phát hiện khi sử dụng).</li>
<li>Có hình ảnh/video chứng minh tình trạng sản phẩm.</li>
</ul>
<h3>3. Quy trình</h3>
<p>Liên hệ hotline <strong>{hotline}</strong> hoặc email <strong>{email}</strong>, cung cấp mã đơn hàng và hình ảnh sản phẩm. Sau khi xác nhận, chúng tôi hướng dẫn gửi trả và đổi sản phẩm mới hoặc hoàn tiền.</p>
<h3>4. Hoàn tiền</h3>
<p>Tiền được hoàn vào ví mua hàng hoặc chuyển khoản về tài khoản quý khách trong vòng 07 ngày làm việc kể từ khi chúng tôi nhận lại hàng. Chi phí vận chuyển đổi trả do lỗi từ phía chúng tôi sẽ do {ten_cong_ty} chi trả.</p>
HTML],

            'chinh-sach-bao-mat-thong-tin' => ['Chính sách bảo mật thông tin', <<<'HTML'
<h3>1. Mục đích thu thập thông tin</h3>
<p>{ten_cong_ty} thu thập thông tin cá nhân của khách hàng (họ tên, số điện thoại, email, địa chỉ nhận hàng, thông tin tài khoản ngân hàng khi rút tiền) nhằm: xử lý đơn hàng, giao hàng, chăm sóc khách hàng, thông báo về đơn hàng và chương trình ưu đãi.</p>
<h3>2. Phạm vi sử dụng thông tin</h3>
<p>Thông tin chỉ được sử dụng trong nội bộ công ty và cung cấp cho đơn vị vận chuyển, cổng thanh toán ở mức cần thiết để hoàn thành đơn hàng. Chúng tôi không mua bán, trao đổi thông tin khách hàng cho bên thứ ba, trừ khi có yêu cầu của cơ quan nhà nước có thẩm quyền theo quy định pháp luật.</p>
<h3>3. Thời gian lưu trữ</h3>
<p>Thông tin được lưu trữ cho đến khi khách hàng yêu cầu hủy bỏ, hoặc theo thời hạn quy định của pháp luật.</p>
<h3>4. Đơn vị thu thập và quản lý thông tin</h3>
<p><strong>{ten_cong_ty}</strong><br>Mã số doanh nghiệp: {ma_so_doanh_nghiep}<br>Địa chỉ: {dia_chi}<br>Điện thoại: {hotline} — Email: {email}</p>
<h3>5. Quyền của khách hàng</h3>
<p>Khách hàng có quyền kiểm tra, cập nhật, điều chỉnh hoặc yêu cầu xóa thông tin cá nhân bằng cách đăng nhập tài khoản hoặc liên hệ với chúng tôi qua thông tin ở trên.</p>
<h3>6. Cam kết bảo mật</h3>
<p>Chúng tôi áp dụng các biện pháp kỹ thuật và quản lý phù hợp để bảo vệ thông tin khách hàng, không để lộ, mất mát hay truy cập trái phép. Khi phát hiện thông tin bị sử dụng sai mục đích, khách hàng vui lòng thông báo ngay qua hotline {hotline}.</p>
HTML],

            'dieu-khoan-su-dung' => ['Điều khoản sử dụng', <<<'HTML'
<p>Khi truy cập và sử dụng website {website}, quý khách đồng ý với các điều khoản dưới đây.</p>
<h3>1. Tài khoản</h3>
<p>Quý khách chịu trách nhiệm cung cấp thông tin chính xác khi đăng ký và tự bảo mật mật khẩu, mã PIN của mình. Mọi giao dịch thực hiện bằng tài khoản được xem là do chủ tài khoản thực hiện.</p>
<h3>2. Thông tin sản phẩm</h3>
<p>Chúng tôi cố gắng cung cấp thông tin sản phẩm chính xác, đầy đủ. Hình ảnh có thể khác thực tế đôi chút do điều kiện chụp/hiển thị. Giá bán có thể thay đổi và được áp dụng theo thời điểm khách hàng đặt hàng.</p>
<h3>3. Hành vi bị nghiêm cấm</h3>
<ul>
<li>Sử dụng website cho mục đích vi phạm pháp luật, lừa đảo.</li>
<li>Can thiệp, phá hoại hệ thống, truy cập trái phép dữ liệu của người khác.</li>
<li>Đăng tải nội dung sai sự thật, xúc phạm tổ chức, cá nhân khác.</li>
</ul>
<h3>4. Quyền sở hữu trí tuệ</h3>
<p>Toàn bộ nội dung, hình ảnh, logo trên website thuộc quyền sở hữu của {ten_cong_ty}. Nghiêm cấm sao chép khi chưa có sự đồng ý bằng văn bản.</p>
<h3>5. Thay đổi điều khoản</h3>
<p>Chúng tôi có thể cập nhật điều khoản này và thông báo trên website. Việc quý khách tiếp tục sử dụng website đồng nghĩa với việc chấp nhận các thay đổi.</p>
HTML],

            'chinh-sach-xu-ly-khieu-nai' => ['Chính sách xử lý khiếu nại', <<<'HTML'
<p>{ten_cong_ty} luôn lắng nghe và giải quyết khiếu nại của khách hàng trên tinh thần cầu thị, nhanh chóng.</p>
<h3>1. Kênh tiếp nhận</h3>
<ul>
<li>Hotline: <strong>{hotline}</strong></li>
<li>Email: <strong>{email}</strong></li>
<li>Địa chỉ: {dia_chi}</li>
</ul>
<h3>2. Quy trình xử lý</h3>
<ol>
<li>Tiếp nhận khiếu nại, ghi nhận thông tin đơn hàng và nội dung phản ánh.</li>
<li>Kiểm tra, xác minh trong vòng 03 ngày làm việc.</li>
<li>Phản hồi kết quả và phương án giải quyết cho khách hàng.</li>
<li>Thực hiện phương án đã thống nhất (đổi hàng, hoàn tiền, bồi hoàn...).</li>
</ol>
<h3>3. Giải quyết tranh chấp</h3>
<p>Mọi tranh chấp được ưu tiên giải quyết bằng thương lượng, hòa giải. Trường hợp không thống nhất, các bên có quyền yêu cầu cơ quan nhà nước có thẩm quyền giải quyết theo quy định của pháp luật Việt Nam.</p>
HTML],
        ];
    }
};
