<?php

namespace App\Services;

use App\Models\LichSuGiaoDich;
use App\Models\Order;
use App\Models\OrderDetail;
use App\Models\Product;
use App\Models\CoCheHoanPhi;
use App\Models\FcmUser;
use App\Models\Setting;
use App\Models\User;
use App\Modules\Shipping\Services\PancakeService;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;

use App\Models\Gift;
use App\Models\ProductPromotion;
use App\Models\Promotion;
use App\Services\FirebaseNotificationService;
use App\Models\Notification;
use Illuminate\Support\Facades\Log;

class OrderService
{
    /** Phí ship cộng vào mỗi đơn mua tự chọn */
    public const SHIPPING_FEE_TU_CHON = 54000;

    // ================================================================
    // ENTRY POINT — orchestrate toàn bộ luồng mua
    // ================================================================

    protected $pancake;
    protected $firebase;

    public function __construct(
        PancakeService              $pancake,
        FirebaseNotificationService $firebase
    )
    {
        $this->pancake = $pancake;
        $this->firebase = $firebase;
    }

    /**
     * ================================================================
     * MUA GÓI
     * ================================================================
     */
    /**
     * @param bool $fromApp true khi đặt qua app: lưu device + thoi_gian_mua (giờ server).
     *                      Đặt trên web thì 2 trường này để trống.
     */
    public function muaGoi(Request $request, Product $product, bool $fromApp = false): Order
    {
        DB::beginTransaction();

        try {
            /** @var User $user */
            $user = auth()->user();

            $qty = max(1, (int)$request->qty);

            // Kiểm tra tồn kho
            $this->ensureStock($product, $qty);

            // Tính tổng tiền
            list($total, $totalHoan) = $this->tinhTongTien(
                $product,
                $qty
            );

            // Kiểm tra ví
            $this->kiemTraViTien(
                $user,
                $total
            );

            // Tạo order
            $order = $this->taoOrder(
                $request,
                $user,
                $product,
                $qty,
                $total,
                $totalHoan,
                $fromApp
            );

            // Trừ tồn kho
            $this->decrementStock(
                $product,
                $qty
            );

            $isSanPhamHoan = $product->san_pham_hoan == 1
                ? 1
                : 0;

            // Trừ ví người mua
            $this->truViNguoiMua(
                $user,
                $total,
                $isSanPhamHoan
            );

            // Ghi lịch sử giao dịch
            $this->ghiLichSuGiaoDich(
                $user->fresh(),
                $total,
                $totalHoan,
                $product->name,
                $product->id
            );

            // Xử lý lần đầu mua
            if (!$user->parent_id) {
                $this->xuLyLanDauMua(
                    $user->fresh(),
                    $order,
                    $isSanPhamHoan
                );
            }

            // Chia hoàn phí
            if ($totalHoan > 0 && $isSanPhamHoan != 1) {
                $this->chiaTienHoanPhi(
                    $user->fresh(),
                    $totalHoan,
                    $product->name,
                    $product,
                    $product->id
                );
            }

            // ========================================================
            // THÔNG BÁO MUA HÀNG
            // ========================================================

            $this->notifyOrderCreated(
                $user,
                $order,
                $product->name
            );

            $title_notification = 'Mua' . ' ' . $product->name . ' ' . 'thành công';
            $body_notification = 'Mua' . ' ' . $product->name . ' ' . 'thành công';
            $this->firebase->sendToUser(
                $user,
                $title_notification,
                $body_notification,
                [],
                FcmUser::VI_TRI_CONG_TY
            );

            // ====================================================
            // DATABASE NOTIFICATION
            // ====================================================

//            $this->createNotification($user, $title_notification, $body_notification,$order->id);


            // Thêm khuyến mại
            $this->addProductPromotion(
                $order,
                $product
            );

            // Clear cache
            $this->clearDashboardCache(
                (int)$user->id
            );

            DB::commit();

            // Đẩy đơn sang Pancake
            $this->pancake->pushOrder($order);

            return $order;

        } catch (\Exception $e) {

            DB::rollback();

            throw $e;
        }
    }

