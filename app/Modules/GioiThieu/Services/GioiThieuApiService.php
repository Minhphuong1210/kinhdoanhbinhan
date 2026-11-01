<?php

namespace App\Modules\GioiThieu\Services;

use App\Modules\GioiThieu\Models\GioiThieu;

class GioiThieuApiService
{
    public function gioiThieuChungToi()
    {
        $gioiThieu = GioiThieu::forWeb()
            ->where('vi_tri', 've_chung_toi')
            ->firstOrFail();

        return [
            'data' => [
                ...$this->gioiThieuChungToiPayload($gioiThieu, true),

                'hero' => [
                    'badge' => 'Thương hiệu uy tín',

                    'title' => 'Sản phẩm chất lượng, giá trị bền vững và dịch vụ tin cậy.',

                    'description' => 'Chúng tôi mang đến những sản phẩm được lựa chọn kỹ lưỡng, phù hợp cho người tiêu dùng và đối tác kinh doanh, cùng hệ thống hỗ trợ rõ ràng và minh bạch.',

                    'buttons' => [
                        [
                            'title' => 'Xem sản phẩm',
                            'url' => '#products',
                            'type' => 'primary',
                        ],
                        [
                            'title' => 'Đăng ký tham gia',
                            'url' => route('admin.register'),
                            'type' => 'outline',
                        ],
                    ],

                    'statistics' => [
                        [
                            'value' => '03+',
                            'label' => 'Năm kinh doanh',
                        ],
                        [
                            'value' => '20K+',
                            'label' => 'Khách hàng tin dùng',
                        ],
                        [
                            'value' => '50+',
                            'label' => 'Mẫu sản phẩm',
                        ],
                    ],

                    'image' => [
                        'url' => 'https://cdn.kinhdoanhbinhan.com/shared/uploads/1788493393410_8942408245054837428_8942408245054837428_032ac36dd57e4ab14c076b738574a5ad.jpg',
                        'alt' => 'Đồng hành cùng doanh nghiệp phát triển bền vững',
                    ],
                ],
            ],
        ];
    }

    private function gioiThieuChungToiPayload($gioiThieu, $detail = false)
    {
        return [
            'name' => $gioiThieu->name,
            'image' => $gioiThieu->image
                ? cdn($gioiThieu->image)
                : null,
            'content' => $gioiThieu->content,
            'vi_tri' => $gioiThieu->vi_tri,
            'year' => $gioiThieu->year,
            'vi_tri_web' => $gioiThieu->vi_tri_web,
            'nam_kinh_doanh' => $gioiThieu->nam_kinh_doanh,
            'khach_hang' => $gioiThieu->khach_hang,
            'san_pham' => $gioiThieu->san_pham,
        ];
    }
}
