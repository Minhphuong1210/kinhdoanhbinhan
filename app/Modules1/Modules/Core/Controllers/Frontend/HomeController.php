<?php

namespace App\Modules\Core\Controllers\Frontend;

use App\Http\Controllers\Controller;
use App\Models\Category;
use App\Models\CategoryPost;
use App\Models\GioiThieu;
use App\Models\Leader;
use App\Models\Order;
use App\Models\Post;
use App\Models\Product;
use App\Models\Widget;
use Illuminate\Support\Facades\Cache;

class HomeController extends Controller
{
    public function index()
    {
        // Cache toàn bộ dữ liệu trang chủ trong 60 phút
        $data = \Cache::remember('homepage_data', 3600, function() {
            // ---- Sản phẩm trang chủ ----
            $products = Product::where('hien_trang_chu', 1)
                ->where('active', 1)
                ->latest()
                ->take(8)
                ->get();

            // ---- Top sản phẩm (noi bat) ----
            $topProducts = Product::where('active', 1)
                ->where('san_pham_noi_bat', 1)
                ->orderByDesc('view')
                ->take(6)
                ->get();

            // ---- Giới thiệu về chúng tôi ----
            $about_us = GioiThieu::forWeb()
                ->where('vi_tri', 've_chung_toi')
                ->first();



            if (!$about_us) {
                $about_us = GioiThieu::forWeb()->first();
            }

            // ---- Câu chuyện ----
            $cauChuyen = GioiThieu::forWeb()
                ->where('vi_tri', '!=', 'gioi_thieu_5')
                ->orderBy('id')
                ->get();

            // ---- Tin tức mới nhất ----
            $posts = Post::forWeb()
                ->where('status', 1)
                ->latest()
                ->take(6)
                ->get();

            // ---- Danh mục tin tức ----
            $categories = CategoryPost::where('active', 1)->get();

            // ---- Đơn hàng mới nhất cho marquee ticker ----
            $latestOrders = Order::with('user')
                ->where('status', 'completed')
                ->latest()
                ->take(20)
                ->get();

            // ---- Đội ngũ lãnh đạo ----
            $leaders = Leader::where('status', 1)
                ->orderBy('order_no', 'desc')
                ->limit(8)
                ->get();

            // ---- Danh mục sản phẩm ----
            $productCategories = Category::where('active', 1)
                ->whereNull('parent_id')
                ->withCount(['products' => function ($q) {
                    $q->where('active', 1);
                }])
                ->orderBy('name')
                ->get();

            // ---- Mô hình kinh doanh ----
            $kinh_doanh = Widget::where('location', 'mo_hinh_kinh_doanh_home')
                ->where('status', 1)
                ->first();

            // ---- Cảm nhận khách hàng ----
            $cam_nhan = Widget::where('location', 'Cảm nhận khách hàng')
                ->orderBy('order_no', 'desc')
                ->where('status', 1)
                ->limit(6)
                ->get();

            return compact(
                'products', 'topProducts',
                'about_us', 'cauChuyen',
                'posts', 'categories',
                'latestOrders',
                'leaders',
                'kinh_doanh', 'cam_nhan',
                'productCategories'
            );
        });

        return view('Core::Frontend.home.index', $data);
    }
}