    /**
     * ================================================================
     * MUA TỰ CHỌN
     * Chọn nhiều sản phẩm con
     * ================================================================
     */
    public function muaTuChon(
        Request $request,
        Product $parentProduct,
        bool    $fromApp = false
    ): Order
    {

        DB::beginTransaction();

        try {
            /** @var User $user */
            $user = auth()->user();

            $childIds = $request->child_ids ?: [];
            $childQtys = $request->child_qtys ?: [];

            $selections = [];
            $totalPrice = 0;

            // Tiền của các sản phẩm được hoàn phí (san_pham_hoan != 1)
            $tienChiaHoan = 0;

            // ========================================================
            // LẤY SẢN PHẨM ĐƯỢC CHỌN
            // ========================================================

            foreach ($childIds as $index => $id) {

                $qty = (int)($childQtys[$index] ?? 0);

                if ($qty > 0) {

                    $child = Product::where('active', 1)
                        ->availableForPurchase()
                        ->find($id);

                    if ($child) {

                        // Kiểm tra tồn kho
                        $this->ensureStock(
                            $child,
                            $qty
                        );

                        $selections[] = [
                            'product' => $child,
                            'qty' => $qty,
                        ];

                        $totalPrice += (
                            $child->price * $qty
                        );

                        if ($child->san_pham_hoan != 1) {
                            $tienChiaHoan += (
                                $child->price * $qty
                            );
                        }
                    }
                }
            }

            // ========================================================
            // KIỂM TRA CÓ SẢN PHẨM HAY KHÔNG
            // ========================================================

            if (empty($selections)) {
                throw new \Exception(
                    'Vui lòng chọn ít nhất 1 sản phẩm với số lượng lớn hơn 0.'
                );
            }

            // ========================================================
            // TÍNH TIỀN
            // ========================================================

            $shippingFee = self::SHIPPING_FEE_TU_CHON;

            $productTotal = $totalPrice;
            $totalPrice = $productTotal + $shippingFee;

            $totalHoan = max(
                0,
                $productTotal
            );

            // Kiểm tra ví
            $this->kiemTraViTien(
                $user,
                $totalPrice
            );

            // VAT 8%
            $price_vat = (
                    $totalHoan * 8
                ) / 100;

            // ========================================================
            // TẠO ORDER
            // ========================================================

            $order = Order::create([
                    'user_id' => $user->id,

                    'ma_don_hang' => $this->maDonHangTuChon(
                        $parentProduct
                    ),

                    'name' => 'Mua tự chọn: ' .
                        $parentProduct->name,

                    'total_price' => $totalPrice,

                    'total_price_hoan_tien' => $totalHoan,

                    'receive_user' => $request->receive_user,

                    'receive_phone' => $request->receive_phone,

                    'receive_address' => $request->receive_address,

                    'province_id' => $request->province_id,

                    'district_id' => $request->district_id,

                    'ward_id' => $request->ward_id,

                    'note' => $request->note,

                    'status' => 'completed',

                    'first_order' => $user->parent_id
                        ? 0
                        : 1,

                    'price_vat' => $price_vat,

                    'shipping_price' => $shippingFee,
                ] + $this->thongTinMuaApp($request, $fromApp));

            // ========================================================
            // TẠO ORDER DETAIL
            // ========================================================

            $productParent = $parentProduct;

            foreach ($selections as $item) {

                $p = $item['product'];

                OrderDetail::create([
                    'order_id' => $order->id,

                    'product_id' => $p->id,

                    'image_product' => $p->image
                        ? $p->image
                        : null,

                    'name_product' => $p->name,

                    'price_product' => $p->price,

                    'quantity' => $item['qty'],

                    'the_loai' => 'product',
                ]);

                // Trừ kho
                $this->decrementStock(
                    $p,
                    (int)$item['qty']
                );

                // Lấy cha sản phẩm
                $productParent = $p->parent
                    ? $p->parent
                    : $parentProduct;
            }

            // ========================================================
            // XÁC ĐỊNH SẢN PHẨM HOÀN
            // ========================================================

            // Xét theo sản phẩm được chọn, không theo sản phẩm cha:
            // chỉ cần 1 sản phẩm được hoàn phí là đơn được lên cây
            $isSanPhamHoan = $tienChiaHoan > 0
                ? 0
                : 1;

            // ========================================================
            // TRỪ VÍ
            // ========================================================

            $this->truViNguoiMua(
                $user,
                $totalPrice,
                $isSanPhamHoan
            );

            // ========================================================
            // GHI LỊCH SỬ
            // ========================================================

            $this->ghiLichSuGiaoDich(
                $user->fresh(),
                $totalPrice,
                $totalHoan,
                'Mua tự chọn ' . $parentProduct->name,
                $parentProduct->id
            );

            // ========================================================
            // XỬ LÝ LẦN ĐẦU MUA
            // ========================================================

            if (!$user->parent_id) {

                $this->xuLyLanDauMua(
                    $user->fresh(),
                    $order,
                    $isSanPhamHoan
                );
            }

            // ========================================================
            // CHIA HOÀN PHÍ
            // ========================================================

            if ($tienChiaHoan > 0) {

                $this->chiaTienHoanPhi(
                    $user->fresh(),
                    $tienChiaHoan,
                    'tự chọn ' . $parentProduct->name,
                    $parentProduct,
                    $parentProduct->id
                );
            }

            // ========================================================
            // THÔNG BÁO MUA TỰ CHỌN
            // ========================================================



            $title_notification = 'Mua' . ' ' . $parentProduct->name . ' ' . 'thành công';
            $body_notification = 'Mua' . ' ' . $parentProduct->name . ' ' . 'thành công';
            $this->firebase->sendToUser(
                $user,
                $title_notification,
                $body_notification,
                [],
                FcmUser::VI_TRI_CONG_TY
            );


            $this->notifyOrderCreated(
                $user,
                $order,
                'tự chọn: ' . $parentProduct->name
            );

            // ========================================================
            // CACHE
            // ========================================================

            $this->clearDashboardCache(
                (int)$user->id
            );

            // ========================================================
            // KHUYẾN MẠI
            // ========================================================

            $this->addProductPromotion(
                $order,
                $productParent
            );

            // ========================================================
            // PANCAKE
            // ========================================================

            $this->pancake->pushOrder(
                $order
            );

            DB::commit();

            return $order;

        } catch (\Exception $e) {

            DB::rollback();

            throw $e;
        }
    }

    // ================================================================
    // 1. TÍNH TỔNG TIỀN
    // ================================================================

    /**
     * @return array [totalBill, totalHoanTien]
     */
    public function tinhTongTien(
        Product $product,
        int     $qty
    ): array
    {

        $total = $product->price * $qty;

        $totalHoan = (
                $product->price_hoan_tien ?? 0
            ) * $qty;

        return [
            $total,
            $totalHoan
        ];
    }

    // ================================================================
    // 2. KIỂM TRA SỐ DƯ
    // ================================================================

    private function kiemTraViTien(
        User  $user,
        float $total
    ): void
    {

        if ($user->vi_tien_mua_hang < $total) {

            $missing =
                $total -
                $user->vi_tien_mua_hang;

            throw new \Exception(
                'Số dư không đủ. Cần nạp thêm ' .
                number_format(
                    $missing,
                    0,
                    ',',
                    '.'
                ) .
                'đ, hiện có ' .
                number_format(
                    $user->vi_tien_mua_hang,
                    0,
                    ',',
                    '.'
                ) .
                'đ.',
                402
            );
        }
    }

    // ================================================================
    // 3. KIỂM TRA TỒN KHO
    // ================================================================

    private function ensureStock(
        Product $product,
        int     $qty
    ): void
    {

        $freshProduct = Product::whereKey(
            $product->id
        )
            ->lockForUpdate()
            ->first();

        if (
            !$freshProduct ||
            (int)$freshProduct->active !== 1
        ) {

            throw new \Exception(
                'San pham "' .
                $product->name .
                '" khong con duoc ban.'
            );
        }

        $product->setAttribute(
            'quantity',
            $freshProduct->quantity
        );

        if ($freshProduct->quantity === null) {
            return;
        }

        if (
            (int)$freshProduct->quantity < $qty
        ) {

            throw new \Exception(
                'San pham "' .
                $product->name .
                '" khong du so luong ton kho.'
            );
        }
    }

    // ================================================================
    // 4. TRỪ TỒN KHO
    // ================================================================

    private function decrementStock(
        Product $product,
        int     $qty
    ): void
    {

        if ($product->quantity === null) {
            return;
        }

        $updated = Product::whereKey(
            $product->id
        )
            ->where(
                'quantity',
                '>=',
                $qty
            )
            ->decrement(
                'quantity',
                $qty
            );

        if ($updated !== 1) {

            throw new \Exception(
                'San pham "' .
                $product->name .
                '" khong du so luong ton kho.'
            );
        }
    }

    // ================================================================
    // 5. TẠO ORDER
    // ================================================================

    private function taoOrder(
        Request $request,
        User    $user,
        Product $product,
        int     $qty,
        float   $total,
        float   $totalHoan,
        bool    $fromApp = false
    ): Order
    {

        // VAT
        $price_vat =
            ($totalHoan * 8) / 100;

        $order = Order::create([
            'user_id' => $user->id,

            'ma_don_hang' =>
                'DH' .
                strtoupper(
                    substr(
                        uniqid(),
                        -6
                    )
                ),

            'name' =>
                $product->name .
                ($qty > 1
                    ? ' x' . $qty
                    : ''),

            'total_price' => $total,

            'total_price_hoan_tien' =>
                $totalHoan,

            'receive_user' =>
                $request->receive_user,

            'receive_phone' =>
                $request->receive_phone,

            'receive_address' =>
                $request->receive_address,

            'province_id' =>
                $request->province_id,

            'district_id' =>
                $request->district_id,

            'ward_id' =>
                $request->ward_id,

            'note' =>
                $request->note,

            'status' =>
                'completed',

            'first_order' =>
                $user->parent_id
                    ? 0
                    : 1,

            'price_vat' =>
                $price_vat,
        ] + $this->thongTinMuaApp($request, $fromApp));

        // Order detail
        OrderDetail::create([
            'order_id' =>
                $order->id,

            'product_id' =>
                $product->id,

            'image_product' =>
                $product->image
                    ? $product->image
                    : null,

            'name_product' =>
                $product->name,

            'price_product' =>
                $product->price,

            'quantity' =>
                $qty,

            'the_loai' =>
                'product',
        ]);

        return $order;
    }

    /**
     * device + thoi_gian_mua chỉ có ở đơn đặt qua app.
     * thoi_gian_mua luôn lấy giờ server, không nhận từ client.
     */
    private function thongTinMuaApp(
        Request $request,
        bool    $fromApp
    ): array
    {

        if (!$fromApp) {
            return [
                'device'        => null,
                'thoi_gian_mua' => null,
            ];
        }

        $device = trim((string) $request->input('device'));

        return [
            'device'        => $device !== '' ? mb_substr($device, 0, 255) : null,
            'thoi_gian_mua' => Carbon::now(),
        ];
    }

    // ================================================================
    // 6. MÃ ĐƠN HÀNG TỰ CHỌN
    // ================================================================

    private function maDonHangTuChon(
        Product $parentProduct
    ): string
    {

        $comboNumber =
            $this->getComboNumber(
                $parentProduct
            );

        $prefix = $comboNumber
            ? 'TCCOMBO' . $comboNumber
            : $this->getTuChonPrefix(
                $parentProduct
            );

        return $prefix .
            strtoupper(
                substr(
                    uniqid(),
                    -6
                )
            );
    }

    // ================================================================
    // 7. PREFIX TỰ CHỌN
    // ================================================================

    private function getTuChonPrefix(
        Product $product
    ): string
    {

        $coChe =
            $product->coCheHoanPhi;

        $coCheName = null;
        $coCheParentName = null;

        if ($coChe) {

            $coCheName =
                isset($coChe->name)
                    ? $coChe->name
                    : null;

            if ($coChe->parent) {

                $coCheParentName =
                    isset($coChe->parent->name)
                        ? $coChe->parent->name
                        : null;
            }
        }

        $source = $this->normalizeSearchText(
            trim(
                implode(
                    ' ',
                    array_filter([
                        isset($product->slug)
                            ? $product->slug
                            : null,

                        isset($product->name)
                            ? $product->name
                            : null,

                        $coCheName,

                        $coCheParentName,
                    ])
                )
            )
        );

        $rate = 0;

        if ($coChe) {
            $rate = (float)(
            isset($coChe->tang_1)
                ? $coChe->tang_1
                : 0
            );
        }

        if (
            ($rate == 50.0 ||
                strpos(
                    $source,
                    '50'
                ) !== false
            )
            &&
            strpos(
                $source,
                'binh an'
            ) !== false
        ) {

            return 'TCCOMBO20';
        }

        if (
            $rate == 40.0 ||
            strpos(
                $source,
                '40'
            ) !== false ||
            strpos(
                $source,
                'lien ket'
            ) !== false ||
            strpos(
                $source,
                'splk'
            ) !== false
        ) {

            return 'TCSPLKHP40';
        }

        return 'TCSPLTTLIENKET';
    }

    // ================================================================
    // 8. LẤY COMBO NUMBER
    // ================================================================

    private function getComboNumber(
        Product $product
    ): ?string
    {

        $coChe =
            $product->coCheHoanPhi;

        $coCheName = null;
        $coCheParentName = null;

        if ($coChe) {

            $coCheName =
                isset($coChe->name)
                    ? $coChe->name
                    : null;

            if ($coChe->parent) {

                $coCheParentName =
                    isset($coChe->parent->name)
                        ? $coChe->parent->name
                        : null;
            }
        }

        $source = trim(
            implode(
                ' ',
                array_filter([
                    isset($product->slug)
                        ? $product->slug
                        : null,

                    isset($product->name)
                        ? $product->name
                        : null,

                    $coCheName,

                    $coCheParentName,
                ])
            )
        );

        if (
            preg_match(
                '/combo[\s\-]*([0-9]+[a-z]?)/i',
                $source,
                $matches
            )
        ) {

            return strtoupper(
                $matches[1]
            );
        }

        return null;
    }

    // ================================================================
    // 9. NORMALIZE SEARCH TEXT
    // ================================================================

    private function normalizeSearchText(
        string $text
    ): string
    {

        $normalized =
            @iconv(
                'UTF-8',
                'ASCII//TRANSLIT//IGNORE',
                $text
            );

        return strtolower(
            $normalized !== false
                ? $normalized
                : $text
        );
    }

    // ================================================================
    // 10. GHI LỊCH SỬ GIAO DỊCH
    // ================================================================

    private function ghiLichSuGiaoDich(
        User   $user,
        float  $total,
        float  $totalHoan,
        string $tenSanPham,
        ?int   $productId = null
    ): void
    {

        LichSuGiaoDich::create([
            'admin_id' =>
                $user->id,

            'sender_id' =>
                $user->id,

            'surplus_before' =>
                $user->vi_tien,

            'amount' =>
                $total,

            'surplus' =>
                $user->vi_tien,

            'note' =>
                'Mua ' .
                $tenSanPham,

            'type' =>
                1,

            'hoan_f1' =>
                $totalHoan,

            'product_id' =>
                $productId,

            'surplus_vi_mua' =>
                $user->vi_tien_mua_hang,

            'surplus_vi_mua_befor' =>
                $user->vi_tien_mua_hang -
                $total,
        ]);
    }

    // ================================================================
    // 11. TRỪ VÍ NGƯỜI MUA
    // ================================================================

    private function truViNguoiMua(
        User  $user,
        float $total,
        int   $san_pham_hoan = 0
    ): void
    {

        $updateData = [
            'vi_tien_mua_hang' =>
                $user->vi_tien_mua_hang -
                $total,
        ];

        if ($san_pham_hoan != 1) {

            $updateData['ngay_mua_don_cuoi'] =
                Carbon::now();
        }

        $user->update(
            $updateData
        );
    }

    // ================================================================
    // 12. XỬ LÝ LẦN ĐẦU MUA
    // ================================================================

    private function xuLyLanDauMua(
        User  $user,
        Order $order,
        int   $san_pham_hoan = 0
    ): void
    {

        $order->update([
            'first_order' => 1
        ]);

        if ($san_pham_hoan == 1) {
            return;
        }

        $parentId =
            $this->timCha(
                $user->invite_by
            );

        if (!$parentId) {

            throw new \Exception(
                'Khong tim thay tai khoan cha. Vui long kiem tra lai ma gioi thieu.'
            );
        }

        $parentUser =
            User::find($parentId);

        $levelCha =
            $parentUser
                ? ($parentUser->level ?? 0)
                : 0;

        $updateData = [
            'parent_id' =>
                $parentId,

            'level' =>
                $levelCha + 1,

            'ngay_tham_gia' =>
                Carbon::now(),
        ];

        $user->update(
            $updateData
        );

        $this->xayDungCay(
            $user->fresh(),
            $parentId
        );

        $this->capNhatNguoiGioiThieu(
            $user->invite_by
        );
    }

    // ================================================================
    // 13. TÌM CHA
    // ================================================================

    private function countDescendants(
        int $userId
    ): int
    {

        $count = 0;

        $childrenIds = [
            $userId
        ];

        while (!empty($childrenIds)) {

            $childrenIds = User::whereIn(
                'parent_id',
                $childrenIds
            )
                ->pluck('id')
                ->toArray();

            $count += count(
                $childrenIds
            );
        }

        return $count;
    }

    /**
     * Tìm cha phù hợp theo logic cân bằng.
     */
    public function timCha(
        ?string $inviteBy
    ): ?int
    {

        if (!$inviteBy) {
            return null;
        }

        $nguoiGT = User::where(
            'id',
            $inviteBy
        )
            ->orWhere(
                'tel',
                $inviteBy
            )
            ->orWhere(
                'email',
                $inviteBy
            )
            ->first();

        if (!$nguoiGT) {
            return null;
        }

        $currentLevel = [
            $nguoiGT
        ];

        while (!empty($currentLevel)) {

            $nextLevel = [];

            $candidate = null;

            $minChild = 999;

            foreach (
                $currentLevel as $parent
            ) {

                $children = User::where(
                    'parent_id',
                    $parent->id
                )
                    ->orderBy(
                        'ngay_tham_gia'
                    )
                    ->get();

                $count =
                    $children->count();

                if (
                    $count < 3 &&
                    $count < $minChild
                ) {

                    $candidate =
                        $parent;

                    $minChild =
                        $count;
                }

                foreach (
                    $children as $child
                ) {

                    $nextLevel[] =
                        $child;
                }
            }

            if ($candidate) {
                return $candidate->id;
            }

            $currentLevel =
                $nextLevel;
        }

        return null;
    }

    // ================================================================
    // 14. XÂY DỰNG CÂY
    // ================================================================

    public function xayDungCay(
        User $user,
        ?int $parentId,
        int  $maxDepth = 6
    ): void
    {

        // Tự liên kết depth 0
        DB::table(
            'admin_hierarchy'
        )->updateOrInsert(
            [
                'ancestor_id' =>
                    $user->id,

                'descendant_id' =>
                    $user->id,
            ],
            [
                'depth' => 0
            ]
        );

        $depth = 1;

        $currentId =
            $parentId;

        while (
            $currentId &&
            $depth <= $maxDepth
        ) {

            DB::table(
                'admin_hierarchy'
            )->updateOrInsert(
                [
                    'ancestor_id' =>
                        $currentId,

                    'descendant_id' =>
                        $user->id,
                ],
                [
                    'depth' =>
                        $depth
                ]
            );

            $parent =
                User::find(
                    $currentId
                );

            if (!$parent) {
                break;
            }

            $currentId =
                $parent->parent_id;

            $depth++;
        }
    }

    // ================================================================
    // 15. CẬP NHẬT NGƯỜI GIỚI THIỆU
    // ================================================================

    private function capNhatNguoiGioiThieu(
        string $inviteBy
    ): void
    {

        $nguoiGT = User::where(
            'id',
            $inviteBy
        )
            ->orWhere(
                'tel',
                $inviteBy
            )
            ->orWhere(
                'email',
                $inviteBy
            )
            ->first();

        if (!$nguoiGT) {
            return;
        }

        $nguoiGT->increment(
            'so_thanh_vien_gt'
        );

        if (
            $nguoiGT->fresh()
                ->so_thanh_vien_gt >= 3 &&
            $nguoiGT->vi_tien_status != 1
        ) {

            $nguoiGT->update([
                'vi_tien_status' => 1
            ]);
        }
    }

    // ================================================================
    // 16. CHIA TIỀN HOÀN PHÍ
    // ================================================================

    private function chiaTienHoanPhi(
        User     $user,
        float    $totalHoan,
        string   $productName,
        ?Product $product = null,
        ?int     $productId = null
    ): void
    {

        $status= $this->setSanPhamDuocHoanPhi($product);


        $tyLe =
            $this->layTyLeHoanPhi(
                $product
            );

        $currentParent =
            User::find(
                $user->parent_id
            );

        $level = 1;

        while (
            $currentParent &&
            $level <= 6
        ) {

            $soNguoi = User::where(
                'invite_by',
                $currentParent->id
            )
                ->whereNotNull(
                    'ngay_tham_gia'
                )
                ->count();

            if ($soNguoi == 0) {

                $maxLevel = 2;

            } elseif ($soNguoi == 1) {

                $maxLevel = 3;

            } elseif ($soNguoi == 2) {

                $maxLevel = 4;

            } else {

                $maxLevel = 6;
            }

            $percent =
                isset($tyLe[$level])
                    ? $tyLe[$level]
                    : 0;

            // Nếu cha quá 60 ngày không mua
            if (
                $currentParent->ngay_mua_don_cuoi &&
                Carbon::parse(
                    $currentParent->ngay_mua_don_cuoi
                )->diffInDays(
                    Carbon::now()
                ) >= 61
            ) {

                $currentParent =
                    User::find(
                        $currentParent->parent_id
                    );

                $level++;

                continue;
            }

            if ($currentParent == null) {
                break;
            }

            if ($percent > 0) {

                if ($level <= $maxLevel) {

                    $soTien =
                        (int)round(
                            $percent *
                            $totalHoan /
                            100
                        );

                    $this->congTienChoNguoiNhan(
                        $currentParent,
                        $user,
                        $soTien,
                        $level,
                        $productName,
                        $percent,
                        $productId
                    );

                    // Matching Bonus
                    if (
                        $currentParent->invite_by
                    ) {

                        $status= $this->setSanPhamDuocHoanPhi($product);

                        if($status == true){
                            $this->congTienChoNguoiGioiThieu(
                                $currentParent->invite_by,
                                $currentParent->id,
                                $soTien,
                                0,
                                $productId
                            );
                        }




                    }
                }
            }

            $currentParent =
                User::find(
                    $currentParent->parent_id
                );

            $level++;
        }
    }

    // ================================================================
    // 17. MATCHING BONUS
    // ================================================================

    public function congTienChoNguoiGioiThieu(
        string $receiverId,
        string $f1Id,
        float  $money,
        int    $billId = 0,
        ?int   $productId = null
    ): void
    {

        $settings = Setting::whereIn(
            'name',
            [
                'yeu_cau_f1',
                'hoan_phi_f1',
                'yeu_cau_f1_2',
                'hoan_phi_f1_2',
                'so_ngay_khong_mua_hang',
            ]
        )
            ->pluck(
                'value',
                'name'
            );

        $yeuCauF1 =
            (int)(
            isset($settings['yeu_cau_f1'])
                ? $settings['yeu_cau_f1']
                : 0
            );

        $hoanPhiF1 =
            (float)(
            isset($settings['hoan_phi_f1'])
                ? $settings['hoan_phi_f1']
                : 0
            );

        $yeuCauF1_2 =
            (int)(
            isset($settings['yeu_cau_f1_2'])
                ? $settings['yeu_cau_f1_2']
                : 0
            );

        $hoanPhiF1_2 =
            (float)(
            isset($settings['hoan_phi_f1_2'])
                ? $settings['hoan_phi_f1_2']
                : 0
            );

        $soNgayKhongMuaHang =
            (int)(
            isset(
                $settings['so_ngay_khong_mua_hang']
            )
                ? $settings['so_ngay_khong_mua_hang']
                : 60
            );

        // ============================================================
        // TÌM NGƯỜI NHẬN
        // ============================================================

        $nguoiNhan = User::where(
            'id',
            $receiverId
        )
            ->orWhere(
                'tel',
                $receiverId
            )
            ->orWhere(
                'email',
                $receiverId
            )
            ->first();

        if (
            !$nguoiNhan ||
            $nguoiNhan->active == 0
        ) {
            return;
        }

        // ============================================================
        // KIỂM TRA NGÀY MUA CUỐI
        // ============================================================

        if (
            $nguoiNhan->ngay_mua_don_cuoi
        ) {

            $lastPurchase =
                Carbon::parse(
                    $nguoiNhan->ngay_mua_don_cuoi
                );

            if (
                $lastPurchase->diffInDays(
                    Carbon::now()
                ) > $soNgayKhongMuaHang
            ) {

                return;
            }
        }

        // ============================================================
        // TÌM F1
        // ============================================================

        $f1 = User::find(
            $f1Id
        );

        // ============================================================
        // ĐẾM F1
        // ============================================================

        $countF1 = User::where(
            'invite_by',
            $nguoiNhan->id
        )
            ->whereNotNull(
                'ngay_tham_gia'
            )
            ->count();

        // ============================================================
        // ĐỦ ĐIỀU KIỆN
        // ============================================================

        if (
            $countF1 >= $yeuCauF1
        ) {

            $tiLeHoan =
                $countF1 >= $yeuCauF1_2
                    ? $hoanPhiF1_2
                    : $hoanPhiF1;

            $soTienHoan =
                (int)round(
                    $tiLeHoan *
                    $money /
                    100
                );

            if ($soTienHoan > 0) {

                // Số dư trước
                $truoc =
                    $nguoiNhan->vi_tien;

                // Cộng tiền
                $nguoiNhan->increment(
                    'vi_tien',
                    $soTienHoan
                );

                // ====================================================
                // LỊCH SỬ GIAO DỊCH
                // ====================================================

                LichSuGiaoDich::create([
                    'admin_id' =>
                        $nguoiNhan->id,

                    'receiver_id' =>
                        $nguoiNhan->id,

                    'sender_id' =>
                        $f1Id,

                    'surplus_before' =>
                        $truoc,

                    'amount' =>
                        $soTienHoan,

                    'surplus' =>
                        $truoc +
                        $soTienHoan,

                    'note' =>
                        sprintf(
                            'Hoàn điểm F1 trực tiếp (%d%%) từ %s - %s (Giao dịch: %s)',
                            $tiLeHoan,
                            $f1
                                ? ($f1->name ?? 'N/A')
                                : 'N/A',
                            $f1
                                ? ($f1->tel ?? 'N/A')
                                : 'N/A',
                            number_format(
                                $money,
                                0,
                                ',',
                                '.'
                            )
                        ),

                    'type' =>
                        2,

                    'hoan_f1' =>
                        1,

                    'product_id' =>
                        $productId,
                ]);

                // ====================================================
                // FIREBASE
                // ====================================================

                $this->firebase->sendToUser(
                    $nguoiNhan,

                    'Bạn vừa nhận được hoàn phí F1',

                    'Bạn vừa nhận được ' .
                    number_format(
                        $soTienHoan,
                        0,
                        ',',
                        '.'
                    ) .
                    'đ hoàn phí F1 từ ' .
                    (
                    $f1
                        ? ($f1->name ?? 'thành viên')
                        : 'thành viên'
                    ),

                    [
                        'type' =>
                            'hoan_phi_f1',

                        'amount' =>
                            $soTienHoan,

                        'user_id' =>
                            $f1Id,
                    ],

                    FcmUser::VI_TRI_HOP_TAC_XA
                );

                // ====================================================
                // DATABASE NOTIFICATION
                // ====================================================

                $this->notifyF1RefundReceived(
                    $nguoiNhan,
                    $soTienHoan,
                    $f1
                        ? ($f1->name ?? null)
                        : null
                );

                // ====================================================
                // CLEAR CACHE
                // ====================================================

                Cache::forget(
                    'user_dashboard_stats_' .
                    $nguoiNhan->id
                );

                // ====================================================
                // ĐỆ QUY
                // ====================================================

                if (
                    $nguoiNhan->invite_by
                ) {

                    $this->congTienChoNguoiGioiThieu(
                        $nguoiNhan->invite_by,
                        $nguoiNhan->id,
                        $soTienHoan,
                        $billId,
                        $productId
                    );
                }
            }
        }
    }

    // ================================================================
    // 18. LẤY TỶ LỆ HOÀN PHÍ
    // ================================================================

    private function layTyLeHoanPhi(
        ?Product $product = null
    ): array
    {

        if (
            $product &&
            $product->co_che_hoan_phi_id
        ) {

            $coChe =
                CoCheHoanPhi::find(
                    $product->co_che_hoan_phi_id
                );

            if ($coChe) {
                return $coChe->getRates();
            }
        }

        $keyMap = [
            1 => 'hoan_phi_nhom_mot',
            2 => 'hoan_phi_nhom_hai',
            3 => 'hoan_phi_nhom_ba',
            4 => 'hoan_phi_nhom_bon',
            5 => 'hoan_phi_nhom_nam',
            6 => 'hoan_phi_nhom_sau',
        ];

        $settings = Setting::whereIn(
            'name',
            array_values($keyMap)
        )
            ->pluck(
                'value',
                'name'
            );

        $tyLe = [];

        foreach (
            $keyMap as $cap => $key
        ) {

            $tyLe[$cap] =
                (float)(
                isset($settings[$key])
                    ? $settings[$key]
                    : 0
                );
        }

        return $tyLe;
    }

    // ================================================================
    // 19. TÍNH MAX LEVEL
    // ================================================================

    /**
     * PHP 7.4 không có match()
     */
    private function tinhMaxLevel(
        int $soNguoiGT
    ): int
    {

        if ($soNguoiGT == 0) {
            return 2;
        }

        if ($soNguoiGT == 1) {
            return 3;
        }

        if ($soNguoiGT == 2) {
            return 4;
        }

        return 6;
    }


    private function congTienChoNguoiNhan(
        User   $nguoiNhan,
        User   $nguoiMua,
        float  $soTien,
        int    $cap,
        string $productName,
        float  $percent,
        ?int   $productId = null
    ): void
    {

        if ($soTien <= 0) {
            return;
        }

        // Số dư trước
        $truoc =
            $nguoiNhan->vi_tien;

        // Cộng tiền
        $nguoiNhan->increment(
            'vi_tien',
            $soTien
        );


        LichSuGiaoDich::create([
            'admin_id' => $nguoiNhan->id,

            'receiver_id' => $nguoiNhan->id,
            'sender_id' => $nguoiMua->id,

            'surplus_before' => $truoc,
            'amount' => $soTien,
            'surplus' => $truoc + $soTien,

            'note' => 'Hoàn phí ' . $percent . '% NHÓM TIÊU DÙNG ' . $cap . ' của ' . $nguoiMua->name . ' mua ' . $productName,
            'type' => 2, 'hoan_f1' => $soTien,
            'product_id' => $productId,
        ]);

        Log::info('Bạn vừa nhận được hoàn phí');

        $this->firebase->sendToUser(
            $nguoiNhan,

            'Bạn vừa nhận được hoàn phí',

            'Bạn vừa nhận được ' .
            number_format(
                $soTien,
                0,
                ',',
                '.'
            ) .
            'đ hoàn phí cấp ' .
            $cap .
            '.',

            [
                'type' =>
                    'hoan_phi',

                'amount' =>
                    $soTien,

                'level' =>
                    $cap,

                'product_id' =>
                    $productId
                        ? $productId
                        : 0,

                'product_name' =>
                    $productName,
            ],

            FcmUser::VI_TRI_HOP_TAC_XA
        );


        $this->notifyRefundReceived(
            $nguoiNhan,
            $soTien,
            $cap
        );


        Cache::forget(
            'user_dashboard_stats_' .
            $nguoiNhan->id
        );
    }

    // ================================================================
    // 21. CLEAR DASHBOARD CACHE
    // ================================================================

    private function clearDashboardCache(
        int $userId
    ): void
    {

        Cache::forget(
            'admin_dashboard_stats'
        );

        Cache::forget(
            'admin_dashboard_top_users'
        );

        Cache::forget(
            'admin_dashboard_category_sales_v3'
        );

        Cache::forget(
            'homepage_data'
        );

        Cache::forget(
            'user_dashboard_stats_' .
            $userId
        );
    }

    // ================================================================
    // 22. THÊM KHUYẾN MẠI
    // ================================================================

    /**
     * Thêm đơn hàng vào order_detail
     * nếu sản phẩm đang có khuyến mại trong tháng.
     */
    public function addProductPromotion(
        $order,
        $product
    )
    {

        $month = date('m');
        $year = date('Y');

        // ============================================================
        // TÌM KHUYẾN MẠI ĐANG HOẠT ĐỘNG
        // ============================================================

        $promotion =
            Promotion::activeThisMonth();

        if (!$promotion) {
            return;
        }

        // ============================================================
        // TÌM SẢN PHẨM KHUYẾN MẠI
        // ============================================================

        $productPromotion =
            $promotion
                ->productPromotions()
                ->where(
                    'product_id',
                    $product->id
                )
                ->with(
                    'gifts.giftProduct'
                )
                ->first();

        if (!$productPromotion) {
            return;
        }

        // ============================================================
        // THÊM QUÀ
        // ============================================================

        foreach (
            $productPromotion->gifts
            as $gift
        ) {

            // Nếu là tặng sản phẩm
            if (
                $gift->isProductGift()
            ) {

                OrderDetail::create([
                    'order_id' =>
                        $order->id,

                    'product_id' =>
                        $gift
                            ->giftProduct
                            ->id,

                    'image_product' =>
                        $gift
                            ->giftProduct
                            ->image,

                    'name_product' =>
                        $gift
                            ->giftProduct
                            ->name,

                    'price_product' =>
                        0,

                    'quantity' =>
                        $gift->quantity,

                    'the_loai' =>
                        'promotion',
                ]);
            }
        }
    }

    // ================================================================
    // 23. NOTIFICATION
    // ================================================================

    /**
     * Function chung để tạo notification.
     *
     * Tất cả notification trong OrderService
     * nên đi qua function này.
     */
    private function createNotification(
        User   $user,
        string $title,
        string $content,
        ?int $orderId = null,
        ?string $viTri = null,
        string $type = Notification::TYPE_ALL
    ): void
    {

        Notification::create([
            'title' =>
                $title,

            'target_id' =>
                $user->id,

            'target_type' =>
                'user',

            'content' =>
                $content,


            'order_id'=>$orderId ?? null,

            // Website tạo thông báo (theo VI_TRI_WEB) — chỉ hiện ở website đó
            'vi_tri' =>
                $viTri ?? FcmUser::defaultViTri(),
            'type' => $type,

        ]);
    }

    /**
     * ================================================================
     * THÔNG BÁO MUA HÀNG
     * ================================================================
     *
     * Dùng cho:
     *
     * 1. Mua sản phẩm bình thường
     * 2. Mua tự chọn
     */
    private function notifyOrderCreated(
        User   $user,
        Order  $order,
        string $productName
    ): void
    {

        $this->createNotification(
            $user,

            'Mua hàng thành công',

            'Bạn vừa mua sản phẩm ' .
            $productName,
            $order->id,
            Notification::TYPE_MUA_HANG
        );
    }

    /**
     * ================================================================
     * THÔNG BÁO NHẬN HOÀN PHÍ
     * ================================================================
     */
    private function notifyRefundReceived(
        User  $user,
        float $amount,
        int   $level,

    ): void
    {
$orderId = null;
        $this->createNotification(
            $user,

            'Hoàn phí',

            'Bạn vừa nhận được ' .
            number_format(
                $amount,
                0,
                ',',
                '.'
            ) .
            'đ hoàn phí cấp ' .
            $level .
            '.',
            null,
            null,
            Notification::TYPE_HOAN_PHI
        );
    }

    /**
     * ================================================================
     * THÔNG BÁO NHẬN HOÀN PHÍ F1
     * ================================================================
     */
    private function notifyF1RefundReceived(
        User    $user,
        float   $amount,
        ?string $memberName = null
    ): void
    {
        $orderId = null;
        $this->createNotification(
            $user,

            'Hoàn phí F1',

            'Bạn vừa nhận được ' .
            number_format(
                $amount,
                0,
                ',',
                '.'
            ) .
            'đ hoàn phí F1 từ ' .
            (
            $memberName
                ? $memberName
                : 'thành viên'
            ),
            null,
            null,
            Notification::TYPE_HOAN_PHI
        );
    }
    private function setSanPhamDuocHoanPhi($prodcuct){
        // lấy các danh mục con cuủa cơ chế hoàn phí này đi

        $danhMucHoanPhiIds= CoCheHoanPhi::where('parent_id',10 )->pluck('id')->toArray();

     if(in_array($prodcuct->co_che_hoan_phi_id, $danhMucHoanPhiIds)){
         return true;
     }
     return false;

    }

}